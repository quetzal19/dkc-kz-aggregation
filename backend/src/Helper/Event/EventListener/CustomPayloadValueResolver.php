<?php

namespace App\Helper\Event\EventListener;

use App\Helper\Validator\ValidatorService;
use LogicException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Serializer\{Exception\UnsupportedFormatException,
    SerializerInterface,
    Normalizer\DenormalizerInterface,
    Exception\PartialDenormalizationException,
    Exception\NotEncodableValueException
};
use Symfony\Component\HttpKernel\{ControllerMetadata\ArgumentMetadata,
    Controller\ValueResolverInterface,
    KernelEvents,
    Exception\HttpException,
    Event\ControllerArgumentsEvent,
    Attribute\MapRequestPayload,
    Attribute\MapQueryString
};
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Validator\{Validator\ValidatorInterface, ConstraintViolationList, ConstraintViolation};
use Symfony\Component\HttpFoundation\{Response, Request};

final readonly class CustomPayloadValueResolver implements ValueResolverInterface, EventSubscriberInterface
{
    /**
     * @see \Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT
     * @see DenormalizerInterface::COLLECT_DENORMALIZATION_ERRORS
     */
    private const CONTEXT_DENORMALIZE = [
        'disable_type_enforcement' => true,
        'collect_denormalization_errors' => true,
    ];

    /**
     * @see DenormalizerInterface::COLLECT_DENORMALIZATION_ERRORS
     */
    private const CONTEXT_DESERIALIZE = [
        'collect_denormalization_errors' => true,
    ];

    public function __construct(
        private SerializerInterface&DenormalizerInterface $serializer,
        private ValidatorService $validatorService,
        private ?ValidatorInterface $validator = null,
        private ?TranslatorInterface $translator = null,
    ) {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $attribute = $argument->getAttributesOfType(MapQueryString::class, ArgumentMetadata::IS_INSTANCEOF)[0]
            ?? $argument->getAttributesOfType(MapRequestPayload::class, ArgumentMetadata::IS_INSTANCEOF)[0]
            ?? null;

        if (!$attribute) {
            return [];
        }

        $attribute->metadata = $argument;

        return [$attribute];
    }

    public function onKernelControllerArguments(ControllerArgumentsEvent $event): void
    {
        $arguments = $event->getArguments();

        foreach ($arguments as $i => $argument) {
            if ($argument instanceof MapQueryString) {
                $payloadMapper = 'mapQueryString';
                $validationFailedCode = Response::HTTP_NOT_FOUND;
            } elseif ($argument instanceof MapRequestPayload) {
                $payloadMapper = 'mapRequestPayload';
                $validationFailedCode = Response::HTTP_UNPROCESSABLE_ENTITY;
            } else {
                continue;
            }
            $request = $event->getRequest();

            if (!$type = $argument->metadata->getType()) {
                throw new LogicException(
                    sprintf(
                        'Could not resolve the "$%s" controller argument: argument should be typed.',
                        $argument->metadata->getName()
                    )
                );
            }

            if ($this->validator) {
                $violations = new ConstraintViolationList();
                try {
                    $payload = $this->$payloadMapper($request, $type, $argument);
                } catch (PartialDenormalizationException $e) {
                    $trans = $this->translator ? $this->translator->trans(...) : static fn($m, $p) => strtr($m, $p);
                    foreach ($e->getErrors() as $error) {
                        $parameters = ['{{ type }}' => implode('|', $error->getExpectedTypes())];
                        if ($error->canUseMessageForUser()) {
                            $parameters['hint'] = $error->getMessage();
                        }

                        if ($parameters['{{ type }}'] === 'unknown') {
                            $template = 'Значение некорректного типа.';
                        } else {
                            $template = 'Значение должно быть типа {{ type }}.';
                        }

                        $message = $trans($template, $parameters, 'validators');
                        $violations->add(
                            new ConstraintViolation($message, $template, $parameters, null, $error->getPath(), null)
                        );
                    }
                    $payload = $e->getData();
                }

                if (null !== $payload) {
                    $violationGroups = [
                        'pagination',
                        'Default',
                        ... is_string(
                            $argument->validationGroups
                        ) ? [$argument->validationGroups] : $argument->validationGroups ?? [],
                    ];

                    $violations->addAll($this->validator->validate($payload, null, $violationGroups));
                }

                $this->validatorService->checkValidations($violations);
            } else {
                try {
                    $payload = $this->$payloadMapper($request, $type, $argument);
                } catch (PartialDenormalizationException $e) {
                    throw new HttpException(
                        $validationFailedCode,
                        implode("\n", array_map(static fn($e) => $e->getMessage(), $e->getErrors())),
                        $e
                    );
                }
            }

            if (null === $payload && !$argument->metadata->isNullable()) {
                throw new HttpException($validationFailedCode);
            }

            $arguments[$i] = $payload;
        }

        $event->setArguments($arguments);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER_ARGUMENTS => 'onKernelControllerArguments',
        ];
    }

    private function mapQueryString(Request $request, string $type, MapQueryString $attribute): ?object
    {
        return $this->serializer->denormalize(
            data: $request->query->all(),
            type: $type,
            context: self::CONTEXT_DENORMALIZE + $attribute->serializationContext,
        );
    }

    private function mapRequestPayload(Request $request, string $type, MapRequestPayload $attribute): ?object
    {
        if (null === $format = $request->getContentTypeFormat()) {
            throw new HttpException(Response::HTTP_UNSUPPORTED_MEDIA_TYPE, 'Unsupported format.');
        }

        if ($attribute->acceptFormat && !in_array($format, (array)$attribute->acceptFormat, true)) {
            throw new HttpException(
                Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
                sprintf(
                    'Unsupported format, expects "%s", but "%s" given.',
                    implode('", "', (array)$attribute->acceptFormat),
                    $format
                )
            );
        }

        if ($data = $request->request->all()) {
            return $this->serializer->denormalize(
                $data,
                $type,
                null,
                self::CONTEXT_DENORMALIZE + $attribute->serializationContext
            );
        }

        if ('' === $data = $request->getContent()) {
            return null;
        }

        if ('form' === $format) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Некорректный form формат.');
        }

        try {
            return $this->serializer->deserialize(
                $data,
                $type,
                $format,
                self::CONTEXT_DESERIALIZE + $attribute->serializationContext
            );
        } catch (UnsupportedFormatException $e) {
            throw new HttpException(
                Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
                sprintf('Unsupported format: "%s".', $format),
                $e
            );
        } catch (NotEncodableValueException $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, sprintf('Некорректный %s формат.', $format), $e);
        }
    }
}

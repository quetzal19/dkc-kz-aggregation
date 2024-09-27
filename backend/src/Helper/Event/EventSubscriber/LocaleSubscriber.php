<?php

namespace App\Helper\Event\EventSubscriber;

use App\Helper\{Enum\LocaleType, Validator\ValidatorService};
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

final readonly class LocaleSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private ValidatorService $validatorService,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        $locale = $request->headers->get('x-language-id', LocaleType::getDefaultLocaleName());

        $this->validatorService->validateLocale($locale);

        $request->setLocale($locale);
    }
}
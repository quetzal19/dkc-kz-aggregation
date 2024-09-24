<?php

namespace App\Helper\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ApiException extends HttpException
{
    public function __construct(
        protected $message = null,
        protected ?string $detail = null,
        protected array $validationError = ['query' => [], 'body' => []],
        protected int $status = Response::HTTP_BAD_REQUEST,
        HttpException $previous = null,
        array $headers = [],
        ?int $code = 0,
    ) {
        $this->message = is_null($message) ? Response::$statusTexts[$status] : $message;

        parent::__construct($status, $message, $previous, $headers, $code);
    }

    public function getResponseBody(): array
    {
        return [
            'status' => $this->status,
            'message' => $this->message,
            'detail' => $this->detail,
            'validationError' => $this->validationError,
        ];
    }
}
<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

abstract class Exception extends \Exception implements HttpExceptionInterface
{
    /**
     * HTTP error code
     *
     * @var int
     * @link https://httpstatuses.com/
     */
    protected int $http_status_code = 400;

    /**
     * Internal numeric error code
     *
     * @var int
     */
    protected $code = 0;

    /**
     * Alphanumerical description for internal error code
     *
     * @var string
     */
    protected string $code_error;

    /**
     * Send to the parent constructor our custom message and code
     *
     * @link https://php.net/manual/en/exception.construct.php
     */
    public function __construct($previous = null, $message = null)
    {
        parent::__construct($message ?: $this->message(), $this->code, $previous);
    }

    /**
     * Custom message to resume the error type
     *
     * @return string
     */
    abstract public function message(): string;

    /**
     * Accessor to get HTTP error code
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->http_status_code;
    }

    /**
     * Accessor to get HTTP error Headers
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return [];
    }

    /**
     * Accessor to get HTTP error description
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description();
    }

    /**
     * Custom description to give more details about the error
     *
     * @return string
     */
    abstract public function description(): string;

    /**
     * Accessor to get internal alphanumeric error code
     *
     * @return string
     */
    public function getCodeError(): string
    {
        return $this->code_error;
    }
}

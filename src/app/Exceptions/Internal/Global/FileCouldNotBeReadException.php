<?php

namespace App\Exceptions\Internal\Global;

use App\Exceptions\Exception;

class FileCouldNotBeReadException extends Exception
{
    /**
     * Internal numeric error code
     *
     * @var int
     */
    protected $code = 101002;

    /**
     * Alphanumerical description for internal error code.
     *
     * @var string
     */
    protected string $code_error = 'file_could_not_be_read';

    /**
     * @return string
     */
    public function description(): string
    {
        return __('File could not be read.');
    }

    /**
     * @return string
     */
    public function message(): string
    {
        return __('The file is missing or invalid. Please, check the file path and content.');
    }
}

<?php

namespace App\Exceptions\Internal\Global;

use App\Exceptions\Exception;

class CustomDataSourceDisabledException extends Exception
{
    /**
     * Internal numeric error code
     *
     * @var int
     */
    protected $code = 101001;

    /**
     * Alphanumerical description for internal error code.
     *
     * @var string
     */
    protected string $code_error = 'custom_data_source_disabled';

    /**
     * @return string
     */
    public function description(): string
    {
        return __('Custom data source disabled.');
    }

    /**
     * @return string
     */
    public function message(): string
    {
        return __('The custom data source is disabled.');
    }
}

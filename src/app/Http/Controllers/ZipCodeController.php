<?php

namespace App\Http\Controllers;

use App\Http\Resources\ZipCodeResource;
use App\Services\ZipCodeService;

class ZipCodeController extends Controller
{
    private ZipCodeService $service;

    public function __construct(ZipCodeService $service)
    {
        $this->service = $service;
    }

    /**
     * Get a zip code resource.
     *
     * @param string $zipCode
     * @return ZipCodeResource
     */
    public function __invoke(string $zipCode)
    {
        return new ZipCodeResource($this->service->getZipCode($zipCode));
    }
}

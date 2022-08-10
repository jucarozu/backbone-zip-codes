<?php

namespace App\Services;

use App\Models\ZipCode;

class ZipCodeService
{
    /**
     * Get a zip code with its federal entity, municipality and settlements.
     *
     * @param string $zipCode
     * @return ZipCode
     */
    public function getZipCode(string $zipCode)
    {
        return \Cache::remember(
            'zipCode:' . $zipCode,
            now()->addDay(),
            fn () =>
                ZipCode::whereZipCode($zipCode)
                    ->with([
                        'municipality.federalEntity',
                        'settlements.settlementType'
                    ])
                    ->firstOrFail()
        );
    }
}

<?php

namespace Tests\Feature;

use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ZipCodeControllerTest extends TestCase
{
    /**
     * Test if the response has a right structure and data.
     *
     * @return void
     */
    public function test_get_zip_code_successfully()
    {
        $response = $this->get(sprintf('/api/zip-codes/%s', '01210'));

        $response->assertOk();

        $response->assertJson(fn (AssertableJson $json) => $json
            ->where('zip_code', '01210')
            ->where('locality', 'CIUDAD DE MEXICO')
            ->has('federal_entity', fn ($json) => $json
                ->where('key', 9)
                ->where('name', 'CIUDAD DE MEXICO')
                ->where('code', null)
            )
            ->has('settlements', 1, fn ($json) => $json
                ->where('key', 82)
                ->where('name', 'SANTA FE')
                ->where('zone_type', 'URBANO')
                ->has('settlement_type', fn ($json) => $json
                    ->where('name', 'Pueblo')
                )
            )
            ->has('municipality', fn ($json) => $json
                ->where('key', 10)
                ->where('name', 'ALVARO OBREGON')
            )
        );
    }
}

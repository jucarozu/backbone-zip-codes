<?php

namespace Database\Seeders;

use App\Exceptions\Internal\Global\CustomDataSourceDisabledException;
use App\Exceptions\Internal\Global\FileCouldNotBeReadException;
use App\Models\FederalEntity;
use App\Models\Municipality;
use App\Models\Settlement;
use App\Models\SettlementType;
use App\Models\ZipCode;
use Illuminate\Database\Seeder;

class ZipCodeSeeder extends Seeder
{
    /**
     * Insert data with federal entities, municipalities, zip codes, settlements types and settlements.
     *
     * @return void
     * @throws \App\Exceptions\Internal\Global\CustomDataSourceDisabledException
     * @throws \App\Exceptions\Internal\Global\FileCouldNotBeReadException
     */
    public function run()
    {
        // Check if the application is enabled to load data from an external source.
        if (!config('services.custom.data.source.enabled')) {
            throw new CustomDataSourceDisabledException();
        }

        // Load data from a configured txt file.
        $source = \Storage::get(config('services.custom.data.source.path'));

        // Check if the file exists and its content is valid.
        if (!$source) {
            throw new FileCouldNotBeReadException();
        }

        // Init keys.
        $keys = null;

        // Get rows from the content file.
        $rows = collect(explode("\n", $source));

        // Create data structure with keys and rows.
        $rows = $rows
            ->map(function ($row) use (&$keys) {
                // Check if the row is empty.
                if (empty($row)) {
                    return [];
                }

                // Get values from the current row.
                $row = collect(explode("|", $row));

                // Set all empty values to null.
                $row = $row->map(fn ($value) => !empty($value) ? $value : null);

                // Set keys from the first row.
                if (!$keys) {
                    $keys = $row;
                    return [];
                }

                // Combine keys with corresponding row values.
                return $keys->combine($row);
            })
            ->filter()
            ->values();

        // Init the bulk inserts to the tables with its relationships.

        \Log::info('Bulk insert started.');

        \DB::beginTransaction();

        #region Federal entities

        \Log::info('Inserting federal entities...');

        // Build each federal entity record from the rows in the file
        // and chunk them into groups for bulk inserts in the federal_entities table.
        // If there are duplicate records, skip them.
        // Also, validate with the upsert method if the current record has already been inserted, then update it.
        $rows
            ->map(fn ($federalEntity) => [
                'key' => (int)$federalEntity['c_estado'],
                'name' => replace_accents(utf8_encode($federalEntity['d_estado'])),
            ])
            ->unique()
            ->chunk(1000)
            ->each(function ($subset) {
                FederalEntity::upsert(
                    $subset->toArray(),
                    ['key']
                );
            });

        // Get all the federal entities and build an array where
        // the index is made up of the federal entity key (from the text file)
        // and the value is the ID of each newly inserted records (primary key),
        // to be later obtained as a foreign key on the insertion of the municipalities.
        FederalEntity::query()
            ->select(
                'federal_entities.id', 'federal_entities.key'
            )
            ->get()
            ->each(function ($federalEntity) use (&$federalEntities) {
                $federalEntities[$federalEntity->key] = $federalEntity->id;
            });

        #endregion

        #region Municipalities

        \Log::info('Inserting municipalities...');

        // Build each municipality record from the rows in the file
        // and chunk them into groups for bulk inserts in the municipalities table.
        // If there are duplicate records, skip them.
        // Also, validate with the upsert method if the current record has already been inserted, then update it.
        $rows
            ->map(fn ($municipality) => [
                'key' => (int)$municipality['c_mnpio'],
                'name' => replace_accents(utf8_encode($municipality['D_mnpio'])),
                'federal_entity_id' => $federalEntities[(int)$municipality['c_estado']],
            ])
            ->unique()
            ->chunk(1000)
            ->each(function ($subset) {
                Municipality::upsert(
                    $subset->toArray(),
                    ['key', 'federal_entity_id']
                );
            });

        // Get all the municipalities and build an array where
        // the index is made up of the federal entity key and the municipality key (from the text file)
        // and the value is the ID of each newly inserted records (primary key),
        // to be later obtained as a foreign key on the insertion of the zip codes.
        Municipality::query()
            ->select(
                'municipalities.id', 'municipalities.key', 'federal_entities.key AS federal_entity_key'
            )
            ->join(
                'federal_entities',
                'federal_entities.id',
                'municipalities.federal_entity_id'
            )
            ->get()
            ->each(function ($municipality) use (&$municipalities) {
                $municipalities[$municipality->federal_entity_key][$municipality->key] = $municipality->id;
            });

        #endregion

        #region Zip codes

        \Log::info('Inserting zip codes...');

        // Build each zip code record from the rows in the file
        // and chunk them into groups for bulk inserts in the zip_codes table.
        // If there are duplicate records, skip them.
        // Also, validate with the upsert method if the current record has already been inserted, then update it.
        $rows
            ->map(fn ($zipCode) => [
                'zip_code' => $zipCode['d_codigo'],
                'locality' => replace_accents(utf8_encode($zipCode['d_ciudad'])),
                'municipality_id' => $municipalities[(int)$zipCode['c_estado']][(int)$zipCode['c_mnpio']],
            ])
            ->unique()
            ->chunk(1000)
            ->each(function ($subset) {
                ZipCode::upsert(
                    $subset->toArray(),
                    ['zip_code', 'locality', 'municipality_id']
                );
            });

        // Get all the zip codes and build an array where
        // the index is made up of the municipality key and the zip code (from the text file)
        // and the value is the ID of each newly inserted records (primary key),
        // to be later obtained as a foreign key on the insertion of the settlements.
        ZipCode::query()
            ->select(
                'zip_codes.id', 'zip_codes.zip_code', 'municipalities.key AS municipality_key'
            )
            ->join(
                'municipalities',
                'municipalities.id',
                'zip_codes.municipality_id'
            )
            ->get()
            ->each(function ($zipCode) use (&$zipCodes) {
                $zipCodes[$zipCode->municipality_key][$zipCode->zip_code] = $zipCode->id;
            });

        #endregion

        #region Settlement types

        \Log::info('Inserting settlement types...');

        // Build each settlement type record from the rows in the file
        // and chunk them into groups for bulk inserts in the settlement_types table.
        // If there are duplicate records, skip them.
        // Also, validate with the upsert method if the current record has already been inserted, then update it.
        $rows
            ->map(fn ($settlementType) => [
                'key' => (int)$settlementType['c_tipo_asenta'],
                'name' => replace_accents(utf8_encode($settlementType['d_tipo_asenta'])),
            ])
            ->unique()
            ->chunk(1000)
            ->each(function ($subset) {
                SettlementType::upsert(
                    $subset->toArray(),
                    ['key']
                );
            });

        // Get all the settlement types and build an array where
        // the index is made up of the settlement type key (from the text file)
        // and the value is the ID of each newly inserted records (primary key),
        // to be later obtained as a foreign key on the insertion of the settlements.
        SettlementType::query()
            ->select(
                'settlement_types.id', 'settlement_types.key'
            )
            ->get()
            ->each(function ($settlementType) use (&$settlementTypes) {
                $settlementTypes[$settlementType->key] = $settlementType->id;
            });

        #endregion

        #region Settlements

        \Log::info('Inserting settlements...');

        // Build each settlement record from the rows in the file
        // and chunk them into groups for bulk inserts in the settlements table.
        // If there are duplicate records, skip them.
        // Also, validate with the upsert method if the current record has already been inserted, then update it.
        $rows
            ->map(fn ($settlement) => [
                'key' => (int)$settlement['id_asenta_cpcons'],
                'name' => replace_accents(utf8_encode($settlement['d_asenta'])),
                'zone_type' => replace_accents(utf8_encode($settlement['d_zona'])),
                'settlement_type_id' => $settlementTypes[(int)$settlement['c_tipo_asenta']],
                'zip_code_id' => $zipCodes[(int)$settlement['c_mnpio']][$settlement['d_codigo']],
            ])
            ->unique()
            ->chunk(1000)
            ->each(function ($subset) {
                Settlement::upsert(
                    $subset->toArray(),
                    ['key', 'zip_code_id']
                );
            });

        #endregion

        \DB::commit();

        \Log::info('Bulk insert completed.');
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Province;
use App\Models\Regency;
use App\Models\District;
use App\Models\Village;

class WilayahSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |---------------------------------
        | PROVINCES
        |---------------------------------
        */
        $file = fopen(
            public_path('storage/wilayah/provinces.csv'),
            'r'
        );

        while (($row = fgetcsv($file)) !== false) {

            Province::create([
                'id'   => $row[0],
                'name' => $row[1],
            ]);
        }

        fclose($file);

        /*
        |---------------------------------
        | REGENCIES
        |---------------------------------
        */
        $file = fopen(
            public_path('storage/wilayah/regencies.csv'),
            'r'
        );

        while (($row = fgetcsv($file)) !== false) {

            Regency::create([
                'id'          => $row[0],
                'province_id' => $row[1],
                'name'        => $row[2],
            ]);
        }

        fclose($file);

        /*
        |---------------------------------
        | DISTRICTS
        |---------------------------------
        */
        $file = fopen(
            public_path('storage/wilayah/districts.csv'),
            'r'
        );

        while (($row = fgetcsv($file)) !== false) {

            District::create([
                'id'         => $row[0],
                'regency_id' => $row[1],
                'name'       => $row[2],
            ]);
        }

        fclose($file);

        /*
        |---------------------------------
        | VILLAGES
        |---------------------------------
        */
        $file = fopen(
            public_path('storage/wilayah/villages.csv'),
            'r'
        );

        while (($row = fgetcsv($file)) !== false) {

            Village::updateOrCreate(
                [
                    'id' => $row[0]
                ],
                [
                    'district_id' => $row[1],
                    'name' => $row[2]
                ]
            );
        }

        fclose($file);
    }
}

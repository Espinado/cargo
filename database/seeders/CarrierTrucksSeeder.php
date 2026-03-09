<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Truck;
use Illuminate\Database\Seeder;

/**
 * По 20 грузовиков на каждую компанию — наши перевозчики (type=forwarder). Все поля заполнены, фейковое фото техпаспорта.
 */
class CarrierTrucksSeeder extends Seeder
{
    private const TRUCKS_PER_CARRIER = 20;

    private const BRANDS = ['Volvo', 'Scania', 'MAN', 'Mercedes-Benz', 'DAF', 'Iveco', 'Renault', 'Scania', 'Volvo', 'MAN'];

    private const MODELS = [
        'Volvo' => ['FH', 'FH16', 'FM', 'FE'],
        'Scania' => ['R450', 'R500', 'S500', 'G450'],
        'MAN' => ['TGX', 'TGS', 'TGM'],
        'Mercedes-Benz' => ['Actros', 'Arocs', 'Econic'],
        'DAF' => ['XF', 'CF', 'LF'],
        'Iveco' => ['S-Way', 'Stralis', 'Eurocargo'],
        'Renault' => ['T', 'T High', 'C'],
    ];

    public function run(): void
    {
        $carriers = Company::where('type', 'forwarder')->get();
        if ($carriers->isEmpty()) {
            $this->command->warn('Нет компаний с type=forwarder. Сначала выполните CarrierExpeditorCompaniesSeeder.');
            return;
        }

        $faker = \Faker\Factory::create();

        foreach ($carriers as $company) {
            for ($i = 0; $i < self::TRUCKS_PER_CARRIER; $i++) {
                $brand = $faker->randomElement(self::BRANDS);
                $models = self::MODELS[$brand] ?? ['Model ' . ($i + 1)];
                $model = $faker->randomElement($models);

                $plate = 'LV-' . strtoupper($faker->unique()->bothify('??####'));
                $vin = strtoupper($faker->unique()->regexify('[A-HJ-NPR-Z0-9]{17}'));

                $inspectionIssued = $faker->dateTimeBetween('-2 years', '-6 months');
                $inspectionExpired = (clone $inspectionIssued)->modify('+1 year');
                $insuranceIssued = $faker->dateTimeBetween('-1 year', '-1 month');
                $insuranceExpired = (clone $insuranceIssued)->modify('+1 year');
                $techPassportIssued = $faker->dateTimeBetween('-5 years', '-2 years');
                $techPassportExpired = (clone $techPassportIssued)->modify('+3 years');
                $licenseIssued = $faker->dateTimeBetween('-2 years', '-6 months');
                $licenseExpired = (clone $licenseIssued)->modify('+1 year');

                Truck::updateOrCreate(
                    [
                        'plate' => $plate,
                    ],
                    [
                        'brand'                 => $brand,
                        'model'                 => $model,
                        'can_available'         => $faker->boolean(30),
                        'plate'                 => $plate,
                        'year'                  => $faker->numberBetween(2015, 2024),
                        'company_id'            => $company->id,
                        'inspection_issued'     => $inspectionIssued->format('Y-m-d'),
                        'inspection_expired'    => $inspectionExpired->format('Y-m-d'),
                        'insurance_number'      => 'INS-' . $faker->numerify('######'),
                        'insurance_issued'      => $insuranceIssued->format('Y-m-d'),
                        'insurance_expired'     => $insuranceExpired->format('Y-m-d'),
                        'insurance_company'     => $faker->randomElement(['If', 'BTA', 'Balta', 'Ergo', 'Compensa']),
                        'license_number'        => 'TL-' . $faker->numerify('########'),
                        'license_issued'        => $licenseIssued->format('Y-m-d'),
                        'license_expired'       => $licenseExpired->format('Y-m-d'),
                        'mapon_box_id'          => $faker->optional(0.4)->numerify('box-#####'),
                        'mapon_unit_id'         => $faker->optional(0.4)->numerify('unit-#####'),
                        'vin'                   => $vin,
                        'status'                => 1,
                        'is_active'             => true,
                        'tech_passport_nr'      => 'TP-' . $faker->numerify('######'),
                        'tech_passport_issued'   => $techPassportIssued->format('Y-m-d'),
                        'tech_passport_expired'  => $techPassportExpired->format('Y-m-d'),
                        'tech_passport_photo'   => 'https://placehold.co/600x400/1a365d/fff?text=Tech+Passport',
                    ]
                );
            }

            $this->command->info("Создано " . self::TRUCKS_PER_CARRIER . " грузовиков для {$company->name}.");
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Trailer;
use Illuminate\Database\Seeder;

/**
 * По 20 прицепов на каждую компанию — наши перевозчики (type=forwarder). Все поля заполнены, фейковое фото техпаспорта.
 */
class CarrierTrailersSeeder extends Seeder
{
    private const TRAILERS_PER_CARRIER = 20;

    private const BRANDS = ['Schmitz Cargobull', 'Krone', 'Schwarzmüller', 'Lamberet', 'Gray Adams', 'Kögel', 'Schmitz Cargobull', 'Krone', 'Lamberet', 'Schwarzmüller'];

    public function run(): void
    {
        $carriers = Company::where('type', 'forwarder')->get();
        if ($carriers->isEmpty()) {
            $this->command->warn('Нет компаний с type=forwarder. Сначала выполните CarrierExpeditorCompaniesSeeder.');
            return;
        }

        $faker = \Faker\Factory::create();

        foreach ($carriers as $company) {
            for ($i = 0; $i < self::TRAILERS_PER_CARRIER; $i++) {
                $plate = 'LV-' . strtoupper($faker->unique()->bothify('??####'));
                $vin = strtoupper($faker->unique()->regexify('[A-HJ-NPR-Z0-9]{17}'));

                $inspectionIssued = $faker->dateTimeBetween('-2 years', '-6 months');
                $inspectionExpired = (clone $inspectionIssued)->modify('+1 year');
                $insuranceIssued = $faker->dateTimeBetween('-1 year', '-1 month');
                $insuranceExpired = (clone $insuranceIssued)->modify('+1 year');
                $techPassportIssued = $faker->dateTimeBetween('-5 years', '-2 years');
                $techPassportExpired = (clone $techPassportIssued)->modify('+3 years');
                $tirIssued = $faker->dateTimeBetween('-2 years', '-6 months');
                $tirExpired = (clone $tirIssued)->modify('+1 year');

                Trailer::updateOrCreate(
                    ['plate' => $plate],
                    [
                        'brand'                  => $faker->randomElement(self::BRANDS),
                        'plate'                  => $plate,
                        'year'                   => $faker->numberBetween(2015, 2024),
                        'type_id'                => $faker->randomElement([1, 2, 3]), // cargo, container, ref
                        'company_id'             => $company->id,
                        'inspection_issued'      => $inspectionIssued->format('Y-m-d'),
                        'inspection_expired'     => $inspectionExpired->format('Y-m-d'),
                        'insurance_number'       => 'TRL-INS-' . $faker->numerify('#####'),
                        'insurance_issued'       => $insuranceIssued->format('Y-m-d'),
                        'insurance_expired'      => $insuranceExpired->format('Y-m-d'),
                        'insurance_company'      => $faker->randomElement(['If', 'BTA', 'Balta', 'Ergo', 'Compensa']),
                        'tir_issued'             => $tirIssued->format('Y-m-d'),
                        'tir_expired'            => $tirExpired->format('Y-m-d'),
                        'vin'                    => $vin,
                        'status'                 => 1,
                        'is_active'              => true,
                        'tech_passport_nr'       => 'TP-TRL-' . $faker->numerify('#####'),
                        'tech_passport_issued'   => $techPassportIssued->format('Y-m-d'),
                        'tech_passport_expired'  => $techPassportExpired->format('Y-m-d'),
                        'tech_passport_photo'    => 'https://placehold.co/600x400/2d5016/fff?text=Trailer+Tech',
                    ]
                );
            }

            $this->command->info("Создано " . self::TRAILERS_PER_CARRIER . " прицепов для {$company->name}.");
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Driver;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * По 15 водителей на каждую компанию — наши перевозчики (type=forwarder). Все поля заполнены, фейковые фото (URL).
 */
class CarrierDriversSeeder extends Seeder
{
    private const DRIVERS_PER_CARRIER = 15;

    public function run(): void
    {
        $carriers = Company::where('type', 'forwarder')->get();
        if ($carriers->isEmpty()) {
            $this->command->warn('Нет компаний с type=forwarder. Сначала выполните CarrierExpeditorCompaniesSeeder.');
            return;
        }

        $faker = \Faker\Factory::create('lv_LV');

        // Латвия: country_id 16, города из config (Rīga = 1 и др.)
        $latviaId = 16;
        $citiesLv = config('cities.lv') ?? [1 => ['name' => 'Rīga'], 2 => ['name' => 'Liepāja'], 3 => ['name' => 'Daugavpils']];
        $cityIds = array_keys($citiesLv);

        foreach ($carriers as $company) {
            for ($i = 0; $i < self::DRIVERS_PER_CARRIER; $i++) {
                $firstName = $faker->firstName();
                $lastName = $faker->lastName();
                $email = $faker->unique()->safeEmail();

                $birthDate = $faker->dateTimeBetween('1975-01-01', '1995-12-31');
                $persCode = $birthDate->format('ymd') . '-' . $faker->numerify('#####');

                $licenseIssued = $faker->dateTimeBetween('-10 years', '-2 years');
                $licenseEnd = $faker->dateTimeBetween('+3 months', '+5 years');
                $code95Issued = $faker->dateTimeBetween('-5 years', '-1 year');
                $code95End = $faker->dateTimeBetween('+6 months', '+3 years');
                $permitIssued = $faker->dateTimeBetween('-2 years', '-6 months');
                $permitExpired = $faker->dateTimeBetween('+1 month', '+2 years');
                $medicalIssued = $faker->dateTimeBetween('-2 years', '-3 months');
                $medicalExpired = $faker->dateTimeBetween('+6 months', '+2 years');
                $declarationIssued = $faker->dateTimeBetween('-1 year', '-1 month');
                $declarationExpired = $faker->dateTimeBetween('+3 months', '+1 year');
                $examPassed = $faker->dateTimeBetween('-1 year', '-1 week');
                $examExpired = (clone $examPassed)->modify('+'.rand(6, 24).' months');

                $declaredCityId = $cityIds[array_rand($cityIds)];
                $actualCityId = $cityIds[array_rand($cityIds)];

                // Фейковые фото — URL placeholder (отображаются в интерфейсе)
                $photoPerson = 'https://i.pravatar.cc/400?u=' . $faker->uuid;
                $photoLicense = 'https://placehold.co/600x400/1e3a5f/fff?text=License';
                $photoMedical = 'https://placehold.co/600x400/166534/fff?text=Medical';

                $user = User::updateOrCreate(
                    ['email' => $email],
                    [
                        'name'     => $firstName . ' ' . $lastName,
                        'password' => Hash::make('driver123'),
                        'role'     => 'driver',
                    ]
                );

                $pin = str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);

                Driver::updateOrCreate(
                    [
                        'company_id' => $company->id,
                        'email'      => $email,
                    ],
                    [
                        'first_name'  => $firstName,
                        'last_name'   => $lastName,
                        'pers_code'   => $persCode,
                        'company_id'  => $company->id,
                        'citizenship_id'      => $latviaId,
                        'declared_country_id' => $latviaId,
                        'declared_city_id'    => $declaredCityId,
                        'declared_street'     => $faker->streetName(),
                        'declared_building'   => $faker->buildingNumber(),
                        'declared_room'       => (string) $faker->numberBetween(1, 99),
                        'declared_postcode'   => 'LV-' . $faker->numerify('####'),
                        'actual_country_id'   => $latviaId,
                        'actual_city_id'      => $actualCityId,
                        'actual_street'       => $faker->streetName(),
                        'actual_building'     => $faker->buildingNumber(),
                        'actual_room'         => (string) $faker->numberBetween(1, 99),
                        'actual_postcode'     => 'LV-' . $faker->numerify('####'),
                        'phone'               => '+371 2' . $faker->numerify('######'),
                        'email'               => $email,
                        'license_number'      => 'LV' . $faker->numerify('########'),
                        'license_issued'      => $licenseIssued->format('Y-m-d'),
                        'license_end'         => $licenseEnd->format('Y-m-d'),
                        'code95_issued'       => $code95Issued->format('Y-m-d'),
                        'code95_end'          => $code95End->format('Y-m-d'),
                        'permit_issued'       => $permitIssued->format('Y-m-d'),
                        'permit_expired'      => $permitExpired->format('Y-m-d'),
                        'medical_issued'      => $medicalIssued->format('Y-m-d'),
                        'medical_expired'     => $medicalExpired->format('Y-m-d'),
                        'declaration_issued'  => $declarationIssued->format('Y-m-d'),
                        'declaration_expired' => $declarationExpired->format('Y-m-d'),
                        'photo'                     => $photoPerson,
                        'license_photo'             => $photoLicense,
                        'medical_certificate_photo' => $photoMedical,
                        'medical_exam_passed'       => $examPassed->format('Y-m-d'),
                        'medical_exam_expired'     => $examExpired->format('Y-m-d'),
                        'status'    => 1, // ON_WORK
                        'is_active' => true,
                        'login_pin' => $pin,
                        'user_id'   => $user->id,
                    ]
                );
            }

            $this->command->info("Создано " . self::DRIVERS_PER_CARRIER . " водителей для {$company->name}.");
        }
    }
}

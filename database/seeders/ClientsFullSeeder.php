<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

/**
 * 60 клиентов с полным заполнением полей. Страны и города — из конфига (config/countries, config/cities/{iso}.php).
 */
class ClientsFullSeeder extends Seeder
{
    private const COUNT = 60;

    public function run(): void
    {
        $countries = config('countries');
        if (empty($countries)) {
            $this->command->warn('Конфиг countries пуст.');
            return;
        }

        // Собираем пары (country_id, city_id) по всем странам, у которых есть города
        $countryCityPairs = [];
        foreach ($countries as $countryId => $countryData) {
            $cities = getCitiesByCountryId((int) $countryId);
            if (!empty($cities)) {
                foreach (array_keys($cities) as $cityId) {
                    $countryCityPairs[] = [(int) $countryId, (int) $cityId];
                }
            }
        }

        if (empty($countryCityPairs)) {
            $this->command->warn('Нет ни одной пары страна+город в конфиге (проверьте config/countries и config/cities/*.php).');
            return;
        }

        $faker = \Faker\Factory::create();

        for ($i = 0; $i < self::COUNT; $i++) {
            [$jurCountryId, $jurCityId] = $countryCityPairs[array_rand($countryCityPairs)];
            [$fizCountryId, $fizCityId] = $faker->boolean(70)
                ? [$jurCountryId, $jurCityId]
                : $countryCityPairs[array_rand($countryCityPairs)];

            $companyName = $faker->company();
            $regNr = $faker->optional(0.9)->numerify('###########');

            Client::create([
                'company_name'   => $companyName,
                'reg_nr'         => $regNr,
                'representative' => $faker->name(),
                'jur_country_id' => $jurCountryId,
                'jur_city_id'    => $jurCityId,
                'jur_address'    => $faker->streetAddress(),
                'jur_post_code'  => $faker->postcode(),
                'fiz_country_id' => $fizCountryId,
                'fiz_city_id'    => $fizCityId,
                'fiz_address'    => $faker->streetAddress(),
                'fiz_post_code'  => $faker->postcode(),
                'bank_name'      => $faker->randomElement(['Swedbank', 'SEB', 'Citadele', 'Luminor', 'Revolut', 'LHV', 'Danske Bank']),
                'swift'          => $faker->optional(0.8)->regexify('[A-Z]{4}LV2X[0-9A-Z]{8}'),
                'email'          => $faker->unique()->companyEmail(),
                'phone'          => $faker->e164PhoneNumber(),
            ]);
        }

        $this->command->info('Создано ' . self::COUNT . ' клиентов.');
    }
}

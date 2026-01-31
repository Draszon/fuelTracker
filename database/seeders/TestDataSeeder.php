<?php

namespace Database\Seeders;

use App\Models\Car;
use App\Models\Fuel;
use App\Models\Service;
use App\Models\TravelCost;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Töröljük a régi adatokat (foreign key check kikapcsolása)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        TravelCost::truncate();
        Service::truncate();
        Fuel::truncate();
        Car::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Cars - 3 autó
        $cars = [
            [
                'name' => 'Családi autó',
                'licence_plate' => 'ABC-123',
                'car_type' => 'Škoda Octavia 1.6 TDI',
                'average_fuel_consumption' => 5.8,
                'year' => 2018,
                'current_km' => 145000,
                'oil_change_cycle_km' => 15000,
                'oil_change_cycle_year' => 1,
                'break_oil_cycle_year' => 2,
                'inspection_valid_from' => '2025-06-15',
                'inspection_valid_until' => '2027-06-15',
                'last_oil_change_km' => 140000,
                'last_oil_change_date' => '2025-09-10',
                'last_break_oil_change_date' => '2024-11-20',
                'user_id' => 1,
            ],
            [
                'name' => 'Munkás autó',
                'licence_plate' => 'XYZ-789',
                'car_type' => 'Ford Focus 1.5 EcoBoost',
                'average_fuel_consumption' => 6.5,
                'year' => 2020,
                'current_km' => 78000,
                'oil_change_cycle_km' => 20000,
                'oil_change_cycle_year' => 1,
                'break_oil_cycle_year' => 2,
                'inspection_valid_from' => '2025-03-20',
                'inspection_valid_until' => '2027-03-20',
                'last_oil_change_km' => 70000,
                'last_oil_change_date' => '2025-07-15',
                'last_break_oil_change_date' => '2025-01-10',
                'user_id' => 1,
            ],
            [
                'name' => 'Hétvégi autó',
                'licence_plate' => 'DEF-456',
                'car_type' => 'Mazda MX-5 2.0',
                'average_fuel_consumption' => 7.2,
                'year' => 2019,
                'current_km' => 42000,
                'oil_change_cycle_km' => 10000,
                'oil_change_cycle_year' => 1,
                'break_oil_cycle_year' => 2,
                'inspection_valid_from' => '2024-09-01',
                'inspection_valid_until' => '2026-09-01',
                'last_oil_change_km' => 40000,
                'last_oil_change_date' => '2025-05-20',
                'last_break_oil_change_date' => '2024-05-15',
                'user_id' => 1,
            ],
        ];

        foreach ($cars as $carData) {
            Car::create($carData);
        }

        // Fuels - 50+ tankolási rekord
        $fuelRecords = [
            // Škoda Octavia (car_id: 1) - gyakori használat
            ['car_id' => 1, 'date' => '2025-01-05', 'name' => 'MOL', 'quantity' => 45.2, 'km' => 120500, 'consumption' => 5.6, 'money' => 27120],
            ['car_id' => 1, 'date' => '2025-01-18', 'name' => 'Shell', 'quantity' => 42.8, 'km' => 121300, 'consumption' => 5.4, 'money' => 26396],
            ['car_id' => 1, 'date' => '2025-02-02', 'name' => 'OMV', 'quantity' => 48.1, 'km' => 122200, 'consumption' => 5.9, 'money' => 28860],
            ['car_id' => 1, 'date' => '2025-02-15', 'name' => 'MOL', 'quantity' => 44.5, 'km' => 123000, 'consumption' => 5.6, 'money' => 26700],
            ['car_id' => 1, 'date' => '2025-03-01', 'name' => 'Shell', 'quantity' => 46.3, 'km' => 123850, 'consumption' => 5.7, 'money' => 27780],
            ['car_id' => 1, 'date' => '2025-03-14', 'name' => 'MOL', 'quantity' => 43.9, 'km' => 124650, 'consumption' => 5.5, 'money' => 26340],
            ['car_id' => 1, 'date' => '2025-03-28', 'name' => 'OMV', 'quantity' => 47.2, 'km' => 125500, 'consumption' => 5.8, 'money' => 28320],
            ['car_id' => 1, 'date' => '2025-04-10', 'name' => 'MOL', 'quantity' => 45.6, 'km' => 126400, 'consumption' => 5.6, 'money' => 27360],
            ['car_id' => 1, 'date' => '2025-04-25', 'name' => 'Shell', 'quantity' => 44.1, 'km' => 127200, 'consumption' => 5.5, 'money' => 26460],
            ['car_id' => 1, 'date' => '2025-05-08', 'name' => 'MOL', 'quantity' => 46.8, 'km' => 128100, 'consumption' => 5.7, 'money' => 28080],
            ['car_id' => 1, 'date' => '2025-05-22', 'name' => 'OMV', 'quantity' => 43.5, 'km' => 128900, 'consumption' => 5.4, 'money' => 26100],
            ['car_id' => 1, 'date' => '2025-06-05', 'name' => 'MOL', 'quantity' => 47.9, 'km' => 129800, 'consumption' => 5.9, 'money' => 28740],
            ['car_id' => 1, 'date' => '2025-06-18', 'name' => 'Shell', 'quantity' => 45.2, 'km' => 130650, 'consumption' => 5.6, 'money' => 27120],
            ['car_id' => 1, 'date' => '2025-07-02', 'name' => 'MOL', 'quantity' => 48.3, 'km' => 131600, 'consumption' => 5.9, 'money' => 28980],
            ['car_id' => 1, 'date' => '2025-07-16', 'name' => 'OMV', 'quantity' => 44.7, 'km' => 132450, 'consumption' => 5.5, 'money' => 26820],
            ['car_id' => 1, 'date' => '2025-08-01', 'name' => 'MOL', 'quantity' => 46.1, 'km' => 133350, 'consumption' => 5.7, 'money' => 27660],
            ['car_id' => 1, 'date' => '2025-08-15', 'name' => 'Shell', 'quantity' => 45.8, 'km' => 134200, 'consumption' => 5.6, 'money' => 27480],
            ['car_id' => 1, 'date' => '2025-09-02', 'name' => 'MOL', 'quantity' => 47.4, 'km' => 135100, 'consumption' => 5.8, 'money' => 28440],
            ['car_id' => 1, 'date' => '2025-09-18', 'name' => 'OMV', 'quantity' => 44.9, 'km' => 136000, 'consumption' => 5.5, 'money' => 26940],
            ['car_id' => 1, 'date' => '2025-10-05', 'name' => 'MOL', 'quantity' => 46.5, 'km' => 136900, 'consumption' => 5.7, 'money' => 27900],
            ['car_id' => 1, 'date' => '2025-10-20', 'name' => 'Shell', 'quantity' => 45.3, 'km' => 137750, 'consumption' => 5.6, 'money' => 27180],
            ['car_id' => 1, 'date' => '2025-11-03', 'name' => 'MOL', 'quantity' => 48.0, 'km' => 138650, 'consumption' => 5.9, 'money' => 28800],
            ['car_id' => 1, 'date' => '2025-11-18', 'name' => 'OMV', 'quantity' => 44.2, 'km' => 139500, 'consumption' => 5.4, 'money' => 26520],
            ['car_id' => 1, 'date' => '2025-12-02', 'name' => 'MOL', 'quantity' => 46.7, 'km' => 140400, 'consumption' => 5.7, 'money' => 28020],
            ['car_id' => 1, 'date' => '2025-12-16', 'name' => 'Shell', 'quantity' => 45.5, 'km' => 141250, 'consumption' => 5.6, 'money' => 27300],
            ['car_id' => 1, 'date' => '2026-01-03', 'name' => 'MOL', 'quantity' => 47.8, 'km' => 142200, 'consumption' => 5.8, 'money' => 28680],
            ['car_id' => 1, 'date' => '2026-01-18', 'name' => 'OMV', 'quantity' => 44.6, 'km' => 143100, 'consumption' => 5.5, 'money' => 26760],
            ['car_id' => 1, 'date' => '2026-01-30', 'name' => 'MOL', 'quantity' => 46.2, 'km' => 145000, 'consumption' => 5.7, 'money' => 27720],

            // Ford Focus (car_id: 2) - közepes használat
            ['car_id' => 2, 'date' => '2025-01-10', 'name' => 'MOL', 'quantity' => 38.5, 'km' => 62000, 'consumption' => 6.4, 'money' => 23100],
            ['car_id' => 2, 'date' => '2025-02-05', 'name' => 'Shell', 'quantity' => 40.2, 'km' => 63200, 'consumption' => 6.7, 'money' => 24120],
            ['car_id' => 2, 'date' => '2025-03-02', 'name' => 'OMV', 'quantity' => 39.1, 'km' => 64400, 'consumption' => 6.5, 'money' => 23460],
            ['car_id' => 2, 'date' => '2025-03-28', 'name' => 'MOL', 'quantity' => 41.3, 'km' => 65700, 'consumption' => 6.8, 'money' => 24780],
            ['car_id' => 2, 'date' => '2025-04-22', 'name' => 'Shell', 'quantity' => 38.8, 'km' => 66900, 'consumption' => 6.3, 'money' => 23280],
            ['car_id' => 2, 'date' => '2025-05-18', 'name' => 'MOL', 'quantity' => 40.5, 'km' => 68150, 'consumption' => 6.6, 'money' => 24300],
            ['car_id' => 2, 'date' => '2025-06-12', 'name' => 'OMV', 'quantity' => 39.7, 'km' => 69400, 'consumption' => 6.4, 'money' => 23820],
            ['car_id' => 2, 'date' => '2025-07-08', 'name' => 'MOL', 'quantity' => 41.0, 'km' => 70700, 'consumption' => 6.7, 'money' => 24600],
            ['car_id' => 2, 'date' => '2025-08-03', 'name' => 'Shell', 'quantity' => 38.9, 'km' => 71950, 'consumption' => 6.4, 'money' => 23340],
            ['car_id' => 2, 'date' => '2025-08-28', 'name' => 'MOL', 'quantity' => 40.8, 'km' => 73200, 'consumption' => 6.6, 'money' => 24480],
            ['car_id' => 2, 'date' => '2025-09-22', 'name' => 'OMV', 'quantity' => 39.4, 'km' => 74450, 'consumption' => 6.5, 'money' => 23640],
            ['car_id' => 2, 'date' => '2025-10-18', 'name' => 'MOL', 'quantity' => 41.2, 'km' => 75750, 'consumption' => 6.7, 'money' => 24720],
            ['car_id' => 2, 'date' => '2025-11-12', 'name' => 'Shell', 'quantity' => 39.0, 'km' => 77000, 'consumption' => 6.4, 'money' => 23400],
            ['car_id' => 2, 'date' => '2025-12-08', 'name' => 'MOL', 'quantity' => 40.6, 'km' => 78000, 'consumption' => 6.6, 'money' => 24360],

            // Mazda MX-5 (car_id: 3) - ritkább használat
            ['car_id' => 3, 'date' => '2025-03-15', 'name' => 'Shell', 'quantity' => 35.2, 'km' => 38500, 'consumption' => 7.0, 'money' => 21720],
            ['car_id' => 3, 'date' => '2025-05-01', 'name' => 'MOL', 'quantity' => 36.8, 'km' => 39200, 'consumption' => 7.4, 'money' => 22080],
            ['car_id' => 3, 'date' => '2025-06-20', 'name' => 'OMV', 'quantity' => 34.5, 'km' => 39850, 'consumption' => 6.9, 'money' => 20700],
            ['car_id' => 3, 'date' => '2025-07-25', 'name' => 'Shell', 'quantity' => 37.1, 'km' => 40500, 'consumption' => 7.2, 'money' => 22260],
            ['car_id' => 3, 'date' => '2025-08-30', 'name' => 'MOL', 'quantity' => 35.8, 'km' => 41100, 'consumption' => 7.1, 'money' => 21480],
            ['car_id' => 3, 'date' => '2025-10-10', 'name' => 'Shell', 'quantity' => 36.4, 'km' => 41700, 'consumption' => 7.3, 'money' => 21840],
            ['car_id' => 3, 'date' => '2025-12-01', 'name' => 'MOL', 'quantity' => 35.0, 'km' => 42000, 'consumption' => 7.0, 'money' => 21000],
        ];

        foreach ($fuelRecords as $fuel) {
            Fuel::create($fuel);
        }

        // Services - 10 szerviz rekord
        $serviceRecords = [
            // Škoda Octavia
            ['car_id' => 1, 'date' => '2025-01-15', 'current_km' => 121000, 'description' => 'Téli gumi csere, futómű ellenőrzés', 'cost' => 15000],
            ['car_id' => 1, 'date' => '2025-04-20', 'current_km' => 127000, 'description' => 'Nyári gumi csere, fékbetét csere első tengely', 'cost' => 45000],
            ['car_id' => 1, 'date' => '2025-09-10', 'current_km' => 140000, 'description' => 'Olajcsere, olajszűrő, levegőszűrő csere', 'cost' => 35000],
            ['car_id' => 1, 'date' => '2025-11-08', 'current_km' => 139000, 'description' => 'Téli gumi csere, akkumulátor ellenőrzés', 'cost' => 12000],

            // Ford Focus
            ['car_id' => 2, 'date' => '2025-03-10', 'current_km' => 64500, 'description' => 'Időszakos szerviz, fékolaj csere', 'cost' => 55000],
            ['car_id' => 2, 'date' => '2025-07-15', 'current_km' => 70000, 'description' => 'Olajcsere, vezérműszíj ellenőrzés', 'cost' => 28000],
            ['car_id' => 2, 'date' => '2025-10-25', 'current_km' => 76000, 'description' => 'Téli gumi felszerelés, futómű beállítás', 'cost' => 22000],

            // Mazda MX-5
            ['car_id' => 3, 'date' => '2025-04-05', 'current_km' => 38700, 'description' => 'Tavasz előtti átvizsgálás, fékfolyadék csere', 'cost' => 32000],
            ['car_id' => 3, 'date' => '2025-05-20', 'current_km' => 40000, 'description' => 'Olajcsere, pollenszűrő csere', 'cost' => 25000],
            ['car_id' => 3, 'date' => '2025-09-15', 'current_km' => 41500, 'description' => 'Klíma fertőtlenítés, szelepszár tömítés csere', 'cost' => 18000],
        ];

        foreach ($serviceRecords as $service) {
            Service::create($service);
        }

        // Travel Costs - 2025 július - 2026 február
        $travelCostRecords = [
            // 2025 Július
            ['car_id' => 1, 'date' => '2025-07-31', 'direction' => 'Budapest - Szeged - Budapest', 'distance' => 340, 'travel_expenses' => 11560, 'fuel_costs' => 9860],
            // 2025 Augusztus
            ['car_id' => 1, 'date' => '2025-08-31', 'direction' => 'Budapest - Debrecen - Budapest', 'distance' => 460, 'travel_expenses' => 15640, 'fuel_costs' => 13340],
            ['car_id' => 2, 'date' => '2025-08-31', 'direction' => 'Budapest - Győr - Budapest', 'distance' => 240, 'travel_expenses' => 8160, 'fuel_costs' => 7800],
            // 2025 Szeptember
            ['car_id' => 1, 'date' => '2025-09-30', 'direction' => 'Budapest - Pécs - Budapest', 'distance' => 400, 'travel_expenses' => 13600, 'fuel_costs' => 11600],
            ['car_id' => 2, 'date' => '2025-09-30', 'direction' => 'Budapest - Székesfehérvár - Budapest', 'distance' => 130, 'travel_expenses' => 4420, 'fuel_costs' => 4225],
            // 2025 Október
            ['car_id' => 1, 'date' => '2025-10-31', 'direction' => 'Budapest - Eger - Budapest', 'distance' => 260, 'travel_expenses' => 8840, 'fuel_costs' => 7540],
            ['car_id' => 2, 'date' => '2025-10-31', 'direction' => 'Budapest - Veszprém - Budapest', 'distance' => 220, 'travel_expenses' => 7480, 'fuel_costs' => 7150],
            ['car_id' => 3, 'date' => '2025-10-31', 'direction' => 'Budapest - Balaton - Budapest', 'distance' => 200, 'travel_expenses' => 6800, 'fuel_costs' => 7200],
            // 2025 November
            ['car_id' => 1, 'date' => '2025-11-30', 'direction' => 'Budapest - Miskolc - Budapest', 'distance' => 360, 'travel_expenses' => 12240, 'fuel_costs' => 10440],
            ['car_id' => 2, 'date' => '2025-11-30', 'direction' => 'Budapest - Tatabánya - Budapest', 'distance' => 140, 'travel_expenses' => 4760, 'fuel_costs' => 4550],
            // 2025 December
            ['car_id' => 1, 'date' => '2025-12-31', 'direction' => 'Budapest - Nyíregyháza - Budapest', 'distance' => 460, 'travel_expenses' => 15640, 'fuel_costs' => 13340],
            ['car_id' => 2, 'date' => '2025-12-31', 'direction' => 'Budapest - Sopron - Budapest', 'distance' => 440, 'travel_expenses' => 14960, 'fuel_costs' => 14300],
            // 2026 Január
            ['car_id' => 1, 'date' => '2026-01-31', 'direction' => 'Budapest - Kecskemét - Budapest', 'distance' => 170, 'travel_expenses' => 5780, 'fuel_costs' => 4930],
            ['car_id' => 2, 'date' => '2026-01-31', 'direction' => 'Budapest - Szolnok - Budapest', 'distance' => 200, 'travel_expenses' => 6800, 'fuel_costs' => 6500],
            ['car_id' => 3, 'date' => '2026-01-31', 'direction' => 'Budapest - Szentendre - Budapest', 'distance' => 50, 'travel_expenses' => 1700, 'fuel_costs' => 1800],
            // 2026 Február
            ['car_id' => 1, 'date' => '2026-02-28', 'direction' => 'Budapest - Szeged - Budapest', 'distance' => 340, 'travel_expenses' => 11560, 'fuel_costs' => 9860],
            ['car_id' => 2, 'date' => '2026-02-28', 'direction' => 'Budapest - Gyula - Budapest', 'distance' => 420, 'travel_expenses' => 14280, 'fuel_costs' => 13650],
        ];

        foreach ($travelCostRecords as $travelCost) {
            TravelCost::create($travelCost);
        }

        $this->command->info('Tesztadatok sikeresen feltöltve!');
        $this->command->info('- 3 autó');
        $this->command->info('- ' . count($fuelRecords) . ' tankolási rekord');
        $this->command->info('- ' . count($serviceRecords) . ' szerviz rekord');
        $this->command->info('- ' . count($travelCostRecords) . ' útiköltség rekord');
    }
}

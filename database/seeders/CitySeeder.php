<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            ['name' => 'Banda Aceh', 'province' => 'Aceh', 'lat' => 5.5483, 'lon' => 95.3238, 'elevation' => 21, 'flood_prone' => 0.6, 'landslide_prone' => 0.3],
            ['name' => 'Medan', 'province' => 'Sumatera Utara', 'lat' => 3.5952, 'lon' => 98.6722, 'elevation' => 25, 'flood_prone' => 0.7, 'landslide_prone' => 0.4],
            ['name' => 'Padang', 'province' => 'Sumatera Barat', 'lat' => -0.9471, 'lon' => 100.4172, 'elevation' => 3, 'flood_prone' => 0.6, 'landslide_prone' => 0.5],
            ['name' => 'Pekanbaru', 'province' => 'Riau', 'lat' => 0.5071, 'lon' => 101.4478, 'elevation' => 10, 'flood_prone' => 0.8, 'landslide_prone' => 0.2],
            ['name' => 'Jambi', 'province' => 'Jambi', 'lat' => -1.6101, 'lon' => 103.6131, 'elevation' => 12, 'flood_prone' => 0.75, 'landslide_prone' => 0.25],
            ['name' => 'Palembang', 'province' => 'Sumatera Selatan', 'lat' => -2.9761, 'lon' => 104.7754, 'elevation' => 8, 'flood_prone' => 0.8, 'landslide_prone' => 0.2],
            ['name' => 'Bengkulu', 'province' => 'Bengkulu', 'lat' => -3.8004, 'lon' => 102.2655, 'elevation' => 3, 'flood_prone' => 0.65, 'landslide_prone' => 0.4],
            ['name' => 'Bandar Lampung', 'province' => 'Lampung', 'lat' => -5.4292, 'lon' => 105.2625, 'elevation' => 8, 'flood_prone' => 0.7, 'landslide_prone' => 0.35],
            ['name' => 'Jakarta', 'province' => 'DKI Jakarta', 'lat' => -6.2088, 'lon' => 106.8456, 'elevation' => 8, 'flood_prone' => 0.9, 'landslide_prone' => 0.1],
            ['name' => 'Bogor', 'province' => 'Jawa Barat', 'lat' => -6.5950, 'lon' => 106.7970, 'elevation' => 265, 'flood_prone' => 0.7, 'landslide_prone' => 0.8],
            ['name' => 'Bandung', 'province' => 'Jawa Barat', 'lat' => -6.9175, 'lon' => 107.6191, 'elevation' => 768, 'flood_prone' => 0.5, 'landslide_prone' => 0.7],
            ['name' => 'Semarang', 'province' => 'Jawa Tengah', 'lat' => -6.9667, 'lon' => 110.4167, 'elevation' => 3, 'flood_prone' => 0.75, 'landslide_prone' => 0.3],
            ['name' => 'Yogyakarta', 'province' => 'DI Yogyakarta', 'lat' => -7.7956, 'lon' => 110.3695, 'elevation' => 114, 'flood_prone' => 0.6, 'landslide_prone' => 0.5],
            ['name' => 'Surabaya', 'province' => 'Jawa Timur', 'lat' => -7.2575, 'lon' => 112.7521, 'elevation' => 3, 'flood_prone' => 0.7, 'landslide_prone' => 0.2],
            ['name' => 'Pontianak', 'province' => 'Kalimantan Barat', 'lat' => -0.0263, 'lon' => 109.3425, 'elevation' => 2, 'flood_prone' => 0.85, 'landslide_prone' => 0.15],
            ['name' => 'Banjarmasin', 'province' => 'Kalimantan Selatan', 'lat' => -3.3194, 'lon' => 114.5908, 'elevation' => 3, 'flood_prone' => 0.9, 'landslide_prone' => 0.1],
            ['name' => 'Samarinda', 'province' => 'Kalimantan Timur', 'lat' => -0.5022, 'lon' => 117.1536, 'elevation' => 10, 'flood_prone' => 0.75, 'landslide_prone' => 0.3],
            ['name' => 'Makassar', 'province' => 'Sulawesi Selatan', 'lat' => -5.1477, 'lon' => 119.4327, 'elevation' => 2, 'flood_prone' => 0.65, 'landslide_prone' => 0.3],
            ['name' => 'Manado', 'province' => 'Sulawesi Utara', 'lat' => 1.4748, 'lon' => 124.8421, 'elevation' => 6, 'flood_prone' => 0.6, 'landslide_prone' => 0.5],
            ['name' => 'Palu', 'province' => 'Sulawesi Tengah', 'lat' => -0.8999, 'lon' => 119.8707, 'elevation' => 89, 'flood_prone' => 0.7, 'landslide_prone' => 0.6],
            ['name' => 'Denpasar', 'province' => 'Bali', 'lat' => -8.6705, 'lon' => 115.2126, 'elevation' => 4, 'flood_prone' => 0.5, 'landslide_prone' => 0.4],
            ['name' => 'Mataram', 'province' => 'Nusa Tenggara Barat', 'lat' => -8.5833, 'lon' => 116.1167, 'elevation' => 14, 'flood_prone' => 0.55, 'landslide_prone' => 0.45],
            ['name' => 'Jayapura', 'province' => 'Papua', 'lat' => -2.5333, 'lon' => 140.7167, 'elevation' => 3, 'flood_prone' => 0.7, 'landslide_prone' => 0.5],
            ['name' => 'Ambon', 'province' => 'Maluku', 'lat' => -3.6954, 'lon' => 128.1814, 'elevation' => 11, 'flood_prone' => 0.6, 'landslide_prone' => 0.5],
        ];

        foreach ($cities as $city) {
            City::create([
                'name' => $city['name'],
                'province' => $city['province'],
                'latitude' => $city['lat'],
                'longitude' => $city['lon'],
                'elevation' => $city['elevation'],
                'flood_prone_index' => $city['flood_prone'],
                'landslide_prone_index' => $city['landslide_prone'],
                'is_active' => true,
            ]);
        }
    }
}

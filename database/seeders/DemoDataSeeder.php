<?php

namespace Database\Seeders;

use App\Models\Artist;
use App\Models\Event;
use App\Models\TicketCategory;
use App\Models\Venue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

// database/seeders/DemoDataSeeder.php
// optional demo catalog, run only when SEED_DEMO_DATA=true
class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        if (Event::query()->exists()) {
            return;
        }

        DB::transaction(function (): void {
            $venues = collect([
                ['name' => 'Stadion Utama Gelora Bung Karno', 'slug' => 'gbk-senayan', 'address' => 'Jl. Pintu Satu Senayan', 'city' => 'Jakarta', 'capacity' => 77000],
                ['name' => 'Jakarta International Stadium', 'slug' => 'jakarta-international-stadium', 'address' => 'Jl. Raya Bekasi Barat', 'city' => 'Jakarta Utara', 'capacity' => 82000],
                ['name' => 'Stadion Gelora Bung Tomo', 'slug' => 'gelora-bung-tomo', 'address' => 'Jl. Mayjen Sungkono', 'city' => 'Surabaya', 'capacity' => 55000],
            ])->map(fn (array $data) => Venue::query()->create($data));

            $artists = collect([
                ['name' => 'Senja Kota', 'slug' => 'senja-kota', 'bio' => 'Band pop indie asal Bandung dengan lirik tentang kota dan rindu.'],
                ['name' => 'Rangga Nada', 'slug' => 'rangga-nada', 'bio' => 'Penyanyi solo dengan warna folk elektrik.'],
                ['name' => 'Kolektif Timur', 'slug' => 'kolektif-timur', 'bio' => 'Kolektif musik eksperimental dari Surabaya.'],
            ])->map(fn (array $data) => Artist::query()->create($data));

            $events = [
                [
                    'venue_id' => $venues[0]->id,
                    'artists' => [$artists[0]->id, $artists[1]->id],
                    'name' => 'Konser Cahaya Senja 2027',
                    'slug' => 'konser-cahaya-senja-2027',
                    'description' => "Konser tahunan terbesar Concert District yang mempertemukan dua nama besar panggung pop Indonesia.\n\nGerbang dibuka tiga jam sebelum pertunjukan. Bawa kode tiket digital Anda.",
                    'start_date' => now()->addDays(45)->setTime(19, 30),
                    'end_date' => now()->addDays(45)->setTime(23, 30),
                    'categories' => [
                        ['name' => 'Tribune', 'price' => 350000, 'quota' => 2000, 'sold' => 120],
                        ['name' => 'Festival', 'price' => 750000, 'quota' => 800, 'sold' => 300],
                        ['name' => 'VIP', 'price' => 1500000, 'quota' => 200, 'sold' => 40],
                    ],
                ],
                [
                    'venue_id' => $venues[1]->id,
                    'artists' => [$artists[2]->id],
                    'name' => 'Malam Eksperimental Timur',
                    'slug' => 'malam-eksperimental-timur',
                    'description' => 'Satu malam penuh eksplorasi bunyi bersama Kolektif Timur dan tamu spesial.',
                    'start_date' => now()->addDays(70)->setTime(20, 0),
                    'end_date' => now()->addDays(70)->setTime(23, 0),
                    'categories' => [
                        ['name' => 'Reguler', 'price' => 250000, 'quota' => 1500, 'sold' => 0],
                        ['name' => 'Premium', 'price' => 650000, 'quota' => 300, 'sold' => 0],
                    ],
                ],
                [
                    'venue_id' => $venues[2]->id,
                    'artists' => [$artists[0]->id],
                    'name' => 'Senja Kota Live di Surabaya',
                    'slug' => 'senja-kota-live-surabaya',
                    'description' => 'Tur nasional Senja Kota singgah di Surabaya untuk satu malam saja.',
                    'start_date' => now()->addDays(100)->setTime(19, 0),
                    'end_date' => now()->addDays(100)->setTime(22, 0),
                    'categories' => [
                        ['name' => 'Tribune Utara', 'price' => 300000, 'quota' => 1000, 'sold' => 0],
                    ],
                ],
            ];

            foreach ($events as $payload) {
                $artistIds = $payload['artists'];
                $categories = $payload['categories'];
                unset($payload['artists'], $payload['categories']);

                $event = Event::query()->create($payload);
                $event->artists()->sync($artistIds);

                foreach ($categories as $category) {
                    TicketCategory::query()->create([...$category, 'event_id' => $event->id]);
                }
            }
        });
    }
}

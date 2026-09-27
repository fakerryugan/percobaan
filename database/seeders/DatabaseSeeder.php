<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $guests = [
            [
                'name' => 'Bapak Dr. H. Bambang Soeprapto & Ibu',
                'slug' => 'bambang-soeprapto',
                'ticket_code' => 'TKT-VIP001',
                'phone' => '081234567890',
                'category' => 'VIP',
                'pax' => 2,
                'table_number' => 'Meja VIP 01',
                'rsvp_status' => 'attending',
                'rsvp_pax' => 2,
                'wishes' => 'Barakallahu lakuma wa baraka alaikuma wa jama\'a bainakuma fii khoir. Selamat menempuh hidup baru!',
                'is_checked_in' => false,
            ],
            [
                'name' => 'Ahmad Rizky Pratama & Partner',
                'slug' => 'ahmad-rizky',
                'ticket_code' => 'TKT-SHB002',
                'phone' => '082198765432',
                'category' => 'Sahabat',
                'pax' => 2,
                'table_number' => 'Meja A-03',
                'rsvp_status' => 'attending',
                'rsvp_pax' => 2,
                'wishes' => 'Selamat bro Dimas dan Sarah! Semoga langgeng sampai kakek nenek dan dilancarkan segala urusannya!',
                'is_checked_in' => true,
                'checked_in_at' => now()->subMinutes(15),
                'checked_in_by' => 'Petugas Resepsionis (Meja 1)',
            ],
            [
                'name' => 'Keluarga Besar H. Syamsuddin',
                'slug' => 'keluarga-h-syamsuddin',
                'ticket_code' => 'TKT-KLG003',
                'phone' => '085712345678',
                'category' => 'Keluarga',
                'pax' => 4,
                'table_number' => 'Meja K-01',
                'rsvp_status' => 'attending',
                'rsvp_pax' => 4,
                'wishes' => 'Alhamdulillah, selamat untuk kedua mempelai dan keluarga besar. Semoga sakinah mawaddah warahmah.',
                'is_checked_in' => false,
            ],
            [
                'name' => 'Siti Nurhaliza, S.T.',
                'slug' => 'siti-nurhaliza',
                'ticket_code' => 'TKT-KNT004',
                'phone' => '081399887766',
                'category' => 'Teman Kantor',
                'pax' => 2,
                'table_number' => 'Meja B-02',
                'rsvp_status' => 'pending',
                'rsvp_pax' => 0,
                'wishes' => null,
                'is_checked_in' => false,
            ],
            [
                'name' => 'Budi Santoso & Istri',
                'slug' => 'budi-santoso',
                'ticket_code' => 'TKT-REG005',
                'phone' => '087811223344',
                'category' => 'Reguler',
                'pax' => 2,
                'table_number' => 'Meja C-05',
                'rsvp_status' => 'attending',
                'rsvp_pax' => 2,
                'wishes' => 'Happy Wedding Sarah & Dimas! Bahagia dan rukun selalu selamanya.',
                'is_checked_in' => false,
            ],
        ];

        foreach ($guests as $guestData) {
            $guest = \App\Models\Guest::create($guestData);

            if ($guest->wishes) {
                \App\Models\Wish::create([
                    'guest_id' => $guest->id,
                    'name' => $guest->name,
                    'relationship' => $guest->category,
                    'attendance' => $guest->rsvp_status == 'attending' ? 'hadir' : 'ragu',
                    'message' => $guest->wishes,
                    'created_at' => now()->subHours(rand(1, 48)),
                ]);
            }
        }

        // Additional community wishes
        \App\Models\Wish::create([
            'name' => 'Farhan & Maya',
            'relationship' => 'Sahabat Kampus',
            'attendance' => 'hadir',
            'message' => 'Congratulations Dimas & Sarah! Lancar sampai hari H yaa!',
            'created_at' => now()->subDays(1),
        ]);
        \App\Models\Wish::create([
            'name' => 'Rian Hidayat',
            'relationship' => 'Alumni SMA',
            'attendance' => 'hadir',
            'message' => 'Semoga menjadi keluarga yang selalu dipenuhi cinta dan kebahagiaan.',
            'created_at' => now()->subHours(5),
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Wish;
use Illuminate\Http\Request;

class WeddingController extends Controller
{
    /**
     * Wedding Configuration Data
     */
    public static function getWeddingData(): array
    {
        return [
            'groom' => [
                'name' => 'Dimas Arya Pratama, S.T.',
                'nickname' => 'Dimas',
                'son_of' => 'Putra Pertama dari Bpk. Ir. H. Hendro Utomo & Ibu Hj. Ratna Sari',
                'instagram' => 'dimas_arya',
                'photo' => '/images/groom.jpg',
            ],
            'bride' => [
                'name' => 'Sarah Amanda Putri, S.Farm.',
                'nickname' => 'Sarah',
                'daughter_of' => 'Putri Kedua dari Bpk. Drs. H. Achmad Fauzan & Ibu Hj. Dewi Kartika',
                'instagram' => 'sarahamanda',
                'photo' => '/images/bride.jpg',
            ],
            'event' => [
                'date_human' => 'Sabtu, 24 Oktober 2026',
                'date_iso' => '2026-10-24T08:00:00',
                'akad' => [
                    'time' => '08:00 - 10:00 WIB',
                    'venue' => 'Masjid Agung Al-Ikhlas',
                    'address' => 'Jl. Boulevard Raya No. 45, Kebayoran Baru, Jakarta Selatan',
                    'maps_url' => 'https://maps.google.com/?q=Jakarta',
                ],
                'resepsi' => [
                    'time' => '11:00 - 14:00 WIB',
                    'venue' => 'Grand Ballroom Puri Begawan',
                    'address' => 'Jl. Raya Pajajaran No. 88, Baranangsiang, Kota Bogor',
                    'maps_url' => 'https://maps.google.com/?q=Bogor',
                ],
                'event_code' => 'WEDDING-DIMAS-SARAH-2026',
            ],
            'bank_accounts' => [
                [
                    'bank' => 'BCA',
                    'account_number' => '8210349812',
                    'account_name' => 'Dimas Arya Pratama',
                    'logo' => 'https://upload.wikimedia.org/wikipedia/commons/5/5c/Bank_Central_Asia.svg',
                ],
                [
                    'bank' => 'Bank Mandiri',
                    'account_number' => '1330029103941',
                    'account_name' => 'Sarah Amanda Putri',
                    'logo' => 'https://upload.wikimedia.org/wikipedia/commons/a/ad/Bank_Mandiri_logo_2016.svg',
                ],
            ],
            'gift_address' => [
                'recipient' => 'Dimas & Sarah',
                'phone' => '081234567890',
                'address' => 'Jl. Mawar Melati Blok B3 No. 12, Cluster Griya Indah, Jakarta Selatan (Kode Pos 12340)',
            ],
        ];
    }

    /**
     * Show the main wedding invitation
     */
    public function index(Request $request)
    {
        $guest = null;
        $guestName = $request->query('to') ?? $request->query('guest');

        if ($request->has('code')) {
            $guest = Guest::where('ticket_code', $request->query('code'))->first();
        }

        if (!$guest && $guestName) {
            $guest = Guest::where('name', 'like', "%{$guestName}%")->first();
        }

        $wedding = self::getWeddingData();
        $wishes = Wish::latest()->take(20)->get();

        return view('wedding.index', compact('wedding', 'guest', 'guestName', 'wishes'));
    }

    /**
     * Personalized invitation by guest slug
     */
    public function showBySlug(string $slug)
    {
        $guest = Guest::where('slug', $slug)->firstOrFail();
        $guestName = $guest->name;
        $wedding = self::getWeddingData();
        $wishes = Wish::latest()->take(20)->get();

        return view('wedding.index', compact('wedding', 'guest', 'guestName', 'wishes'));
    }

    /**
     * Show guest E-Ticket page
     */
    public function ticket(string $code)
    {
        $guest = Guest::where('ticket_code', $code)->firstOrFail();
        $wedding = self::getWeddingData();

        return view('wedding.ticket', compact('guest', 'wedding'));
    }

    /**
     * Guest RSVP submission
     */
    public function rsvp(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'attendance' => 'required|in:hadir,tidak_hadir,ragu',
            'pax' => 'nullable|integer|min:1|max:10',
            'message' => 'required|string|max:1000',
            'guest_id' => 'nullable|exists:guests,id',
        ]);

        $guest = null;
        if ($request->filled('guest_id')) {
            $guest = Guest::find($request->guest_id);
        } elseif ($request->filled('ticket_code')) {
            $guest = Guest::where('ticket_code', $request->ticket_code)->first();
        }

        if ($guest) {
            $guest->update([
                'rsvp_status' => $request->attendance == 'hadir' ? 'attending' : ($request->attendance == 'tidak_hadir' ? 'declined' : 'tentative'),
                'rsvp_pax' => $request->attendance == 'hadir' ? ($request->pax ?? $guest->pax) : 0,
                'wishes' => $request->message,
            ]);
        }

        $wish = Wish::create([
            'guest_id' => $guest?->id,
            'name' => $request->name,
            'relationship' => $guest?->category ?? 'Tamu Undangan',
            'attendance' => $request->attendance,
            'message' => $request->message,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Terima kasih atas konfirmasi dan ucapan doa restunya!',
                'wish' => [
                    'name' => $wish->name,
                    'message' => $wish->message,
                    'attendance' => $wish->attendance,
                    'time_ago' => 'Baru saja',
                ],
            ]);
        }

        return back()->with('success', 'Konfirmasi kehadiran dan ucapan Anda telah berhasil dikirim!');
    }

    /**
     * Self-checkin by guest scanning venue QR
     */
    public function selfCheckIn(Request $request)
    {
        $request->validate([
            'ticket_code' => 'required|string',
            'event_code' => 'required|string',
        ]);

        $wedding = self::getWeddingData();
        if ($request->event_code !== $wedding['event']['event_code']) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code Acara tidak valid!',
            ], 422);
        }

        $guest = Guest::where('ticket_code', $request->ticket_code)->first();
        if (!$guest) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket tamu tidak ditemukan!',
            ], 404);
        }

        if ($guest->is_checked_in) {
            return response()->json([
                'success' => false,
                'already_checked_in' => true,
                'message' => 'Anda sudah check-in sebelumnya pada pukul ' . $guest->checked_in_at->format('H:i') . ' WIB.',
                'guest' => $guest,
            ]);
        }

        $guest->update([
            'is_checked_in' => true,
            'checked_in_at' => now(),
            'checked_in_by' => 'Scan Mandiri oleh Tamu',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Check-in Berhasil! Selamat Datang di Pernikahan Dimas & Sarah.',
            'guest' => $guest,
        ]);
    }
}

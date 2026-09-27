<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use Illuminate\Http\Request;

class ScannerController extends Controller
{
    /**
     * Show the ticket scanner page (for receptionists / ushers)
     */
    public function index()
    {
        $recentCheckins = Guest::where('is_checked_in', true)
            ->latest('checked_in_at')
            ->take(10)
            ->get();

        $stats = [
            'total_guests' => Guest::count(),
            'checked_in' => Guest::where('is_checked_in', true)->count(),
            'pending' => Guest::where('is_checked_in', false)->count(),
            'total_pax_present' => Guest::where('is_checked_in', true)->sum('pax'),
        ];

        return view('scanner.index', compact('recentCheckins', 'stats'));
    }

    /**
     * Verify and process check-in for a scanned ticket QR
     */
    public function verify(Request $request)
    {
        $request->validate([
            'ticket_code' => 'required|string',
            'checker_name' => 'nullable|string|max:100',
        ]);

        $code = trim($request->ticket_code);

        // Allow passing full URL if the QR contains the URL instead of just the code
        if (str_contains($code, '/ticket/')) {
            $parts = explode('/ticket/', $code);
            $code = end($parts);
            $code = explode('?', $code)[0];
        }

        $guest = Guest::where('ticket_code', $code)->first();

        if (!$guest) {
            return response()->json([
                'success' => false,
                'status' => 'not_found',
                'message' => 'Tiket Tidak Valid / Tidak Terdaftar!',
                'code' => $code,
            ], 404);
        }

        if ($guest->is_checked_in) {
            return response()->json([
                'success' => false,
                'status' => 'already_checked_in',
                'message' => 'PERINGATAN: Tiket sudah digunakan sebelumnya!',
                'guest' => [
                    'name' => $guest->name,
                    'category' => $guest->category,
                    'pax' => $guest->pax,
                    'table_number' => $guest->table_number ?? '-',
                    'ticket_code' => $guest->ticket_code,
                    'checked_in_at' => $guest->checked_in_at ? $guest->checked_in_at->format('d M Y - H:i:s') : '-',
                    'checked_in_by' => $guest->checked_in_by ?? 'Petugas',
                ],
            ]);
        }

        // Perform Check-in
        $checker = $request->checker_name ?: 'Petugas Penerima Tamu';
        $guest->update([
            'is_checked_in' => true,
            'checked_in_at' => now(),
            'checked_in_by' => $checker,
        ]);

        return response()->json([
            'success' => true,
            'status' => 'checked_in',
            'message' => 'Check-in Berhasil! Silakan Masuk.',
            'guest' => [
                'id' => $guest->id,
                'name' => $guest->name,
                'category' => $guest->category,
                'pax' => $guest->pax,
                'table_number' => $guest->table_number ?? '-',
                'ticket_code' => $guest->ticket_code,
                'checked_in_at' => $guest->checked_in_at->format('H:i:s') . ' WIB',
                'checked_in_by' => $checker,
            ],
            'stats' => [
                'total_guests' => Guest::count(),
                'checked_in' => Guest::where('is_checked_in', true)->count(),
                'pending' => Guest::where('is_checked_in', false)->count(),
            ],
        ]);
    }

    /**
     * Show printable / displayable Event QR Code for guests to scan themselves
     */
    public function eventQr()
    {
        $wedding = WeddingController::getWeddingData();
        return view('scanner.event_qr', compact('wedding'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Wish;
use Illuminate\Http\Request;

class GuestBookController extends Controller
{
    /**
     * Display guestbook and guest management dashboard
     */
    public function index(Request $request)
    {
        $query = Guest::query();

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by check-in status
        if ($request->filled('status')) {
            if ($request->status === 'checked_in') {
                $query->where('is_checked_in', true);
            } elseif ($request->status === 'not_checked_in') {
                $query->where('is_checked_in', false);
            }
        }

        // Search by name or ticket code
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('ticket_code', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $guests = $query->latest()->paginate(25)->withQueryString();

        $stats = [
            'total_guests' => Guest::count(),
            'checked_in' => Guest::where('is_checked_in', true)->count(),
            'not_checked_in' => Guest::where('is_checked_in', false)->count(),
            'total_pax_capacity' => Guest::sum('pax'),
            'total_pax_present' => Guest::where('is_checked_in', true)->sum('pax'),
            'total_attending_rsvp' => Guest::where('rsvp_status', 'attending')->count(),
            'categories' => Guest::selectRaw('category, count(*) as count')->groupBy('category')->pluck('count', 'category'),
        ];

        $categories = ['VIP', 'VVIP', 'Keluarga', 'Sahabat', 'Teman Kantor', 'Reguler'];

        return view('admin.guests', compact('guests', 'stats', 'categories'));
    }

    /**
     * Store a newly created guest
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'phone' => 'nullable|string|max:30',
            'category' => 'required|string|max:50',
            'pax' => 'required|integer|min:1|max:20',
            'table_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:500',
        ]);

        $guest = Guest::create($validated);

        return redirect()->route('admin.guests.index')->with('success', "Tamu \"{$guest->name}\" berhasil ditambahkan dengan kode tiket: {$guest->ticket_code}");
    }

    /**
     * Update guest details
     */
    public function update(Request $request, Guest $guest)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'phone' => 'nullable|string|max:30',
            'category' => 'required|string|max:50',
            'pax' => 'required|integer|min:1|max:20',
            'table_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:500',
        ]);

        $guest->update($validated);

        return redirect()->route('admin.guests.index')->with('success', "Data tamu \"{$guest->name}\" berhasil diperbarui.");
    }

    /**
     * Delete guest
     */
    public function destroy(Guest $guest)
    {
        $name = $guest->name;
        $guest->delete();

        return redirect()->route('admin.guests.index')->with('success', "Tamu \"{$name}\" berhasil dihapus.");
    }

    /**
     * Toggle manual check-in
     */
    public function toggleCheckIn(Guest $guest)
    {
        if ($guest->is_checked_in) {
            $guest->update([
                'is_checked_in' => false,
                'checked_in_at' => null,
                'checked_in_by' => null,
            ]);
            $msg = "Check-in tamu \"{$guest->name}\" dibatalkan.";
        } else {
            $guest->update([
                'is_checked_in' => true,
                'checked_in_at' => now(),
                'checked_in_by' => 'Admin (Manual)',
            ]);
            $msg = "Tamu \"{$guest->name}\" berhasil di-check-in secara manual.";
        }

        return back()->with('success', $msg);
    }
}

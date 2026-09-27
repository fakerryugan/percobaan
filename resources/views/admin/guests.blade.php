@extends('layouts.app')

@section('title', 'Buku Tamu & Manajemen Undangan - The Wedding of Dimas & Sarah')

@section('content')
<div class="min-h-screen bg-[#F4EFE6] text-stone-800 pb-20">
    <!-- Admin Top Navbar -->
    <header class="bg-white border-b border-stone-200 px-6 py-4 sticky top-0 z-30 shadow-sm">
        <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-gold-100 text-gold-700 flex items-center justify-center text-lg shadow-sm">
                    <i class="fa-solid fa-book-open"></i>
                </div>
                <div>
                    <h1 class="font-serif text-lg font-bold text-stone-900">Buku Tamu Digital & Manajemen Undangan</h1>
                    <p class="text-xs text-stone-500">The Wedding of Dimas & Sarah</p>
                </div>
            </div>

            <div class="flex items-center space-x-3 text-xs">
                <a href="{{ route('scanner.index') }}" class="px-4 py-2 bg-stone-900 hover:bg-stone-800 text-white rounded-xl shadow transition flex items-center space-x-2 font-semibold">
                    <i class="fa-solid fa-camera text-gold-400"></i>
                    <span>Scanner Tiket</span>
                </a>
                <a href="{{ route('scanner.event-qr') }}" target="_blank" class="px-3.5 py-2 bg-white hover:bg-stone-100 border border-stone-300 text-stone-700 rounded-xl transition flex items-center space-x-1.5 font-medium">
                    <i class="fa-solid fa-qrcode text-gold-600"></i>
                    <span>QR Acara</span>
                </a>
                <a href="{{ route('wedding.home') }}" target="_blank" class="px-3.5 py-2 bg-white hover:bg-stone-100 border border-stone-300 text-stone-700 rounded-xl transition flex items-center space-x-1.5 font-medium">
                    <i class="fa-solid fa-globe"></i>
                    <span>Lihat Web</span>
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 mt-8">
        <!-- Stats Summary Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-2xl p-5 border border-stone-200 shadow-sm">
                <div class="flex justify-between items-start">
                    <div>
                        <span class="text-[10px] uppercase tracking-wider text-stone-500 font-bold block">Total Tamu</span>
                        <span class="font-serif text-3xl font-bold text-stone-800 mt-1 block">{{ $stats['total_guests'] }}</span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-base">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-stone-100 text-xs text-stone-500 flex justify-between">
                    <span>Kapasitas Pax:</span>
                    <span class="font-semibold text-stone-700">{{ $stats['total_pax_capacity'] }} orang</span>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-stone-200 shadow-sm">
                <div class="flex justify-between items-start">
                    <div>
                        <span class="text-[10px] uppercase tracking-wider text-emerald-600 font-bold block">Tamu Hadir (Check-in)</span>
                        <span class="font-serif text-3xl font-bold text-emerald-600 mt-1 block">{{ $stats['checked_in'] }}</span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-base">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-stone-100 text-xs text-stone-500 flex justify-between">
                    <span>Pax Hadir di Venue:</span>
                    <span class="font-semibold text-emerald-700">{{ $stats['total_pax_present'] }} orang</span>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-stone-200 shadow-sm">
                <div class="flex justify-between items-start">
                    <div>
                        <span class="text-[10px] uppercase tracking-wider text-amber-600 font-bold block">Belum Hadir</span>
                        <span class="font-serif text-3xl font-bold text-amber-600 mt-1 block">{{ $stats['not_checked_in'] }}</span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-base">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-stone-100 text-xs text-stone-500 flex justify-between">
                    <span>Tingkat Kehadiran:</span>
                    <span class="font-semibold text-amber-700">
                        {{ $stats['total_guests'] > 0 ? round(($stats['checked_in'] / $stats['total_guests']) * 100) : 0 }}%
                    </span>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-stone-200 shadow-sm">
                <div class="flex justify-between items-start">
                    <div>
                        <span class="text-[10px] uppercase tracking-wider text-purple-600 font-bold block">RSVP Konfirmasi</span>
                        <span class="font-serif text-3xl font-bold text-purple-600 mt-1 block">{{ $stats['total_attending_rsvp'] }}</span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-base">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-stone-100 text-xs text-stone-500 flex justify-between">
                    <span>Konfirmasi Hadir</span>
                    <span class="font-semibold text-purple-700">via Form Web</span>
                </div>
            </div>
        </div>

        <!-- Filter, Search & Add Guest Action Bar -->
        <div class="bg-white rounded-2xl p-4 border border-stone-200 shadow-sm mb-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <!-- Search & Filters -->
                <form action="{{ route('admin.guests.index') }}" method="GET" class="flex flex-wrap items-center gap-3 flex-1">
                    <div class="relative flex-1 min-w-[200px]">
                        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-stone-400 text-xs"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, tiket, atau WA..." class="w-full pl-9 pr-4 py-2 rounded-xl border border-stone-300 text-xs focus:outline-none focus:border-gold-500">
                    </div>

                    <select name="status" onchange="this.form.submit()" class="px-3 py-2 rounded-xl border border-stone-300 text-xs focus:outline-none focus:border-gold-500 bg-white">
                        <option value="">Semua Status</option>
                        <option value="checked_in" {{ request('status') === 'checked_in' ? 'selected' : '' }}>Sudah Hadir</option>
                        <option value="not_checked_in" {{ request('status') === 'not_checked_in' ? 'selected' : '' }}>Belum Hadir</option>
                    </select>

                    <select name="category" onchange="this.form.submit()" class="px-3 py-2 rounded-xl border border-stone-300 text-xs focus:outline-none focus:border-gold-500 bg-white">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>

                    @if(request('search') || request('status') || request('category'))
                        <a href="{{ route('admin.guests.index') }}" class="px-3 py-2 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-600 text-xs font-semibold">
                            Reset
                        </a>
                    @endif
                </form>

                <!-- Actions -->
                <div class="flex items-center space-x-2">
                    <button onclick="openAddGuestModal()" class="px-4 py-2 bg-gold-500 hover:bg-gold-600 text-white font-bold rounded-xl text-xs shadow transition flex items-center space-x-2">
                        <i class="fa-solid fa-user-plus"></i>
                        <span>Tambah Tamu Baru</span>
                    </button>
                    <button onclick="window.print()" class="px-3 py-2 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-xl text-xs font-semibold border border-stone-300 transition" title="Cetak Daftar Tamu">
                        <i class="fa-solid fa-print"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Guests Table -->
        <div class="bg-white rounded-3xl border border-stone-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-stone-600">
                    <thead class="bg-stone-50 border-b border-stone-200 text-stone-500 font-semibold uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="py-4 px-6">Nama & Kategori</th>
                            <th class="py-4 px-4">Kode Tiket</th>
                            <th class="py-4 px-4">Kuota / Pax</th>
                            <th class="py-4 px-4">Nomor Meja</th>
                            <th class="py-4 px-4">Status Kehadiran</th>
                            <th class="py-4 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse($guests as $guest)
                            <tr class="hover:bg-stone-50/80 transition">
                                <!-- Name & Category -->
                                <td class="py-4 px-6">
                                    <div class="font-bold text-stone-900 text-sm">{{ $guest->name }}</div>
                                    <div class="flex items-center space-x-2 mt-1">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wider bg-gold-100 text-gold-800">
                                            {{ $guest->category }}
                                        </span>
                                        @if($guest->phone)
                                            <span class="text-stone-400 text-[10px] flex items-center">
                                                <i class="fa-brands fa-whatsapp text-emerald-600 mr-1"></i>
                                                {{ $guest->phone }}
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Ticket Code -->
                                <td class="py-4 px-4">
                                    <a href="{{ route('wedding.ticket', $guest->ticket_code) }}" target="_blank" class="font-mono font-bold text-gold-700 hover:text-gold-900 underline flex items-center space-x-1" title="Buka Tiket Digital">
                                        <span>{{ $guest->ticket_code }}</span>
                                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                                    </a>
                                </td>

                                <!-- Pax -->
                                <td class="py-4 px-4 font-semibold text-stone-800">
                                    <i class="fa-solid fa-user-group text-stone-400 mr-1"></i>
                                    {{ $guest->pax }} Orang
                                </td>

                                <!-- Table Number -->
                                <td class="py-4 px-4">
                                    <span class="px-2.5 py-1 rounded-lg bg-stone-100 font-semibold text-stone-700">
                                        {{ $guest->table_number ?? 'Reguler' }}
                                    </span>
                                </td>

                                <!-- Check-in Status -->
                                <td class="py-4 px-4">
                                    @if($guest->is_checked_in)
                                        <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                            <i class="fa-solid fa-circle-check text-emerald-600"></i>
                                            <span>Hadir ({{ $guest->checked_in_at ? $guest->checked_in_at->format('H:i') : '' }})</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-stone-100 text-stone-500 border border-stone-200">
                                            <i class="fa-solid fa-clock text-stone-400"></i>
                                            <span>Belum Hadir</span>
                                        </span>
                                    @endif
                                </td>

                                <!-- Action Buttons -->
                                <td class="py-4 px-6 text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        <!-- WhatsApp Share Invitation -->
                                        <button onclick="shareWhatsApp('{{ $guest->name }}', '{{ route('wedding.to', $guest->slug) }}', '{{ $guest->ticket_code }}', '{{ $guest->phone }}')" class="p-2 rounded-xl bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition" title="Kirim Link Undangan via WhatsApp">
                                            <i class="fa-brands fa-whatsapp text-sm"></i>
                                        </button>

                                        <!-- Copy Personal Link -->
                                        <button onclick="copyLink('{{ route('wedding.to', $guest->slug) }}')" class="p-2 rounded-xl bg-stone-100 text-stone-600 hover:bg-stone-200 transition" title="Salin Link Undangan">
                                            <i class="fa-regular fa-copy text-sm"></i>
                                        </button>

                                        <!-- Toggle Check-in -->
                                        <form action="{{ route('admin.guests.toggle-checkin', $guest) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-2 rounded-xl {{ $guest->is_checked_in ? 'bg-amber-50 text-amber-600 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }} transition" title="{{ $guest->is_checked_in ? 'Batalkan Check-in' : 'Manual Check-in' }}">
                                                <i class="fa-solid {{ $guest->is_checked_in ? 'fa-user-xmark' : 'fa-user-check' }} text-sm"></i>
                                            </button>
                                        </form>

                                        <!-- Delete Guest -->
                                        <form action="{{ route('admin.guests.destroy', $guest) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tamu {{ $guest->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 transition" title="Hapus Tamu">
                                                <i class="fa-regular fa-trash-can text-sm"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-12 text-stone-400">
                                    <i class="fa-regular fa-folder-open text-3xl mb-2 block"></i>
                                    Tidak ada data tamu yang sesuai.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4 border-t border-stone-200">
                {{ $guests->links() }}
            </div>
        </div>
    </main>
</div>

<!-- ========================================== -->
<!-- MODAL: TAMBAH TAMU BARU                    -->
<!-- ========================================== -->
<div id="add-guest-modal" class="fixed inset-0 z-50 bg-black/60 hidden items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl relative">
        <button onclick="closeAddGuestModal()" class="absolute top-4 right-4 text-stone-400 hover:text-stone-700 text-xl">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <h3 class="font-serif text-xl font-bold text-stone-900 mb-1">Tambah Tamu Undangan</h3>
        <p class="text-xs text-stone-500 mb-6">Sistem akan secara otomatis membuat kode tiket dan tautan personal untuk tamu ini.</p>

        <form action="{{ route('admin.guests.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-1">Nama Tamu / Gelar</label>
                <input type="text" name="name" required placeholder="Contoh: Bpk. Prof. Dr. Hendra & Ibu" class="w-full px-4 py-2.5 rounded-xl border border-stone-300 text-xs focus:outline-none focus:border-gold-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-1">Kategori</label>
                    <select name="category" required class="w-full px-4 py-2.5 rounded-xl border border-stone-300 text-xs focus:outline-none focus:border-gold-500 bg-white">
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-1">Kuota Pax</label>
                    <input type="number" name="pax" value="2" min="1" max="20" required class="w-full px-4 py-2.5 rounded-xl border border-stone-300 text-xs focus:outline-none focus:border-gold-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-1">Nomor Meja</label>
                    <input type="text" name="table_number" placeholder="Contoh: Meja VIP 01" class="w-full px-4 py-2.5 rounded-xl border border-stone-300 text-xs focus:outline-none focus:border-gold-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-1">Nomor WhatsApp</label>
                    <input type="text" name="phone" placeholder="081234567890" class="w-full px-4 py-2.5 rounded-xl border border-stone-300 text-xs focus:outline-none focus:border-gold-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-1">Catatan Khusus (Opsional)</label>
                <textarea name="notes" rows="2" placeholder="Catatan untuk panitia penerima tamu..." class="w-full px-4 py-2.5 rounded-xl border border-stone-300 text-xs focus:outline-none focus:border-gold-500"></textarea>
            </div>

            <div class="pt-4 flex justify-end space-x-2">
                <button type="button" onclick="closeAddGuestModal()" class="px-4 py-2.5 rounded-xl bg-stone-100 text-stone-600 text-xs font-semibold">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gold-500 hover:bg-gold-600 text-white text-xs font-bold shadow-md">
                    Simpan Tamu
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function openAddGuestModal() {
        const modal = document.getElementById('add-guest-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeAddGuestModal() {
        const modal = document.getElementById('add-guest-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function copyLink(url) {
        navigator.clipboard.writeText(url).then(() => {
            alert("Tautan undangan personal berhasil disalin ke clipboard:\n" + url);
        }).catch(err => {
            prompt("Salin tautan ini:", url);
        });
    }

    function shareWhatsApp(name, url, code, phone) {
        const message = `*Undangan Pernikahan Dimas & Sarah*\n\nKepada Yth. Bapak/Ibu/Saudara/i:\n*${name}*\n\nTanpa mengurangi rasa hormat, perkenankan kami mengundang Anda untuk hadir di hari bahagia pernikahan kami.\n\n*Jadwal Acara:*\nSabtu, 24 Oktober 2026\nGrand Ballroom Puri Begawan, Bogor\n\nUntuk info lengkap acara, lokasi, dan E-Ticket kehadiran Anda (Kode: *${code}*), silakan buka tautan berikut:\n${url}\n\nMerupakan suatu kehormatan dan kebahagiaan bagi kami apabila Anda berkenan hadir dan memberikan doa restu.\n\nTerima kasih,\n*Dimas & Sarah*`;

        const encoded = encodeURIComponent(message);
        let waUrl = `https://api.whatsapp.com/send?text=${encoded}`;
        if (phone) {
            let cleanPhone = phone.replace(/[^0-9]/g, '');
            if (cleanPhone.startsWith('0')) {
                cleanPhone = '62' + cleanPhone.substring(1);
            }
            waUrl = `https://api.whatsapp.com/send?phone=${cleanPhone}&text=${encoded}`;
        }

        window.open(waUrl, '_blank');
    }
</script>
@endpush

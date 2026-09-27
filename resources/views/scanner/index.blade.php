@extends('layouts.app')

@section('title', 'Scanner Tiket Penerima Tamu - The Wedding of Dimas & Sarah')

@push('styles')
<style>
    /* Pulse scanning line animation */
    @keyframes scan-line {
        0% { transform: translateY(0); opacity: 0.8; }
        50% { transform: translateY(220px); opacity: 0.3; }
        100% { transform: translateY(0); opacity: 0.8; }
    }
    .scanner-laser {
        animation: scan-line 2.5s infinite ease-in-out;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-stone-950 text-stone-100 pb-16">
    <!-- Top Header Bar -->
    <header class="bg-stone-900 border-b border-stone-800 px-6 py-4 flex flex-wrap items-center justify-between gap-4 sticky top-0 z-30 shadow-md">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-gold-500/20 text-gold-400 border border-gold-500/40 flex items-center justify-center text-lg">
                <i class="fa-solid fa-qrcode"></i>
            </div>
            <div>
                <h1 class="font-serif text-lg font-bold text-white">Scanner Tiket Penerima Tamu</h1>
                <p class="text-xs text-stone-400">The Wedding of Dimas & Sarah</p>
            </div>
        </div>

        <div class="flex items-center space-x-3 text-xs">
            <a href="{{ route('scanner.event-qr') }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-stone-800 hover:bg-stone-700 text-gold-300 border border-gold-500/30 transition flex items-center space-x-1.5 font-semibold">
                <i class="fa-solid fa-print"></i>
                <span>Cetak QR Acara</span>
            </a>
            <a href="{{ route('admin.guests.index') }}" class="px-3.5 py-2 rounded-xl bg-gold-500 hover:bg-gold-400 text-stone-950 transition flex items-center space-x-1.5 font-bold shadow-md">
                <i class="fa-solid fa-users"></i>
                <span>Buku Tamu / Admin</span>
            </a>
            <a href="{{ route('wedding.home') }}" class="px-3.5 py-2 rounded-xl bg-stone-800 hover:bg-stone-700 text-stone-300 transition">
                <i class="fa-solid fa-house"></i>
            </a>
        </div>
    </header>

    <!-- Main Content Grid -->
    <main class="max-w-6xl mx-auto px-4 mt-8">
        <!-- Live Statistics Counter -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-stone-900/90 border border-stone-800 rounded-2xl p-4 shadow-lg">
                <span class="text-[10px] text-stone-400 uppercase tracking-widest font-semibold block">Total Undangan</span>
                <span id="stat-total-guests" class="font-serif text-2xl font-bold text-white mt-1 block">{{ $stats['total_guests'] }}</span>
                <span class="text-[11px] text-stone-500">Tercatat di sistem</span>
            </div>
            <div class="bg-stone-900/90 border border-emerald-900/50 rounded-2xl p-4 shadow-lg">
                <span class="text-[10px] text-emerald-400 uppercase tracking-widest font-semibold block">Tamu Hadir</span>
                <span id="stat-checked-in" class="font-serif text-2xl font-bold text-emerald-400 mt-1 block">{{ $stats['checked_in'] }}</span>
                <span class="text-[11px] text-emerald-500/80">Sudah check-in</span>
            </div>
            <div class="bg-stone-900/90 border border-amber-900/50 rounded-2xl p-4 shadow-lg">
                <span class="text-[10px] text-amber-400 uppercase tracking-widest font-semibold block">Belum Hadir</span>
                <span id="stat-pending" class="font-serif text-2xl font-bold text-amber-400 mt-1 block">{{ $stats['pending'] }}</span>
                <span class="text-[11px] text-amber-500/80">Menunggu kedatangan</span>
            </div>
            <div class="bg-stone-900/90 border border-gold-900/50 rounded-2xl p-4 shadow-lg">
                <span class="text-[10px] text-gold-400 uppercase tracking-widest font-semibold block">Total Orang (Pax)</span>
                <span id="stat-pax-present" class="font-serif text-2xl font-bold text-gold-300 mt-1 block">{{ $stats['total_pax_present'] }}</span>
                <span class="text-[11px] text-gold-500/80">Pax yang hadir di venue</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <!-- Left: QR Scanner & Manual Input (7 Cols) -->
            <div class="lg:col-span-7 space-y-6">
                <!-- Camera Scanner Box -->
                <div class="bg-stone-900 border border-stone-800 rounded-3xl p-6 shadow-2xl relative">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-2">
                            <span class="w-3 h-3 rounded-full bg-emerald-500 animate-ping"></span>
                            <span class="text-xs font-semibold uppercase tracking-wider text-stone-300">Kamera Siap Scan</span>
                        </div>
                        <!-- Camera Selector -->
                        <select id="camera-select" class="bg-stone-800 border border-stone-700 text-stone-200 text-xs rounded-xl px-3 py-1.5 focus:outline-none focus:border-gold-500">
                            <option value="">Pilih Kamera...</option>
                        </select>
                    </div>

                    <!-- Scanner Video Frame -->
                    <div class="relative w-full aspect-square max-w-sm mx-auto rounded-2xl overflow-hidden bg-black border-2 border-stone-700 shadow-inner flex items-center justify-center">
                        <div id="reader" class="w-full h-full"></div>
                        <!-- Scanning Laser Line overlay -->
                        <div id="scanner-laser-line" class="scanner-laser absolute inset-x-4 top-4 h-0.5 bg-gradient-to-r from-transparent via-red-500 to-transparent shadow-[0_0_12px_#ef4444] pointer-events-none"></div>
                    </div>

                    <!-- Audio toggle / feedback note -->
                    <div class="mt-4 flex items-center justify-between text-xs text-stone-400 px-2">
                        <span class="flex items-center"><i class="fa-solid fa-volume-high text-emerald-400 mr-2"></i> Suara Beep Aktif</span>
                        <span id="scan-status-text" class="text-gold-400 font-mono">Menunggu QR...</span>
                    </div>
                </div>

                <!-- Manual Code Input Fallback -->
                <div class="bg-stone-900 border border-stone-800 rounded-3xl p-6 shadow-xl">
                    <h3 class="text-xs uppercase tracking-wider font-semibold text-stone-400 mb-3 flex items-center">
                        <i class="fa-solid fa-keyboard text-gold-400 mr-2"></i>
                        Input Manual Kode Tiket (Jika Kamera Tidak Tersedia)
                    </h3>
                    <form id="manual-verify-form" class="flex gap-2">
                        <input type="text" id="manual-ticket-code" placeholder="Contoh: TKT-VIP001" class="flex-1 px-4 py-3 bg-stone-950 border border-stone-700 rounded-xl text-white placeholder-stone-600 text-sm focus:outline-none focus:border-gold-500 uppercase tracking-widest font-mono">
                        <button type="submit" class="px-6 py-3 bg-gold-500 hover:bg-gold-400 text-stone-950 font-bold text-xs uppercase tracking-wider rounded-xl transition shadow-md">
                            Check-in
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right: Real-time Scan Result Card & Recent Check-ins (5 Cols) -->
            <div class="lg:col-span-5 space-y-6">
                <!-- Dynamic Scan Feedback Card -->
                <div id="scan-feedback-container" class="transition-all duration-300">
                    <!-- Default Idle State -->
                    <div id="scan-idle-state" class="bg-stone-900 border border-stone-800 rounded-3xl p-8 text-center text-stone-500 shadow-xl">
                        <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-stone-800/80 flex items-center justify-center text-2xl text-stone-600">
                            <i class="fa-solid fa-camera-rotate"></i>
                        </div>
                        <h4 class="font-serif text-base text-stone-400 font-semibold mb-1">Arahkan QR Code Tiket ke Kamera</h4>
                        <p class="text-xs text-stone-500">Hasil verifikasi dan detail meja tamu akan muncul di sini secara otomatis.</p>
                    </div>

                    <!-- Success State Card (Hidden by default) -->
                    <div id="scan-success-state" class="hidden bg-emerald-950/90 border-2 border-emerald-500 rounded-3xl p-6 text-white shadow-2xl animate-fade-in relative overflow-hidden">
                        <div class="absolute -top-12 -right-12 w-32 h-32 bg-emerald-500/20 rounded-full blur-2xl"></div>
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-500 text-stone-950 flex items-center justify-center text-2xl font-bold shadow-lg">
                                <i class="fa-solid fa-check"></i>
                            </div>
                            <div>
                                <span class="text-[10px] uppercase tracking-widest font-bold text-emerald-400 block">Check-in Berhasil!</span>
                                <h3 id="res-guest-name" class="font-serif text-xl font-bold text-white">Nama Tamu</h3>
                            </div>
                        </div>

                        <div class="space-y-3 bg-stone-950/60 p-4 rounded-2xl border border-emerald-900/60 text-xs">
                            <div class="flex justify-between items-center py-1 border-b border-emerald-900/40">
                                <span class="text-stone-400">Kategori</span>
                                <span id="res-guest-category" class="font-bold text-gold-400">VIP</span>
                            </div>
                            <div class="flex justify-between items-center py-1 border-b border-emerald-900/40">
                                <span class="text-stone-400">Jumlah Tamu (Pax)</span>
                                <span id="res-guest-pax" class="font-bold text-white">2 Orang</span>
                            </div>
                            <div class="flex justify-between items-center py-1 border-b border-emerald-900/40">
                                <span class="text-stone-400">Nomor Meja</span>
                                <span id="res-guest-table" class="font-bold text-emerald-300 text-sm">Meja VIP 01</span>
                            </div>
                            <div class="flex justify-between items-center py-1">
                                <span class="text-stone-400">Waktu Masuk</span>
                                <span id="res-guest-time" class="font-mono text-stone-300">11:15 WIB</span>
                            </div>
                        </div>
                    </div>

                    <!-- Warning State Card: Duplicate Checkin (Hidden by default) -->
                    <div id="scan-warning-state" class="hidden bg-amber-950/90 border-2 border-amber-500 rounded-3xl p-6 text-white shadow-2xl animate-fade-in relative">
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="w-12 h-12 rounded-2xl bg-amber-500 text-stone-950 flex items-center justify-center text-2xl font-bold shadow-lg">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                            </div>
                            <div>
                                <span class="text-[10px] uppercase tracking-widest font-bold text-amber-400 block">TIKET SUDAH DIGUNAKAN!</span>
                                <h3 id="warn-guest-name" class="font-serif text-xl font-bold text-white">Nama Tamu</h3>
                            </div>
                        </div>

                        <div class="space-y-3 bg-stone-950/60 p-4 rounded-2xl border border-amber-900/60 text-xs">
                            <p class="text-amber-300 text-xs leading-relaxed" id="warn-message">
                                Tamu ini sudah melakukan check-in sebelumnya.
                            </p>
                            <div class="flex justify-between items-center pt-2 border-t border-amber-900/40">
                                <span class="text-stone-400">Waktu Check-in Awal</span>
                                <span id="warn-guest-time" class="font-mono text-amber-200">-</span>
                            </div>
                        </div>
                    </div>

                    <!-- Error State Card: Not Found (Hidden by default) -->
                    <div id="scan-error-state" class="hidden bg-rose-950/90 border-2 border-rose-500 rounded-3xl p-6 text-white shadow-2xl animate-fade-in">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 rounded-2xl bg-rose-500 text-white flex items-center justify-center text-2xl font-bold shadow-lg">
                                <i class="fa-solid fa-xmark"></i>
                            </div>
                            <div>
                                <span class="text-[10px] uppercase tracking-widest font-bold text-rose-400 block">Verifikasi Gagal</span>
                                <h3 class="font-serif text-base font-bold text-white" id="err-message">Tiket Tidak Terdaftar!</h3>
                                <p class="text-xs text-rose-300/80 mt-0.5">Kode: <span id="err-code" class="font-mono font-bold"></span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Check-ins Stream -->
                <div class="bg-stone-900 border border-stone-800 rounded-3xl p-6 shadow-xl">
                    <h3 class="font-serif text-base font-bold text-white mb-4 flex items-center justify-between">
                        <span class="flex items-center">
                            <i class="fa-solid fa-clock-rotate-left text-gold-400 mr-2 text-sm"></i>
                            Tamu Baru Saja Masuk
                        </span>
                        <span class="text-[11px] text-stone-500 font-sans">Live Updates</span>
                    </h3>

                    <div id="recent-checkins-list" class="space-y-3 max-h-80 overflow-y-auto pr-1">
                        @forelse($recentCheckins as $chk)
                            <div class="p-3 bg-stone-950/80 border border-stone-800 rounded-2xl flex items-center justify-between text-xs">
                                <div>
                                    <span class="font-semibold text-white block">{{ $chk->name }}</span>
                                    <span class="text-[10px] text-gold-400">{{ $chk->category }} • {{ $chk->pax }} Pax • {{ $chk->table_number ?? 'Reguler' }}</span>
                                </div>
                                <span class="text-[10px] font-mono text-emerald-400 bg-emerald-950/60 px-2 py-0.5 rounded-full border border-emerald-900/50">
                                    {{ $chk->checked_in_at ? $chk->checked_in_at->format('H:i') : '-' }}
                                </span>
                            </div>
                        @empty
                            <p class="text-center text-xs text-stone-600 py-6">Belum ada tamu yang check-in hari ini.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
    // Audio Synthesizer Beep for fast response (Web Audio API)
    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    function playBeep(success = true) {
        if (!audioCtx) return;
        try {
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.connect(gain);
            gain.connect(audioCtx.destination);

            if (success) {
                // Happy high double beep
                osc.type = 'sine';
                osc.frequency.setValueAtTime(880, audioCtx.currentTime); // A5
                osc.frequency.setValueAtTime(1174.66, audioCtx.currentTime + 0.1); // D6
                gain.gain.setValueAtTime(0.3, audioCtx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.35);
                osc.start(audioCtx.currentTime);
                osc.stop(audioCtx.currentTime + 0.35);
            } else {
                // Low warning buzz
                osc.type = 'sawtooth';
                osc.frequency.setValueAtTime(220, audioCtx.currentTime);
                gain.gain.setValueAtTime(0.3, audioCtx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.4);
                osc.start(audioCtx.currentTime);
                osc.stop(audioCtx.currentTime + 0.4);
            }
        } catch (e) {
            console.error(e);
        }
    }

    // HTML5 QR Code Scanner Setup
    let html5QrScanner = null;
    let isVerifying = false;

    const idleState = document.getElementById('scan-idle-state');
    const successState = document.getElementById('scan-success-state');
    const warningState = document.getElementById('scan-warning-state');
    const errorState = document.getElementById('scan-error-state');
    const scanStatusText = document.getElementById('scan-status-text');

    function resetStates() {
        idleState.classList.add('hidden');
        successState.classList.add('hidden');
        warningState.classList.add('hidden');
        errorState.classList.add('hidden');
    }

    function verifyTicket(code) {
        if (isVerifying) return;
        isVerifying = true;
        scanStatusText.innerText = "Memproses " + code + "...";

        fetch("{{ route('scanner.verify') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                ticket_code: code,
                checker_name: "Petugas Resepsionis"
            })
        })
        .then(res => res.json().then(data => ({ status: res.status, body: data })))
        .then(result => {
            const data = result.body;
            resetStates();

            if (result.status === 200 && data.success) {
                // SUCCESS
                playBeep(true);
                confetti({ particleCount: 70, spread: 60 });

                document.getElementById('res-guest-name').innerText = data.guest.name;
                document.getElementById('res-guest-category').innerText = data.guest.category;
                document.getElementById('res-guest-pax').innerText = data.guest.pax + " Orang";
                document.getElementById('res-guest-table').innerText = data.guest.table_number;
                document.getElementById('res-guest-time').innerText = data.guest.checked_in_at;

                successState.classList.remove('hidden');

                // Update Stats
                if (data.stats) {
                    document.getElementById('stat-checked-in').innerText = data.stats.checked_in;
                    document.getElementById('stat-pending').innerText = data.stats.pending;
                }

                // Add to recent check-ins list
                const list = document.getElementById('recent-checkins-list');
                const newItem = `
                    <div class="p-3 bg-emerald-950/40 border border-emerald-800/80 rounded-2xl flex items-center justify-between text-xs animate-fade-in">
                        <div>
                            <span class="font-semibold text-white block">${data.guest.name}</span>
                            <span class="text-[10px] text-emerald-400">${data.guest.category} • ${data.guest.pax} Pax • ${data.guest.table_number}</span>
                        </div>
                        <span class="text-[10px] font-mono text-emerald-300 bg-emerald-900/60 px-2 py-0.5 rounded-full">
                            ${data.guest.checked_in_at.split(' ')[0]}
                        </span>
                    </div>
                `;
                list.insertAdjacentHTML('afterbegin', newItem);

            } else if (data.status === 'already_checked_in') {
                // ALREADY CHECKED IN WARNING
                playBeep(false);
                document.getElementById('warn-guest-name').innerText = data.guest.name;
                document.getElementById('warn-message').innerText = `${data.message} (Oleh: ${data.guest.checked_in_by})`;
                document.getElementById('warn-guest-time').innerText = data.guest.checked_in_at;

                warningState.classList.remove('hidden');

            } else {
                // NOT FOUND OR ERROR
                playBeep(false);
                document.getElementById('err-message').innerText = data.message || "Tiket Tidak Ditemukan!";
                document.getElementById('err-code').innerText = data.code || code;

                errorState.classList.remove('hidden');
            }

            // Re-arm scanner after 3 seconds cooldown
            setTimeout(() => {
                isVerifying = false;
                scanStatusText.innerText = "Kamera Siap...";
            }, 3000);
        })
        .catch(err => {
            resetStates();
            playBeep(false);
            document.getElementById('err-message').innerText = "Terjadi kesalahan koneksi server.";
            document.getElementById('err-code').innerText = code;
            errorState.classList.remove('hidden');

            setTimeout(() => {
                isVerifying = false;
                scanStatusText.innerText = "Kamera Siap...";
            }, 3000);
        });
    }

    // Initialize Camera Scanner
    Html5Qrcode.getCameras().then(devices => {
        if (devices && devices.length) {
            const select = document.getElementById('camera-select');
            devices.forEach((device, index) => {
                const opt = document.createElement('option');
                opt.value = device.id;
                opt.text = device.label || `Kamera ${index + 1}`;
                select.appendChild(opt);
            });

            // Start with back camera if available, or first camera
            const backCamera = devices.find(d => d.label.toLowerCase().includes('back') || d.label.toLowerCase().includes('environment'));
            const selectedCameraId = backCamera ? backCamera.id : devices[0].id;
            select.value = selectedCameraId;

            startScanner(selectedCameraId);

            select.addEventListener('change', () => {
                if (html5QrScanner) {
                    html5QrScanner.stop().then(() => {
                        startScanner(select.value);
                    });
                }
            });
        }
    }).catch(err => {
        console.error("Camera detection error", err);
    });

    function startScanner(cameraId) {
        html5QrScanner = new Html5Qrcode("reader");
        html5QrScanner.start(
            cameraId,
            {
                fps: 12,
                qrbox: { width: 220, height: 220 },
                aspectRatio: 1.0
            },
            (decodedText) => {
                verifyTicket(decodedText);
            },
            (error) => {
                // Ignore silent frame errors
            }
        ).catch(err => {
            console.error("Error starting camera", err);
        });
    }

    // Manual Verification Form
    const manualForm = document.getElementById('manual-verify-form');
    manualForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const input = document.getElementById('manual-ticket-code');
        const code = input.value.trim();
        if (code) {
            verifyTicket(code);
            input.value = '';
        }
    });
</script>
@endpush

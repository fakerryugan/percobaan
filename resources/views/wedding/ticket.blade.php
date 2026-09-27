@extends('layouts.app')

@section('title', 'E-Ticket Undangan: ' . $guest->name)

@push('styles')
<style>
    /* Luxury gold ticket border */
    .ticket-card {
        background: linear-gradient(145deg, #18181b 0%, #09090b 100%);
        border: 2px solid rgba(208, 170, 110, 0.4);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7), 0 0 30px rgba(208, 170, 110, 0.15);
    }

    /* Perforated ticket cutout */
    .ticket-cutout-left {
        left: -16px;
        top: 68%;
        width: 32px;
        height: 32px;
        background-color: #FAF7F2;
        border-radius: 50%;
        border-right: 2px solid rgba(208, 170, 110, 0.4);
    }
    .ticket-cutout-right {
        right: -16px;
        top: 68%;
        width: 32px;
        height: 32px;
        background-color: #FAF7F2;
        border-radius: 50%;
        border-left: 2px solid rgba(208, 170, 110, 0.4);
    }

    /* Perforated dashed line */
    .perforated-line {
        position: absolute;
        left: 20px;
        right: 20px;
        top: 68%;
        border-top: 2px dashed rgba(208, 170, 110, 0.35);
        transform: translateY(15px);
    }

    @media print {
        body {
            background: white !important;
            color: black !important;
        }
        .no-print {
            display: none !important;
        }
        .ticket-card {
            border: 2px solid #000 !important;
            box-shadow: none !important;
            background: white !important;
            color: black !important;
        }
        .ticket-cutout-left, .ticket-cutout-right, .perforated-line {
            display: none;
        }
    }
</style>
@endpush

@section('content')
<div class="min-h-screen flex flex-col justify-center items-center py-12 px-4">
    <!-- Back to Invitation Link -->
    <div class="w-full max-w-md mb-6 flex justify-between items-center no-print">
        <a href="{{ route('wedding.to', $guest->slug) }}" class="inline-flex items-center space-x-2 text-xs font-semibold text-stone-600 hover:text-gold-700 transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Undangan</span>
        </a>
        <a href="{{ route('admin.guests.index') }}" class="text-xs text-stone-400 hover:text-stone-600">
            Admin / Buku Tamu
        </a>
    </div>

    <!-- Main E-Ticket Container -->
    <div id="printable-ticket" class="ticket-card relative w-full max-w-md rounded-3xl overflow-hidden p-8 text-white">
        <!-- Ticket Cutouts & Perforation -->
        <div class="ticket-cutout-left absolute pointer-events-none"></div>
        <div class="ticket-cutout-right absolute pointer-events-none"></div>
        <div class="perforated-line pointer-events-none"></div>

        <!-- Ticket Header -->
        <div class="text-center pb-6 border-b border-gold-500/20">
            <div class="w-12 h-12 mx-auto mb-2 rounded-full border border-gold-400/50 flex items-center justify-center bg-gold-500/10">
                <span class="font-cinzel text-lg font-bold text-gold-300">D & S</span>
            </div>
            <p class="font-cinzel text-[10px] tracking-[0.3em] uppercase text-gold-400">Wedding E-Ticket Pass</p>
            <h1 class="font-serif text-2xl font-bold text-white mt-1">Dimas & Sarah</h1>
            <p class="text-xs text-stone-400 mt-1">{{ $wedding['event']['date_human'] }}</p>
        </div>

        <!-- Ticket Body Details -->
        <div class="py-6 space-y-5">
            <!-- Guest Name & Category -->
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-[10px] text-stone-400 uppercase tracking-widest block font-sans">Nama Tamu</span>
                    <h2 class="font-serif text-xl font-bold text-gold-200 mt-0.5">{{ $guest->name }}</h2>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-cinzel font-bold bg-gold-500/20 text-gold-300 border border-gold-500/40 uppercase tracking-wider">
                    {{ $guest->category }}
                </span>
            </div>

            <!-- Grid Details: Pax & Table -->
            <div class="grid grid-cols-2 gap-4 bg-stone-900/60 p-4 rounded-2xl border border-stone-800">
                <div>
                    <span class="text-[10px] text-stone-400 uppercase tracking-widest block">Jumlah Tamu (Pax)</span>
                    <span class="text-base font-bold text-white mt-0.5 block">
                        <i class="fa-solid fa-users text-gold-500 mr-1 text-xs"></i>
                        {{ $guest->pax }} Orang
                    </span>
                </div>
                <div>
                    <span class="text-[10px] text-stone-400 uppercase tracking-widest block">Nomor Meja</span>
                    <span class="text-base font-bold text-gold-300 mt-0.5 block">
                        <i class="fa-solid fa-chair text-gold-500 mr-1 text-xs"></i>
                        {{ $guest->table_number ?? 'Bebas / Reguler' }}
                    </span>
                </div>
            </div>

            <!-- Venue Details -->
            <div class="text-xs text-stone-300 space-y-1">
                <p class="flex items-center">
                    <i class="fa-solid fa-clock text-gold-400 mr-2 text-xs w-4"></i>
                    <span>Akad: {{ $wedding['event']['akad']['time'] }} | Resepsi: {{ $wedding['event']['resepsi']['time'] }}</span>
                </p>
                <p class="flex items-start">
                    <i class="fa-solid fa-location-dot text-gold-400 mr-2 text-xs w-4 mt-1"></i>
                    <span>{{ $wedding['event']['resepsi']['venue'] }} ({{ $wedding['event']['resepsi']['address'] }})</span>
                </p>
            </div>

            <!-- Check-in Status Banner -->
            <div>
                <span class="text-[10px] text-stone-400 uppercase tracking-widest block mb-1">Status Kehadiran</span>
                @if($guest->is_checked_in)
                    <div class="p-3 rounded-2xl bg-emerald-950/80 border border-emerald-500/60 flex items-center space-x-3 text-emerald-200">
                        <i class="fa-solid fa-circle-check text-emerald-400 text-xl"></i>
                        <div>
                            <p class="text-xs font-bold text-emerald-300">SUDAH CHECK-IN</p>
                            <p class="text-[11px] text-emerald-400/80">Waktu: {{ $guest->checked_in_at->format('d M Y - H:i') }} WIB ({{ $guest->checked_in_by }})</p>
                        </div>
                    </div>
                @else
                    <div class="p-3 rounded-2xl bg-amber-950/70 border border-amber-500/50 flex items-center space-x-3 text-amber-200">
                        <i class="fa-solid fa-clock text-amber-400 text-xl"></i>
                        <div>
                            <p class="text-xs font-bold text-amber-300">BELUM CHECK-IN</p>
                            <p class="text-[11px] text-amber-400/80">Tunjukkan QR Code di bawah kepada petugas penerima tamu saat tiba.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Ticket Footer / QR Code Section -->
        <div class="pt-8 text-center flex flex-col items-center justify-center">
            <!-- QR Box -->
            <div class="p-3 bg-white rounded-2xl shadow-xl border-2 border-gold-400/50 inline-block mb-3">
                <div id="ticket-qr-container"></div>
            </div>

            <!-- Ticket Code -->
            <span class="font-mono text-base font-bold tracking-widest text-gold-300">
                {{ $guest->ticket_code }}
            </span>
            <p class="text-[10px] text-stone-400 mt-1 uppercase tracking-wider">
                Digital Verification Pass
            </p>
        </div>
    </div>

    <!-- Action Buttons for Guest (Print, Download, Self-scan) -->
    <div class="w-full max-w-md mt-6 space-y-3 no-print">
        <div class="grid grid-cols-2 gap-3">
            <button onclick="window.print()" class="py-3 px-4 rounded-xl bg-stone-900 hover:bg-stone-800 text-white border border-stone-700 text-xs font-semibold uppercase tracking-wider transition flex items-center justify-center space-x-2">
                <i class="fa-solid fa-print text-gold-400"></i>
                <span>Cetak Tiket</span>
            </button>
            <button onclick="openSelfScanModal()" class="py-3 px-4 rounded-xl bg-gold-500 hover:bg-gold-400 text-stone-950 text-xs font-bold uppercase tracking-wider transition flex items-center justify-center space-x-2 shadow-lg">
                <i class="fa-solid fa-camera"></i>
                <span>Scan QR Acara</span>
            </button>
        </div>

        <a href="{{ $wedding['event']['resepsi']['maps_url'] }}" target="_blank" class="w-full py-3 px-4 rounded-xl bg-white hover:bg-stone-50 border border-stone-300 text-stone-700 text-xs font-semibold uppercase tracking-wider transition flex items-center justify-center space-x-2 shadow-sm">
            <i class="fa-solid fa-location-arrow text-gold-600"></i>
            <span>Buka Google Maps Menuju Venue</span>
        </a>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL: GUEST SELF-CHECKIN CAMERA SCANNER   -->
<!-- ========================================== -->
<div id="self-scan-modal" class="fixed inset-0 z-50 bg-stone-950/90 hidden items-center justify-center p-4 backdrop-blur-md no-print">
    <div class="bg-stone-900 border border-gold-500/40 rounded-3xl p-6 max-w-md w-full text-center relative shadow-2xl">
        <button onclick="closeSelfScanModal()" class="absolute top-4 right-4 text-stone-400 hover:text-white text-xl">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-gold-500/20 text-gold-400 flex items-center justify-center text-xl">
            <i class="fa-solid fa-qrcode"></i>
        </div>
        <h3 class="font-serif text-xl font-bold text-white mb-1">Scan QR Code Acara</h3>
        <p class="text-xs text-stone-400 mb-4">
            Arahkan kamera HP Anda ke QR Code yang terpajang di Meja Resepsi untuk Check-in otomatis.
        </p>

        <!-- QR Camera Video Viewport -->
        <div id="self-qr-reader" class="rounded-2xl overflow-hidden bg-black aspect-square w-full mb-4 border border-gold-500/30"></div>

        <div id="self-scan-result" class="hidden p-3 rounded-xl text-xs mb-3 font-semibold"></div>

        <button onclick="closeSelfScanModal()" class="w-full py-2.5 bg-stone-800 hover:bg-stone-700 text-stone-300 font-semibold rounded-xl text-xs transition">
            Tutup Kamera
        </button>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
    // Generate QR Code for Ticket
    const ticketCode = "{{ $guest->ticket_code }}";
    new QRCode(document.getElementById("ticket-qr-container"), {
        text: ticketCode,
        width: 160,
        height: 160,
        colorDark : "#0f172a",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H
    });

    // Self Check-in Scanner
    let html5QrScanner = null;
    const selfScanModal = document.getElementById('self-scan-modal');
    const selfScanResult = document.getElementById('self-scan-result');

    function openSelfScanModal() {
        selfScanModal.classList.remove('hidden');
        selfScanModal.classList.add('flex');
        selfScanResult.classList.add('hidden');

        html5QrScanner = new Html5Qrcode("self-qr-reader");
        html5QrScanner.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: { width: 220, height: 220 } },
            onQrScanSuccess,
            onQrScanFailure
        ).catch(err => {
            alert("Gagal membuka kamera: " + err);
            closeSelfScanModal();
        });
    }

    function closeSelfScanModal() {
        if (html5QrScanner) {
            html5QrScanner.stop().then(() => {
                html5QrScanner.clear();
            }).catch(e => console.log(e));
        }
        selfScanModal.classList.add('hidden');
        selfScanModal.classList.remove('flex');
    }

    function onQrScanSuccess(decodedText) {
        if (html5QrScanner) {
            html5QrScanner.pause();
        }

        selfScanResult.classList.remove('hidden');
        selfScanResult.className = "p-3 rounded-xl text-xs mb-3 font-semibold bg-stone-800 text-stone-300";
        selfScanResult.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Memvalidasi kehadiran...';

        fetch("{{ route('wedding.self-checkin') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                ticket_code: "{{ $guest->ticket_code }}",
                event_code: decodedText
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                selfScanResult.className = "p-3 rounded-xl text-xs mb-3 font-semibold bg-emerald-900 text-emerald-200 border border-emerald-500";
                selfScanResult.innerHTML = '<i class="fa-solid fa-circle-check text-emerald-400 mr-1"></i> ' + data.message;
                confetti({ particleCount: 100, spread: 90 });
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                selfScanResult.className = "p-3 rounded-xl text-xs mb-3 font-semibold bg-amber-900 text-amber-200 border border-amber-500";
                selfScanResult.innerHTML = '<i class="fa-solid fa-triangle-exclamation mr-1"></i> ' + data.message;
                setTimeout(() => {
                    if (html5QrScanner) html5QrScanner.resume();
                }, 3000);
            }
        })
        .catch(err => {
            selfScanResult.className = "p-3 rounded-xl text-xs mb-3 font-semibold bg-rose-900 text-rose-200 border border-rose-500";
            selfScanResult.innerHTML = 'Terjadi kesalahan sistem saat verifikasi.';
            setTimeout(() => {
                if (html5QrScanner) html5QrScanner.resume();
            }, 3000);
        });
    }

    function onQrScanFailure(error) {
        // Quiet
    }
</script>
@endpush

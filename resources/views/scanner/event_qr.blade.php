@extends('layouts.app')

@section('title', 'QR Code Meja Resepsi - The Wedding of Dimas & Sarah')

@push('styles')
<style>
    @media print {
        body {
            background: white !important;
            padding: 0 !important;
        }
        .no-print {
            display: none !important;
        }
        .print-card {
            border: 2px solid #000 !important;
            box-shadow: none !important;
            width: 100% !important;
            max-width: 100% !important;
            height: 98vh !important;
            margin: 0 !important;
        }
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-stone-100 flex flex-col items-center justify-center py-10 px-4">
    <!-- Top Actions -->
    <div class="w-full max-w-lg mb-6 flex justify-between items-center no-print">
        <a href="{{ route('scanner.index') }}" class="inline-flex items-center space-x-2 text-xs font-semibold text-stone-600 hover:text-gold-700 transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Scanner</span>
        </a>
        <div class="flex items-center space-x-3">
            <button onclick="window.print()" class="px-4 py-2 bg-stone-900 hover:bg-stone-800 text-white font-semibold text-xs rounded-xl shadow-md transition flex items-center space-x-2">
                <i class="fa-solid fa-print text-gold-400"></i>
                <span>Cetak Standee Meja</span>
            </button>
        </div>
    </div>

    <!-- Printable Table Standee Card -->
    <div class="print-card bg-white rounded-3xl border-2 border-gold-300 shadow-2xl p-10 max-w-lg w-full text-center relative overflow-hidden flex flex-col justify-between items-center">
        <!-- Top Ornamental Header -->
        <div>
            <div class="w-16 h-16 mx-auto mb-3 rounded-full border-2 border-gold-400 flex items-center justify-center bg-gold-50 shadow-sm">
                <span class="font-cinzel text-xl font-bold text-gold-700">D & S</span>
            </div>
            <p class="font-cinzel text-xs tracking-[0.3em] uppercase text-gold-600 font-semibold">Selamat Datang di Acara Pernikahan</p>
            <h1 class="font-serif text-3xl font-bold text-stone-900 mt-1">Dimas & Sarah</h1>
            <p class="text-xs text-stone-500 mt-1">{{ $wedding['event']['date_human'] }}</p>
            <div class="w-20 h-0.5 bg-gold-400 mx-auto mt-4"></div>
        </div>

        <!-- QR Code Standee Focus -->
        <div class="my-8 flex flex-col items-center">
            <div class="p-6 bg-white rounded-3xl border-4 border-gold-400 shadow-xl inline-block">
                <div id="event-qr-display"></div>
            </div>
            <p class="font-serif text-xl font-bold text-stone-800 mt-6 tracking-wide">
                SCAN UNTUK BUKU TAMU & CHECK-IN MANDIRI
            </p>
            <p class="text-xs text-stone-600 max-w-xs mt-2 leading-relaxed">
                Buka web undangan pernikahan di ponsel Anda, lalu pilih <strong>"Scan QR Acara"</strong> untuk mencatat kehadiran Anda secara otomatis.
            </p>
        </div>

        <!-- Footer Instructions -->
        <div class="pt-6 border-t border-stone-200 w-full text-stone-500 text-[11px] space-y-1">
            <p>Terima kasih atas kehadiran dan doa restu Bapak/Ibu/Saudara/i</p>
            <p class="font-semibold text-stone-700">Keluarga Besar Bpk. H. Hendro Utomo & Bpk. H. Achmad Fauzan</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const eventSecretCode = "{{ $wedding['event']['event_code'] }}";
    new QRCode(document.getElementById("event-qr-display"), {
        text: eventSecretCode,
        width: 240,
        height: 240,
        colorDark : "#1c1917",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H
    });
</script>
@endpush

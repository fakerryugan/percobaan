@extends('layouts.app')

@section('title', 'The Wedding of Dimas & Sarah')

@push('styles')
<style>
    /* Envelope Cover Animation */
    #welcome-cover {
        transition: transform 0.9s cubic-bezier(0.77, 0, 0.175, 1), opacity 0.8s ease-in-out;
    }
    .cover-closed {
        transform: translateY(0);
        opacity: 1;
        pointer-events: auto;
    }
    .cover-opened {
        transform: translateY(-100%);
        opacity: 0;
        pointer-events: none;
    }

    /* Floating Navigation */
    .floating-nav {
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }

    /* Lightbox */
    .lightbox-modal {
        transition: opacity 0.3s ease;
    }

    /* Soft pulse for badges */
    @keyframes soft-glow {
        0%, 100% { box-shadow: 0 0 15px rgba(208, 170, 110, 0.4); }
        50% { box-shadow: 0 0 25px rgba(208, 170, 110, 0.8); }
    }
    .glow-gold {
        animation: soft-glow 3s infinite ease-in-out;
    }
</style>
@endpush

@section('content')

<!-- ========================================== -->
<!-- 1. WELCOME COVER / ENVELOPE MODAL OVERLAY  -->
<!-- ========================================== -->
<div id="welcome-cover" class="fixed inset-0 z-50 flex items-center justify-center bg-stone-900 text-stone-100 overflow-hidden cover-closed">
    <!-- Ambient Background Image with Dark Overlay -->
    <div class="absolute inset-0 z-0 opacity-40 bg-cover bg-center scale-105 transition-transform duration-1000" style="background-image: url('https://images.unsplash.com/photo-1519741497674-611481863552?q=80&w=1600&auto=format&fit=crop');"></div>
    <div class="absolute inset-0 bg-gradient-to-t from-stone-950 via-stone-950/80 to-stone-900/60 z-0"></div>

    <div class="relative z-10 max-w-lg w-full px-6 py-12 text-center flex flex-col items-center">
        <!-- Floral / Monogram Crest -->
        <div class="w-20 h-20 mb-6 rounded-full border-2 border-gold-400/60 flex items-center justify-center shadow-lg bg-stone-900/60 backdrop-blur-md">
            <span class="font-cinzel text-2xl font-bold text-gold-300 tracking-wider">D & S</span>
        </div>

        <p class="font-cinzel text-xs tracking-[0.3em] uppercase text-gold-300 mb-2">The Wedding Of</p>
        <h1 class="font-cursive text-5xl md:text-6xl text-gold-200 mb-6 leading-tight drop-shadow-md">Dimas & Sarah</h1>

        <!-- Recipient Card -->
        <div class="glass-card-dark rounded-2xl p-6 w-full max-w-sm mb-8 border border-gold-400/30 text-center shadow-2xl">
            <p class="text-xs uppercase tracking-widest text-stone-300 font-sans mb-1">Kepada Yth. Bapak/Ibu/Saudara/i:</p>
            <h2 class="font-serif text-2xl font-bold text-gold-100 mt-2">
                {{ $guestName ?? ($guest->name ?? 'Tamu Undangan Yang Berbahagia') }}
            </h2>
            @if(isset($guest) && $guest->category)
                <span class="inline-block mt-2 px-3 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wider bg-gold-500/20 text-gold-300 border border-gold-500/40">
                    {{ $guest->category }} • {{ $guest->pax }} Kuota
                </span>
            @endif
        </div>

        <!-- Open Invitation Button -->
        <button id="btn-open-invitation" class="group relative px-8 py-3.5 bg-gradient-to-r from-gold-500 to-gold-600 hover:from-gold-400 hover:to-gold-500 text-stone-950 font-semibold rounded-full shadow-xl transition-all duration-300 transform hover:scale-105 active:scale-95 flex items-center space-x-3 text-sm tracking-wide">
            <i class="fa-solid fa-envelope-open text-stone-950 group-hover:rotate-12 transition-transform duration-300"></i>
            <span>Buka Undangan</span>
        </button>

        <p class="text-[11px] text-stone-400 mt-6 tracking-wide italic">
            *Mohon maaf bila ada kesalahan penulisan nama/gelar
        </p>
    </div>
</div>

<!-- ========================================== -->
<!-- 2. FLOATING CONTROLS & NAVIGATION          -->
<!-- ========================================== -->
<!-- Floating Audio Controller -->
<div class="fixed top-6 right-6 z-40">
    <button id="audio-control-btn" class="w-12 h-12 rounded-full glass-card border border-gold-400 shadow-xl flex items-center justify-center text-gold-600 hover:text-gold-700 transition duration-300 focus:outline-none" title="Putar / Hentikan Musik">
        <i id="audio-icon" class="fa-solid fa-compact-disc text-xl text-gold-500"></i>
    </button>
</div>

<!-- Bottom Floating Dock Navigation -->
<nav class="fixed bottom-4 inset-x-0 z-40 flex justify-center px-4 pointer-events-none">
    <div class="pointer-events-auto glass-card rounded-full px-4 py-2 flex items-center space-x-4 md:space-x-6 text-stone-600 border border-gold-300/40 shadow-2xl">
        <a href="#hero" class="p-2 hover:text-gold-600 transition" title="Beranda"><i class="fa-solid fa-home text-base"></i></a>
        <a href="#couple" class="p-2 hover:text-gold-600 transition" title="Mempelai"><i class="fa-solid fa-heart text-base"></i></a>
        <a href="#event" class="p-2 hover:text-gold-600 transition" title="Acara"><i class="fa-solid fa-calendar-days text-base"></i></a>
        <a href="#ticket" class="p-2 text-gold-600 hover:text-gold-700 transition font-bold" title="Tiket & Scan"><i class="fa-solid fa-qrcode text-lg"></i></a>
        <a href="#gallery" class="p-2 hover:text-gold-600 transition" title="Galeri"><i class="fa-solid fa-images text-base"></i></a>
        <a href="#rsvp" class="p-2 hover:text-gold-600 transition" title="RSVP & Doa"><i class="fa-solid fa-comment-dots text-base"></i></a>
        <a href="#gift" class="p-2 hover:text-gold-600 transition" title="Kado"><i class="fa-solid fa-gift text-base"></i></a>
    </div>
</nav>

<!-- Audio Element with Fallback Synthesizer -->
<audio id="wedding-audio" loop preload="auto">
    <source src="https://assets.mixkit.co/music/preview/mixkit-wedding-dreams-253.mp3" type="audio/mp3">
</audio>

<!-- ========================================== -->
<!-- 3. HERO SECTION                            -->
<!-- ========================================== -->
<section id="hero" class="relative min-h-screen flex items-center justify-center py-20 px-4 text-center overflow-hidden">
    <!-- Background Image with Overlay -->
    <div class="absolute inset-0 bg-cover bg-center z-0 opacity-25 filter blur-[1px]" style="background-image: url('https://images.unsplash.com/photo-1519741497674-611481863552?q=80&w=1600&auto=format&fit=crop');"></div>
    <div class="absolute inset-0 bg-gradient-to-b from-champagne/90 via-champagne/70 to-champagne z-0"></div>

    <div class="relative z-10 max-w-2xl mx-auto flex flex-col items-center">
        <!-- Floral Badge -->
        <div class="inline-flex items-center space-x-2 px-4 py-1.5 rounded-full border border-gold-400/50 bg-white/70 backdrop-blur-sm text-gold-700 text-xs font-cinzel tracking-widest uppercase mb-6 shadow-sm">
            <i class="fa-solid fa-rings-wedding text-gold-500"></i>
            <span>The Wedding Celebration</span>
        </div>

        <h1 class="font-cursive text-6xl md:text-8xl text-stone-800 mb-4 leading-none">Dimas & Sarah</h1>
        <p class="font-serif italic text-lg text-stone-600 mb-8 max-w-lg">
            "Dan di antara tanda-tanda kekuasaan-Nya ialah Dia menciptakan untukmu pasangan hidup dari jenismu sendiri, supaya kamu merasa tenteram kepadanya."
        </p>

        <!-- Date & Location Card -->
        <div class="glass-card rounded-2xl p-6 w-full max-w-md shadow-xl border border-gold-300/40 mb-10">
            <p class="font-cinzel text-sm uppercase tracking-wider text-gold-700 font-semibold mb-1">
                {{ $wedding['event']['date_human'] }}
            </p>
            <p class="text-xs text-stone-500 font-sans">
                {{ $wedding['event']['resepsi']['venue'] }} • Jakarta & Bogor
            </p>

            <!-- Countdown Timer -->
            <div class="grid grid-cols-4 gap-3 mt-6 pt-6 border-t border-gold-200">
                <div class="text-center p-2 rounded-xl bg-gold-50/80 border border-gold-200/50">
                    <span id="countdown-days" class="font-serif font-bold text-2xl text-stone-800">00</span>
                    <span class="block text-[10px] uppercase tracking-wider text-stone-500">Hari</span>
                </div>
                <div class="text-center p-2 rounded-xl bg-gold-50/80 border border-gold-200/50">
                    <span id="countdown-hours" class="font-serif font-bold text-2xl text-stone-800">00</span>
                    <span class="block text-[10px] uppercase tracking-wider text-stone-500">Jam</span>
                </div>
                <div class="text-center p-2 rounded-xl bg-gold-50/80 border border-gold-200/50">
                    <span id="countdown-minutes" class="font-serif font-bold text-2xl text-stone-800">00</span>
                    <span class="block text-[10px] uppercase tracking-wider text-stone-500">Menit</span>
                </div>
                <div class="text-center p-2 rounded-xl bg-gold-50/80 border border-gold-200/50">
                    <span id="countdown-seconds" class="font-serif font-bold text-2xl text-stone-800">00</span>
                    <span class="block text-[10px] uppercase tracking-wider text-stone-500">Detik</span>
                </div>
            </div>
        </div>

        <a href="#ticket" class="inline-flex items-center space-x-2 text-xs font-semibold uppercase tracking-wider text-gold-700 bg-gold-100/80 hover:bg-gold-200 border border-gold-300 px-5 py-2.5 rounded-full transition shadow-sm">
            <i class="fa-solid fa-ticket text-gold-600"></i>
            <span>Lihat Tiket Tamu & QR Code</span>
        </a>
    </div>
</section>

<!-- ========================================== -->
<!-- 4. QURAN VERSE / AYAT SUCI                 -->
<!-- ========================================== -->
<section class="py-16 px-4 bg-white/60 text-center relative border-y border-gold-200/40">
    <div class="max-w-3xl mx-auto">
        <i class="fa-solid fa-leaf text-gold-500 text-2xl mb-4"></i>
        <p class="font-serif text-2xl md:text-3xl text-stone-800 leading-relaxed font-arabic mb-6" dir="rtl" style="font-family: 'Amiri', serif, sans-serif;">
            وَمِنْ آيَاتِهِ أَنْ خَلَقَ لَكُم مِّنْ أَنفُسِكُمْ أَزْوَاجًا لِّتَسْكُنُوا إِلَيْهَا وَجَعَلَ بَيْنَكُم مَّوَدَّةً وَرَحْمَةً ۚ إِنَّ فِي ذَٰلِكَ لَآيَاتٍ لِّقَوْمٍ يَتَفَكَّرُونَ
        </p>
        <p class="text-stone-600 text-sm md:text-base leading-relaxed italic max-w-2xl mx-auto">
            "Dan di antara tanda-tanda (kebesaran)-Nya ialah Dia menciptakan pasangan-pasangan untukmu dari jenismu sendiri, agar kamu cenderung dan merasa tenteram kepadanya, dan Dia menjadikan di antaramu rasa kasih dan sayang. Sungguh, pada yang demikian itu benar-benar terdapat tanda-tanda bagi kaum yang berpikir."
        </p>
        <span class="block text-xs uppercase tracking-widest text-gold-600 font-cinzel mt-4 font-semibold">
            (QS. Ar-Rum: 21)
        </span>
    </div>
</section>

<!-- ========================================== -->
<!-- 5. GROOM & BRIDE PROFILE                   -->
<!-- ========================================== -->
<section id="couple" class="py-24 px-4 max-w-5xl mx-auto">
    <div class="text-center mb-16">
        <p class="font-cinzel text-xs uppercase tracking-[0.25em] text-gold-600 mb-2">Pasangan Mempelai</p>
        <h2 class="font-serif text-3xl md:text-4xl text-stone-800">Mempelai Pria & Wanita</h2>
        <div class="w-16 h-0.5 bg-gold-400 mx-auto mt-4"></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 lg:gap-16 items-center">
        <!-- Groom Card -->
        <div class="glass-card rounded-3xl p-8 text-center border border-gold-200/80 shadow-xl hover:shadow-2xl transition duration-500 transform hover:-translate-y-1">
            <div class="w-48 h-48 mx-auto mb-6 rounded-full overflow-hidden border-4 border-gold-300 shadow-xl relative">
                <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?q=80&w=800&auto=format&fit=crop" alt="Dimas Arya" class="w-full h-full object-cover">
            </div>
            <h3 class="font-serif text-2xl font-bold text-stone-800">{{ $wedding['groom']['name'] }}</h3>
            <p class="font-cursive text-3xl text-gold-600 my-2">{{ $wedding['groom']['nickname'] }}</p>
            <p class="text-xs text-stone-500 leading-relaxed px-4 mb-6">
                {{ $wedding['groom']['son_of'] }}
            </p>
            <a href="https://instagram.com/{{ $wedding['groom']['instagram'] }}" target="_blank" class="inline-flex items-center space-x-2 text-xs text-stone-600 hover:text-gold-600 transition">
                <i class="fa-brands fa-instagram text-base text-pink-600"></i>
                <span>@ {{ $wedding['groom']['instagram'] }}</span>
            </a>
        </div>

        <!-- Bride Card -->
        <div class="glass-card rounded-3xl p-8 text-center border border-gold-200/80 shadow-xl hover:shadow-2xl transition duration-500 transform hover:-translate-y-1">
            <div class="w-48 h-48 mx-auto mb-6 rounded-full overflow-hidden border-4 border-gold-300 shadow-xl relative">
                <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=800&auto=format&fit=crop" alt="Sarah Amanda" class="w-full h-full object-cover">
            </div>
            <h3 class="font-serif text-2xl font-bold text-stone-800">{{ $wedding['bride']['name'] }}</h3>
            <p class="font-cursive text-3xl text-gold-600 my-2">{{ $wedding['bride']['nickname'] }}</p>
            <p class="text-xs text-stone-500 leading-relaxed px-4 mb-6">
                {{ $wedding['bride']['daughter_of'] }}
            </p>
            <a href="https://instagram.com/{{ $wedding['bride']['instagram'] }}" target="_blank" class="inline-flex items-center space-x-2 text-xs text-stone-600 hover:text-gold-600 transition">
                <i class="fa-brands fa-instagram text-base text-pink-600"></i>
                <span>@ {{ $wedding['bride']['instagram'] }}</span>
            </a>
        </div>
    </div>
</section>

<!-- ========================================== -->
<!-- 6. WEDDING EVENT SCHEDULE & VENUE          -->
<!-- ========================================== -->
<section id="event" class="py-24 px-4 bg-stone-100/70 relative">
    <div class="max-w-5xl mx-auto">
        <div class="text-center mb-16">
            <p class="font-cinzel text-xs uppercase tracking-[0.25em] text-gold-600 mb-2">Save The Date</p>
            <h2 class="font-serif text-3xl md:text-4xl text-stone-800">Waktu & Tempat Acara</h2>
            <div class="w-16 h-0.5 bg-gold-400 mx-auto mt-4"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Akad Nikah -->
            <div class="bg-white rounded-3xl p-8 border border-gold-200/80 shadow-lg text-center flex flex-col justify-between">
                <div>
                    <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-gold-100 text-gold-700 flex items-center justify-center text-xl">
                        <i class="fa-solid fa-handshake"></i>
                    </div>
                    <h3 class="font-serif text-2xl font-bold text-stone-800 mb-2">Akad Nikah</h3>
                    <p class="text-xs uppercase tracking-wider font-semibold text-gold-600 mb-4">{{ $wedding['event']['date_human'] }}</p>
                    <p class="text-sm font-semibold text-stone-700 mb-2"><i class="fa-regular fa-clock text-gold-500 mr-2"></i>{{ $wedding['event']['akad']['time'] }}</p>
                    <p class="text-stone-800 font-bold text-base mb-1">{{ $wedding['event']['akad']['venue'] }}</p>
                    <p class="text-xs text-stone-500 max-w-sm mx-auto leading-relaxed mb-6">{{ $wedding['event']['akad']['address'] }}</p>
                </div>
                <div class="pt-4 border-t border-stone-100">
                    <a href="{{ $wedding['event']['akad']['maps_url'] }}" target="_blank" class="inline-flex items-center space-x-2 text-xs font-semibold text-gold-700 hover:text-gold-800 bg-gold-50 hover:bg-gold-100 px-4 py-2 rounded-xl transition">
                        <i class="fa-solid fa-location-dot"></i>
                        <span>Petunjuk Lokasi (Google Maps)</span>
                    </a>
                </div>
            </div>

            <!-- Resepsi Pernikahan -->
            <div class="bg-white rounded-3xl p-8 border border-gold-200/80 shadow-lg text-center flex flex-col justify-between">
                <div>
                    <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-gold-100 text-gold-700 flex items-center justify-center text-xl">
                        <i class="fa-solid fa-champagne-glasses"></i>
                    </div>
                    <h3 class="font-serif text-2xl font-bold text-stone-800 mb-2">Resepsi Pernikahan</h3>
                    <p class="text-xs uppercase tracking-wider font-semibold text-gold-600 mb-4">{{ $wedding['event']['date_human'] }}</p>
                    <p class="text-sm font-semibold text-stone-700 mb-2"><i class="fa-regular fa-clock text-gold-500 mr-2"></i>{{ $wedding['event']['resepsi']['time'] }}</p>
                    <p class="text-stone-800 font-bold text-base mb-1">{{ $wedding['event']['resepsi']['venue'] }}</p>
                    <p class="text-xs text-stone-500 max-w-sm mx-auto leading-relaxed mb-6">{{ $wedding['event']['resepsi']['address'] }}</p>
                </div>
                <div class="pt-4 border-t border-stone-100">
                    <a href="{{ $wedding['event']['resepsi']['maps_url'] }}" target="_blank" class="inline-flex items-center space-x-2 text-xs font-semibold text-gold-700 hover:text-gold-800 bg-gold-50 hover:bg-gold-100 px-4 py-2 rounded-xl transition">
                        <i class="fa-solid fa-location-dot"></i>
                        <span>Petunjuk Lokasi (Google Maps)</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========================================== -->
<!-- 7. GUEST TICKET & SCAN SECTION             -->
<!-- ========================================== -->
<section id="ticket" class="py-24 px-4 bg-gradient-to-b from-stone-900 to-stone-950 text-white relative overflow-hidden">
    <!-- Decorative background elements -->
    <div class="absolute -top-40 -right-40 w-96 h-96 bg-gold-600/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-gold-600/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-4xl mx-auto relative z-10">
        <div class="text-center mb-12">
            <span class="inline-block px-4 py-1 rounded-full text-xs font-cinzel uppercase tracking-[0.25em] bg-gold-500/20 text-gold-300 border border-gold-500/40 mb-3">
                E-Ticket & Digital Check-in
            </span>
            <h2 class="font-serif text-3xl md:text-4xl text-gold-100">Tiket Masuk Tamu Undangan</h2>
            <p class="text-xs md:text-sm text-stone-400 mt-2 max-w-md mx-auto">
                Tunjukkan QR Code ini di meja penerima tamu saat tiba, atau scan QR Acara untuk check-in mandiri.
            </p>
            <div class="w-16 h-0.5 bg-gold-500 mx-auto mt-4"></div>
        </div>

        @if(isset($guest))
            <!-- VIP / Guest Ticket Pass Card -->
            <div class="max-w-xl mx-auto bg-stone-900/90 border border-gold-500/50 rounded-3xl p-6 md:p-8 shadow-2xl relative backdrop-blur-xl">
                <!-- Golden Ribbon / Watermark -->
                <div class="flex items-center justify-between pb-6 border-b border-gold-500/30">
                    <div>
                        <span class="text-[10px] uppercase tracking-widest text-gold-400 font-cinzel font-bold">Wedding Admission Pass</span>
                        <h3 class="font-serif text-xl font-bold text-white">The Wedding of Dimas & Sarah</h3>
                    </div>
                    <span class="px-3 py-1 bg-gold-500 text-stone-950 font-cinzel font-bold text-xs rounded-full uppercase tracking-wider shadow-md">
                        {{ $guest->category }}
                    </span>
                </div>

                <!-- Ticket Content Body -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center my-6">
                    <!-- Guest Details -->
                    <div class="space-y-4">
                        <div>
                            <span class="text-[10px] text-stone-400 uppercase tracking-wider block">Nama Tamu</span>
                            <span class="font-serif text-xl font-bold text-gold-200">{{ $guest->name }}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div>
                                <span class="text-[10px] text-stone-400 uppercase tracking-wider block">Kuota Tamu</span>
                                <span class="font-semibold text-white">{{ $guest->pax }} Orang</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-stone-400 uppercase tracking-wider block">Nomor Meja</span>
                                <span class="font-semibold text-gold-300">{{ $guest->table_number ?? 'Reguler' }}</span>
                            </div>
                        </div>
                        <div>
                            <span class="text-[10px] text-stone-400 uppercase tracking-wider block">Status Check-in</span>
                            @if($guest->is_checked_in)
                                <div class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full text-xs bg-emerald-950 text-emerald-300 border border-emerald-500/50 mt-1">
                                    <i class="fa-solid fa-circle-check text-emerald-400"></i>
                                    <span>Sudah Hadir ({{ $guest->checked_in_at->format('H:i') }} WIB)</span>
                                </div>
                            @else
                                <div class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full text-xs bg-amber-950 text-amber-300 border border-amber-500/50 mt-1">
                                    <i class="fa-solid fa-clock text-amber-400"></i>
                                    <span>Belum Check-In</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- QR Code Card -->
                    <div class="flex flex-col items-center justify-center p-4 bg-white rounded-2xl shadow-inner text-stone-900">
                        <div id="guest-ticket-qr" class="p-1"></div>
                        <span class="font-mono text-xs font-bold tracking-widest mt-2 text-stone-800">
                            {{ $guest->ticket_code }}
                        </span>
                        <span class="text-[9px] text-stone-500 uppercase tracking-wider mt-0.5">Scan untuk konfirmasi kehadiran</span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="pt-6 border-t border-gold-500/30 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <a href="{{ route('wedding.ticket', $guest->ticket_code) }}" target="_blank" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-gold-500 hover:bg-gold-400 text-stone-950 font-bold text-xs tracking-wider uppercase text-center transition flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        <span>Halaman Tiket Lengkap</span>
                    </a>

                    <!-- Button to scan Event QR from guest phone -->
                    <button onclick="openSelfScanModal()" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-stone-800 hover:bg-stone-700 text-gold-300 border border-gold-500/40 text-xs font-semibold tracking-wider uppercase transition flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-camera"></i>
                        <span>Scan QR Acara (Check-in)</span>
                    </button>
                </div>
            </div>
        @else
            <!-- Guest Not Found / Public visitor card -->
            <div class="max-w-md mx-auto bg-stone-900/80 border border-gold-500/30 rounded-3xl p-8 text-center backdrop-blur-lg">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gold-500/20 text-gold-400 flex items-center justify-center text-2xl border border-gold-500/40">
                    <i class="fa-solid fa-id-card"></i>
                </div>
                <h3 class="font-serif text-xl font-bold text-white mb-2">Cek Tiket Undangan Anda</h3>
                <p class="text-xs text-stone-400 mb-6">
                    Masukkan kode tiket Anda (contoh: <code>TKT-VIP001</code>) untuk menampilkan E-Ticket dan QR Code kehadiran.
                </p>

                <form action="{{ route('wedding.home') }}" method="GET" class="flex flex-col gap-3">
                    <input type="text" name="code" placeholder="Kode Tiket (misal: TKT-VIP001)" required class="w-full px-4 py-3 rounded-xl bg-stone-950 border border-stone-700 text-white placeholder-stone-500 text-sm focus:outline-none focus:border-gold-500 text-center tracking-wider font-mono">
                    <button type="submit" class="w-full py-3 bg-gold-500 hover:bg-gold-400 text-stone-950 font-bold rounded-xl text-xs uppercase tracking-wider transition">
                        Buka Tiket Saya
                    </button>
                </form>

                <div class="mt-6 pt-6 border-t border-stone-800 text-stone-500 text-[11px]">
                    Belum memiliki kode tiket? Silakan hubungi kedua mempelai atau isi konfirmasi kehadiran pada formulir RSVP di bawah.
                </div>
            </div>
        @endif
    </div>
</section>

<!-- ========================================== -->
<!-- 8. LOVE STORY TIMELINE                     -->
<!-- ========================================== -->
<section class="py-24 px-4 max-w-4xl mx-auto">
    <div class="text-center mb-16">
        <p class="font-cinzel text-xs uppercase tracking-[0.25em] text-gold-600 mb-2">Perjalanan Cinta</p>
        <h2 class="font-serif text-3xl md:text-4xl text-stone-800">Cerita Kami</h2>
        <div class="w-16 h-0.5 bg-gold-400 mx-auto mt-4"></div>
    </div>

    <div class="relative border-l-2 border-gold-300 ml-4 md:ml-32 space-y-12">
        <!-- Story 1 -->
        <div class="relative pl-8 group">
            <div class="absolute -left-[9px] top-1.5 w-4 h-4 rounded-full bg-gold-500 border-4 border-[#FAF7F2]"></div>
            <span class="text-xs font-cinzel font-bold text-gold-600 tracking-wider">Januari 2021</span>
            <h3 class="font-serif text-xl font-bold text-stone-800 mt-1">Pertemuan Pertama</h3>
            <p class="text-stone-600 text-xs md:text-sm mt-2 leading-relaxed max-w-lg">
                Berawal dari pertemuan tak sengaja di sebuah seminar nasional di Bandung. Obrolan singkat mengenai riset dan cita-cita membuka pintu persahabatan yang hangat.
            </p>
        </div>

        <!-- Story 2 -->
        <div class="relative pl-8 group">
            <div class="absolute -left-[9px] top-1.5 w-4 h-4 rounded-full bg-gold-500 border-4 border-[#FAF7F2]"></div>
            <span class="text-xs font-cinzel font-bold text-gold-600 tracking-wider">Agustus 2024</span>
            <h3 class="font-serif text-xl font-bold text-stone-800 mt-1">Komitmen & Lamaran</h3>
            <p class="text-stone-600 text-xs md:text-sm mt-2 leading-relaxed max-w-lg">
                Dengan restu kedua orang tua dan niat yang tulus karena Allah SWT, kami melangkah ke jenjang yang lebih serius dalam ikatan lamaran yang sakral.
            </p>
        </div>

        <!-- Story 3 -->
        <div class="relative pl-8 group">
            <div class="absolute -left-[9px] top-1.5 w-4 h-4 rounded-full bg-gold-500 border-4 border-[#FAF7F2]"></div>
            <span class="text-xs font-cinzel font-bold text-gold-600 tracking-wider">Oktober 2026</span>
            <h3 class="font-serif text-xl font-bold text-stone-800 mt-1">Hari Bahagia & Selamanya</h3>
            <p class="text-stone-600 text-xs md:text-sm mt-2 leading-relaxed max-w-lg">
                Insya Allah kami akan mengikat janji suci pernikahan untuk mengarungi bahtera rumah tangga bersama hingga surga-Nya.
            </p>
        </div>
    </div>
</section>

<!-- ========================================== -->
<!-- 9. PHOTO GALLERY                           -->
<!-- ========================================== -->
<section id="gallery" class="py-24 px-4 bg-stone-100/60">
    <div class="max-w-5xl mx-auto">
        <div class="text-center mb-16">
            <p class="font-cinzel text-xs uppercase tracking-[0.25em] text-gold-600 mb-2">Memori Indah</p>
            <h2 class="font-serif text-3xl md:text-4xl text-stone-800">Galeri Foto</h2>
            <div class="w-16 h-0.5 bg-gold-400 mx-auto mt-4"></div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="overflow-hidden rounded-2xl shadow-md cursor-pointer group" onclick="openPhotoModal('https://images.unsplash.com/photo-1519741497674-611481863552?q=80&w=1200&auto=format&fit=crop')">
                <img src="https://images.unsplash.com/photo-1519741497674-611481863552?q=80&w=600&auto=format&fit=crop" class="w-full h-64 object-cover transform group-hover:scale-110 transition duration-500">
            </div>
            <div class="overflow-hidden rounded-2xl shadow-md cursor-pointer group" onclick="openPhotoModal('https://images.unsplash.com/photo-1583939003579-730e3918a45a?q=80&w=1200&auto=format&fit=crop')">
                <img src="https://images.unsplash.com/photo-1583939003579-730e3918a45a?q=80&w=600&auto=format&fit=crop" class="w-full h-64 object-cover transform group-hover:scale-110 transition duration-500">
            </div>
            <div class="overflow-hidden rounded-2xl shadow-md cursor-pointer group" onclick="openPhotoModal('https://images.unsplash.com/photo-1465495976277-4387d4b0b4c6?q=80&w=1200&auto=format&fit=crop')">
                <img src="https://images.unsplash.com/photo-1465495976277-4387d4b0b4c6?q=80&w=600&auto=format&fit=crop" class="w-full h-64 object-cover transform group-hover:scale-110 transition duration-500">
            </div>
            <div class="overflow-hidden rounded-2xl shadow-md cursor-pointer group" onclick="openPhotoModal('https://images.unsplash.com/photo-1511285560929-80b456fea0bc?q=80&w=1200&auto=format&fit=crop')">
                <img src="https://images.unsplash.com/photo-1511285560929-80b456fea0bc?q=80&w=600&auto=format&fit=crop" class="w-full h-64 object-cover transform group-hover:scale-110 transition duration-500">
            </div>
        </div>
    </div>
</section>

<!-- ========================================== -->
<!-- 10. RSVP & GUEST WISHES                    -->
<!-- ========================================== -->
<section id="rsvp" class="py-24 px-4 max-w-4xl mx-auto">
    <div class="text-center mb-16">
        <p class="font-cinzel text-xs uppercase tracking-[0.25em] text-gold-600 mb-2">Konfirmasi & Doa Restu</p>
        <h2 class="font-serif text-3xl md:text-4xl text-stone-800">Buku Tamu & Ucapan</h2>
        <div class="w-16 h-0.5 bg-gold-400 mx-auto mt-4"></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
        <!-- RSVP Form -->
        <div class="glass-card rounded-3xl p-6 md:p-8 border border-gold-200/80 shadow-xl">
            <h3 class="font-serif text-xl font-bold text-stone-800 mb-4 flex items-center">
                <i class="fa-solid fa-pen-nib text-gold-600 mr-2 text-base"></i>
                Konfirmasi Kehadiran
            </h3>

            <form id="rsvp-form" action="{{ route('wedding.rsvp') }}" method="POST" class="space-y-4">
                @csrf
                @if(isset($guest))
                    <input type="hidden" name="guest_id" value="{{ $guest->id }}">
                    <input type="hidden" name="ticket_code" value="{{ $guest->ticket_code }}">
                @endif

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-1">Nama Anda</label>
                    <input type="text" name="name" value="{{ $guest->name ?? '' }}" required placeholder="Masukkan nama lengkap" class="w-full px-4 py-2.5 rounded-xl border border-stone-300 focus:outline-none focus:border-gold-500 text-sm bg-white/80">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-1">Konfirmasi Kehadiran</label>
                    <select name="attendance" required class="w-full px-4 py-2.5 rounded-xl border border-stone-300 focus:outline-none focus:border-gold-500 text-sm bg-white/80">
                        <option value="hadir" {{ isset($guest) && $guest->rsvp_status == 'attending' ? 'selected' : '' }}>Hadir</option>
                        <option value="tidak_hadir" {{ isset($guest) && $guest->rsvp_status == 'declined' ? 'selected' : '' }}>Tidak Dapat Hadir</option>
                        <option value="ragu" {{ isset($guest) && $guest->rsvp_status == 'tentative' ? 'selected' : '' }}>Masih Ragu</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-1">Jumlah Tamu Hadir</label>
                    <input type="number" name="pax" min="1" max="{{ isset($guest) ? $guest->pax : 5 }}" value="{{ isset($guest) ? ($guest->rsvp_pax ?: $guest->pax) : 1 }}" class="w-full px-4 py-2.5 rounded-xl border border-stone-300 focus:outline-none focus:border-gold-500 text-sm bg-white/80">
                    @if(isset($guest))
                        <span class="text-[10px] text-stone-500">Maksimum kuota undangan Anda: {{ $guest->pax }} orang</span>
                    @endif
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-1">Ucapan & Doa Restu</label>
                    <textarea name="message" rows="4" required placeholder="Tuliskan doa restu dan ucapan hangat untuk kedua mempelai..." class="w-full px-4 py-2.5 rounded-xl border border-stone-300 focus:outline-none focus:border-gold-500 text-sm bg-white/80">{{ isset($guest) ? $guest->wishes : '' }}</textarea>
                </div>

                <button type="submit" id="btn-submit-rsvp" class="w-full py-3 bg-gradient-to-r from-gold-500 to-gold-600 hover:from-gold-600 hover:to-gold-700 text-white font-bold rounded-xl text-xs uppercase tracking-wider transition shadow-md flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>Kirim Konfirmasi & Doa</span>
                </button>
            </form>
        </div>

        <!-- Real-time Wishes List -->
        <div class="glass-card rounded-3xl p-6 md:p-8 border border-gold-200/80 shadow-xl flex flex-col h-[520px]">
            <h3 class="font-serif text-xl font-bold text-stone-800 mb-4 flex items-center justify-between">
                <span class="flex items-center">
                    <i class="fa-solid fa-heart text-rose-500 mr-2 text-base"></i>
                    Doa & Ucapan Restu
                </span>
                <span id="wishes-count" class="text-xs font-sans px-2.5 py-1 bg-gold-100 text-gold-800 rounded-full font-bold">
                    {{ $wishes->count() }} Ucapan
                </span>
            </h3>

            <div id="wishes-container" class="space-y-4 overflow-y-auto pr-2 flex-1">
                @forelse($wishes as $wish)
                    <div class="p-4 rounded-2xl bg-white/90 border border-stone-200/80 shadow-sm">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-semibold text-xs text-stone-800">{{ $wish->name }}</span>
                            <span class="text-[10px] text-stone-400">{{ $wish->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="mb-2">
                            @if($wish->attendance === 'hadir')
                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 font-semibold"><i class="fa-solid fa-check mr-1"></i>Hadir</span>
                            @elseif($wish->attendance === 'tidak_hadir')
                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-rose-100 text-rose-700 font-semibold">Tidak Hadir</span>
                            @else
                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 font-semibold">Ragu</span>
                            @endif
                            @if($wish->relationship)
                                <span class="text-[10px] text-stone-500 ml-1">• {{ $wish->relationship }}</span>
                            @endif
                        </div>
                        <p class="text-xs text-stone-600 leading-relaxed">{{ $wish->message }}</p>
                    </div>
                @empty
                    <p class="text-center text-stone-400 text-xs py-8">Belum ada ucapan. Jadilah yang pertama memberikan doa restu!</p>
                @endforelse
            </div>
        </div>
    </div>
</section>

<!-- ========================================== -->
<!-- 11. WEDDING GIFT / DIGITAL CASH            -->
<!-- ========================================== -->
<section id="gift" class="py-24 px-4 bg-stone-100/70">
    <div class="max-w-3xl mx-auto text-center">
        <div class="mb-12">
            <p class="font-cinzel text-xs uppercase tracking-[0.25em] text-gold-600 mb-2">Tanda Kasih</p>
            <h2 class="font-serif text-3xl md:text-4xl text-stone-800">Wedding Gift</h2>
            <p class="text-xs md:text-sm text-stone-500 mt-2 max-w-md mx-auto">
                Doa restu Anda merupakan karunia yang paling berarti bagi kami. Dan jika memberi adalah ungkapan tanda kasih Anda, Anda dapat memberi kado secara cashless:
            </p>
            <div class="w-16 h-0.5 bg-gold-400 mx-auto mt-4"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            @foreach($wedding['bank_accounts'] as $acc)
                <div class="bg-white rounded-3xl p-6 border border-gold-200 shadow-md text-left relative overflow-hidden">
                    <div class="flex items-center justify-between mb-4">
                        <span class="font-bold text-stone-800 text-sm tracking-wide">{{ $acc['bank'] }}</span>
                        <i class="fa-solid fa-credit-card text-gold-500 text-xl"></i>
                    </div>
                    <p class="text-xs text-stone-400 uppercase tracking-wider">Nomor Rekening</p>
                    <p class="font-mono text-xl font-bold text-stone-800 tracking-wider my-1">{{ $acc['account_number'] }}</p>
                    <p class="text-xs text-stone-600 mb-4">a/n {{ $acc['account_name'] }}</p>

                    <button onclick="copyToClipboard('{{ $acc['account_number'] }}', 'Nomor rekening {{ $acc['bank'] }}')" class="w-full py-2 bg-gold-50 hover:bg-gold-100 text-gold-700 font-semibold rounded-xl text-xs transition border border-gold-200 flex items-center justify-center space-x-2">
                        <i class="fa-regular fa-copy"></i>
                        <span>Salin No. Rekening</span>
                    </button>
                </div>
            @endforeach
        </div>

        <!-- Physical Gift Address -->
        <div class="bg-white rounded-3xl p-6 border border-gold-200 shadow-md text-left">
            <div class="flex items-center space-x-3 mb-3">
                <i class="fa-solid fa-box-open text-gold-600 text-xl"></i>
                <h3 class="font-serif font-bold text-stone-800 text-base">Kirim Kado Fisik</h3>
            </div>
            <p class="text-xs text-stone-600 leading-relaxed mb-4">
                Penerima: <strong>{{ $wedding['gift_address']['recipient'] }}</strong> ({{ $wedding['gift_address']['phone'] }})<br>
                Alamat: {{ $wedding['gift_address']['address'] }}
            </p>
            <button onclick="copyToClipboard('{{ $wedding['gift_address']['address'] }}', 'Alamat pengiriman')" class="px-4 py-2 bg-stone-100 hover:bg-stone-200 text-stone-700 font-semibold rounded-xl text-xs transition flex items-center space-x-2">
                <i class="fa-regular fa-copy"></i>
                <span>Salin Alamat Lengkap</span>
            </button>
        </div>
    </div>
</section>

<!-- ========================================== -->
<!-- 12. FOOTER & SHORTCUT LINKS                -->
<!-- ========================================== -->
<footer class="py-16 px-4 bg-stone-900 text-stone-400 text-center relative">
    <div class="max-w-md mx-auto">
        <h4 class="font-cursive text-5xl text-gold-300 mb-4">Dimas & Sarah</h4>
        <p class="text-xs text-stone-400 leading-relaxed mb-8">
            Merupakan suatu kehormatan dan kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i berkenan hadir dan memberikan doa restu bagi kami.
        </p>

        <!-- Admin & Receptionist Quick Links -->
        <div class="pt-8 border-t border-stone-800 flex items-center justify-center space-x-6 text-xs">
            <a href="{{ route('scanner.index') }}" class="text-gold-400 hover:text-gold-300 transition flex items-center space-x-1.5">
                <i class="fa-solid fa-qrcode"></i>
                <span>Scanner Petugas</span>
            </a>
            <span class="text-stone-700">•</span>
            <a href="{{ route('admin.guests.index') }}" class="text-stone-400 hover:text-white transition flex items-center space-x-1.5">
                <i class="fa-solid fa-book-bookmark"></i>
                <span>Buku Tamu / Admin</span>
            </a>
        </div>

        <p class="text-[11px] text-stone-600 mt-8">
            © 2026 The Wedding of Dimas & Sarah • Powered by Lerd & Laravel
        </p>
    </div>
</footer>

<!-- ========================================== -->
<!-- MODAL: LIGHTBOX PHOTO MODAL                -->
<!-- ========================================== -->
<div id="photo-modal" class="fixed inset-0 z-50 bg-black/90 hidden items-center justify-center p-4">
    <button onclick="closePhotoModal()" class="absolute top-6 right-6 text-white text-3xl hover:text-gold-400 transition">
        <i class="fa-solid fa-xmark"></i>
    </button>
    <img id="photo-modal-img" src="" class="max-w-full max-h-[85vh] rounded-2xl shadow-2xl object-contain">
</div>

<!-- ========================================== -->
<!-- MODAL: GUEST SELF-CHECKIN CAMERA SCANNER   -->
<!-- ========================================== -->
<div id="self-scan-modal" class="fixed inset-0 z-50 bg-stone-950/90 hidden items-center justify-center p-4 backdrop-blur-md">
    <div class="bg-stone-900 border border-gold-500/40 rounded-3xl p-6 max-w-md w-full text-center relative shadow-2xl">
        <button onclick="closeSelfScanModal()" class="absolute top-4 right-4 text-stone-400 hover:text-white text-xl">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-gold-500/20 text-gold-400 flex items-center justify-center text-xl">
            <i class="fa-solid fa-qrcode"></i>
        </div>
        <h3 class="font-serif text-xl font-bold text-white mb-1">Scan QR Code Acara</h3>
        <p class="text-xs text-stone-400 mb-4">
            Arahkan kamera ke QR Code yang terpajang di Meja Resepsi untuk Check-in otomatis.
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
    // 1. OPEN INVITATION & MUSIC PLAY
    const welcomeCover = document.getElementById('welcome-cover');
    const btnOpenInvitation = document.getElementById('btn-open-invitation');
    const audio = document.getElementById('wedding-audio');
    const audioBtn = document.getElementById('audio-control-btn');
    const audioIcon = document.getElementById('audio-icon');
    let isPlaying = false;

    // Web Audio Synthesizer fallback if external audio fails
    let audioCtx = null;
    let synthInterval = null;

    function playSynthesizedMelody() {
        if (!window.AudioContext && !window.webkitAudioContext) return;
        try {
            audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            const notes = [261.63, 329.63, 392.00, 523.25, 440.00, 392.00, 349.23, 329.63]; // C-E-G-C-A-G-F-E
            let step = 0;
            synthInterval = setInterval(() => {
                if (!isPlaying || !audioCtx) return;
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.type = 'sine';
                osc.frequency.value = notes[step % notes.length];
                gain.gain.setValueAtTime(0.04, audioCtx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.0001, audioCtx.currentTime + 1.2);
                osc.connect(gain);
                gain.connect(audioCtx.destination);
                osc.start();
                osc.stop(audioCtx.currentTime + 1.2);
                step++;
            }, 600);
        } catch (e) {
            console.log("Synthesizer error", e);
        }
    }

    function toggleMusic() {
        if (isPlaying) {
            audio.pause();
            if (synthInterval) clearInterval(synthInterval);
            audioIcon.classList.remove('fa-spin', 'text-gold-500');
            audioIcon.classList.add('text-stone-400');
            isPlaying = false;
        } else {
            audio.play().then(() => {
                isPlaying = true;
                audioIcon.classList.add('fa-spin', 'text-gold-500');
                audioIcon.classList.remove('text-stone-400');
            }).catch(err => {
                // Autoplay blocked or external audio unreachable, use Web Audio API
                isPlaying = true;
                playSynthesizedMelody();
                audioIcon.classList.add('fa-spin', 'text-gold-500');
            });
        }
    }

    btnOpenInvitation.addEventListener('click', () => {
        welcomeCover.classList.remove('cover-closed');
        welcomeCover.classList.add('cover-opened');
        toggleMusic();

        // Confetti celebration
        confetti({
            particleCount: 50,
            spread: 70,
            origin: { y: 0.6 }
        });
    });

    audioBtn.addEventListener('click', toggleMusic);

    // 2. COUNTDOWN TIMER
    const targetDate = new Date("{{ $wedding['event']['date_iso'] }}").getTime();
    function updateCountdown() {
        const now = new Date().getTime();
        const diff = targetDate - now;

        if (diff > 0) {
            const days = Math.floor(diff / (1000 * 60 * 60 * 24));
            const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((diff % (1000 * 60)) / 1000);

            document.getElementById('countdown-days').innerText = String(days).padStart(2, '0');
            document.getElementById('countdown-hours').innerText = String(hours).padStart(2, '0');
            document.getElementById('countdown-minutes').innerText = String(minutes).padStart(2, '0');
            document.getElementById('countdown-seconds').innerText = String(seconds).padStart(2, '0');
        }
    }
    setInterval(updateCountdown, 1000);
    updateCountdown();

    // 3. GENERATE QR CODE FOR GUEST TICKET
    @if(isset($guest))
        const guestCode = "{{ $guest->ticket_code }}";
        const qrContainer = document.getElementById('guest-ticket-qr');
        if (qrContainer) {
            new QRCode(qrContainer, {
                text: guestCode,
                width: 140,
                height: 140,
                colorDark : "#1c1917",
                colorLight : "#ffffff",
                correctLevel : QRCode.CorrectLevel.H
            });
        }
    @endif

    // 4. COPY TO CLIPBOARD
    function copyToClipboard(text, label) {
        navigator.clipboard.writeText(text).then(() => {
            alert(label + ' berhasil disalin ke clipboard!');
        }).catch(err => {
            prompt('Salin teks berikut:', text);
        });
    }

    // 5. PHOTO LIGHTBOX MODAL
    const photoModal = document.getElementById('photo-modal');
    const photoModalImg = document.getElementById('photo-modal-img');
    function openPhotoModal(src) {
        photoModalImg.src = src;
        photoModal.classList.remove('hidden');
        photoModal.classList.add('flex');
    }
    function closePhotoModal() {
        photoModal.classList.add('hidden');
        photoModal.classList.remove('flex');
    }

    // 6. RSVP FORM SUBMISSION VIA AJAX
    const rsvpForm = document.getElementById('rsvp-form');
    if (rsvpForm) {
        rsvpForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('btn-submit-rsvp');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';

            const formData = new FormData(rsvpForm);

            fetch("{{ route('wedding.rsvp') }}", {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Konfirmasi & Doa Terkirim';

                if (data.success) {
                    alert(data.message);
                    confetti({ particleCount: 60, spread: 60 });

                    // Append new wish to list
                    const container = document.getElementById('wishes-container');
                    const newWishHtml = `
                        <div class="p-4 rounded-2xl bg-white/90 border border-gold-300 shadow-sm animate-fade-in">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-semibold text-xs text-stone-800">${data.wish.name}</span>
                                <span class="text-[10px] text-stone-400">${data.wish.time_ago}</span>
                            </div>
                            <div class="mb-2">
                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 font-semibold"><i class="fa-solid fa-check mr-1"></i>Hadir</span>
                            </div>
                            <p class="text-xs text-stone-600 leading-relaxed">${data.wish.message}</p>
                        </div>
                    `;
                    container.insertAdjacentHTML('afterbegin', newWishHtml);
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Coba Lagi';
                alert('Terjadi kesalahan saat mengirim formulir.');
            });
        });
    }

    // 7. GUEST SELF CHECK-IN SCANNER
    let html5QrScanner = null;
    const selfScanModal = document.getElementById('self-scan-modal');
    const selfScanResult = document.getElementById('self-scan-result');

    function openSelfScanModal() {
        @if(!isset($guest))
            alert("Harap masukkan kode tiket Anda terlebih dahulu untuk check-in.");
            return;
        @endif

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
                ticket_code: "{{ $guest->ticket_code ?? '' }}",
                event_code: decodedText
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                selfScanResult.className = "p-3 rounded-xl text-xs mb-3 font-semibold bg-emerald-900 text-emerald-200 border border-emerald-500";
                selfScanResult.innerHTML = '<i class="fa-solid fa-circle-check text-emerald-400 mr-1"></i> ' + data.message;
                confetti({ particleCount: 80, spread: 80 });
                setTimeout(() => {
                    location.reload();
                }, 2500);
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
        // Quiet scanning
    }
</script>
@endpush

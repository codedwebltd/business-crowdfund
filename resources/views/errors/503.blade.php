<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Under Maintenance - {{ config('app.name', 'CrowdPower') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes blob {
            0%, 100% { transform: translate(0, 0) scale(1); }
            25% { transform: translate(20px, -50px) scale(1.1); }
            50% { transform: translate(-20px, 20px) scale(0.9); }
            75% { transform: translate(50px, 50px) scale(1.05); }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        @keyframes pulse-slow {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        @keyframes rotate-slow {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .animate-blob { animation: blob 7s infinite; }
        .animation-delay-2000 { animation-delay: 2s; }
        .animation-delay-4000 { animation-delay: 4s; }
        .animate-float { animation: float 3s ease-in-out infinite; }
        .animate-pulse-slow { animation: pulse-slow 2s ease-in-out infinite; }
        .animate-rotate-slow { animation: rotate-slow 20s linear infinite; }
        .bg-grid-pattern {
            background-image: radial-gradient(circle at 1px 1px, rgba(255, 255, 255, 0.05) 1px, transparent 0);
            background-size: 40px 40px;
        }
        .overflow-wrap-anywhere {
            overflow-wrap: anywhere;
            word-break: break-word;
            hyphens: auto;
        }
    </style>
</head>
<body>
    @php
        $settings = \App\Models\GlobalSetting::first();
        $maintenanceEndAt = $settings->maintenance_end_at ?? null;
        $supportEmail = $settings->support_email ?? 'support@' . parse_url(config('app.url'), PHP_URL_HOST);
        $supportPhone = $settings->support_phone ?? null;
        $supportWhatsapp = $settings->support_whatsapp ?? null;
        $appName = $settings->app_name ?? config('app.name', 'CrowdPower');
    @endphp

    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 relative overflow-hidden">
        <!-- Animated Background Elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-purple-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-orange-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-pink-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>
            <div class="absolute inset-0 bg-grid-pattern opacity-10"></div>
        </div>

        <!-- Content -->
        <div class="relative z-10 min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl w-full overflow-hidden">
                <!-- Logo/Header -->
                <div class="text-center mb-8 sm:mb-12 px-2">
                    <h1 class="text-2xl xs:text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-extrabold mb-4 break-words overflow-wrap-anywhere sm:animate-float">
                        <span class="bg-gradient-to-r from-orange-400 via-purple-500 to-pink-500 bg-clip-text text-transparent inline-block max-w-full">
                            {{ $appName }}
                        </span>
                    </h1>
                </div>

                <!-- Main Card -->
                <div class="bg-white/10 backdrop-blur-lg rounded-2xl sm:rounded-3xl shadow-2xl border border-white/20 overflow-hidden">
                    <div class="p-4 sm:p-8 md:p-10 lg:p-16">
                        <!-- Construction SVG Icon -->
                        <div class="flex justify-center mb-8">
                            <svg class="w-32 h-32 sm:w-40 sm:h-40 text-orange-400 animate-pulse-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>

                        <!-- Title -->
                        <h2 class="text-xl sm:text-2xl md:text-3xl lg:text-4xl xl:text-5xl font-bold text-center text-white mb-4 px-2 break-words">
                            We're Under Maintenance
                        </h2>

                        <!-- Description -->
                        <p class="text-sm sm:text-base md:text-lg lg:text-xl text-center text-gray-300 mb-8 max-w-2xl mx-auto leading-relaxed px-4">
                            Our platform is currently undergoing scheduled maintenance to bring you exciting new features and improvements. We'll be back online shortly!
                        </p>

                        @if($maintenanceEndAt)
                        <!-- Countdown Timer -->
                        <div class="mb-8 sm:mb-12">
                            <h3 class="text-center text-gray-300 text-xs sm:text-sm font-semibold mb-3 sm:mb-4 uppercase tracking-wider px-2">We'll Be Back In</h3>
                            <div id="countdown" class="grid grid-cols-4 gap-2 sm:gap-3 md:gap-4 max-w-xl mx-auto px-2">
                                <div class="bg-white/10 backdrop-blur-sm rounded-lg sm:rounded-xl p-2 sm:p-3 md:p-4 border border-white/20 transform hover:scale-105 transition-transform">
                                    <div id="days" class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-bold text-white">--</div>
                                    <div class="text-[10px] sm:text-xs md:text-sm text-gray-300 mt-1">Days</div>
                                </div>
                                <div class="bg-white/10 backdrop-blur-sm rounded-lg sm:rounded-xl p-2 sm:p-3 md:p-4 border border-white/20 transform hover:scale-105 transition-transform">
                                    <div id="hours" class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-bold text-white">--</div>
                                    <div class="text-[10px] sm:text-xs md:text-sm text-gray-300 mt-1">Hours</div>
                                </div>
                                <div class="bg-white/10 backdrop-blur-sm rounded-lg sm:rounded-xl p-2 sm:p-3 md:p-4 border border-white/20 transform hover:scale-105 transition-transform">
                                    <div id="minutes" class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-bold text-white">--</div>
                                    <div class="text-[10px] sm:text-xs md:text-sm text-gray-300 mt-1">Mins</div>
                                </div>
                                <div class="bg-white/10 backdrop-blur-sm rounded-lg sm:rounded-xl p-2 sm:p-3 md:p-4 border border-white/20 transform hover:scale-105 transition-transform">
                                    <div id="seconds" class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-bold text-white">--</div>
                                    <div class="text-[10px] sm:text-xs md:text-sm text-gray-300 mt-1">Secs</div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Support Information -->
                        <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-6 border border-white/10">
                            <h3 class="text-xl font-bold text-white mb-4 text-center">Need Immediate Assistance?</h3>
                            <div class="space-y-3">
                                @if($supportEmail)
                                <a href="mailto:{{ $supportEmail }}" class="flex items-center justify-center gap-3 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-xl p-4 border border-white/20 transition-all group">
                                    <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="text-white font-medium group-hover:text-orange-400 transition-colors">{{ $supportEmail }}</span>
                                </a>
                                @endif

                                @if($supportPhone)
                                <a href="tel:{{ $supportPhone }}" class="flex items-center justify-center gap-3 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-xl p-4 border border-white/20 transition-all group">
                                    <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                    <span class="text-white font-medium group-hover:text-purple-400 transition-colors">{{ $supportPhone }}</span>
                                </a>
                                @endif

                                @if($supportWhatsapp)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $supportWhatsapp) }}" target="_blank" class="flex items-center justify-center gap-3 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-xl p-4 border border-white/20 transition-all group">
                                    <svg class="w-6 h-6 text-green-400" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                    </svg>
                                    <span class="text-white font-medium group-hover:text-green-400 transition-colors">WhatsApp Support</span>
                                </a>
                                @endif
                            </div>
                        </div>

                        <!-- Status Message -->
                        <div class="mt-8 text-center">
                            <p class="text-sm text-gray-400">
                                Thank you for your patience. We appreciate your understanding.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="text-center mt-8 text-gray-400 text-sm">
                    <p>&copy; {{ date('Y') }} {{ $appName }}. All rights reserved.</p>
                </div>
            </div>
        </div>
    </div>

    @if($maintenanceEndAt)
    <script>
        // Countdown Timer
        const countdownDate = new Date("{{ $maintenanceEndAt }}").getTime();

        const countdownFunction = setInterval(function() {
            const now = new Date().getTime();
            const distance = countdownDate - now;

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            document.getElementById("days").innerText = String(days).padStart(2, '0');
            document.getElementById("hours").innerText = String(hours).padStart(2, '0');
            document.getElementById("minutes").innerText = String(minutes).padStart(2, '0');
            document.getElementById("seconds").innerText = String(seconds).padStart(2, '0');

            if (distance < 0) {
                clearInterval(countdownFunction);
                document.getElementById("countdown").innerHTML = '<div class="text-center text-2xl text-white font-bold">Maintenance Complete! Refreshing...</div>';
                setTimeout(() => location.reload(), 2000);
            }
        }, 1000);
    </script>
    @endif
</body>
</html>

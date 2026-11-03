<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dalam Pemeliharaan - {{ function_exists('system_setting') ? system_setting('app_name', 'Linkpemda') : config('app.name', 'Linkpemda') }}</title>

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --brand-green: #008F83;
            --brand-green-light: #2ad5c4;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #011412;
            color: #f1f5f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            position: relative;
            overflow: hidden;
        }

        /* Cahaya Spotlight Mengikuti Kursor Mouse */
        .cursor-glow {
            position: fixed;
            width: 650px;
            height: 650px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(0, 143, 131, 0.32) 0%, rgba(42, 213, 196, 0.08) 35%, transparent 70%);
            pointer-events: none;
            z-index: 0;
            top: 0;
            left: 0;
            transform: translate(-50%, -50%);
            filter: blur(60px);
            will-change: left, top;
        }

        /* Pattern kotak-kotak (Grid Background) */
        .grid-pattern {
            position: absolute;
            inset: 0;
            background-image: 
                linear-gradient(to right, rgba(255, 255, 255, 0.045) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.045) 1px, transparent 1px);
            background-size: 34px 34px;
            pointer-events: none;
            z-index: 0;
        }

        /* Konten Terpusat (Tanpa Card) */
        .maintenance-wrap {
            position: relative;
            z-index: 1;
            max-width: 480px;
            width: 100%;
            text-align: center;
            padding: 20px;
        }

        /* Ikon Maintenance */
        .icon-wrap {
            width: 80px;
            height: 80px;
            margin: 0 auto 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 143, 131, 0.16);
            border: 1px solid rgba(0, 143, 131, 0.4);
            border-radius: 24px;
            box-shadow: 0 0 30px rgba(0, 143, 131, 0.3);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }

        .icon-wrap svg {
            width: 42px;
            height: 42px;
            color: var(--brand-green-light);
            animation: rotateGear 12s linear infinite;
        }

        @keyframes rotateGear {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Status Badge */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 5px 14px;
            border-radius: 9999px;
            background: rgba(0, 143, 131, 0.16);
            border: 1px solid rgba(0, 143, 131, 0.35);
            color: var(--brand-green-light);
            font-size: 12.5px;
            font-weight: 600;
            letter-spacing: 0.3px;
            margin-bottom: 20px;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }

        .dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background-color: var(--brand-green-light);
            box-shadow: 0 0 8px var(--brand-green-light);
        }

        /* Typography */
        h1 {
            font-size: 26px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 12px;
            letter-spacing: -0.4px;
        }

        p {
            font-size: 15px;
            line-height: 1.65;
            color: #94a3b8;
            margin-bottom: 32px;
        }

        /* Footer */
        .footer {
            font-size: 12.5px;
            color: rgba(255, 255, 255, 0.5);
        }
    </style>
</head>
<body>

    <!-- Hanya satu cahaya: Spotlight Mengikuti Kursor -->
    <div class="cursor-glow" id="cursorGlow"></div>
    <div class="grid-pattern"></div>

    <!-- Konten Terpusat (Cardless) -->
    <div class="maintenance-wrap">
        
        <!-- Ikon Maintenance -->
        <div class="icon-wrap">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
        </div>

        <!-- Status Badge -->
        <div class="badge">
            <span class="dot"></span>
            <span>Dalam Pemeliharaan</span>
        </div>

        <!-- Judul & Keterangan Singkat -->
        <h1>Sistem Sedang Diperbarui</h1>
        <p>
            Kami sedang melakukan pemeliharaan rutin dan akan segera kembali.
        </p>

        <!-- Footer -->
        <div class="footer">
            &copy; {{ date('Y') }} {{ function_exists('system_setting') ? system_setting('app_name', 'Linkpemda') : config('app.name', 'Linkpemda') }}. All rights reserved.
        </div>

    </div>

    <!-- Script Spotlight Mengikuti Kursor -->
    <script>
        (function() {
            const glow = document.getElementById('cursorGlow');
            let mouseX = window.innerWidth / 2;
            let mouseY = window.innerHeight / 2;
            let currentX = mouseX;
            let currentY = mouseY;

            // Inisialisasi posisi awal kursor
            glow.style.left = currentX + 'px';
            glow.style.top = currentY + 'px';

            window.addEventListener('mousemove', function(e) {
                mouseX = e.clientX;
                mouseY = e.clientY;
            });

            window.addEventListener('touchmove', function(e) {
                if (e.touches.length > 0) {
                    mouseX = e.touches[0].clientX;
                    mouseY = e.touches[0].clientY;
                }
            }, { passive: true });

            function animate() {
                // Smooth interpolation (lerp)
                currentX += (mouseX - currentX) * 0.12;
                currentY += (mouseY - currentY) * 0.12;
                glow.style.left = currentX + 'px';
                glow.style.top = currentY + 'px';
                requestAnimationFrame(animate);
            }

            animate();
        })();
    </script>

</body>
</html>

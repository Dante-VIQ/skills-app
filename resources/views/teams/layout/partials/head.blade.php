    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    
    <title>@yield('title', 'Dashboard')</title>
    
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        :root.dark {
            color-scheme: dark;
        }
    </style>
    
    <script>
        // Dark mode handler
        (function() {
            function applyAppearance(appearance) {
                const applyDark = () => document.documentElement.classList.add('dark');
                const applyLight = () => document.documentElement.classList.remove('dark');

                if (appearance === 'system') {
                    const media = window.matchMedia('(prefers-color-scheme: dark)');
                    localStorage.removeItem('appearance');
                    media.matches ? applyDark() : applyLight();
                } else if (appearance === 'dark') {
                    localStorage.setItem('appearance', 'dark');
                    applyDark();
                } else if (appearance === 'light') {
                    localStorage.setItem('appearance', 'light');
                    applyLight();
                }
            }

            applyAppearance(localStorage.getItem('appearance') || 'system');
        })();
    </script>


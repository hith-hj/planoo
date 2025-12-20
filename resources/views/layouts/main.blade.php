<!DOCTYPE html>
<html lang="en" dir="{{app()->getLocale() === 'ar'? 'rtl':'ltr'}}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Better | Leisure Centres, Gyms, Pools & Libraries</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        teal: '#00A89C',
                        tealhover: '#04cec8',
                        purple: '#462e8e',
                        yellow: '#FFC845',
                        dark: '#333333',
                        lightgray: '#F4F4F4',
                        border: '#E5E5E5',
                        gold: '#FFC845',
                    },
                    fontFamily: {
                        sans: ['Verdana', 'Arial', 'sans-serif'],
                    },
                    maxWidth: {
                        'container': '1180px'
                    }
                }
            }
        }
    </script>
    <style>
        /* Specific overrides to match the exact "Better" feel */
        .hero-bg {
            background-image: url('{{asset('images/hero-bg.avif')}}');
            background-size: cover;
            background-position: center;
        }

        .tab-active {
            background-color: white;
            color: #4C2263;
            border-top: 4px solid #00A099;
        }

        .tab-inactive {
            background-color: rgba(255,255,255,0.9);
            color: #666;
            border-top: 4px solid transparent;
        }
        .tab-inactive:hover {
            background-color: #fff;
        }
        /* Hide scrollbar for Chrome, Safari, and Opera */
        .scrollbar-hide::-webkit-scrollbar {
          display: none;
        }

        /* Hide scrollbar for IE, Edge, and Firefox */
        .scrollbar-hide {
          -ms-overflow-style: none;  /* IE and Edge */
          scrollbar-width: none;     /* Firefox */
        }
    </style>
</head>
<body class="font-sans text-dark antialiased bg-white rtl:text-2xl">
    <!-- MAIN HEADER -->
    @include('partials.nav')

    <!-- MAIN CONTENT -->
    <main>
        @yield('content')
    </main>

    <section id="contact" class="">
        @include('partials.footer')
    </section>

    {{-- script for responsive menu --}}
    <script>
        const menuBtn = document.getElementById('mobile-menu-btn');
        const closeBtn = document.getElementById('close-menu-btn');
        const menu = document.getElementById('mobile-menu');
        const overlay = document.getElementById('mobile-menu-overlay');

        function toggleMenu() {
            const isClosed = menu.classList.contains('translate-x-full');
            if (isClosed) {
                menu.classList.remove('translate-x-full');
                overlay.classList.remove('hidden');
            } else {
                menu.classList.add('translate-x-full');
                overlay.classList.add('hidden');
            }
        }

        menuBtn.addEventListener('click', toggleMenu);
        closeBtn.addEventListener('click', toggleMenu);
        overlay.addEventListener('click', toggleMenu);
    </script>

    {{-- script for hero search section --}}
    <script>
      function showTab(tab) {
        // Hide both contents
        document.getElementById('content-court').classList.add('hidden');
        document.getElementById('content-activity').classList.add('hidden');

        // Remove active styles
        document.getElementById('tab-court').classList.remove('tab-active','border-teal');
        document.getElementById('tab-activity').classList.remove('tab-active','border-teal');

        // Add inactive styles
        document.getElementById('tab-court').classList.add('tab-inactive');
        document.getElementById('tab-activity').classList.add('tab-inactive');

        // Show selected content + activate tab
        if(tab === 'court') {
          document.getElementById('content-court').classList.remove('hidden');
          document.getElementById('tab-court').classList.add('tab-active','border-teal');
          document.getElementById('tab-court').classList.remove('tab-inactive');
        } else {
          document.getElementById('content-activity').classList.remove('hidden');
          document.getElementById('tab-activity').classList.add('tab-active','border-teal');
          document.getElementById('tab-activity').classList.remove('tab-inactive');
        }
      }
    </script>

    {{-- script for nav taps  --}}
    <script>
      const observer = new IntersectionObserver(
        entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    document.querySelectorAll('a[data-section]').forEach(link =>link.classList.remove('text-teal') );
                    const activeLink = document.querySelector(`a[data-section="${entry.target.id}"]`);
                    if (activeLink) {
                        activeLink.classList.add('text-teal');
                    }
                }
            });
        },
        { threshold: 0.5 }
      );
      document.querySelectorAll('section').forEach(section => observer.observe(section));
    </script>

</body>
</html>

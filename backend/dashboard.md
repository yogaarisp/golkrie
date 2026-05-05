<!DOCTYPE html>

<html class="dark" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;600;700;800;900&amp;family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            "colors": {
                    "on-primary-container": "#fff6f5",
                    "tertiary-fixed-dim": "#c9c6c5",
                    "on-surface": "#e3e2e2",
                    "outline-variant": "#5c403c",
                    "secondary-container": "#eec200",
                    "on-secondary": "#3c2f00",
                    "on-primary": "#690005",
                    "primary-container": "#dc2626",
                    "background": "#121314",
                    "secondary": "#ffe083",
                    "surface-container-low": "#1b1c1c",
                    "on-tertiary-container": "#faf7f6",
                    "inverse-on-surface": "#303031",
                    "on-secondary-fixed-variant": "#574500",
                    "inverse-surface": "#e3e2e2",
                    "on-error": "#690005",
                    "surface-dim": "#121314",
                    "on-background": "#e3e2e2",
                    "tertiary-fixed": "#e5e2e1",
                    "on-error-container": "#ffdad6",
                    "error-container": "#93000a",
                    "tertiary-container": "#737171",
                    "on-secondary-container": "#645000",
                    "outline": "#ac8884",
                    "primary-fixed": "#ffdad6",
                    "on-tertiary-fixed": "#1c1b1b",
                    "primary-fixed-dim": "#ffb4ab",
                    "surface-tint": "#ffb4ab",
                    "surface": "#121314",
                    "on-primary-fixed": "#410002",
                    "tertiary": "#c9c6c5",
                    "surface-container": "#1f2020",
                    "error": "#ffb4ab",
                    "on-tertiary": "#313030",
                    "inverse-primary": "#bf0715",
                    "surface-container-high": "#292a2a",
                    "on-secondary-fixed": "#231b00",
                    "on-tertiary-fixed-variant": "#474646",
                    "surface-variant": "#343535",
                    "surface-container-lowest": "#0d0e0f",
                    "secondary-fixed": "#ffe083",
                    "surface-container-highest": "#343535",
                    "on-surface-variant": "#e6bdb8",
                    "secondary-fixed-dim": "#eec200",
                    "surface-bright": "#393939",
                    "on-primary-fixed-variant": "#93000b",
                    "primary": "#ffb4ab"
            },
            "borderRadius": {
                    "DEFAULT": "0.25rem",
                    "lg": "0.5rem",
                    "xl": "0.75rem",
                    "2xl": "1rem",
                    "full": "9999px"
            },
            "spacing": {
                    "container-margin": "32px",
                    "gutter": "24px",
                    "section-gap": "64px",
                    "unit": "8px"
            },
            "fontFamily": {
                    "h2": ["Lexend"],
                    "h3": ["Lexend"],
                    "body-lg": ["Lexend"],
                    "h1": ["Lexend"],
                    "body-md": ["Lexend"],
                    "label-caps": ["Lexend"]
            },
            "fontSize": {
                    "h2": ["32px", {"lineHeight": "1.2", "letterSpacing": "-0.01em", "fontWeight": "700"}],
                    "h3": ["24px", {"lineHeight": "1.3", "fontWeight": "600"}],
                    "body-lg": ["18px", {"lineHeight": "1.6", "fontWeight": "400"}],
                    "h1": ["48px", {"lineHeight": "1.1", "letterSpacing": "-0.02em", "fontWeight": "700"}],
                    "body-md": ["16px", {"lineHeight": "1.5", "fontWeight": "400"}],
                    "label-caps": ["12px", {"lineHeight": "1.0", "letterSpacing": "0.05em", "fontWeight": "700"}]
            }
          },
        },
      }
    </script>
<style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .bento-card {
            background: linear-gradient(145deg, #1f2020 0%, #121314 100%);
        }
        .glass-nav {
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
    </style>
</head>
<body class="bg-background text-on-background font-body-md selection:bg-primary-container selection:text-on-primary-container">
<!-- TopNavBar -->
<header class="fixed top-0 w-full z-50 bg-surface-container-lowest/80 backdrop-blur-md border-b border-outline-variant shadow-[0_0_15px_rgba(220,38,38,0.1)]">
<nav class="flex justify-between items-center h-20 px-8 max-w-7xl mx-auto">
<div class="text-2xl font-black text-primary tracking-tighter uppercase font-['Lexend']">Golkire</div>
<div class="hidden md:flex items-center gap-8">
<a class="text-primary border-b-2 border-primary pb-1 font-['Lexend'] font-bold uppercase tracking-wider" href="#">Home</a>
<a class="text-on-surface-variant hover:text-white transition-colors font-['Lexend'] font-bold uppercase tracking-wider hover:scale-105 transition-all duration-200" href="#">Schedule</a>
<a class="text-on-surface-variant hover:text-white transition-colors font-['Lexend'] font-bold uppercase tracking-wider hover:scale-105 transition-all duration-200" href="#">Leaderboard</a>
<a class="text-on-surface-variant hover:text-white transition-colors font-['Lexend'] font-bold uppercase tracking-wider hover:scale-105 transition-all duration-200" href="#">Gallery</a>
<a class="text-on-surface-variant hover:text-white transition-colors font-['Lexend'] font-bold uppercase tracking-wider hover:scale-105 transition-all duration-200" href="#">Admin Login</a>
</div>
<button class="bg-primary-container text-on-primary-container px-6 py-2 rounded-full font-h3 text-body-md hover:scale-105 hover:shadow-[0_0_10px_#dc2626] transition-all duration-200 active:scale-95">
            Join Next Match
        </button>
</nav>
</header>
<main>
<!-- Hero Section -->
<section class="relative h-[870px] flex items-center justify-center overflow-hidden">
<div class="absolute inset-0 z-0">
<img alt="Cinematic football pitch at night" class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCq5TuGLX0gfygok5O7QwkKTB2K5VA2XnJGRanV9TSL5a0CZl2Y3F2zo79OKYnzQbK2sL33KigXyWn7nK1n6sjPjJp5HLxJBsDQ_qxK3X0oGEk0tczdnuoDgCxSOlNzv2bhD9aheEFr3zKdBlaqtwdFi-snI8I_whSMoP4fYZOIcLd_s5ViFplESpBCylFvof27qk2qDMI31rV813YnV-E4NmBrd_cQNOFfZvP9YgGWyb9w1PiYOhI69uBIsw4BZRk0M4H2yvZKWj8"/>
<div class="absolute inset-0 bg-gradient-to-b from-background/90 via-background/60 to-background"></div>
</div>
<div class="relative z-10 text-center px-6 max-w-4xl mx-auto">
<h1 class="font-h1 text-h1 mb-6 text-white leading-none">Golek Kringet,<br/><span class="text-primary">Jalin Seduluran.</span></h1>
<p class="font-body-lg text-body-lg text-on-surface-variant mb-10 max-w-2xl mx-auto">
                Push your limits twice a week with the elite Golkire community. Professional matches, casual vibes, and lifelong brotherhood on the pitch.
            </p>
<div class="flex flex-wrap justify-center gap-4">
<div class="flex items-center gap-2 bg-surface-container-high px-4 py-2 rounded-xl border border-outline-variant">
<span class="material-symbols-outlined text-primary">calendar_month</span>
<span class="font-label-caps text-label-caps">Tuesdays &amp; Saturdays</span>
</div>
<div class="flex items-center gap-2 bg-surface-container-high px-4 py-2 rounded-xl border border-outline-variant">
<span class="material-symbols-outlined text-secondary">schedule</span>
<span class="font-label-caps text-label-caps">19:00 - 21:00 WIB</span>
</div>
</div>
</div>
</section>
<!-- Upcoming Matches -->
<section class="px-8 max-w-7xl mx-auto -mt-32 relative z-20 mb-section-gap">
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
<!-- Card 1: Futsal -->
<div class="bento-card p-6 rounded-2xl border border-outline-variant hover:border-primary/50 transition-all duration-300 group shadow-2xl">
<div class="flex justify-between items-start mb-6">
<span class="bg-primary/10 text-primary px-3 py-1 rounded-full text-label-caps border border-primary/20">Futsal Arena</span>
<span class="text-on-surface-variant text-body-md font-bold">Slot 12/14</span>
</div>
<h3 class="font-h3 text-h3 mb-4 text-white">Indoor Technical Session</h3>
<div class="space-y-3 mb-6">
<div class="flex items-center gap-3 text-on-surface-variant">
<span class="material-symbols-outlined text-primary text-xl">event</span>
<span class="text-body-md">Tuesday, 24 Oct</span>
</div>
<div class="flex items-center gap-3 text-on-surface-variant">
<span class="material-symbols-outlined text-primary text-xl">location_on</span>
<span class="text-body-md">Grand Futsal Hub</span>
</div>
</div>
<div class="w-full bg-surface-container-highest h-2 rounded-full mb-6 overflow-hidden">
<div class="bg-primary-container h-full rounded-full w-[85%] shadow-[0_0_10px_#dc2626]"></div>
</div>
<div class="flex items-center justify-between">
<div class="flex -space-x-3">
<img alt="Athlete portrait" class="w-10 h-10 rounded-full border-2 border-background object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBY4Lvhh4uXYr30xfS-G6M6HsSEWdibL-RYEzTUtzfcrr_qK7IGo-42t__9TELo0ophD9o6_EmQOVaHduS351FmOWPvhrUDZO79MVc4iy2hdTEE8qt_Yfs9z8oc4s5G7Fsk1pf8WWp0pWsksyWXfjLsSWhlTAg0_B-HoZUblKzn-6B6xNBVjubybSdaNZNKOuO4cXhRk379oqTqaxZxrgAEo3kSmkePdNjsArYYeQSzJl38Y97QQwKWcgeNqjgeyYD3-k2UfzmAOXw"/>
<img alt="Athlete portrait" class="w-10 h-10 rounded-full border-2 border-background object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDyFyBtlTa6l3aZBLhdTBU1ZhzSRWtwCHZR9iHWuQsCrjbEgA-nXspjb7Xnl17S8Rq3AzF7Mm1YQkZejLUHdoPHpENhNEQAWA0aw-vkJOFo_RX6sUu36WvWo7JfX8atkkjtaGwwoGlzOZ3xQ7VqJwyi1nXhlAHuNTj-ctuwwDTKVts7JpoLzLfU_SBDhbYoMOfwcRym31ydIE7GnDD1hr0th6nxCzfalDb2qUqKkWOmzkEX6dHRrIr22s0Y4_Yn5vFo58lqGoLfUrg"/>
<img alt="Athlete portrait" class="w-10 h-10 rounded-full border-2 border-background object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCuL4fedRU0gMfK0zIqpOa0SENG9_mjLWKV7dZjUbxEwn8ZEjOFhjZ0X7TtDnxEN1lXHxg97tXuD8L3mdRnWyw3H3iffIL6_oPrqwkg_AbgaHEUQrTmmf6ObeUNspsK8QSvlA8aa8RbumPM3mrteWZFQolHDp0BYxJMjocxcsVN3n6SrhSez0ysev7wPfRpcywhWGkZ9HgEszEJrwGhY8hiY5mI_6Kp-Bt2Q4DCOcAlGZzIOU0TG4Zd6TYHt687G4kpxL_1dewnfIc"/>
<div class="w-10 h-10 rounded-full border-2 border-background bg-surface-container-high flex items-center justify-center text-[10px] font-bold text-primary">+9</div>
</div>
<button class="bg-primary-container text-on-primary-container px-5 py-2 rounded-xl font-bold hover:scale-105 active:scale-95 transition-all">Join Match</button>
</div>
</div>
<!-- Card 2: Mini Soccer -->
<div class="bento-card p-6 rounded-2xl border border-outline-variant hover:border-secondary/50 transition-all duration-300 group shadow-2xl scale-105 bg-surface-container-low ring-2 ring-secondary/20">
<div class="flex justify-between items-start mb-6">
<span class="bg-secondary/10 text-secondary px-3 py-1 rounded-full text-label-caps border border-secondary/20">Mini Soccer</span>
<span class="text-on-surface-variant text-body-md font-bold">Slot 14/14</span>
</div>
<h3 class="font-h3 text-h3 mb-4 text-white">Friday Night Lights</h3>
<div class="space-y-3 mb-6">
<div class="flex items-center gap-3 text-on-surface-variant">
<span class="material-symbols-outlined text-primary text-xl">event</span>
<span class="text-body-md">Friday, 27 Oct</span>
</div>
<div class="flex items-center gap-3 text-on-surface-variant">
<span class="material-symbols-outlined text-primary text-xl">location_on</span>
<span class="text-body-md">South Park Stadium</span>
</div>
</div>
<div class="w-full bg-surface-container-highest h-2 rounded-full mb-6 overflow-hidden">
<div class="bg-secondary-container h-full rounded-full w-full shadow-[0_0_10px_#eec200]"></div>
</div>
<div class="flex items-center justify-between">
<div class="flex -space-x-3">
<img alt="Athlete portrait" class="w-10 h-10 rounded-full border-2 border-background object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCvOxohPYY-u7dwvc2XGoEA8t-L9UaksmjeMfLxVlaE15kx0sP2KtCZ38s4036G7qyjpYi9m0ULOZou00BOPdgTVObjHMqh2aLdjJUEf0n2TIHNSZnqr7ZLk7zDdqP-bALU4oJZMgX4WarJsp29rgohxqgktVjRKjqJ9kYWS5JuF9iC9kuFeYTuHHwkKz18eLcUieMFTPv_rPrjuwteOFyJHJSmBn4LN7GbUDk5F_mu0n2Puipai8f1ETO_MY2GykhdhG2wprH0hMw"/>
<img alt="Athlete portrait" class="w-10 h-10 rounded-full border-2 border-background object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCoBTi8K2WFWWwey7k8V2c7nHmW1mxOrGJv2Qe3to7Ooo4-H0NBpQQlzwFyekcrvH_F961AB7KfTQKabMJjI3Ru7Qoz9EYHGy_9nGS_1TiBCzNMxO99lGm3-vfKdULO-oTk9bO2m7xdCAqooKBeqfzXtcIRqdJXSL0JgKIe0iL4CFQpkqQ8SPUvXRRx-nHsvG1D42vF_DgebUoMuf5ZTR5xhPu0Qc6WjGyimqnXWi-5XBSjV0KJlfihMV1rx9Yr005-EXrMdZc0868"/>
<div class="w-10 h-10 rounded-full border-2 border-background bg-surface-container-high flex items-center justify-center text-[10px] font-bold text-secondary">FULL</div>
</div>
<button class="bg-surface-container-highest text-on-surface-variant px-5 py-2 rounded-xl font-bold cursor-not-allowed">Waitlist</button>
</div>
</div>
<!-- Card 3: Big Pitch -->
<div class="bento-card p-6 rounded-2xl border border-outline-variant hover:border-primary/50 transition-all duration-300 group shadow-2xl">
<div class="flex justify-between items-start mb-6">
<span class="bg-primary/10 text-primary px-3 py-1 rounded-full text-label-caps border border-primary/20">11 vs 11 Full Pitch</span>
<span class="text-on-surface-variant text-body-md font-bold">Slot 18/22</span>
</div>
<h3 class="font-h3 text-h3 mb-4 text-white">Elite Sunday League</h3>
<div class="space-y-3 mb-6">
<div class="flex items-center gap-3 text-on-surface-variant">
<span class="material-symbols-outlined text-primary text-xl">event</span>
<span class="text-body-md">Sunday, 29 Oct</span>
</div>
<div class="flex items-center gap-3 text-on-surface-variant">
<span class="material-symbols-outlined text-primary text-xl">location_on</span>
<span class="text-body-md">National Arena</span>
</div>
</div>
<div class="w-full bg-surface-container-highest h-2 rounded-full mb-6 overflow-hidden">
<div class="bg-primary-container h-full rounded-full w-[82%] shadow-[0_0_10px_#dc2626]"></div>
</div>
<div class="flex items-center justify-between">
<div class="flex -space-x-3">
<img alt="Athlete portrait" class="w-10 h-10 rounded-full border-2 border-background object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuB8O-nBt3f7Nekxv_CaKmneiWQAgB0-LvvqgDK8BY36wxRlu4RliVSmM6MbY_8C_qqxmg2bHr9wMyPYNPVQ7uSRFu8ILLBshKJQTPYGoVijjyiENl7rrKzGcFO7ovQZv9kTym8lmo8PsW8ubv7LehemZfG0f4QanQBPbif9_4d5NAzU-qnii1FmF7KlffUVtcFHxJDJIS5Te7Kueu90bGkN-aeC-AE5okTiEQD39hKghwy58GFMwr1L6rVym4IVYaFlkug2LkDsJ04"/>
<div class="w-10 h-10 rounded-full border-2 border-background bg-surface-container-high flex items-center justify-center text-[10px] font-bold text-primary">+17</div>
</div>
<button class="bg-primary-container text-on-primary-container px-5 py-2 rounded-xl font-bold hover:scale-105 active:scale-95 transition-all">Join Match</button>
</div>
</div>
</div>
</section>
<!-- Member List Table -->
<section class="px-8 max-w-7xl mx-auto mb-section-gap">
<div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
<div>
<h2 class="font-h2 text-h2 text-white mb-2">Squad List</h2>
<p class="text-on-surface-variant">Match: Friday Night Lights @ South Park Stadium</p>
</div>
<div class="bg-surface-container-high p-1 rounded-xl flex">
<button class="px-4 py-2 bg-primary-container text-on-primary-container rounded-lg font-bold text-body-md">Confirmed (14)</button>
<button class="px-4 py-2 text-on-surface-variant hover:text-white transition-colors text-body-md">Waiting List (4)</button>
</div>
</div>
<div class="bg-surface-container overflow-hidden rounded-2xl border border-outline-variant shadow-lg">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-surface-container-high border-b border-outline-variant">
<th class="px-6 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase">No</th>
<th class="px-6 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase">Player Name</th>
<th class="px-6 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase">Position</th>
<th class="px-6 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase text-right">Status</th>
</tr>
</thead>
<tbody class="divide-y divide-outline-variant">
<tr class="hover:bg-surface-container-highest/50 transition-colors">
<td class="px-6 py-4 text-on-surface-variant font-bold">01</td>
<td class="px-6 py-4">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-full bg-primary-container/20 flex items-center justify-center text-primary font-bold text-xs">RK</div>
<span class="text-white font-semibold">Rangga Kotto</span>
</div>
</td>
<td class="px-6 py-4 text-on-surface-variant">Forward</td>
<td class="px-6 py-4 text-right">
<span class="bg-primary-container/20 text-primary-container px-3 py-1 rounded-full text-xs font-bold border border-primary-container/30">PAID</span>
</td>
</tr>
<tr class="hover:bg-surface-container-highest/50 transition-colors">
<td class="px-6 py-4 text-on-surface-variant font-bold">02</td>
<td class="px-6 py-4">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-full bg-secondary-container/20 flex items-center justify-center text-secondary font-bold text-xs">AM</div>
<span class="text-white font-semibold">Adit Mahardika</span>
</div>
</td>
<td class="px-6 py-4 text-on-surface-variant">Midfielder</td>
<td class="px-6 py-4 text-right">
<span class="bg-primary-container/20 text-primary-container px-3 py-1 rounded-full text-xs font-bold border border-primary-container/30">PAID</span>
</td>
</tr>
<tr class="hover:bg-surface-container-highest/50 transition-colors">
<td class="px-6 py-4 text-on-surface-variant font-bold">03</td>
<td class="px-6 py-4">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-full bg-surface-variant flex items-center justify-center text-on-surface-variant font-bold text-xs">DW</div>
<span class="text-white font-semibold">Diki Wahyudi</span>
</div>
</td>
<td class="px-6 py-4 text-on-surface-variant">Goalkeeper</td>
<td class="px-6 py-4 text-right">
<span class="bg-secondary-container/20 text-secondary px-3 py-1 rounded-full text-xs font-bold border border-secondary-container/30">PENDING</span>
</td>
</tr>
<tr class="hover:bg-surface-container-highest/50 transition-colors">
<td class="px-6 py-4 text-on-surface-variant font-bold">04</td>
<td class="px-6 py-4">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-full bg-primary-container/20 flex items-center justify-center text-primary font-bold text-xs">BP</div>
<span class="text-white font-semibold">Bagus Prasetyo</span>
</div>
</td>
<td class="px-6 py-4 text-on-surface-variant">Defender</td>
<td class="px-6 py-4 text-right">
<span class="bg-primary-container/20 text-primary-container px-3 py-1 rounded-full text-xs font-bold border border-primary-container/30">PAID</span>
</td>
</tr>
</tbody>
</table>
</div>
<!-- Waiting List Sub-section -->
<div class="mt-8">
<h3 class="font-h3 text-h3 text-white mb-4">Waiting List</h3>
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
<div class="bg-surface-container-low border border-outline-variant p-4 rounded-xl flex items-center justify-between">
<span class="text-on-surface font-semibold">1. Gilang Ramadhan</span>
<span class="text-[10px] text-on-surface-variant font-bold">Pos: MID</span>
</div>
<div class="bg-surface-container-low border border-outline-variant p-4 rounded-xl flex items-center justify-between">
<span class="text-on-surface font-semibold">2. Fajar Utama</span>
<span class="text-[10px] text-on-surface-variant font-bold">Pos: FW</span>
</div>
</div>
</div>
</section>
<!-- Admin Quick Actions -->
<section class="px-8 max-w-7xl mx-auto mb-section-gap">
<div class="bg-surface-container-high rounded-3xl p-8 border border-primary-container/20 shadow-[0_0_50px_rgba(220,38,38,0.05)]">
<div class="flex flex-col md:flex-row items-center justify-between gap-8">
<div class="text-center md:text-left">
<h2 class="font-h2 text-h2 text-white mb-2">Admin Dashboard</h2>
<p class="text-on-surface-variant">Manage matches and broadcast updates to the community.</p>
</div>
<div class="flex flex-wrap justify-center gap-4">
<button class="flex items-center gap-2 bg-primary-container text-on-primary-container px-6 py-3 rounded-xl font-bold hover:scale-105 active:scale-95 transition-all shadow-lg shadow-primary-container/20">
<span class="material-symbols-outlined">add_circle</span>
                        Add New Match
                    </button>
<button class="flex items-center gap-2 bg-[#25D366] text-white px-6 py-3 rounded-xl font-bold hover:scale-105 active:scale-95 transition-all shadow-lg shadow-[#25D366]/20">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">send</span>
                        Broadcast to WhatsApp
                    </button>
<button class="flex items-center gap-2 bg-error-container text-on-error-container px-6 py-3 rounded-xl font-bold hover:scale-105 active:scale-95 transition-all">
<span class="material-symbols-outlined">restart_alt</span>
                        Reset List
                    </button>
</div>
</div>
</div>
</section>
</main>
<!-- Footer -->
<footer class="bg-surface-container-lowest w-full py-12 mt-20 border-t border-outline-variant">
<div class="flex flex-col md:flex-row justify-between items-center px-8 max-w-7xl mx-auto gap-6">
<div class="text-xl font-bold text-on-surface font-['Lexend']">Golkire</div>
<div class="text-sm text-on-surface-variant font-['Lexend'] text-center md:text-left">
            © 2024 Golkire Community. Golek Kringet, Jalin Seduluran.
        </div>
<div class="flex gap-6">
<a class="text-on-surface-variant hover:text-primary transition-colors font-['Lexend'] text-sm opacity-80 hover:opacity-100" href="#">Privacy Policy</a>
<a class="text-on-surface-variant hover:text-primary transition-colors font-['Lexend'] text-sm opacity-80 hover:opacity-100" href="#">Terms of Service</a>
<a class="text-on-surface-variant hover:text-primary transition-colors font-['Lexend'] text-sm opacity-80 hover:opacity-100" href="#">Contact Us</a>
<a class="text-on-surface-variant hover:text-primary transition-colors font-['Lexend'] text-sm opacity-80 hover:opacity-100" href="#">Instagram</a>
</div>
</div>
</footer>
</body></html>
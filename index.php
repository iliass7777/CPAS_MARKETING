<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>ResourceHub - Discovery Homepage</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <!-- Google Fonts: Lexend & Material Symbols -->
    <link
        href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&amp;family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#137fec",
                        "background-light": "#f6f7f8",
                        "background-dark": "#101922",
                    },
                    fontFamily: {
                        "display": ["Lexend", "sans-serif"]
                    },
                    borderRadius: { "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px" },
                },
            },
        }
    </script>
    <style>
        body {
            font-family: 'Lexend', sans-serif;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        .active-filter {
            background-color: #137fec;
            color: white;
        }
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark text-[#111418] dark:text-white transition-colors duration-200">
    <!-- Top Navigation Bar -->
    <header
        class="sticky top-0 z-50 w-full border-b border-[#f0f2f4] dark:border-gray-800 bg-white dark:bg-[#1a242f] px-4 lg:px-20">
        <div class="max-w-[1440px] mx-auto flex h-16 items-center justify-between gap-8">
            <!-- Logo -->
            <div class="flex items-center gap-3 shrink-0">
                <div class="text-primary">
                    <span class="material-symbols-outlined text-3xl">hub</span>
                </div>
                <h1 class="text-xl font-bold tracking-tight text-[#111418] dark:text-white">ResourceHub</h1>
            </div>
            <!-- Search Bar -->
            <div class="flex-1 max-w-xl">
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-[#617589]">
                        <span class="material-symbols-outlined text-xl">search</span>
                    </div>
                    <input
                        class="block w-full rounded-lg border-none bg-background-light dark:bg-gray-800 py-2 pl-10 pr-3 text-sm placeholder-[#617589] focus:ring-2 focus:ring-primary focus:bg-white dark:focus:bg-gray-700 transition-all"
                        placeholder="Search for tools, sites, or topics..." type="text" />
                </div>
            </div>
            <!-- Nav Actions -->
            <div class="flex items-center gap-6">
                <nav class="hidden md:flex items-center gap-6">
                    <a class="text-sm font-medium hover:text-primary" href="#">Explore</a>
                    <a class="text-sm font-medium hover:text-primary" href="#">Top Leaders</a>
                    <a class="text-sm font-medium hover:text-primary" href="#">Community</a>
                </nav>
                <div class="flex items-center gap-3 border-l border-[#f0f2f4] dark:border-gray-800 pl-6">
                    <button
                        class="flex h-10 px-4 items-center justify-center rounded-lg bg-primary text-white text-sm font-bold hover:bg-blue-600 transition-colors">
                        Login
                    </button>
                    <div class="size-10 rounded-full bg-cover bg-center border border-[#f0f2f4]"
                        data-alt="User avatar placeholder"
                        style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuAXit4Fh-YCqc2uznG1uMYQeFJmLwFnpnJ2qDFvELeml7NgbBViJH-d2qyJexlz3raEr0Tsm2FdlgZCir55RuJXiqZz2_XqsgkB9_1UjtQ_zo7GS07KilzDVqUaXqR_oQIyjh7W_zgM9XKSV5shlJSb_brNy-t2U5cecn8fbKNOXCwD8E8vDByXvs4mIVAqj_y_qSdL-AI6RcDME-4UcS4LNPj29ZBIpXK1wpCkebyLzxmQldBBQv2BR9p3_ZKrHDRgMDdhjgghYt4");'>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <main class="max-w-[1440px] mx-auto flex flex-col px-4 lg:px-20 py-8">
        <!-- Page Heading & Category Chips -->
        <div class="flex flex-col gap-6 mb-8">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div class="flex flex-col gap-1">
                    <h2 class="text-3xl font-black leading-tight tracking-tight text-[#111418] dark:text-white">Discover
                        Top Resources</h2>
                    <p class="text-[#617589] dark:text-gray-400">Curated websites and tools for professionals and
                        enthusiasts.</p>
                </div>
                <div class="flex items-center gap-2 text-sm text-[#617589]">
                    <span>Sorted by:</span>
                    <button class="flex items-center gap-1 font-bold text-[#111418] dark:text-white">
                        Most Popular <span class="material-symbols-outlined text-sm">expand_more</span>
                    </button>
                </div>
            </div>
            <!-- Categories -->
            <div class="flex gap-2 overflow-x-auto pb-2 no-scrollbar">
                <button
                    class="flex items-center gap-2 h-9 px-4 rounded-full bg-primary text-white text-sm font-medium whitespace-nowrap">
                    <span class="material-symbols-outlined text-lg">dashboard</span> All Categories
                </button>
                <button
                    class="flex items-center gap-2 h-9 px-4 rounded-full bg-white dark:bg-gray-800 border border-[#f0f2f4] dark:border-gray-700 hover:border-primary transition-colors text-sm font-medium whitespace-nowrap">
                    <span class="material-symbols-outlined text-lg text-primary">code</span> Coding
                </button>
                <button
                    class="flex items-center gap-2 h-9 px-4 rounded-full bg-white dark:bg-gray-800 border border-[#f0f2f4] dark:border-gray-700 hover:border-primary transition-colors text-sm font-medium whitespace-nowrap">
                    <span class="material-symbols-outlined text-lg text-orange-500">palette</span> Design
                </button>
                <button
                    class="flex items-center gap-2 h-9 px-4 rounded-full bg-white dark:bg-gray-800 border border-[#f0f2f4] dark:border-gray-700 hover:border-primary transition-colors text-sm font-medium whitespace-nowrap">
                    <span class="material-symbols-outlined text-lg text-green-500">public</span> General Culture
                </button>
                <button
                    class="flex items-center gap-2 h-9 px-4 rounded-full bg-white dark:bg-gray-800 border border-[#f0f2f4] dark:border-gray-700 hover:border-primary transition-colors text-sm font-medium whitespace-nowrap">
                    <span class="material-symbols-outlined text-lg text-purple-500">bolt</span> Productivity
                </button>
                <button
                    class="flex items-center gap-2 h-9 px-4 rounded-full bg-white dark:bg-gray-800 border border-[#f0f2f4] dark:border-gray-700 hover:border-primary transition-colors text-sm font-medium whitespace-nowrap">
                    <span class="material-symbols-outlined text-lg text-red-500">trending_up</span> Marketing
                </button>
            </div>
        </div>
        <div class="flex flex-col md:flex-row gap-8">
            <!-- Side Navigation (Filters) -->
            <aside class="w-full md:w-64 shrink-0 flex flex-col gap-8">
                <div
                    class="bg-white dark:bg-[#1a242f] p-6 rounded-xl border border-[#f0f2f4] dark:border-gray-800 shadow-sm sticky top-24">
                    <div class="flex flex-col gap-6">
                        <div>
                            <h3 class="text-sm font-bold uppercase tracking-wider text-[#617589] mb-4">Filters</h3>
                            <div class="flex flex-col gap-1">
                                <a class="flex items-center gap-3 px-3 py-2 rounded-lg bg-primary/10 text-primary font-bold transition-colors"
                                    href="#">
                                    <span class="material-symbols-outlined">auto_awesome</span> All Resources
                                </a>
                                <a class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-background-light dark:hover:bg-gray-800 text-[#617589] dark:text-gray-300 font-medium transition-colors"
                                    href="#">
                                    <span class="material-symbols-outlined">stars</span> Top Rated
                                </a>
                                <a class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-background-light dark:hover:bg-gray-800 text-[#617589] dark:text-gray-300 font-medium transition-colors"
                                    href="#">
                                    <span class="material-symbols-outlined">trending_up</span> Most Popular
                                </a>
                            </div>
                        </div>
                        <div class="h-px bg-[#f0f2f4] dark:bg-gray-800"></div>
                        <div>
                            <h3 class="text-sm font-bold uppercase tracking-wider text-[#617589] mb-4">Pricing</h3>
                            <div class="flex flex-col gap-3">
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input checked=""
                                        class="w-5 h-5 rounded border-gray-300 text-primary focus:ring-primary"
                                        type="checkbox" />
                                    <span
                                        class="text-sm text-[#111418] dark:text-gray-200 group-hover:text-primary transition-colors">Free
                                        Resources</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input class="w-5 h-5 rounded border-gray-300 text-primary focus:ring-primary"
                                        type="checkbox" />
                                    <span
                                        class="text-sm text-[#111418] dark:text-gray-200 group-hover:text-primary transition-colors">Paid
                                        Premium</span>
                                </label>
                            </div>
                        </div>
                        <div class="h-px bg-[#f0f2f4] dark:bg-gray-800"></div>
                        <div>
                            <h3 class="text-sm font-bold uppercase tracking-wider text-[#617589] mb-4">Rating</h3>
                            <div class="flex flex-col gap-2">
                                <button class="flex items-center justify-between group">
                                    <div class="flex text-yellow-500">
                                        <span class="material-symbols-outlined fill-1">star</span>
                                        <span class="material-symbols-outlined fill-1">star</span>
                                        <span class="material-symbols-outlined fill-1">star</span>
                                        <span class="material-symbols-outlined fill-1">star</span>
                                        <span class="material-symbols-outlined">star</span>
                                    </div>
                                    <span class="text-xs text-[#617589] font-medium">&amp; Up</span>
                                </button>
                                <button class="flex items-center justify-between group">
                                    <div class="flex text-yellow-500">
                                        <span class="material-symbols-outlined fill-1">star</span>
                                        <span class="material-symbols-outlined fill-1">star</span>
                                        <span class="material-symbols-outlined fill-1">star</span>
                                        <span class="material-symbols-outlined">star</span>
                                        <span class="material-symbols-outlined">star</span>
                                    </div>
                                    <span class="text-xs text-[#617589] font-medium">&amp; Up</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>
            <!-- Main Resource Grid -->
            <div class="flex-1">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Card 1 -->
                    <div
                        class="bg-white dark:bg-[#1a242f] rounded-xl border border-[#f0f2f4] dark:border-gray-800 overflow-hidden shadow-sm hover:shadow-lg transition-all group">
                        <div class="relative h-48 bg-[#f8fafc] dark:bg-gray-800">
                            <div class="absolute inset-0 bg-cover bg-center transition-transform duration-500 group-hover:scale-110"
                                data-alt="GitHub website logo and interface"
                                style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuDs2uGhTyvuiLD-QVr0DmK6_bgvfOdRO9t3gYbhGJ4cSChuAHDtnfSJUZs2YLqr7gZ_MVMhCShsZ9F5xWuOP2HBh_aHp8zn0LZLzsz6ObeQ9dMfCsrXTq9wpNS7TM9pduV9N0ps_BYgQQ3BxSdTPEhAuyXKXDTHqzWEgjSehqlWN-2lNgur26bJms6whvmR0WL55-upZIdnusI3M_s53iqVK3YxvjhRgJp5p-ULpzhzPcaXrhAoNQm7D38ULHnxGZWHhTguYser6yk");'>
                            </div>
                            <div class="absolute top-3 right-3">
                                <span
                                    class="bg-primary text-white text-[10px] font-black uppercase px-2 py-1 rounded-full flex items-center gap-1 shadow-lg">
                                    <span class="material-symbols-outlined text-xs">verified</span> Top Leader
                                </span>
                            </div>
                        </div>
                        <div class="p-5 flex flex-col gap-4">
                            <div>
                                <h3 class="text-lg font-bold text-[#111418] dark:text-white mb-1">GitHub</h3>
                                <p class="text-sm text-[#617589] dark:text-gray-400 line-clamp-2">World's leading
                                    development platform for hosting and managing software projects.</p>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-1">
                                    <span class="material-symbols-outlined text-yellow-500 fill-1 text-base">star</span>
                                    <span class="text-sm font-bold text-[#111418] dark:text-white">4.9</span>
                                    <span class="text-xs text-[#617589]">(2.4k reviews)</span>
                                </div>
                                <span
                                    class="text-xs font-bold text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/30 px-2 py-1 rounded">FREE</span>
                            </div>
                            <button
                                class="w-full h-10 rounded-lg border border-primary text-primary font-bold text-sm hover:bg-primary hover:text-white transition-colors">
                                View Details
                            </button>
                        </div>
                    </div>
                    <!-- Card 2 -->
                    <div
                        class="bg-white dark:bg-[#1a242f] rounded-xl border border-[#f0f2f4] dark:border-gray-800 overflow-hidden shadow-sm hover:shadow-lg transition-all group">
                        <div class="relative h-48 bg-[#f8fafc] dark:bg-gray-800">
                            <div class="absolute inset-0 bg-cover bg-center transition-transform duration-500 group-hover:scale-110"
                                data-alt="Stack Overflow community platform"
                                style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuA8PrFxhPaHwKZuXhk60mcwZjZj_-ggciZ5AoXlX6tN_voBrWsTukTH-lpTqMyZ5LEfCpf6w1JRggExsyxIMQw3CEt8dDhEGwhPwc4OOVpGNsSJJQS1is07E7c1yv8jchJi5bAHkRsx38xlHW7v5YmOJWzeX0JHVe2-VZWvN-ZHFDHMyQt5KYvog9tL266jyXu_1DG96q1H_yIaz4vEx4O4kUpO_SIpda5rH-G0TO0yLFpbFgtpH2HD-wMSOLKFZo_htyyR7VkS_W4");'>
                            </div>
                        </div>
                        <div class="p-5 flex flex-col gap-4">
                            <div>
                                <h3 class="text-lg font-bold text-[#111418] dark:text-white mb-1">Stack Overflow</h3>
                                <p class="text-sm text-[#617589] dark:text-gray-400 line-clamp-2">The largest online
                                    community for developers to learn, share knowledge, and build careers.</p>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-1">
                                    <span class="material-symbols-outlined text-yellow-500 fill-1 text-base">star</span>
                                    <span class="text-sm font-bold text-[#111418] dark:text-white">4.7</span>
                                    <span class="text-xs text-[#617589]">(1.8k reviews)</span>
                                </div>
                                <span
                                    class="text-xs font-bold text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/30 px-2 py-1 rounded">FREE</span>
                            </div>
                            <button
                                class="w-full h-10 rounded-lg border border-primary text-primary font-bold text-sm hover:bg-primary hover:text-white transition-colors">
                                View Details
                            </button>
                        </div>
                    </div>
                    <!-- Card 3 -->
                    <div
                        class="bg-white dark:bg-[#1a242f] rounded-xl border border-[#f0f2f4] dark:border-gray-800 overflow-hidden shadow-sm hover:shadow-lg transition-all group">
                        <div class="relative h-48 bg-[#f8fafc] dark:bg-gray-800">
                            <div class="absolute inset-0 bg-cover bg-center transition-transform duration-500 group-hover:scale-110"
                                data-alt="Figma design tool interface"
                                style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCuSkt73pMrCekTkEGd7nxNti0P3Mfp79kpZp2mVAoGBaaW2-Vw1Xk99buj9fxGY9lLohYngY7uwCtY6soToySA6G64T9sqfqAL410wU9tcsNkVHDO-Hq4AY9xLICBBVVTEcZJjWY5kor0IF6RWJTw4Qsp1xYN7Y-JrDYLzeuhIIk8doZNjeeWEgEMR5Z9WZoCrhpawfbVmz6m5TtZehCHHuyy87wHRcHWPNIflfLQZ2jfdyldpeO9AHb0JWLN7ZULAzzhZbbyeGOY");'>
                            </div>
                            <div class="absolute top-3 right-3">
                                <span
                                    class="bg-primary text-white text-[10px] font-black uppercase px-2 py-1 rounded-full flex items-center gap-1 shadow-lg">
                                    <span class="material-symbols-outlined text-xs">verified</span> Top Leader
                                </span>
                            </div>
                        </div>
                        <div class="p-5 flex flex-col gap-4">
                            <div>
                                <h3 class="text-lg font-bold text-[#111418] dark:text-white mb-1">Figma</h3>
                                <p class="text-sm text-[#617589] dark:text-gray-400 line-clamp-2">Collaborative
                                    interface design tool that powers modern product teams.</p>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-1">
                                    <span class="material-symbols-outlined text-yellow-500 fill-1 text-base">star</span>
                                    <span class="text-sm font-bold text-[#111418] dark:text-white">4.8</span>
                                    <span class="text-xs text-[#617589]">(3.1k reviews)</span>
                                </div>
                                <span
                                    class="text-xs font-bold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 px-2 py-1 rounded">FREEMIUM</span>
                            </div>
                            <button
                                class="w-full h-10 rounded-lg border border-primary text-primary font-bold text-sm hover:bg-primary hover:text-white transition-colors">
                                View Details
                            </button>
                        </div>
                    </div>
                    <!-- Card 4 -->
                    <div
                        class="bg-white dark:bg-[#1a242f] rounded-xl border border-[#f0f2f4] dark:border-gray-800 overflow-hidden shadow-sm hover:shadow-lg transition-all group">
                        <div class="relative h-48 bg-[#f8fafc] dark:bg-gray-800">
                            <div class="absolute inset-0 bg-cover bg-center transition-transform duration-500 group-hover:scale-110"
                                data-alt="Coursera online education platform"
                                style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuBm6TEi2iGwtj1KHhMeBcW7ONde6UHZ-zuxsSHoDQXJihNt_YZIL-E8WEmaQepH8YZgOgEK4AWYhP09Amct_SH7aG-ZeBYwaieKRcpE8EEGtxiQqmQqzMN_JJSSy8JL0PwhJQvDLJ8C19CbqGWbzLUTR_L2SsqVYYbsO-Y9wdcX32XNAd4YOs7g3suDhwtyTHBr0rZIZY1gYzHhYXgit1CIJtDxP4je-gQ7ds6mpSxTCmqWF2wUOVsD7donciKQcOzan0NIEtVLmCg");'>
                            </div>
                        </div>
                        <div class="p-5 flex flex-col gap-4">
                            <div>
                                <h3 class="text-lg font-bold text-[#111418] dark:text-white mb-1">Coursera</h3>
                                <p class="text-sm text-[#617589] dark:text-gray-400 line-clamp-2">Access online courses
                                    from top universities and industry leaders around the globe.</p>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-1">
                                    <span class="material-symbols-outlined text-yellow-500 fill-1 text-base">star</span>
                                    <span class="text-sm font-bold text-[#111418] dark:text-white">4.5</span>
                                    <span class="text-xs text-[#617589]">(980 reviews)</span>
                                </div>
                                <span
                                    class="text-xs font-bold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/30 px-2 py-1 rounded">PAID</span>
                            </div>
                            <button
                                class="w-full h-10 rounded-lg border border-primary text-primary font-bold text-sm hover:bg-primary hover:text-white transition-colors">
                                View Details
                            </button>
                        </div>
                    </div>
                    <!-- Card 5 -->
                    <div
                        class="bg-white dark:bg-[#1a242f] rounded-xl border border-[#f0f2f4] dark:border-gray-800 overflow-hidden shadow-sm hover:shadow-lg transition-all group">
                        <div class="relative h-48 bg-[#f8fafc] dark:bg-gray-800">
                            <div class="absolute inset-0 bg-cover bg-center transition-transform duration-500 group-hover:scale-110"
                                data-alt="Dribbble design portfolio site"
                                style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCg-WviioSuzXfuNx-FOL37a_26vCKVd9cuEQIqYdV90Tm4U8xHmk89MANDZJN1zh9BrH3fcTt2RTYxz8tX4Czh7_uslWtKRk-U5FaCPJ3nKXGwSKlL_mxLcpxDqRduJZ2QI3ZG1Ck8Ig6tPf7HB0eUBfmNNNrp_yUAplaDwa3COpnQdO6ynjZZ2XmQ33edqGer-xeYJ3daaxToznB_GTBpndK23a7dnRPU7MQ96yNcLdUd2CIx803CNqpYBdFMwOLAd198gcx7h2M");'>
                            </div>
                        </div>
                        <div class="p-5 flex flex-col gap-4">
                            <div>
                                <h3 class="text-lg font-bold text-[#111418] dark:text-white mb-1">Dribbble</h3>
                                <p class="text-sm text-[#617589] dark:text-gray-400 line-clamp-2">The go-to destination
                                    for designers and creative professionals to share work.</p>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-1">
                                    <span class="material-symbols-outlined text-yellow-500 fill-1 text-base">star</span>
                                    <span class="text-sm font-bold text-[#111418] dark:text-white">4.6</span>
                                    <span class="text-xs text-[#617589]">(1.5k reviews)</span>
                                </div>
                                <span
                                    class="text-xs font-bold text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/30 px-2 py-1 rounded">FREE</span>
                            </div>
                            <button
                                class="w-full h-10 rounded-lg border border-primary text-primary font-bold text-sm hover:bg-primary hover:text-white transition-colors">
                                View Details
                            </button>
                        </div>
                    </div>
                    <!-- Card 6 -->
                    <div
                        class="bg-white dark:bg-[#1a242f] rounded-xl border border-[#f0f2f4] dark:border-gray-800 overflow-hidden shadow-sm hover:shadow-lg transition-all group">
                        <div class="relative h-48 bg-[#f8fafc] dark:bg-gray-800">
                            <div class="absolute inset-0 bg-cover bg-center transition-transform duration-500 group-hover:scale-110"
                                data-alt="Wikipedia encyclopedia"
                                style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuDEePB5vvQgWniugQRGaTEgkMcpY_OcdoUzl95UVh5nZU1jdja3ge2lWigDDzJCz_6bQDnyPDPpPEz8yry45MFKa1LPHNHnlBhdGNdRSrr5H8-HTx7IOAHukWiTSsrteDoc0HAANy6DBCRhLaS0M70VI8aDMjZKRVX4mo0DdAI3_yoBrpuS3x5MqHjd3SuBXrnqcfMLagIdcRI4pfD23XV0b6jQQQmDGZAEErS1u_J54XZ_JpZXADD4i8_X32mC46X-tffLTmeMxK0");'>
                            </div>
                        </div>
                        <div class="p-5 flex flex-col gap-4">
                            <div>
                                <h3 class="text-lg font-bold text-[#111418] dark:text-white mb-1">Wikipedia</h3>
                                <p class="text-sm text-[#617589] dark:text-gray-400 line-clamp-2">The free encyclopedia
                                    that anyone can edit, providing vast knowledge on every subject.</p>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-1">
                                    <span class="material-symbols-outlined text-yellow-500 fill-1 text-base">star</span>
                                    <span class="text-sm font-bold text-[#111418] dark:text-white">4.4</span>
                                    <span class="text-xs text-[#617589]">(5k reviews)</span>
                                </div>
                                <span
                                    class="text-xs font-bold text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/30 px-2 py-1 rounded">FREE</span>
                            </div>
                            <button
                                class="w-full h-10 rounded-lg border border-primary text-primary font-bold text-sm hover:bg-primary hover:text-white transition-colors">
                                View Details
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Pagination Placeholder -->
                <div class="mt-12 flex justify-center">
                    <nav class="flex items-center gap-2">
                        <button
                            class="size-10 flex items-center justify-center rounded-lg border border-[#f0f2f4] dark:border-gray-800 hover:bg-white dark:hover:bg-gray-800 transition-colors">
                            <span class="material-symbols-outlined">chevron_left</span>
                        </button>
                        <button
                            class="size-10 flex items-center justify-center rounded-lg bg-primary text-white font-bold">1</button>
                        <button
                            class="size-10 flex items-center justify-center rounded-lg border border-[#f0f2f4] dark:border-gray-800 hover:bg-white dark:hover:bg-gray-800 transition-colors">2</button>
                        <button
                            class="size-10 flex items-center justify-center rounded-lg border border-[#f0f2f4] dark:border-gray-800 hover:bg-white dark:hover:bg-gray-800 transition-colors">3</button>
                        <span class="mx-2 text-[#617589]">...</span>
                        <button
                            class="size-10 flex items-center justify-center rounded-lg border border-[#f0f2f4] dark:border-gray-800 hover:bg-white dark:hover:bg-gray-800 transition-colors">
                            <span class="material-symbols-outlined">chevron_right</span>
                        </button>
                    </nav>
                </div>
            </div>
        </div>
    </main>
    <!-- Footer -->
    <footer class="mt-20 border-t border-[#f0f2f4] dark:border-gray-800 bg-white dark:bg-[#1a242f] py-12 px-4 lg:px-20">
        <div class="max-w-[1440px] mx-auto grid grid-cols-1 md:grid-cols-4 gap-12">
            <div class="col-span-1 md:col-span-1 flex flex-col gap-4">
                <div class="flex items-center gap-3">
                    <div class="text-primary">
                        <span class="material-symbols-outlined text-3xl">hub</span>
                    </div>
                    <h2 class="text-xl font-bold tracking-tight text-[#111418] dark:text-white">ResourceHub</h2>
                </div>
                <p class="text-sm text-[#617589] dark:text-gray-400">Discovering and ranking the web's best tools since
                    2023.</p>
            </div>
            <div>
                <h4 class="font-bold mb-4">Categories</h4>
                <ul class="flex flex-col gap-2 text-sm text-[#617589] dark:text-gray-400">
                    <li><a class="hover:text-primary" href="#">Development</a></li>
                    <li><a class="hover:text-primary" href="#">Graphic Design</a></li>
                    <li><a class="hover:text-primary" href="#">Marketing Tools</a></li>
                    <li><a class="hover:text-primary" href="#">Education</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold mb-4">Support</h4>
                <ul class="flex flex-col gap-2 text-sm text-[#617589] dark:text-gray-400">
                    <li><a class="hover:text-primary" href="#">Help Center</a></li>
                    <li><a class="hover:text-primary" href="#">Submit a Resource</a></li>
                    <li><a class="hover:text-primary" href="#">API Documentation</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold mb-4">Newsletter</h4>
                <p class="text-sm text-[#617589] dark:text-gray-400 mb-4">Get the weekly top leaders list in your inbox.
                </p>
                <div class="flex gap-2">
                    <input class="flex-1 rounded-lg border-[#f0f2f4] dark:border-gray-800 dark:bg-gray-800 text-sm"
                        placeholder="Email" type="email" />
                    <button class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-bold">Join</button>
                </div>
            </div>
        </div>
        <div
            class="max-w-[1440px] mx-auto mt-12 pt-8 border-t border-[#f0f2f4] dark:border-gray-800 flex justify-between items-center text-xs text-[#617589]">
            <p>© 2024 ResourceHub Inc. All rights reserved.</p>
            <div class="flex gap-4">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
            </div>
        </div>
    </footer>
</body>
</html>

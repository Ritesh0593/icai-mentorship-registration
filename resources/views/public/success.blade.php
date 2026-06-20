<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Successful - EventQR</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="h-full flex items-center justify-center p-4 relative overflow-hidden bg-slate-100">
    <!-- Background glows -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl"></div>

    <div class="w-full max-w-md bg-white p-8 md:p-10 rounded-3xl shadow-xl border border-slate-200/60 text-center relative z-10">
        
        <!-- Animated Green Checkmark Icon wrapper -->
        <div class="w-20 h-20 bg-emerald-50 border border-emerald-200 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner animate-bounce">
            <i data-lucide="check" class="w-10 h-10 text-emerald-600"></i>
        </div>

        <h2 class="text-3xl font-extrabold text-slate-800 tracking-tight">Registration Complete!</h2>
        
        <p class="text-slate-500 mt-3 text-sm px-2">
            Hi <span class="font-bold text-slate-800">{{ session('registered_name', 'there') }}</span>, your spot for the event has been successfully reserved!
        </p>

        <!-- Ticket / Event Info Card -->
        <div class="my-8 p-5 rounded-2xl bg-slate-50 border border-slate-200/60 text-left">
            <div class="flex items-center justify-between border-b border-slate-200/60 pb-3 mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Event Pass</span>
                <span class="text-xs font-semibold px-2 py-0.5 bg-emerald-100 border border-emerald-200 text-emerald-800 rounded-full">Confirmed</span>
            </div>
            
            <div class="space-y-2.5">
                <div class="flex items-start gap-2.5">
                    <i data-lucide="map-pin" class="w-4 h-4 text-slate-400 shrink-0 mt-0.5"></i>
                    <div>
                        <p class="text-[10px] text-slate-400 uppercase font-semibold">Location</p>
                        <p class="text-xs font-bold text-slate-700">{{ session('success_city', 'your chosen city') }}</p>
                    </div>
                </div>
            </div>
        </div>
        
    </div>

    <!-- Initialize Lucide Icons -->
    <script>
        lucide.createIcons();
    </script>
</body>
</html>

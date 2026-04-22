<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin | E-Library SMPN 1 Bilah Barat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .bg-admin-theme { background-color: #3c8dbc; }
    </style>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-4xl w-full bg-white shadow-2xl rounded-2xl overflow-hidden flex flex-col md:flex-row min-h-[550px]">
        
        <div class="md:w-1/2 bg-admin-theme p-12 text-white flex flex-col justify-between relative overflow-hidden">
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-8">
                    <img src="{{ asset('Logo-Tut-Wuri-Handayani-PNG-Warna.png') }}" alt="Logo" class="h-12 w-auto brightness-0 invert">
                    <div class="flex flex-col">
                        <span class="font-black text-xl leading-none uppercase">Admin Panel</span>
                        <span class="text-[10px] tracking-[0.2em] opacity-80 uppercase">E-Library SMPN 1</span>
                    </div>
                </div>
                
                <h2 class="text-3xl font-extrabold leading-tight mb-4">Kendali Pusat <br>Perpustakaan Digital</h2>
                <p class="text-sm text-blue-100 leading-relaxed opacity-90">Silahkan masuk menggunakan akun petugas untuk mengelola sirkulasi buku, data siswa, dan laporan perpustakaan.</p>
            </div>

            <div class="relative z-10 border-t border-white/20 pt-6">
                <p class="text-[10px] font-bold uppercase tracking-widest opacity-60">Sistem Informasi v2.0</p>
            </div>

            <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-white/10 rounded-full"></div>
        </div>

        <div class="md:w-1/2 p-8 md:p-12 flex flex-col justify-center bg-white">
            <div class="mb-10">
                <h3 class="text-2xl font-black text-slate-800 uppercase tracking-tight">Login Petugas</h3>
                <p class="text-slate-400 text-sm mt-1 font-medium">Masukan kredensial admin Anda.</p>
            </div>

            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf
                <input type="hidden" name="remember" value="true">
                
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Email Administrator</label>
                    <div class="relative group">
                        <i class="fas fa-user-shield absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-blue-500 transition-colors"></i>
                        <input type="email" name="email" required autofocus
                            class="w-full pl-12 pr-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl focus:border-blue-500 focus:bg-white focus:outline-none transition-all text-sm font-semibold text-slate-700" 
                            placeholder="admin@mail.com">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Password</label>
                    <div class="relative group">
                        <i class="fas fa-key absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-blue-500 transition-colors"></i>
                        <input type="password" name="password" required 
                            class="w-full pl-12 pr-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl focus:border-blue-500 focus:bg-white focus:outline-none transition-all text-sm font-semibold text-slate-700" 
                            placeholder="••••••••">
                    </div>
                </div>

                <button type="submit" 
                    class="w-full bg-admin-theme hover:bg-blue-700 text-white font-black py-4 rounded-xl shadow-lg shadow-blue-100 transition-all transform active:scale-[0.98] uppercase text-xs tracking-[0.2em] mt-4">
                    Autentikasi Sekarang
                </button>
            </form>

            <div class="mt-10 text-center">
                <a href="/" class="text-[10px] font-black text-slate-400 hover:text-blue-600 transition-colors uppercase tracking-widest flex items-center justify-center gap-2">
                    <i class="fas fa-arrow-left"></i> Kembali ke Beranda Utama
                </a>
            </div>
        </div>
    </div>

</body>
</html>
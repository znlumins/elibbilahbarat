<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Library | SMPN 1 Bilah Barat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
        
        body { 
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #ecf0f5; 
        }
        .header-top {
            background-color: #3c8dbc; 
        }
        .card-book {
            transition: all 0.2s ease;
            border-top: 3px solid #d2d6de;
        }
        .card-book:hover {
            border-top: 3px solid #3c8dbc;
            transform: translateY(-3px);
        }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;  
            overflow: hidden;
        }
    </style>
</head>
<body class="text-slate-700">

    <header class="header-top text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 h-16 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <img src="{{ asset('Logo-Tut-Wuri-Handayani-PNG-Warna.png') }}" alt="Logo" class="h-12 w-auto object-contain">
                <div class="flex flex-col justify-center">
                    <span class="font-bold text-lg tracking-tight uppercase leading-none">Perpustakaan</span>
                    <span class="text-xs font-medium tracking-wider opacity-90 uppercase">SMPN 1 Bilah Barat</span>
                </div>
            </div>

            <nav class="flex items-center gap-4">
                @auth
                    @if(auth()->user()->role === 'admin')
                        <a href="/admin" class="text-[10px] font-bold uppercase hover:bg-white hover:text-blue-600 border border-white/50 px-3 py-2 rounded transition flex items-center gap-2">
                            <i class="fa-solid fa-gauge"></i> Dashboard Admin
                        </a>
                    @else
                        <a href="/my-loans" class="text-[10px] font-bold uppercase hover:bg-white hover:text-blue-600 border border-white/50 px-3 py-2 rounded transition flex items-center gap-2">
                            <i class="fa-solid fa-history"></i> Riwayat Peminjaman
                        </a>
                        
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-[10px] font-bold uppercase bg-red-600 hover:bg-red-700 px-3 py-2 rounded transition flex items-center gap-2 shadow-sm">
                                <i class="fa-solid fa-right-from-bracket"></i> Keluar
                            </button>
                        </form>
                    @endif
                @else
                    <a href="/login" class="border border-white/50 hover:bg-white hover:text-blue-600 px-4 py-1.5 rounded text-xs font-bold uppercase transition-all flex items-center gap-2">
                        <i class="fa-solid fa-user-graduate"></i> Login Siswa
                    </a>
                @endauth
            </nav>
        </div>
    </header>

    <section class="bg-white border-b border-gray-300 py-12 px-4 shadow-sm">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="text-center md:text-left">
                <h2 class="text-3xl font-extrabold text-gray-800 mb-2 uppercase tracking-tight">Selamat Datang di E-Library</h2>
                <p class="text-gray-500 font-medium italic">Temukan ribuan koleksi buku referensi dan literasi secara digital.</p>
            </div>
            
    <div class="w-full md:w-1/2">
        <form action="/" method="GET" class="relative flex items-stretch shadow-sm">
            <div class="relative flex-grow">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fa-solid fa-search text-gray-400"></i>
                </div>
                
                <input type="text" name="search" value="{{ request('search') }}" 
                    class="w-full p-3.5 pl-11 border-2 border-gray-300 bg-white focus:border-blue-500 focus:ring-0 focus:outline-none transition-all text-sm font-medium" 
                    placeholder="Cari Judul Buku, Penulis, atau Kategori..."
                    style="border-radius: 0px !important;"> </div>

            <button type="submit" 
                class="bg-[#3c8dbc] hover:bg-[#367fa9] text-white px-8 font-bold text-xs uppercase tracking-widest transition-colors flex items-center border-2 border-[#3c8dbc]"
                style="border-radius: 0px !important;"> CARI
            </button>
        </form>
    </div>
        </div>
    </section>

    <main class="max-w-7xl mx-auto py-10 px-4 min-h-[500px]">
        {{-- <div class="flex items-center gap-3 mb-8 text-gray-700 border-b-2 border-gray-300 pb-3">
            <i class="fa-solid fa-book-open text-blue-600"></i>
            <h3 class="text-xl font-bold uppercase tracking-wide">Katalog Koleksi Terbaru</h3>
        </div> --}}

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
            @forelse($books as $book)
                <div class="card-book bg-white shadow-sm overflow-hidden flex flex-col">
                    <div class="aspect-[3/4] relative bg-gray-100 border-b border-gray-100">
                        @if($book->cover)
                            <img src="{{ asset('storage/' . $book->cover) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center text-gray-300 italic p-4 text-center">
                                <i class="fa-solid fa-image text-3xl mb-2"></i>
                                <span class="text-[10px] font-bold uppercase">No Visual</span>
                            </div>
                        @endif
                        <div class="absolute top-0 right-0 bg-yellow-500 text-white text-[10px] font-bold px-2 py-0.5 shadow-sm">
                            {{ $book->year }}
                        </div>
                    </div>
                    
                    <div class="p-4 flex-grow flex flex-col justify-between">
                        <div>
                            <span class="text-[9px] font-bold text-blue-600 uppercase tracking-tighter block mb-1">
                                {{ $book->category->name ?? 'UMUM' }}
                            </span>
                            <h4 class="text-sm font-bold text-gray-800 line-clamp-2 leading-tight">
                                {{ $book->title }}
                            </h4>
                            <p class="text-[11px] text-gray-400 mt-1 italic truncate">By {{ $book->author }}</p>
                        </div>
                        
                        <a href="{{ route('books.show', $book->id) }}" 
                           class="mt-4 block text-center bg-gray-50 text-blue-700 py-2 rounded border border-gray-200 text-[10px] font-extrabold uppercase hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all">
                            Detail Buku
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-24 text-center">
                    <i class="fa-solid fa-box-open text-6xl text-gray-300 mb-4"></i>
                    <h4 class="text-lg font-bold text-gray-400 uppercase tracking-widest">Buku tidak ditemukan</h4>
                </div>
            @endforelse
        </div>
    </main>

    <footer class="bg-slate-800 text-white py-12 px-4 mt-12">
        <div class="max-w-7xl mx-auto grid md:grid-cols-2 gap-12 items-end">
            <div>
                <div class="flex items-center gap-4 mb-5">
                    <img src="{{ asset('Logo-Tut-Wuri-Handayani-PNG-Warna.png') }}" alt="Logo" class="h-14 w-auto object-contain">
                    <div>
                        <h5 class="font-bold text-lg uppercase leading-none text-white">SMPN 1 Bilah Barat</h5>
                        <p class="text-xs text-slate-400 mt-1 uppercase tracking-wider font-semibold">Sistem Informasi Perpustakaan Digital</p>
                    </div>
                </div>
                <div class="text-xs text-slate-500 leading-relaxed max-w-sm">
                    <p class="mb-1">Tebing Linggahara, Bilah Barat, Labuhan Batu Regency, Sumatra Utara 21411</p>
                </div>
            </div>
            
            <div class="md:text-right flex flex-col gap-3">
                <p class="text-[10px] text-slate-500 uppercase tracking-[0.2em] font-black">©2026 IT E-Library SMPN 1 Bilah Barat</p>
                
                <div class="border-t border-slate-700 pt-3">
                    @guest
                        <a href="/admin/login" class="text-[10px] text-slate-600 hover:text-blue-400 transition-colors flex items-center md:justify-end gap-1.5 italic group">
                            <i class="fa-solid fa-lock text-[9px] group-hover:animate-pulse"></i> 
                            Admin Login
                        </a>
                    @else
                        <form action="{{ route('logout') }}" method="POST" class="m-0 p-0">
                            @csrf
                        </form>
                    @endguest
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
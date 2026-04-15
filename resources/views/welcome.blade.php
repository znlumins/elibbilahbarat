<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Library | SMPN 1 Bilah Barat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;500;700&family=Inter:wght@400;700;900&display=swap');
        
        body { 
            font-family: 'Inter', sans-serif;
            background-color: #fcfcfc;
        }
        
        h1, h2, h3, .font-heading { 
            font-family: 'Space Grotesk', sans-serif; 
        }

        /* Hilangkan kesan card AI dengan border tipis */
        .book-wrapper {
            border-bottom: 1px solid #e5e7eb;
            transition: all 0.3s ease;
        }

        .book-wrapper:hover {
            background-color: #f8fafc;
        }

        /* Cover buku dengan gaya majalah */
        .book-cover-container {
            position: relative;
            box-shadow: 15px 15px 0px -5px rgba(29, 78, 216, 0.05);
            transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .book-wrapper:hover .book-cover-container {
            transform: rotate(-2deg) scale(1.02);
            box-shadow: 20px 20px 0px -5px rgba(29, 78, 216, 0.1);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-thumb { background: #1d4ed8; }
    </style>
</head>
<body class="text-slate-900 leading-relaxed">

    <header class="border-b border-slate-200 py-6 px-6 md:px-12 bg-white sticky top-0 z-50">
        <div class="max-w-[1600px] mx-auto flex justify-between items-center">
            <div class="flex items-center gap-6">
                <img src="{{ asset('Logo-Tut-Wuri-Handayani-PNG-Warna.png') }}" class="h-12 w-auto">
                <div class="hidden md:block">
                    <h1 class="text-2xl font-black tracking-tighter leading-none">ELIB.1BB</h1>
                    <span class="text-[10px] uppercase tracking-[0.3em] font-bold text-slate-400">Perpustakaan Digital SMPN 1 Bilah Barat</span>
                </div>
            </div>

            <nav class="flex items-center gap-8">
                @auth
                    <a href="{{ route('my.loans') }}" class="text-xs font-bold uppercase tracking-widest hover:text-blue-700 transition">Koleksi Saya</a>
                    <div class="flex items-center gap-4 border-l pl-8 border-slate-200">
                        <div class="text-right">
                            <p class="text-[10px] font-bold text-slate-400 leading-none mb-1">USER ACCESS</p>
                            <p class="text-sm font-bold uppercase">{{ Auth::user()->name }}</p>
                        </div>
                        <form action="{{ route('logout') }}" method="POST" class="m-0">
                            @csrf
                            <button class="text-red-500 hover:text-red-700 transition"><i class="fa-solid fa-arrow-right-from-bracket"></i></button>
                        </form>
                    </div>
                @else
                    <a href="/login" class="px-8 py-3 bg-blue-700 text-white text-xs font-black uppercase tracking-widest hover:bg-black transition-all">
                        Sign In →
                    </a>
                @endauth
            </nav>
        </div>
    </header>

    <section class="py-20 px-6 bg-blue-700 text-white relative overflow-hidden">
        <div class="max-w-[1600px] mx-auto relative z-10">
            <span class="inline-block px-3 py-1 bg-yellow-400 text-blue-900 text-[10px] font-black uppercase mb-6">Explore Knowledge</span>
            <h2 class="text-5xl md:text-7xl font-black tracking-tighter mb-12 max-w-4xl leading-[0.9]">TEMUKAN BUKU <br>TANPA BATAS.</h2>
            
            <form action="/" method="GET" class="max-w-3xl flex flex-col md:flex-row gap-0 border-4 border-white">
                <input type="text" name="search" value="{{ request('search') }}" 
                    class="flex-grow p-6 bg-transparent text-white placeholder:text-blue-200 focus:outline-none text-xl font-medium" 
                    placeholder="Judul buku, penulis, atau ISBN...">
                <button type="submit" class="bg-white text-blue-700 px-12 py-6 font-black uppercase text-sm hover:bg-yellow-400 hover:text-blue-900 transition-colors">
                    Cari Sekarang
                </button>
            </form>
        </div>
        <span class="absolute bottom-[-50px] right-[-20px] text-[200px] font-black opacity-10 select-none pointer-events-none tracking-tighter">READ</span>
    </section>

    <main class="max-w-[1600px] mx-auto py-20 px-6 md:px-12">
        <div class="flex items-end justify-between mb-16 border-b-4 border-slate-900 pb-4">
            <h3 class="text-3xl font-black uppercase tracking-tighter font-heading">Katalog Sekolah</h3>
            <p class="text-sm font-bold text-slate-500 italic pb-1">{{ $books->count() }} Items Available</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-0 border-t border-l border-slate-200">
            @forelse($books as $book)
                <div class="book-wrapper p-8 border-r border-b border-slate-200 group">
                    <div class="book-cover-container aspect-[3/4] mb-8 bg-slate-100 overflow-hidden">
                        @if($book->cover)
                            <img src="{{ asset('storage/' . $book->cover) }}" class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-500">
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center p-6 text-center">
                                <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest">No Visual Available</span>
                            </div>
                        @endif
                        
                        <div class="absolute top-4 right-4 bg-black text-white text-[9px] font-black px-2 py-1 uppercase">
                            {{ $book->year }}
                        </div>
                    </div>

                    <div class="space-y-3">
                        <span class="text-[10px] font-black text-blue-700 uppercase tracking-widest">
                            // {{ $book->category->name ?? 'GENERAL' }}
                        </span>
                        <h4 class="text-lg font-bold leading-none tracking-tight text-slate-900 h-12 overflow-hidden">
                            {{ $book->title }}
                        </h4>
                        <p class="text-xs font-medium text-slate-400">By {{ $book->author }}</p>
                        
                        <div class="pt-6">
                            <a href="{{ route('books.show', $book->id) }}" 
                               class="inline-block text-[10px] font-black uppercase tracking-[0.2em] text-slate-900 border-b-2 border-blue-700 pb-1 hover:text-blue-700 transition-colors">
                                View Detail →
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-40 text-center border-b border-r border-slate-200">
                    <h3 class="text-4xl font-black text-slate-200 uppercase tracking-tighter">Buku Tidak Ditemukan</h3>
                </div>
            @endforelse
        </div>
    </main>

    <footer class="bg-black text-white py-20 px-6">
        <div class="max-w-[1600px] mx-auto flex flex-col md:flex-row justify-between items-start gap-12">
            <div class="max-w-xs">
                <img src="{{ asset('Logo-Tut-Wuri-Handayani-PNG-Warna.png') }}" class="h-12 mb-6">
                <p class="text-slate-500 text-sm font-medium">Perpustakaan Digital SMPN 1 Bilah Barat. Mengintegrasikan teknologi dalam literasi sekolah.</p>
            </div>
            
            <div class="flex flex-col items-end">
                <span class="text-[120px] font-black leading-none tracking-tighter opacity-10">2026</span>
                <p class="text-[10px] font-black tracking-[0.5em] text-slate-400 uppercase mt-4">© SMPN 1 BILAH BARAT. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

</body>
</html>
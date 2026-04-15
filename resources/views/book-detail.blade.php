<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $book->title }} - Detail E-Library</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 pb-20 text-slate-900">

    <nav class="bg-blue-800 p-4 text-white shadow-lg mb-8">
        <div class="container mx-auto flex items-center px-4">
            <a href="/" class="hover:text-blue-200 transition flex items-center font-bold text-sm uppercase tracking-tight">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Katalog
            </a>
        </div>
    </nav>

    <div class="container mx-auto px-4">
        
        <div class="flex flex-col md:flex-row gap-8 mb-10 bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
            
            <div class="w-full md:w-1/4">
                <div class="aspect-[3/4] bg-gray-50 rounded-2xl overflow-hidden border border-gray-200 shadow-inner relative group">
                    @if($book->cover)
                        <img src="{{ asset('storage/'.$book->cover) }}" class="w-full h-full object-cover">
                    @else
                        <div class="flex flex-col items-center justify-center h-full text-gray-300">
                            <i class="fas fa-book text-7xl opacity-20"></i>
                            <span class="text-[10px] font-black uppercase tracking-widest mt-4">No Cover</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="w-full md:w-3/4 flex flex-col justify-center">
                <span class="bg-blue-50 text-blue-600 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest w-fit mb-4">
                    {{ $book->category->name ?? 'Umum' }}
                </span>
                
                <h1 class="text-4xl font-black text-gray-900 leading-tight mb-2 tracking-tighter italic">
                    {{ $book->title }}
                </h1>
                
                <p class="text-xl text-gray-400 font-medium italic mb-6">Oleh: {{ $book->author }}</p>

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Tahun Terbit</p>
                        <p class="text-lg font-black text-gray-800">{{ $book->year ?? '-' }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Stok Buku</p>
                        <p class="text-lg font-black text-gray-800">{{ $book->stock }} <span class="text-xs font-normal">Pcs</span></p>
                    </div>
                </div>

                <div class="bg-blue-50/50 p-5 rounded-2xl border border-blue-100/50 border-dashed">
                    <h4 class="text-xs font-black uppercase text-blue-800 tracking-widest mb-2 flex items-center">
                        <i class="fas fa-quote-left mr-2"></i> Sinopsis
                    </h4>
                    <p class="text-sm text-blue-900/70 leading-relaxed italic">
                        {{ $book->description ?? 'Tidak ada deskripsi atau sinopsis untuk buku ini.' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-50 flex justify-between items-center bg-white">
                    <h3 class="font-black text-gray-800 flex items-center uppercase tracking-tight">
                        <i class="fas fa-history mr-3 text-blue-600"></i> Sejarah Peminjam Buku
                    </h3>
                    <div class="text-[10px] font-bold text-blue-600 bg-blue-50 px-3 py-1 rounded-full uppercase tracking-tighter">
                        Total {{ $book->loans->count() }} Kali Dipinjam
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[10px] uppercase font-black text-gray-400 tracking-[0.1em] bg-gray-50/50 border-b border-gray-50">
                                <th class="p-6">Nama Peminjam</th>
                                <th class="p-6 text-center">Tgl Pinjam</th>
                                <th class="p-6 text-center">Tgl Kembali</th>
                                <th class="p-6 text-center">Denda</th>
                                <th class="p-6 text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($book->loans as $loan)
                            <tr class="hover:bg-blue-50/20 transition-colors">
                                <td class="p-6">
                                    <div class="text-sm font-black text-gray-800">{{ $loan->user->name }}</div>
                                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Kelas: {{ $loan->user->class ?? '-' }}</div>
                                </td>
                                <td class="p-6 text-center text-xs font-bold text-gray-600">
                                    {{ \Carbon\Carbon::parse($loan->loan_date)->format('d/m/Y') }}
                                </td>
                                <td class="p-6 text-center text-xs font-bold text-gray-600">
                                    {{ $loan->return_date ? \Carbon\Carbon::parse($loan->return_date)->format('d/m/Y') : '—' }}
                                </td>
                                <td class="p-6 text-center">
                                    @if($loan->total_fine > 0)
                                        <span class="text-red-600 font-black text-xs bg-red-50 px-2 py-1 rounded-lg">Rp {{ number_format($loan->total_fine, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-gray-300 font-bold">—</span>
                                    @endif
                                </td>
                                <td class="p-6 text-right">
                                    @if($loan->status == 'overdue' || $loan->total_fine > 0)
                                        <span class="bg-red-500 text-white px-3 py-1 rounded-full text-[9px] font-black uppercase shadow-sm">Terlambat</span>
                                    @elseif($loan->status == 'borrowed')
                                        <span class="bg-amber-400 text-white px-3 py-1 rounded-full text-[9px] font-black uppercase italic shadow-sm">Aktif</span>
                                    @else
                                        <span class="bg-emerald-500 text-white px-3 py-1 rounded-full text-[9px] font-black uppercase shadow-sm">Selesai</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="p-24 text-center">
                                    <div class="flex flex-col items-center opacity-30">
                                        <i class="fas fa-folder-open text-6xl mb-4 text-gray-300"></i>
                                        <p class="font-black italic text-gray-400 uppercase tracking-widest text-sm">Belum ada riwayat</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
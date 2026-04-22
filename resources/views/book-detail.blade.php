<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $book->title }} - E-Library SMPN 1 Bilah Barat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #ecf0f5; 
        }
        .header-top { background-color: #3c8dbc; }
        .btn-primary { background-color: #3c8dbc; transition: all 0.3s; }
        .btn-primary:hover { background-color: #367fa9; }
        /* Style Siku AdminLTE */
        * { border-radius: 0px !important; }
    </style>
</head>
<body class="text-slate-800 pb-20">

    <nav class="header-top p-4 text-white shadow-md mb-8">
        <div class="max-w-7xl mx-auto flex items-center px-4">
            <a href="/" class="hover:underline transition flex items-center font-bold text-xs uppercase tracking-wider">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Katalog
            </a>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4">
        
        <div class="flex flex-col md:flex-row gap-8 mb-8 bg-white p-8 border border-gray-300 shadow-sm">
            
            <div class="w-full md:w-1/4">
                <div class="aspect-[3/4] bg-gray-100 overflow-hidden border border-gray-200 relative">
                    @if($book->cover)
                        <img src="{{ asset('storage/'.$book->cover) }}" class="w-full h-full object-cover">
                    @else
                        <div class="flex flex-col items-center justify-center h-full text-gray-300">
                            <i class="fas fa-image text-6xl opacity-20"></i>
                            <span class="text-[10px] font-bold uppercase mt-4 text-center">Gambar Tidak Tersedia</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="w-full md:w-3/4 flex flex-col">
                <span class="text-blue-600 text-[10px] font-extrabold uppercase tracking-widest mb-2">
                    {{ $book->category->name ?? 'Kategori Umum' }}
                </span>
                
                <h1 class="text-3xl font-black text-gray-900 leading-tight mb-1 uppercase">
                    {{ $book->title }}
                </h1>
                
                <p class="text-lg text-gray-500 font-medium italic mb-6 border-b border-gray-100 pb-4">
                    Oleh: {{ $book->author }}
                </p>

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    <div class="bg-gray-50 p-4 border border-gray-200">
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-1">Tahun Terbit</p>
                        <p class="text-lg font-bold text-gray-800">{{ $book->year ?? '-' }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 border border-gray-200">
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-1">Stok Tersedia</p>
                        <p class="text-lg font-bold text-gray-800">{{ $book->stock }} <span class="text-xs font-normal">Eksemplar</span></p>
                    </div>
                </div>

                <div class="bg-white p-5 border-l-4 border-blue-500 bg-gray-50/50">
                    <h4 class="text-xs font-bold uppercase text-gray-700 tracking-widest mb-3 flex items-center">
                        <i class="fas fa-align-left mr-2 text-blue-600"></i> Ringkasan / Sinopsis
                    </h4>
                    <p class="text-sm text-gray-600 leading-relaxed italic">
                        {{ $book->description ?? 'Deskripsi buku belum tersedia.' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-300 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                <h3 class="font-bold text-gray-800 flex items-center uppercase text-sm tracking-wide">
                    <i class="fas fa-history mr-3 text-blue-600"></i> Riwayat Peminjaman
                </h3>
                <div class="text-[10px] font-bold text-gray-500 uppercase tracking-tight">
                    Total {{ $book->loans->count() }} Kali Dipinjam
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[10px] uppercase font-bold text-gray-500 tracking-wider bg-white border-b border-gray-200">
                            <th class="p-4">Nama Peminjam</th>
                            <th class="p-4 text-center">Tgl Pinjam</th>
                            <th class="p-4 text-center">Tgl Kembali</th>
                            <th class="p-4 text-center">Denda</th>
                            <th class="p-4 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($book->loans as $loan)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="p-4">
                                <div class="text-sm font-bold text-gray-800">{{ $loan->user->name }}</div>
                                <div class="text-[9px] font-semibold text-gray-400 uppercase tracking-tighter">Kelas: {{ $loan->user->class ?? '-' }}</div>
                            </td>
                            <td class="p-4 text-center text-xs font-medium text-gray-600">
                                {{ \Carbon\Carbon::parse($loan->loan_date)->format('d/m/Y') }}
                            </td>
                            <td class="p-4 text-center text-xs font-medium text-gray-600">
                                {{ $loan->return_date ? \Carbon\Carbon::parse($loan->return_date)->format('d/m/Y') : '—' }}
                            </td>
                            <td class="p-4 text-center">
                                {{-- PERBAIKAN: Menghitung akumulasi denda sistem + denda manual --}}
                                @php 
                                    $totalDendaKeseluruhan = ($loan->total_fine ?? 0) + ($loan->additional_fine ?? 0); 
                                @endphp

                                @if($totalDendaKeseluruhan > 0)
                                    <div class="flex flex-col items-center">
                                        <span class="text-red-600 font-bold text-xs">
                                            Rp {{ number_format($totalDendaKeseluruhan, 0, ',', '.') }}
                                        </span>
                                        {{-- Menampilkan alasan denda (Misal: Buku Hilang) --}}
                                        @if($loan->fine_reason)
                                            <span class="text-[8px] text-gray-400 uppercase font-black tracking-tighter leading-none mt-1">
                                                ({{ $loan->fine_reason }})
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="p-4 text-right text-[10px]">
                                @if($loan->status == 'overdue' || ($loan->total_fine + $loan->additional_fine) > 0)
                                    <span class="text-red-600 border border-red-600 px-2 py-0.5 font-bold uppercase">Bermasalah / Denda</span>
                                @elseif($loan->status == 'borrowed')
                                    <span class="text-orange-600 border border-orange-600 px-2 py-0.5 font-bold uppercase">Dipinjam</span>
                                @else
                                    <span class="text-emerald-600 border border-emerald-600 px-2 py-0.5 font-bold uppercase tracking-tight">Dikembalikan</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="p-16 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-info-circle text-3xl mb-3 text-gray-200"></i>
                                    <p class="font-bold text-gray-400 uppercase tracking-widest text-[10px]">Belum ada riwayat peminjaman.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-8 text-center">
            <p class="text-[10px] text-gray-400 uppercase tracking-widest font-bold">©2026 IT E-Library SMPN 1 Bilah Barat</p>
        </div>
    </div>

</body>
</html>
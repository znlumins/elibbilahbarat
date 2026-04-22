<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pinjaman Saya | E-Library</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #ecf0f5; }
        /* Style Navbar sesuai gambar */
        .header-top { background-color: #3c8dbc; height: 50px; display: flex; align-items: center; justify-content: center; }
        .table-container { background: white; border-top: 3px solid #3c8dbc; box-shadow: 0 1px 1px rgba(0,0,0,0.1); }
        [x-cloak] { display: none !important; }
        /* Paksa siku kotak ala AdminLTE */
        * { border-radius: 0px !important; }
    </style>
</head>
<body class="text-slate-700 pb-20" x-data="{ openModal: false, selectedLoan: null, fineAmount: 0, rawFine: 0 }">
    <nav class="header-top p-4 text-white shadow-md mb-8">
        <div class="max-w-7xl mx-auto flex items-center justify-between px-4 w-full">
            
            <a href="/" class="hover:underline transition flex items-center font-bold text-xs uppercase tracking-wider">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Katalog
            </a>

            <div class="flex items-center">
                <span class="text-xs font-bold uppercase tracking-wider opacity-90">
                    <i class="fas fa-user-circle mr-1 text-sm"></i> {{ Auth::user()->name }}
                </span>
            </div>

        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4">
        @if(session('success'))
            <div class="bg-green-500 text-white px-4 py-3 mb-6 shadow-md flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span class="text-xs font-bold uppercase">{{ session('success') }}</span>
            </div>
        @endif

        <div class="flex items-center gap-3 mb-6 text-gray-700 border-b-2 border-gray-300 pb-3">
            <i class="fa-solid fa-history text-blue-600"></i>
            <h3 class="text-xl font-bold uppercase">Riwayat Peminjaman Buku</h3>
        </div>

        <div class="table-container overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[11px] uppercase tracking-wider text-gray-600 border-b bg-gray-50">
                            <th class="p-4 font-bold">Informasi Buku</th>
                            <th class="p-4 font-bold text-center">Tenggat Kembali</th>
                            <th class="p-4 font-bold text-center">Denda</th>
                            <th class="p-4 font-bold text-right">Status & Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($loans as $loan)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="p-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-14 bg-gray-100 shadow-sm overflow-hidden border border-gray-200">
                                        @if($loan->book->cover)
                                            <img src="{{ asset('storage/'.$loan->book->cover) }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="flex items-center justify-center h-full bg-blue-50"><i class="fas fa-book text-blue-200"></i></div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-800 uppercase">{{ $loan->book->title }}</div>
                                        <div class="text-[10px] text-gray-400 italic">Penulis: {{ $loan->book->author }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4 text-center">
                                <div class="text-xs font-bold {{ (\Carbon\Carbon::now()->gt($loan->due_date) && !$loan->is_paid) ? 'text-red-600' : 'text-gray-600' }}">
                                    {{ \Carbon\Carbon::parse($loan->due_date)->format('d/m/Y') }}
                                </div>
                            </td>
                            <td class="p-4 text-center">
                                {{-- Menghitung total denda (sistem + manual) --}}
                                @php 
                                    $totalDenda = ($loan->total_fine ?? 0) + ($loan->additional_fine ?? 0); 
                                @endphp

                                @if($totalDenda > 0 && !$loan->is_paid)
                                    <div class="flex flex-col items-center">
                                        <span class="text-red-600 font-bold text-sm">Rp {{ number_format($totalDenda, 0, ',', '.') }}</span>
                                        @if($loan->fine_reason)
                                            <span class="text-[8px] text-gray-400 uppercase font-bold">({{ $loan->fine_reason }})</span>
                                        @endif
                                    </div>
                                @elseif($loan->is_paid)
                                    <span class="text-green-600 text-[10px] font-bold uppercase bg-green-50 px-2 py-1 border border-green-200">Lunas</span>
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="p-4 text-right">
                                @if(!$loan->is_paid && (($loan->total_fine + $loan->additional_fine) > 0))
                                    <button @click="openModal = true; selectedLoan = {{ $loan->id }}; fineAmount = '{{ number_format($totalDenda, 0, ',', '.') }}'; rawFine = {{ $totalDenda }}" 
                                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-[10px] font-bold uppercase transition shadow-sm">
                                        {{ $loan->payment_proof ? 'Edit Bukti' : 'Bayar Denda' }}
                                    </button>
                                @elseif($loan->is_paid)
                                    <span class="text-gray-400 text-[9px] font-bold uppercase italic border border-gray-200 px-2 py-1">Selesai</span>
                                @else
                                    <span class="text-gray-400 text-[9px] font-bold uppercase italic">Aktif</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="p-16 text-center text-gray-400 uppercase text-xs">Belum ada riwayat peminjaman</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="mt-8 text-center">
            <p class="text-[10px] text-gray-400 uppercase tracking-widest font-bold">©2026 IT E-Library SMPN 1 Bilah Barat</p>
        </div>
    </div>

    <div x-show="openModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" x-cloak>
        <div class="bg-white shadow-2xl max-w-sm w-full border-t-4 border-blue-600 relative" @click.away="openModal = false">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-sm font-bold text-gray-800 uppercase">Konfirmasi Pembayaran</h3>
                    <button @click="openModal = false" class="text-gray-400 hover:text-red-500 font-bold text-xl">&times;</button>
                </div>

                <div class="bg-gray-50 p-4 border text-center mb-4">
                    <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Total Denda</p>
                    <p class="text-2xl font-black text-blue-600">Rp <span x-text="fineAmount"></span></p>
                </div>

                @if($qris && $qris->value)
                <div class="mb-6 p-2 border-2 border-dashed border-gray-200 text-center bg-white">
                    <p class="text-[9px] font-bold text-gray-500 uppercase mb-2">Scan QRIS Untuk Membayar</p>
                    <img src="{{ asset('storage/' . $qris->value) }}" alt="QRIS" class="mx-auto w-48 h-auto">
                </div>
                @endif

                <form action="{{ route('payment.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="loan_id" :value="selectedLoan">
                    <input type="hidden" name="amount_paid" :value="rawFine">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Metode</label>
                            <select name="payment_method" class="w-full border rounded-none px-3 py-2 text-xs font-bold">
                                <option value="qris">QRIS / Transfer</option>
                                <option value="cash">Tunai</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Upload Bukti</label>
                            <input type="file" name="proof_file" required class="w-full text-[10px]">
                        </div>
                        <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 text-[11px] uppercase tracking-widest hover:bg-blue-700 transition">
                            Kirim Konfirmasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
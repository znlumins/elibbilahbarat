<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pinjaman Saya - E-Library</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 pb-20" x-data="{ openModal: false, selectedLoan: null, fineAmount: 0, rawFine: 0 }">

    <nav class="bg-blue-800 p-4 text-white shadow-lg mb-8">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-6">
                <a href="/" class="flex items-center group bg-blue-700 hover:bg-blue-600 px-4 py-2 rounded-xl transition-all duration-300 shadow-inner">
                    <i class="fas fa-home mr-2 group-hover:-translate-y-0.5 transition-transform"></i>
                    <span class="font-medium text-sm">Beranda</span>
                </a>
                
                <div class="h-6 w-px bg-blue-400 opacity-50 hidden md:block"></div>
                
                <h1 class="font-bold text-lg hidden md:block tracking-tight">Riwayat Peminjaman</h1>
            </div>

            <div class="flex items-center space-x-3 bg-blue-900/30 px-4 py-2 rounded-full border border-blue-400/20">
                <i class="fas fa-user-circle text-blue-200"></i>
                <span class="text-sm font-medium tracking-wide">{{ Auth::user()->name }}</span>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4">
        @if(session('success'))
            <div class="bg-emerald-500 text-white px-5 py-4 rounded-2xl mb-8 shadow-xl shadow-emerald-200 flex items-center animate-bounce">
                <i class="fas fa-check-circle mr-3 text-xl"></i>
                <span class="font-bold">{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-[10px] uppercase tracking-[0.15em] text-gray-400 border-b bg-gray-50/50">
                            <th class="p-6 font-black">Informasi Buku</th>
                            <th class="p-6 font-black text-center">Tenggat</th>
                            <th class="p-6 font-black text-center">Denda</th>
                            <th class="p-6 font-black text-right">Aksi & Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($loans as $loan)
                        <tr class="hover:bg-blue-50/30 transition-colors duration-300">
                            <td class="p-6">
                                <div class="flex items-center">
                                    <div class="w-12 h-16 bg-gray-100 rounded-lg mr-4 flex-shrink-0 overflow-hidden shadow-sm">
                                        @if($loan->book->cover)
                                            <img src="{{ asset('storage/'.$loan->book->cover) }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="flex items-center justify-center h-full bg-blue-50"><i class="fas fa-book text-blue-200"></i></div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-800 leading-tight mb-1">{{ $loan->book->title }}</div>
                                        <div class="text-[11px] text-gray-400 flex items-center">
                                            <i class="fas fa-pen-nib mr-1 text-[10px]"></i> {{ $loan->book->author }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="p-6 text-center">
                                <div class="text-xs font-medium {{ $loan->hari_telat > 0 && !$loan->is_paid ? 'text-red-500' : 'text-gray-600' }}">
                                    {{ \Carbon\Carbon::parse($loan->due_date)->format('d M Y') }}
                                </div>
                                @if($loan->hari_telat > 0 && !$loan->is_paid)
                                    <span class="text-[10px] bg-red-50 text-red-600 px-2 py-0.5 rounded-md font-bold mt-1 inline-block">Telat {{ $loan->hari_telat }} Hari</span>
                                @endif
                            </td>
                            <td class="p-6 text-center">
                                @if($loan->total_denda > 0 && !$loan->is_paid)
                                    <div class="flex flex-col">
                                        <span class="text-red-600 font-black text-sm tracking-tight">Rp {{ number_format($loan->total_denda, 0, ',', '.') }}</span>
                                        @if($loan->additional_fine > 0)
                                            <span class="text-[9px] text-orange-500 font-bold uppercase mt-1 italic">({{ $loan->fine_reason ?? 'Denda Tambahan' }})</span>
                                        @endif
                                    </div>
                                @elseif($loan->is_paid)
                                    <span class="text-emerald-500 text-xs font-black tracking-widest uppercase italic"><i class="fas fa-check-circle mr-1"></i>Lunas</span>
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="p-6 text-right">
                                @if(!$loan->is_paid)
                                    @if($loan->payment_proof)
                                        <button @click="openModal = true; selectedLoan = {{ $loan->id }}; fineAmount = '{{ number_format($loan->total_denda, 0, ',', '.') }}'; rawFine = {{ $loan->total_denda }}" 
                                                class="group bg-orange-500 hover:bg-orange-600 text-white px-5 py-2 rounded-xl text-xs font-bold transition-all shadow-lg shadow-orange-100 flex items-center ml-auto">
                                            <i class="fas fa-edit mr-2 group-hover:rotate-12 transition-transform"></i> Edit Bukti
                                        </button>
                                        <span class="text-[9px] text-orange-400 font-bold uppercase tracking-tighter mt-2 block">Menunggu Verifikasi Admin</span>
                                    @elseif($loan->total_denda > 0)
                                        <button @click="openModal = true; selectedLoan = {{ $loan->id }}; fineAmount = '{{ number_format($loan->total_denda, 0, ',', '.') }}'; rawFine = {{ $loan->total_denda }}" 
                                                class="group bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-xl text-xs font-bold transition-all shadow-lg shadow-red-100 flex items-center ml-auto">
                                            <i class="fas fa-qrcode mr-2 group-hover:scale-110 transition-transform"></i> Bayar Sekarang
                                        </button>
                                    @endif
                                @else
                                    <div class="inline-flex items-center bg-gray-50 px-4 py-2 rounded-xl border border-gray-100 text-gray-400 text-[10px] font-black uppercase">
                                        <i class="fas fa-lock mr-2"></i> Transaksi Selesai
                                    </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="p-20 text-center">
                                <div class="bg-blue-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-ghost text-blue-200 text-3xl"></i>
                                </div>
                                <p class="text-gray-400 text-sm font-medium">Ops! Kamu belum memiliki riwayat peminjaman.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div x-show="openModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-blue-900/60 backdrop-blur-md" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
        <div class="bg-white rounded-[2.5rem] max-w-md w-full p-8 shadow-2xl relative overflow-hidden" @click.away="openModal = false">
            
            <div class="flex justify-between items-center mb-8">
                <h3 class="text-xl font-black text-gray-800 tracking-tight">Konfirmasi Bayar</h3>
                <button @click="openModal = false" class="bg-gray-100 hover:bg-gray-200 text-gray-400 w-8 h-8 rounded-full flex items-center justify-center transition-colors">&times;</button>
            </div>

            <div class="text-center mb-8">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Nominal Denda</p>
                <p class="text-4xl font-black text-red-600 tracking-tighter">Rp <span x-text="fineAmount"></span></p>
            </div>

            <form action="{{ route('payment.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="loan_id" :value="selectedLoan">
                <input type="hidden" name="amount_paid" :value="rawFine">
                
                <div class="space-y-5">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Pilih Metode</label>
                        <select name="payment_method" class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-blue-500 transition-all">
                            <option value="qris">QRIS / Transfer Bank</option>
                            <option value="cash">Bayar Tunai di Perpustakaan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Upload Bukti</label>
                        <div class="relative group">
                            <input type="file" name="proof_file" required class="w-full text-xs text-gray-500 file:mr-4 file:py-3 file:px-6 file:rounded-2xl file:border-0 file:text-xs file:font-black file:bg-blue-600 file:text-white hover:file:bg-blue-700 transition-all cursor-pointer">
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-blue-800 hover:bg-blue-900 text-white font-black py-4 rounded-2xl transition-all shadow-xl shadow-blue-200 flex items-center justify-center transform active:scale-95">
                        <i class="fas fa-paper-plane mr-2 text-sm"></i> Kirim Konfirmasi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>

</body>
</html>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Peminjaman | E-Library</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #ecf0f5; }
        .header-top { background-color: #3c8dbc; height: 50px; display: flex; align-items: center; }
        .table-container { background: white; border-top: 3px solid #3c8dbc; }
        [x-cloak] { display: none !important; }
        /* Style kaku ala sistem akademik sekolah */
        * { border-radius: 0px !important; }
    </style>
</head>
<body class="text-slate-700 pb-20" x-data="{ openModal: false, selectedLoan: null, fineAmount: 0, rawFine: 0, paymentMethod: 'qris' }">

    <nav class="header-top p-4 text-white shadow-md mb-8">
        <div class="max-w-7xl mx-auto flex items-center justify-between px-4 w-full">
            <a href="/" class="hover:underline transition flex items-center font-bold text-xs uppercase tracking-wider">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
            <div class="text-xs font-bold uppercase tracking-wider opacity-90">{{ Auth::user()->name }}</div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4">
        @if(session('success'))
            <div class="bg-green-600 text-white px-4 py-3 mb-6 shadow-md font-bold text-xs uppercase">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            </div>
        @endif

        <div class="table-container overflow-hidden">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-[11px] uppercase text-gray-600 border-b bg-gray-50">
                        <th class="p-4">Buku</th>
                        <th class="p-4 text-center">Tenggat</th>
                        <th class="p-4 text-center">Denda</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($loans as $loan)
                    @php
                        $setting = \App\Models\Setting::where('key', 'fine_per_day')->first();
                        $finePerDay = $setting ? (int)$setting->value : 0;
                        $dueDate = \Carbon\Carbon::parse($loan->due_date)->startOfDay();
                        $today = \Carbon\Carbon::now()->startOfDay();
                        $daysDiff = $today->diffInDays($dueDate, false);
                        $daysLate = $daysDiff < 0 ? abs($daysDiff) : 0;
                        $totalDenda = ($daysLate * $finePerDay) + ($loan->additional_fine ?? 0);
                    @endphp
                    <tr>
                        <td class="p-4 font-bold text-sm uppercase">{{ $loan->book->title }}</td>
                        <td class="p-4 text-center text-xs">{{ $dueDate->format('d/m/Y') }}</td>
                        <td class="p-4 text-center font-bold text-red-600">
                            {{ $totalDenda > 0 ? 'Rp ' . number_format($totalDenda, 0, ',', '.') : '-' }}
                        </td>
                        <td class="p-4 text-right">
                            @if(!$loan->is_paid && $totalDenda > 0)
                                <button @click="openModal = true; selectedLoan = {{ $loan->id }}; fineAmount = '{{ number_format($totalDenda, 0, ',', '.') }}'; rawFine = {{ $totalDenda }}" 
                                        class="bg-blue-600 text-white px-4 py-2 text-[10px] font-bold uppercase hover:bg-blue-700 transition">
                                    Bayar Denda
                                </button>
                            @else
                                <span class="text-gray-400 text-[10px] uppercase italic">Lunas / Aktif</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="p-8 text-center text-gray-400 text-xs">Belum ada riwayat peminjaman</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL PEMBAYARAN -->
    <div x-show="openModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" x-cloak>
        <div class="bg-white max-w-sm w-full p-6 border-t-4 border-blue-600 shadow-2xl" @click.away="openModal = false">
            <h3 class="text-sm font-bold uppercase mb-4 text-center">Form Konfirmasi Pembayaran</h3>
            
            <!-- Info Nominal -->
            <div class="mb-4 text-center bg-blue-50 py-3 border border-blue-100">
                <span class="text-[10px] text-blue-600 font-bold uppercase">Total Tagihan</span>
                <div class="text-xl font-bold text-red-600" x-text="'Rp ' + fineAmount"></div>
            </div>

            <!-- Media Instruksi (QRIS atau Cash) -->
            <div class="mb-6 p-2 border border-dashed border-gray-300 text-center bg-gray-50">
                <div x-show="paymentMethod === 'qris'">
                    @php $qris = \App\Models\Setting::where('key', 'qris_image')->first(); @endphp
                    @if($qris && $qris->value)
                        <!-- Tampilan QRIS yang diperbesar agar mudah discan -->
                        <img src="{{ asset('storage/' . $qris->value) }}" alt="QRIS" class="mx-auto w-64 h-auto shadow-sm border p-1 bg-white">
                        <p class="text-[9px] text-gray-500 mt-2 uppercase">Scan menggunakan e-wallet atau mobile banking</p>
                    @else
                        <p class="text-red-500 text-[10px] py-4">Pengaturan QRIS belum tersedia.</p>
                    @endif
                </div>

                <div x-show="paymentMethod === 'cash'" class="text-center p-4">
                    <i class="fas fa-camera text-3xl text-orange-500 mb-2"></i>
                    <p class="text-[10px] font-bold text-gray-500 uppercase mb-1">Instruksi Bayar di Tempat:</p>
                    <p class="text-xs text-gray-800 leading-relaxed">Berikan uang tunai kepada petugas, lalu <strong>foto uang tersebut</strong> atau kuitansi dari petugas untuk diunggah di bawah ini.</p>
                </div>
            </div>
            
            <form action="{{ route('payment.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="loan_id" :value="selectedLoan">
                
                <!-- Pilihan Metode -->
                <div class="mb-4">
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Pilih Metode Pembayaran</label>
                    <select name="payment_method" x-model="paymentMethod" required class="w-full border p-2 text-xs focus:outline-none focus:border-blue-600">
                        <option value="qris">Pembayaran Digital (QRIS)</option>
                        <option value="cash">Bayar Tunai di Perpustakaan</option>
                    </select>
                </div>
                
                <!-- Wajib Upload Bukti untuk semua metode -->
                <div class="mb-6">
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">
                        Upload Bukti Foto <span class="text-red-500">*</span>
                    </label>
                    <input type="file" name="proof_file" required class="w-full border p-2 text-xs bg-white file:mr-4 file:py-1 file:px-2 file:border-0 file:text-[10px] file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-700">
                    <p class="text-[9px] text-gray-400 mt-1 italic leading-tight">Meskipun bayar cash, wajib upload foto bukti fisik agar denda lunas di sistem.</p>
                </div>
                
                <div class="flex gap-2">
                    <button type="button" @click="openModal = false" class="w-1/3 bg-gray-200 text-gray-700 font-bold py-3 text-[11px] uppercase tracking-widest hover:bg-gray-300">
                        Batal
                    </button>
                    <button type="submit" class="w-2/3 bg-blue-600 text-white font-bold py-3 text-[11px] uppercase tracking-widest hover:bg-blue-700 shadow-lg transition">
                        Kirim Konfirmasi
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
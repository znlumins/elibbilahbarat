<!DOCTYPE html>
<html>
<head>
    <title>Laporan Perpustakaan Lengkap</title>
    <style>
        /* Pengaturan Halaman */
        @page { 
            margin: 0cm; 
        }
        body { 
            font-family: 'Helvetica', sans-serif; 
            font-size: 10px; 
            color: #333; 
            margin-top: 4.8cm; /* Jarak pas di bawah garis kop surat */
            margin-left: 1.5cm; 
            margin-right: 1.5cm; 
            margin-bottom: 2cm;
        }

        /* Kop Surat sebagai Background Fixed */
        .kop-surat { 
            position: fixed; 
            top: 0; 
            left: 0; 
            width: 100%; 
            z-index: -1000; 
        }
        
        /* Judul Laporan */
        .title-rekap { text-align: center; font-size: 14px; font-weight: bold; text-decoration: underline; margin-bottom: 5px; text-transform: uppercase; }
        .subtitle { text-align: center; font-size: 10px; margin-bottom: 15px; }

        /* Styling Tabel */
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; table-layout: fixed; }
        th, td { border: 1px solid #444; padding: 5px; word-wrap: break-word; }
        th { background-color: #f0f0f0; text-transform: uppercase; font-size: 8px; font-weight: bold; }
        
        /* Judul Bagian/Seksi */
        .section-title { background-color: #1e40af; color: white; padding: 4px 10px; font-weight: bold; margin-bottom: 8px; font-size: 9px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        /* Badge Status */
        .badge { padding: 2px 4px; border-radius: 3px; font-size: 7px; font-weight: bold; }
        .bg-success { background-color: #dcfce7; color: #166534; }
        .bg-danger { background-color: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    @php
        // Path gambar kop surat (format .jpg sesuai permintaan sebelumnya)
        $path = public_path('images/kop-surat.jpg');
        $base64 = "";
        if(file_exists($path)){
            $data = file_get_contents($path);
            $base64 = 'data:image/jpeg;base64,' . base64_encode($data);
        }
    @endphp
    
    @if($base64)
        <img src="{{ $base64 }}" class="kop-surat">
    @endif

    <div class="title-rekap">Laporan Manajemen & Inventaris Perpustakaan</div>
    <div class="subtitle">Periode Data Per Tanggal: {{ date('d F Y') }}</div>

    <div class="section-title">A. DAFTAR KOLEKSI BUKU (INVENTARIS)</div>
    <table>
        <thead>
            <tr>
                <th width="5%">NO</th>
                <th width="40%">JUDUL BUKU</th>
                <th width="25%">PENGARANG</th>
                <th width="20%">PENERBIT</th>
                <th width="10%" class="text-center">STOK</th>
            </tr>
        </thead>
        <tbody>
            @forelse($books as $index => $b)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td><strong>{{ $b->title }}</strong> ({{ $b->year ?? '-' }})</td>
                <td>{{ $b->author }}</td>
                <td>{{ $b->publisher ?? '-' }}</td>
                <td class="text-center">{{ $b->stock }} Eks</td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center">Belum ada koleksi buku terdaftar.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">B. PERINGKAT 10 SISWA TERAKTIF (TOP BORROWERS)</div>
    <table>
        <thead>
            <tr>
                <th width="8%">NO</th>
                <th width="42%">NAMA SISWA</th>
                <th width="20%">KELAS</th>
                <th width="30%">TOTAL PEMINJAMAN</th>
            </tr>
        </thead>
        <tbody>
            @forelse($top_borrowers as $index => $t)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $t->name }}</td>
                <td class="text-center">{{ $t->class ?? '-' }}</td>
                <td class="text-center"><strong>{{ $t->loans_count }}</strong> Kali Meminjam</td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center">Belum ada riwayat peminjaman.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">C. REKAPITULASI PENDAPATAN DENDA (LUNAS)</div>
    <table>
        <thead>
            <tr>
                <th width="25%">PEMINJAM</th>
                <th width="30%">JUDUL BUKU</th>
                <th width="15%" class="text-center">METODE</th>
                <th width="30%" class="text-right">TOTAL DENDA</th>
            </tr>
        </thead>
        <tbody>
            @php $total_masuk = 0; @endphp
            @forelse($data_lunas as $l)
                @php $total_masuk += ($l->total_fine + $l->additional_fine); @endphp
                <tr>
                    <td>{{ $l->user->name }}</td>
                    <td>{{ $l->book->title }}</td>
                    <td class="text-center">
                        <span class="badge bg-success">{{ strtoupper($l->payment_method ?? 'CASH') }}</span>
                    </td>
                    <td class="text-right">Rp {{ number_format($l->total_fine + $l->additional_fine, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center">Tidak ada denda lunas yang tercatat.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f8fafc;">
                <th colspan="3" class="text-right">TOTAL PENDAPATAN DITERIMA</th>
                <th class="text-right" style="color: #166534;">Rp {{ number_format($total_masuk, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>

    <div class="section-title">D. DAFTAR TUNGGAKAN & PIUTANG (BELUM BAYAR)</div>
    <table>
        <thead>
            <tr>
                <th width="30%">NAMA SISWA</th>
                <th width="30%">JUDUL BUKU</th>
                <th width="15%" class="text-center">TENGGAT</th>
                <th width="25%" class="text-right">ESTIMASI DENDA</th>
            </tr>
        </thead>
        <tbody>
            @php $total_piutang = 0; @endphp
            @forelse($data_hutang as $h)
                @php $total_piutang += ($h->total_fine + $h->additional_fine); @endphp
                <tr>
                    <td>{{ $h->user->name }}</td>
                    <td>{{ $h->book->title }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($h->due_date)->format('d/m/y') }}</td>
                    <td class="text-right">Rp {{ number_format($h->total_fine + $h->additional_fine, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center">Tidak ada tunggakan saat ini.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #fff1f2;">
                <th colspan="3" class="text-right">TOTAL ESTIMASI PIUTANG</th>
                <th class="text-right" style="color: #991b1b;">Rp {{ number_format($total_piutang, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 30px;">
        <table style="border: none;">
            <tr style="border: none;">
                <td style="border: none; width: 65%;"></td>
                <td style="border: none; text-align: center;">
                    Dicetak pada: {{ date('d/m/Y, H:i') }}<br>
                    Petugas Perpustakaan SMPN 1 Bilah Barat,<br><br><br><br>
                    <strong>( .................................... )</strong>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
<!DOCTYPE html>
<html>
<head>
    <title>Laporan Perpustakaan</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .header { text-align: center; margin-bottom: 30px; }
        h2 { margin-bottom: 5px; color: #1e40af; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN REKAP PERPUSTAKAAN</h2>
        <p>Tanggal Cetak: {{ date('d M Y H:i') }}</p>
    </div>

    <h3>A. Top 10 Siswa Terajin</h3>
    <table>
        <tr>
            <th>Nama Siswa</th>
            <th>Kelas</th>
            <th>Total Pinjam</th>
        </tr>
        @foreach($top_borrowers as $t)
        <tr>
            <td>{{ $t->name }}</td>
            <td>{{ $t->class ?? '-' }}</td>
            <td>{{ $t->loans_count }} Buku</td>
        </tr>
        @endforeach
    </table>

    <h3>B. Data Denda Lunas (Pendapatan)</h3>
    <table>
        <tr>
            <th>Siswa</th>
            <th>Buku</th>
            <th class="text-right">Total Bayar</th>
        </tr>
        @php $total_masuk = 0; @endphp
        @forelse($data_lunas as $l)
            @php $total_masuk += $l->total_denda; @endphp
            <tr>
                <td>{{ $l->user->name }}</td>
                <td>{{ $l->book->title }}</td>
                <td class="text-right">Rp {{ number_format($l->total_denda, 0, ',', '.') }}</td>
            </tr>
        @empty
            <tr><td colspan="3" style="text-align:center">Belum ada denda lunas</td></tr>
        @endforelse
        <tr>
            <th colspan="2" class="text-right">TOTAL TERIMA</th>
            <th class="text-right">Rp {{ number_format($total_masuk, 0, ',', '.') }}</th>
        </tr>
    </table>

    <h3>C. Data Piutang (Belum Bayar)</h3>
    <table>
        <tr>
            <th>Siswa</th>
            <th>Status</th>
            <th class="text-right">Estimasi Denda</th>
        </tr>
        @foreach($data_hutang as $h)
        <tr>
            <td>{{ $h->user->name }}</td>
            <td>{{ $h->return_date ? 'Sudah Kembali (Belum Bayar)' : 'Masih Dipinjam (Terlambat)' }}</td>
            <td class="text-right">Rp {{ number_format($h->total_denda, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </table>
</body>
</html>
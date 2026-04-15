<table class="table table-bordered">
    <thead>
        <tr>
            <th>Nama Siswa</th>
            <th>Judul Buku</th>
            <th>Jatuh Tempo</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($transactions as $t)
        <tr>
            <td>{{ $t->student->name }}</td>
            <td>{{ $t->book->title }}</td>
            <td>{{ $t->due_date }}</td>
            <td>
                <span class="badge {{ $t->status == 'borrowed' ? 'bg-warning' : 'bg-success' }}">
                    {{ $t->status }}
                </span>
            </td>
            <td>
                @if($t->status == 'borrowed')
                    <form action="/return/{{ $t->id }}" method="POST">
                        @csrf
                        <button class="btn btn-primary btn-sm">Kembalikan & Hitung Denda</button>
                    </form>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
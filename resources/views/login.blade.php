<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Siswa - SMPN 1 Bilah Barat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center relative">

    <a href="/" class="absolute top-6 left-6 flex items-center text-gray-500 hover:text-blue-600 font-bold text-sm transition-all group">
        <i class="fas fa-arrow-left mr-2 transition-transform group-hover:-translate-x-1"></i> 
        Kembali ke Beranda
    </a>

    <div class="bg-white p-8 rounded-lg shadow-md w-96">
        <h2 class="text-2xl font-bold mb-6 text-center text-blue-600">Login Siswa</h2>
        <form action="/login" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block mb-2 font-semibold">NIS (Nomor Induk Siswa)</label>
                <input type="text" name="login_data" value="{{ old('login_data') }}" 
                    class="w-full border p-2 rounded focus:outline-none focus:border-blue-500" 
                    placeholder="Masukkan NIS" required>
            </div>
            <div class="mb-6">
                <label class="block mb-2 font-semibold">Password</label>
                <input type="password" name="password" 
                    class="w-full border p-2 rounded focus:outline-none focus:border-blue-500" 
                    placeholder="Masukkan Password" required>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded font-bold hover:bg-blue-700 transition">
                Masuk ke Perpustakaan
            </button>
        </form>

        @if($errors->any())
            <p class="text-red-500 mt-4 text-sm">{{ $errors->first() }}</p>
        @endif
    </div>
</body>
</html>
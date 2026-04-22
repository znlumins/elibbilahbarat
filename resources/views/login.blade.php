<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PERPUSTAKAAN | Login Siswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow: hidden;
        }

        /* Container Slideshow */
        .bg-slideshow {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background-color: #000;
        }

        /* Slide Style - Gambar Anti Pecah */
        .bg-slide {
            position: absolute;
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            opacity: 0;
            transition: opacity 1.2s ease-in-out;
            transform: scale(1.1);
        }

        .bg-slide.active {
            opacity: 1;
            transform: scale(1);
            transition: opacity 1.2s ease-in-out, transform 5s linear;
        }

        /* Overlay Gelap */
        .bg-overlay {
            position: absolute;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            z-index: 1;
        }

        .login-box {
            width: 380px;
            background-color: rgba(255, 255, 255, 0.98);
            z-index: 2;
        }

        .btn-masuk {
            background-color: #3c8dbc;
        }

        .btn-masuk:hover {
            background-color: #367fa9;
        }

        .back-link {
            position: fixed;
            top: 25px;
            left: 25px;
            z-index: 10;
        }
    </style>
</head>
<body class="flex flex-col items-center justify-center">

    <div class="bg-slideshow">
        <div class="bg-slide active" style="background-image: url('https://lh3.googleusercontent.com/gps-cs-s/APNQkAHGoHP0-Vm6JbEmmcc266Uy7F1VcvGUhx8kIEKsTMeGCDXy86JU86_MMnglb0qx6kegz9EN3o4J5MyUIQMUU5Gk3nbLJV56yT_G_5rXz2KUfMzCxNOFbEh5jGQAqNXLiVVw0Es0kQ=w408-h544-k-no')"></div>
        <div class="bg-slide" style="background-image: url('https://lh3.googleusercontent.com/gps-cs-s/APNQkAF6Ne3Gx8UpK3gUk32AbS_1bbJS5QD5rS6ZeK0eWtM7QXM_XOjGDtN_w-aMAIbKhbWzHfjLOzILpKs8snubiP3X6HdBJCdjz5Mdo2FAgkdKxbqelP-Wgbv85cojE8QdZX0KkIOBNQ')"></div>
        <div class="bg-slide" style="background-image: url('https://lh3.googleusercontent.com/gps-cs-s/APNQkAGwyX3yYGeyLbMF1bpeL8-xgKOEbq6DhWjD9Stlb1ivUEWrKtHdK2O6e6Yh6IdUdKPtVoA7Pwx7ikw9RG96H0UOV9EMYwNu45CAWqEWjSHrq_4EbjP3KlkvPkV9Mxy_qjkvilF6')"></div>
        <div class="bg-overlay"></div>
    </div>

    <div class="back-link">
        <a href="/" class="flex items-center text-white/90 hover:text-white transition-all gap-2 text-xs font-bold group">
            <i class="fa-solid fa-arrow-left transition-transform group-hover:-translate-x-1"></i> 
            KEMBALI
        </a>
    </div>

    <div class="mb-6 text-center z-10">
        <h1 class="text-4xl font-bold text-white uppercase drop-shadow-lg tracking-normal">
            PERPUSTAKAAN
        </h1>
    </div>

    <div class="login-box p-8 rounded shadow-2xl border-t-4 border-blue-500 relative">
        
        <div class="flex justify-center mb-8">
            <img src="{{ asset('Logo-Tut-Wuri-Handayani-PNG-Warna.png') }}" alt="Logo" class="h-24 w-auto">
        </div>

        <form action="/login" method="POST" id="formLoginSiswa">
            @csrf
            
            <div class="relative mb-5">
                <input type="text" name="login_data" id="inputUser" value="{{ old('login_data') }}" 
                    class="w-full border-b-2 border-gray-200 p-2 pr-10 focus:outline-none focus:border-blue-500 transition-all placeholder-gray-400" 
                    placeholder="Nama Pengguna (NIS)" required>
                <i class="fa-solid fa-user absolute right-2 top-3 text-gray-300"></i>
            </div>

            <div class="relative mb-8">
                <input type="password" name="password" 
                    class="w-full border-b-2 border-gray-200 p-2 pr-10 focus:outline-none focus:border-blue-500 transition-all placeholder-gray-400" 
                    placeholder="Kata Sandi" required>
                <i class="fa-solid fa-lock absolute right-2 top-3 text-gray-300"></i>
            </div>

            <button type="submit" class="w-full btn-masuk text-white py-3 rounded shadow hover:shadow-lg transition-all font-bold uppercase tracking-widest">
                LOGIN
            </button>
        </form>

        <div id="errorWrapper">
            @if($errors->any())
                <div class="mt-4 p-2 bg-red-50 text-red-600 text-[11px] rounded border border-red-100 text-center font-bold">
                    {{ $errors->first() }}
                </div>
            @endif
        </div>
    </div>

    <script>
        // Logika Slideshow
        let slides = document.querySelectorAll('.bg-slide');
        let currentSlide = 0;

        function nextSlide() {
            slides[currentSlide].classList.remove('active');
            currentSlide = (currentSlide + 1) % slides.length;
            slides[currentSlide].classList.add('active');
        }

        setInterval(nextSlide, 5000);

        // Logika Blokir Admin di Frontend
        const form = document.getElementById('formLoginSiswa');
        const userInput = document.getElementById('inputUser');
        const errorWrapper = document.getElementById('errorWrapper');

        form.addEventListener('submit', function(e) {
            const username = userInput.value.toLowerCase();
            
            // Cek jika input mengandung kata 'admin'
            if (username.includes('admin')) {
                e.preventDefault(); // Batalkan pengiriman form
                
                // Tampilkan pesan error palsu (seolah dari database)
                errorWrapper.innerHTML = `
                    <div class="mt-4 p-2 bg-red-50 text-red-600 text-[11px] rounded border border-red-100 text-center font-bold italic">
                        NAMA PENGGUNA ATAU KATA SANDI TIDAK DITEMUKAN DALAM DATABASE SISWA.
                    </div>
                `;
                
                // Kosongkan input password untuk keamanan tambahan
                form.querySelector('input[type="password"]').value = '';
            }
        });
    </script>

</body>
</html>
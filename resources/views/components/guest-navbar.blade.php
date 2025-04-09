<!-- Guest Navbar Component -->
<nav class="flex justify-between items-center px-6 py-4 bg-transparent backdrop-blur-lg shadow-none text-black">
    <div class="text-2xl font-bold">Kependudukan</div>
    <ul class="flex gap-6 font-medium">
        <li><a href="#pelayanan" class="hover:underline">Pelayanan</a></li>
        <li><a href="#harga" class="hover:underline">Harga</a></li>
        <li><a href="#tentang" class="hover:underline">Tentang</a></li>
    </ul>
    <div class="flex gap-2">
        <a href="{{ route('login') }}" class="bg-white text-black px-5 py-2 rounded-full shadow hover:opacity-80 inline-block">Login</a>
        <a href="{{ route('register') }}" class="bg-[#969BE7] text-white px-5 py-2 rounded-full shadow-md hover:shadow-lg hover:opacity-90 transition duration-200 inline-block">Daftar</a>
    </div>
</nav>

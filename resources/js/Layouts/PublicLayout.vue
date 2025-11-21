<script setup>
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>

<Head>
  <title>Üzemanyag</title>
</Head>

<header class="bg-white h-16 flex justify-center items-center">
  <div class="md:hidden w-12" id="nav-btn">
    <img src="" alt="Hamburger menü">
  </div>
  <nav class="hidden md:block">
    <ul class="flex gap-7 font-medium">
      <Link href="#" class="hover:text-gray-500 transition ease-in-out duration-150"><li>Főoldal</li></Link>
      <Link href="#" class="hover:text-gray-500 transition ease-in-out duration-150"><li>Kocsi adatai</li></Link>
      <Link href="#" class="hover:text-gray-500 transition ease-in-out duration-150"><li>Üzemanyag nyilvántartó</li></Link>
      <Link href="#" class="hover:text-gray-500 transition ease-in-out duration-150"><li>Szerviznapló</li></Link>
      <Link href="#" class="hover:text-gray-500 transition ease-in-out duration-150"><li>Biztosítási adatok</li></Link>
    </ul>
  </nav>
</header>

<nav class="bg-white flex md:hidden items-center px-2 max-h-0 overflow-hidden" id="mobile-nav">
  <ul class="flex flex-col gap-2 font-medium w-full">
    <Link href="#" class="hover:text-gray-500 transition ease-in-out duration-150 menu-btn"><li>Főoldal</li></Link>
    <Link href="#" class="hover:text-gray-500 transition ease-in-out duration-150 menu-btn"><li>Kocsi adatai</li></Link>
    <Link href="#" class="hover:text-gray-500 transition ease-in-out duration-150 menu-btn"><li>Üzemanyag nyilvántartó</li></Link>
    <Link href="#" class="hover:text-gray-500 transition ease-in-out duration-150 menu-btn"><li>Szerviznapló</li></Link>
    <Link href="#" class="hover:text-gray-500 transition ease-in-out duration-150 menu-btn"><li>Biztosítási adatok</li></Link>
  </ul>
</nav>

<main>
    <slot/>
</main>

<footer>
  <div class="bg-white h-28 flex justify-center items-center">Minden jog fenntartva 2025 PETER</div>
</footer>

</template>

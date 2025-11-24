<script setup>
import { Head, Link, router } from '@inertiajs/vue3';



const logout = () => {
  router.post(route('logout'));
};
</script>

<template>

<Head>
  <title>Üzemanyag</title>
</Head>

<header class="bg-white h-16 flex justify-center items-center">
  <div class="md:hidden w-12" id="nav-btn">
    <img src="/images/hamburger-menu-closed.svg" alt="Hamburger menü">
  </div>
  <nav class="hidden md:block">
    <ul class="flex gap-7 font-medium">
      <Link href="/" class="hover:text-gray-500 transition ease-in-out duration-150"><li>Főoldal</li></Link>
      <Link href="#" class="hover:text-gray-500 transition ease-in-out duration-150"><li>Kocsi adatai</li></Link>
      <Link href="/fuel-tracker" class="hover:text-gray-500 transition ease-in-out duration-150"><li>Üzemanyag nyilvántartó</li></Link>
      <Link href="#" class="hover:text-gray-500 transition ease-in-out duration-150"><li>Szerviznapló</li></Link>
      <Link href="#" class="hover:text-gray-500 transition ease-in-out duration-150"><li>Biztosítási adatok</li></Link>
      <form @submit.prevent="logout">
        <button type="submit">Kijelentkezés</button>
      </form>
    </ul>
  </nav>
</header>

<nav class="bg-white flex md:hidden items-center px-2 max-h-0 overflow-hidden" id="mobile-nav">
  <ul class="flex flex-col gap-2 font-medium w-full">
    <Link href="/" class="hover:text-gray-500 transition ease-in-out duration-150 menu-btn"><li>Főoldal</li></Link>
    <Link href="#" class="hover:text-gray-500 transition ease-in-out duration-150 menu-btn"><li>Kocsi adatai</li></Link>
    <Link href="/fuel-tracker" class="hover:text-gray-500 transition ease-in-out duration-150 menu-btn"><li>Üzemanyag nyilvántartó</li></Link>
    <Link href="#" class="hover:text-gray-500 transition ease-in-out duration-150 menu-btn"><li>Szerviznapló</li></Link>
    <Link href="#" class="hover:text-gray-500 transition ease-in-out duration-150 menu-btn"><li>Biztosítási adatok</li></Link>
    <form @submit.prevent="logout">
        <button type="submit">Kijelentkezés</button>
      </form>
  </ul>
</nav>

<main>
    <slot/>
</main>

</template>

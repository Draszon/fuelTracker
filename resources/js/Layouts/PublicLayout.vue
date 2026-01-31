<script setup>
import { Link, router } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

const logout = () => {
  router.post(route('logout'));
};

const mobileMenu = ref(false);

</script>

<template>

<header class="bg-gradient-to-r from-slate-900 to-slate-800 shadow-lg sticky top-0 z-50">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between h-16">
      <!-- Logo / Brand -->
      <div class="flex-shrink-0">
        <Link href="/" class="text-white font-bold text-xl tracking-tight hover:text-emerald-400 transition-colors duration-200">
          🚗 VehicleTracker
        </Link>
      </div>

      <!-- Desktop Navigation -->
      <nav class="hidden md:flex items-center space-x-1">
        <Link href="/" class="text-gray-300 hover:text-white hover:bg-slate-700 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200">
          Főoldal
        </Link>
        <Link href="/car-tracker" class="text-gray-300 hover:text-white hover:bg-slate-700 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200">
          Kocsi adatai
        </Link>
        <Link href="/fuel-tracker" class="text-gray-300 hover:text-white hover:bg-slate-700 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200">
          Üzemanyag
        </Link>
        <Link href="/service-tracker" class="text-gray-300 hover:text-white hover:bg-slate-700 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200">
          Szerviznapló
        </Link>
        <Link href="/insurance-tracker" class="text-gray-300 hover:text-white hover:bg-slate-700 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200">
          Biztosítás
        </Link>
        <Link href="/dashboard" class="text-gray-300 hover:text-white hover:bg-slate-700 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200">
          Dashboard
        </Link>
        <div class="ml-4 pl-4 border-l border-slate-600">
          <form @submit.prevent="logout">
            <button type="submit" class="text-gray-300 hover:text-white bg-red-600/20 hover:bg-red-600 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200">
              Kijelentkezés
            </button>
          </form>
        </div>
      </nav>

      <!-- Mobile menu button -->
      <div class="md:hidden">
        <button @click="mobileMenu = !mobileMenu" class="text-gray-300 hover:text-white hover:bg-slate-700 p-2 rounded-lg transition-all duration-200">
          <svg v-if="!mobileMenu" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
          <svg v-else class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>
  </div>

  <!-- Mobile Navigation -->
  <transition
    enter-active-class="transition ease-out duration-200"
    enter-from-class="opacity-0 -translate-y-2"
    enter-to-class="opacity-100 translate-y-0"
    leave-active-class="transition ease-in duration-150"
    leave-from-class="opacity-100 translate-y-0"
    leave-to-class="opacity-0 -translate-y-2"
  >
    <nav v-if="mobileMenu" class="md:hidden bg-slate-800 border-t border-slate-700">
      <div class="px-4 py-3 space-y-1">
        <Link href="/" class="block text-gray-300 hover:text-white hover:bg-slate-700 px-3 py-2 rounded-lg text-base font-medium transition-all duration-200">
          Főoldal
        </Link>
        <Link href="/car-tracker" class="block text-gray-300 hover:text-white hover:bg-slate-700 px-3 py-2 rounded-lg text-base font-medium transition-all duration-200">
          Kocsi adatai
        </Link>
        <Link href="/fuel-tracker" class="block text-gray-300 hover:text-white hover:bg-slate-700 px-3 py-2 rounded-lg text-base font-medium transition-all duration-200">
          Üzemanyag nyilvántartó
        </Link>
        <Link href="/service-tracker" class="block text-gray-300 hover:text-white hover:bg-slate-700 px-3 py-2 rounded-lg text-base font-medium transition-all duration-200">
          Szerviznapló
        </Link>
        <Link href="/insurance-tracker" class="block text-gray-300 hover:text-white hover:bg-slate-700 px-3 py-2 rounded-lg text-base font-medium transition-all duration-200">
          Biztosítási adatok
        </Link>
        <Link href="/dashboard" class="block text-gray-300 hover:text-white hover:bg-slate-700 px-3 py-2 rounded-lg text-base font-medium transition-all duration-200">
          Dashboard
        </Link>
        <div class="pt-2 mt-2 border-t border-slate-700">
          <form @submit.prevent="logout">
            <button type="submit" class="w-full text-left text-gray-300 hover:text-white bg-red-600/20 hover:bg-red-600 px-3 py-2 rounded-lg text-base font-medium transition-all duration-200">
              Kijelentkezés
            </button>
          </form>
        </div>
      </div>
    </nav>
  </transition>
</header>

<main class="min-h-screen bg-gray-50">
    <slot/>
</main>

</template>

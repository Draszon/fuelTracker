<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

const props = defineProps({
  insuranceDatas: Array,
  carDatas: Array
});

const page = usePage();
const flashMessage = computed(() => page.props.flash?.message);
const message = ref(flashMessage.value);

watch(flashMessage, (val) => {
  if (val) {
    message.value = val;
    setTimeout(() => {
      message.value = null;
    }, 3000);
  }
});

const editActive = ref(false);
const selectedCarId = ref(null);

const form = useForm({
  id: null,
  car_id: '',
  insturance_type: '',
  provider: '',
  cost: '',
  valid_from: '',
  valid_until: '',
  notes: ''
});

const storeInsuranceData = () => {
  form.post(`/insurance-store`, {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
    }
  });
}

const loadSelectedInsurance = (selected) => {
  form.reset();
  editActive.value = true;
  form.id = selected.id;
  form.car_id = selected.car_id;
  form.insturance_type = selected.insturance_type;
  form.provider = selected.provider;
  form.cost = selected.cost;
  form.valid_from = selected.valid_from;
  form.valid_until = selected.valid_until;
  form.notes = selected.notes;
}

const updateSelectedInsurance = (id) => {
  form.put(`/insurance-update/${id}`, {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
      editActive.value = false;
    }
  });
}

const cancelEdit = () => {
  form.reset();
  editActive.value = false;
}

const deleteSelectedInsurance = (id) => {
  router.delete(`/insurance-delete/${id}`, {
    preserveScroll: true
  });
}

const filteredInsuranceData = computed(() => {
  if(!selectedCarId.value) return props.insuranceDatas;
  return props.insuranceDatas.filter(f => f.car_id === selectedCarId.value);
});

// Pagination
const currentPage = ref(1);
const itemsPerPage = 30;

const totalPages = computed(() => Math.ceil(filteredInsuranceData.value.length / itemsPerPage));

const paginatedData = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  return filteredInsuranceData.value.slice(start, end);
});

watch(selectedCarId, () => {
  currentPage.value = 1;
});

function goToPage(pageNum) {
  if (pageNum >= 1 && pageNum <= totalPages.value) {
    currentPage.value = pageNum;
  }
}

const visiblePages = computed(() => {
  const pages = [];
  const total = totalPages.value;
  const current = currentPage.value;
  
  if (total <= 7) {
    for (let i = 1; i <= total; i++) pages.push(i);
  } else {
    if (current <= 3) {
      pages.push(1, 2, 3, 4, '...', total);
    } else if (current >= total - 2) {
      pages.push(1, '...', total - 3, total - 2, total - 1, total);
    } else {
      pages.push(1, '...', current - 1, current, current + 1, '...', total);
    }
  }
  return pages;
});

// Summary stats
const insuranceCount = computed(() => filteredInsuranceData.value.length);
const totalCost = computed(() => filteredInsuranceData.value.reduce((sum, i) => sum + Number(i.cost), 0));

</script>

<template>
<Head>
  <title>Biztosítás</title>
</Head>

<PublicLayout>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

  <!-- Biztosítás feltöltés form -->
  <section class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="bg-gradient-to-r from-blue-500 to-cyan-500 px-6 py-4">
      <h2 class="text-lg font-semibold text-white flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
        </svg>
        {{ editActive ? 'Biztosítás szerkesztése' : 'Új biztosítás felvétele' }}
      </h2>
    </div>
    <div class="p-6">
      <!-- Flash üzenet -->
      <div v-if="message" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
        <p class="font-medium text-red-600 flex items-center gap-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          {{ message }}
        </p>
      </div>

      <form @submit.prevent="editActive ? updateSelectedInsurance(form.id) : storeInsuranceData()" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div class="space-y-2">
            <label for="car" class="block text-sm font-medium text-gray-700">Jármű</label>
            <select required v-model="form.car_id" id="car"
              class="w-full rounded-xl border-gray-200 bg-gray-50 py-3 px-4
              focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20
              focus:bg-white transition-all duration-200">
              <option value="" disabled>Válassz járművet...</option>
              <option v-for="car in carDatas" :key="car.id" :value="car.id">{{ car.name }}</option>
            </select>
          </div>

          <div class="space-y-2">
            <label for="provider" class="block text-sm font-medium text-gray-700">Szolgáltató</label>
            <input type="text" placeholder="Alfa biztosító" required id="provider" v-model="form.provider"
              class="w-full rounded-xl border-gray-200 bg-gray-50 py-3 px-4
              focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20
              focus:bg-white transition-all duration-200">
          </div>

          <div class="space-y-2">
            <label for="insurance-type" class="block text-sm font-medium text-gray-700">Típusa</label>
            <input type="text" placeholder="Kötelező / casco" required id="insurance-type" v-model="form.insturance_type"
              class="w-full rounded-xl border-gray-200 bg-gray-50 py-3 px-4
              focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20
              focus:bg-white transition-all duration-200">
          </div>

          <div class="space-y-2">
            <label for="cost" class="block text-sm font-medium text-gray-700">Biztosítás ára (Ft)</label>
            <input type="number" placeholder="55000" required id="cost" v-model="form.cost"
              class="w-full rounded-xl border-gray-200 bg-gray-50 py-3 px-4
              focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20
              focus:bg-white transition-all duration-200">
          </div>

          <div class="space-y-2">
            <label for="valid-from" class="block text-sm font-medium text-gray-700">Érvényesség kezdete</label>
            <input type="date" required id="valid-from" v-model="form.valid_from"
              class="w-full rounded-xl border-gray-200 bg-gray-50 py-3 px-4
              focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20
              focus:bg-white transition-all duration-200">
          </div>

          <div class="space-y-2">
            <label for="valid_until" class="block text-sm font-medium text-gray-700">Érvényesség vége</label>
            <input type="date" required id="valid_until" v-model="form.valid_until"
              class="w-full rounded-xl border-gray-200 bg-gray-50 py-3 px-4
              focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20
              focus:bg-white transition-all duration-200">
          </div>

          <div class="space-y-2 md:col-span-2 lg:col-span-3">
            <label for="notes" class="block text-sm font-medium text-gray-700">Egyéb jegyzetek</label>
            <input type="text" placeholder="További megjegyzések..." id="notes" v-model="form.notes"
              class="w-full rounded-xl border-gray-200 bg-gray-50 py-3 px-4
              focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20
              focus:bg-white transition-all duration-200">
          </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
          <button type="submit" 
            class="px-8 py-3 bg-blue-500 hover:bg-blue-600 text-white font-medium
            rounded-xl transition-all duration-200 flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ editActive ? 'Frissítés' : 'Feltöltés' }}
          </button>
          <button v-if="editActive" type="button" @click="cancelEdit"
            class="px-8 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium
            rounded-xl transition-all duration-200 flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            Mégse
          </button>
        </div>
      </form>
    </div>
  </section>

  <!-- Biztosítások listája -->
  <section class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="bg-gradient-to-r from-slate-800 to-slate-700 px-6 py-4">
      <h2 class="text-lg font-semibold text-white flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        Biztosítások
      </h2>
    </div>
    <div class="p-6">
      <!-- Szűrő -->
      <div class="mb-6 p-4 bg-gray-50 rounded-xl border border-gray-100">
        <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center">
          <div class="flex items-center gap-2 text-sm font-medium text-gray-700">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            Szűrés:
          </div>
          <select v-model="selectedCarId"
            class="w-full sm:w-56 rounded-xl border-gray-200 bg-white py-2.5 px-4 text-sm
            focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20
            transition-all duration-200">
            <option :value="null">Összes jármű</option>
            <option v-for="car in carDatas" :key="car.id" :value="car.id">{{ car.name }}</option>
          </select>
          <button v-if="selectedCarId" @click="selectedCarId = null"
            class="px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium text-sm
            rounded-xl transition-all duration-200 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            Szűrő törlése
          </button>
        </div>
      </div>

      <!-- Táblázat -->
      <div class="overflow-x-auto rounded-xl border border-gray-200">
        <table class="w-full">
          <thead>
            <tr class="bg-gradient-to-r from-gray-100 to-gray-50">
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jármű</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Szolgáltató</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Típus</th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Ár</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Érvényesség</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jegyzetek</th>
              <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Műveletek</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="insuranceData in paginatedData" :key="insuranceData.id"
              class="hover:bg-gray-50 transition-colors duration-150">
              <td class="px-4 py-3 whitespace-nowrap">
                <span class="px-2.5 py-1 bg-slate-100 text-slate-700 text-xs font-medium rounded-lg">
                  {{ insuranceData.car.name }}
                </span>
              </td>
              <td class="px-4 py-3 text-sm text-gray-800 whitespace-nowrap">{{ insuranceData.provider }}</td>
              <td class="px-4 py-3 whitespace-nowrap">
                <span class="px-2.5 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-lg">
                  {{ insuranceData.insturance_type }}
                </span>
              </td>
              <td class="px-4 py-3 text-right whitespace-nowrap">
                <span class="text-sm font-semibold text-gray-800">{{ Number(insuranceData.cost).toLocaleString('hu-HU') }} Ft</span>
              </td>
              <td class="px-4 py-3 text-sm text-gray-800 whitespace-nowrap">
                <div class="flex items-center gap-1">
                  <span>{{ insuranceData.valid_from }}</span>
                  <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                  </svg>
                  <span>{{ insuranceData.valid_until }}</span>
                </div>
              </td>
              <td class="px-4 py-3 text-sm text-gray-600 max-w-[200px] truncate">{{ insuranceData.notes || '-' }}</td>
              <td class="px-4 py-3 whitespace-nowrap">
                <div class="flex justify-center gap-2">
                  <button @click="loadSelectedInsurance(insuranceData)"
                    class="p-2 text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition-all duration-200"
                    title="Szerkesztés">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                  </button>
                  <button @click="deleteSelectedInsurance(insuranceData.id)"
                    class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-all duration-200"
                    title="Törlés">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Üres állapot -->
        <div v-if="filteredInsuranceData.length === 0" class="text-center py-12">
          <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
          </svg>
          <p class="text-gray-500">Nincs biztosítási adat</p>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="totalPages > 1" class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="text-sm text-gray-500">
          Összesen <span class="font-semibold text-gray-700">{{ filteredInsuranceData.length }}</span> rekord, 
          <span class="font-semibold text-gray-700">{{ totalPages }}</span> oldal
        </div>
        <nav class="flex items-center gap-1">
          <button @click="goToPage(1)" :disabled="currentPage === 1"
            class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 disabled:opacity-40 disabled:cursor-not-allowed transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
            </svg>
          </button>
          <button @click="goToPage(currentPage - 1)" :disabled="currentPage === 1"
            class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 disabled:opacity-40 disabled:cursor-not-allowed transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
          </button>
          
          <template v-for="pageNum in visiblePages" :key="pageNum">
            <span v-if="pageNum === '...'" class="px-2 text-gray-400">...</span>
            <button v-else @click="goToPage(pageNum)"
              :class="[
                'min-w-[40px] h-10 rounded-lg font-medium transition-all duration-200',
                currentPage === pageNum 
                  ? 'bg-blue-500 text-white shadow-sm' 
                  : 'text-gray-600 hover:bg-gray-100 hover:text-gray-800'
              ]">
              {{ pageNum }}
            </button>
          </template>

          <button @click="goToPage(currentPage + 1)" :disabled="currentPage === totalPages"
            class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 disabled:opacity-40 disabled:cursor-not-allowed transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </button>
          <button @click="goToPage(totalPages)" :disabled="currentPage === totalPages"
            class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 disabled:opacity-40 disabled:cursor-not-allowed transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
            </svg>
          </button>
        </nav>
      </div>

      <!-- Összesítés -->
      <div v-if="filteredInsuranceData.length > 0" class="mt-6 grid grid-cols-2 gap-2 sm:gap-4">
        <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl p-3 sm:p-4 border border-blue-100">
          <p class="text-[10px] sm:text-xs font-medium text-gray-500 uppercase tracking-wider">Biztosítások</p>
          <p class="text-lg sm:text-2xl font-bold text-gray-800 mt-1">{{ insuranceCount }} <span class="text-sm sm:text-base font-normal text-gray-500">db</span></p>
        </div>
        <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl p-3 sm:p-4 border border-blue-100">
          <p class="text-[10px] sm:text-xs font-medium text-gray-500 uppercase tracking-wider">Összköltség</p>
          <p class="text-lg sm:text-2xl font-bold text-gray-800 mt-1 truncate">{{ totalCost.toLocaleString('hu-HU') }} <span class="text-sm sm:text-base font-normal text-gray-500">Ft</span></p>
        </div>
      </div>
    </div>
  </section>

</div>

</PublicLayout>
</template>
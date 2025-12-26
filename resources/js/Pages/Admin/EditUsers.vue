<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';

const page = usePage();
const props = defineProps({
  users: Array
});
const activePasswordUserId = ref(null);

const form = useForm({
  name: '',
  email: '',
  is_admin: ''
});

const passwdForm = useForm({
  passwd: ''
})

const loadSelected = (selected) => {
  form.id = selected.id;
  form.name = selected.name;
  form.email = selected.email;
  form.is_admin = Boolean(selected.is_admin);
}

const update = (id) => {
  form.put(`/user/update/${id}`, {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
    },
    onError: (errors) => {
      console.log('Hiba történt: ', errors);
    }
  });
}

const showPasswordForm = (userId) => {
  activePasswordUserId.value = userId;
  passwdForm.passwd = '';
}

const updatePasswd = (id) => {
  passwdForm.put(`/update/passwd/${id}`, {
    preserveScroll: true,
    onSuccess: () => {
      passwdForm.reset();
      activePasswordUserId.value = null;
    },
    onError: (errors) => {
      console.log('Hiba történt: ', errors);
    }
  });
}

const deleteUser = (id) => {
  router.delete(`/user/delete/${id}`, {
    preserveScroll: true,
  });
}

</script>

<template>
<Head>
  <title>Felhasználók kezelése</title>
</Head>

<AdminLayout>
  <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Felhasználók kezelése
      </h2>
  </template>

<section class="my-10">
  <div class="bg-white py-10 rounded-md shadow-sm w-full max-w-[1280px] xl:mx-auto">
    <div class="px-2 xl:px-10">
      <h2 class="font-bold text-2xl mb-5">Felhasználói adatok szerkesztése</h2>

      <h2 class="text-red-600" v-if="page.props.flash.message">
        {{ page.props.flash.message }}
      </h2>

      <div class="flex flex-col sm:flex-row w-full gap-5">
        <div class="sm:w-1/2 flex gap-5 flex-wrap">
          <div v-for="user in users"
            class="bg-gray-100 my-5 p-5 rounded w-52"
          >
            <p class="mb-5 sm:text-lg font-bold">{{ user.name }}</p>

            <div class="flex flex-col items-start">
              <button @click="loadSelected(user)"
              class="mb-2"
              >Adatok módosítása</button>

              <button
              v-if="activePasswordUserId !== user.id"
              @click="showPasswordForm(user.id)"
              class="mb-2"
              >
                Új jelszó
              </button>

              <div class="my-2" v-if="activePasswordUserId === user.id">
                <form @submit.prevent="updatePasswd(user.id)">
                  <input type="password" required id="password" v-model="passwdForm.passwd"
                    class="rounded-lg border-gray-200 shadow-none w-40
                    focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500
                    focus:shadow-lg transition ease-in-out"
                  >
                  <button type="submit" class="mt-2">
                    Módosítás
                  </button>
                </form>
              </div>            

              <button @click="deleteUser(user.id)"
              class="text-red-600"
              >Törlés</button>
            </div>
          </div>
        </div>

        <div class="sm:w-1/2">
          <div>
            <form @submit.prevent="update(form.id)">
              <div class="flex flex-col mb-5">
                <label for="name">Név</label>
                <input type="text" required v-model="form.name" id="name"
                  class="rounded-lg border-gray-200 shadow-none max-w-80
                  focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500
                  focus:shadow-lg transition ease-in-out"
                >
              </div>

              <div class="flex flex-col mb-5">
                <label for="email">Email</label>
                <input type="text" required v-model="form.email" id="email"
                  class="rounded-lg border-gray-200 shadow-none max-w-80
                  focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500
                  focus:shadow-lg transition ease-in-out"
                >
              </div>

              <div class="flex flex-col mb-5">
                <label for="is-admin">Admin felhasználó</label>
                <input
                  type="checkbox"
                  name="is-admin"
                  id="is-admin"
                  minlength="8"
                  class="rounded"
                  v-model="form.is_admin">
              </div>

              <button type="submit"
              class="transition ease-in-out delay-150 text-white
              rounded py-2 px-10 bg-gray-500 hover:bg-gray-700"
              >Frissítés</button>
            </form>

          </div>
        </div>
      </div>
    </div>
  </div>
</section>
</AdminLayout>
</template>
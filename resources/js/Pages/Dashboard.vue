<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Welcome from '@/Components/Welcome.vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
</script>

<template>
    <AdminLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div v-if="page.props.flash?.success"   class="mx-8 mt-10 text-green-600">
                        <p>{{ page.props.flash.success }}</p>
                    </div>

                    <div v-if="page.props.flash?.message" class="font-bold mx-8 mt-10 text-red-500">
                        <p>{{ page.props.flash.message }}</p>
                    </div>

                    <Welcome />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import TeacherLayout from '@/layouts/TeacherLayout.vue';
import { ref, onMounted } from 'vue';
import axios from 'axios';

const klasses = ref([]);
const isLoading = ref(true);
const error = ref(null);

onMounted(async () => {
    try {
        const response = await axios.get(route('teacher.klass.index'));
        klasses.value = response.data;
    } catch (err) {
        error.value = "Failed to load classes.";
        console.error(err);
    } finally {
        isLoading.value = false;
    }
});
</script>

<template>
    <TeacherLayout>
        <div class="p-6">
            <h1 class="text-2xl font-bold mb-4">All Classes</h1>

            <div v-if="isLoading">
                Loading classes...
            </div>

            <div v-else-if="error">
                {{ error }}
            </div>

            <table v-else class="min-w-full table-auto border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border px-4 py-2">#</th>
                        <th class="border px-4 py-2">Class Name</th>
                        <th class="border px-4 py-2">Year</th>
                        <th class="border px-4 py-2">Department</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(klass, index) in klasses" :key="klass.id">
                        <td class="border px-4 py-2">{{ index + 1 }}</td>
                        <td class="border px-4 py-2">{{ klass.class_name }}</td>
                        <td class="border px-4 py-2">{{ klass.year }}</td>
                        <td class="border px-4 py-2">{{ klass.department }}</td>
                    </tr>
                </tbody>
            </table>

            <div v-if="klasses.length === 0" class="mt-4 text-gray-500">
                No classes available.
            </div>
        </div>
    </TeacherLayout>
</template>

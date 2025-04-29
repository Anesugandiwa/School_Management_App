<script setup>
import { ref, watch } from 'vue';
import axios from 'axios';
import TeacherLayout from '@/layouts/TeacherLayout.vue';
import { usePage } from '@inertiajs/vue3';
const page = usePage();

const klasses = page.props.klasses;
const subjects = page.props.subjects;

const selectedKlass = ref('');
const selectedSubject = ref('');
const students = ref([]);
const fetchStudents = async () => {
    if (selectedKlass.value) {
        try {
            const response = await axios.post(route('teacher.fetchStudents'), {
                klass_id: selectedKlass.value,  // careful with spelling
            });
            students.value = response.data;
        } catch (error) {
            console.error(error);
        }
    }
};


// watch([selectedKlass, selectedSubject], async ([newKlass, newSubject]) => {
//     if (newKlass && newSubject) {
//         const response = await axios.post('/teacher/fetch-students', {
//             Klass_id: newKlass,
//             subject_id: newSubject,
//         });
//         students.value = response.data;
//     }
// });

// watch(selectedKlass, async (newKlass) => {
//     if (newKlass) {
//         const response = await axios.post('/teacher/fetch-students', {
//             klass_id: newKlass,  // Make sure this matches the controller
//         });
//         students.value = response.data;
//     }
// });
</script>

<template>
  <TeacherLayout>
    <div class="p-6">
      <h2 class="text-h5 font-bold mb-4">Add Marks</h2>

      <v-select
        v-model="selectedKlass"
        :items="klasses?? []"
        item-title="class_name"
        item-value="id"
        label="Select Class"
      />

      <v-select
        v-model="selectedSubject"
        :items="subjects?? []"
        item-title="name"
        item-value="id"
        label="Select Subject"
      />
      <v-btn color="primary" @click="fetchStudents" class="mb-4">
        Fetch Students
      </v-btn>

      <v-data-table
        :headers="[
          { title: 'Student Name', key: 'name' },
          { title: 'Mark', key: 'mark' }
        ]"
        :items="students ?? []"
      >
      </v-data-table>

      <v-btn class="mt-4" @click="saveMarks">Save Marks</v-btn>
    </div>
  </TeacherLayout>
</template>

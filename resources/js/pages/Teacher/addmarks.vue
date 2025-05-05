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
const loading = ref(false);
const saveSuccess = ref(false);
const saveError = ref(false);

// Add a marks object to store student marks
const studentMarks = ref({});

const fetchStudents = async () => {
    if (selectedKlass.value) {
        loading.value = true;
        try {
            const response = await axios.get(route('teacher.fetchStudents'), {
                params: {
                    klass_id: selectedKlass.value,
                }
            });
            students.value = response.data;

            console.log( response.data)
            
            // Initialize marks for each student
            response.data.forEach(student => {
                studentMarks.value[student.id] = {
                    mark: student.mark || '', // Use existing mark if available
                    student_id: student.id
                };
            });
        } catch (error) {
            console.error(error);
            students.value = [];
        } finally {
            loading.value = false;
        }
    } else {
        students.value = [];
        studentMarks.value = {};
    }
};

// Watch for class changes and automatically fetch students
watch(selectedKlass, () => {
    fetchStudents();
});

// Reset marks when subject changes
watch(selectedSubject, () => {
    // Reset marks but keep students
    Object.keys(studentMarks.value).forEach(studentId => {
        studentMarks.value[studentId].mark = '';
    });
    saveSuccess.value = false;
    saveError.value = false;
});
</script>

<template>
  <TeacherLayout>
    <div class="p-6">
      <h2 class="text-h5 font-bold mb-4">Add Marks</h2>
      
      <v-row>
        <v-col cols="12" md="6">
          <v-select
            v-model="selectedKlass"
            :items="klasses ?? []"
            item-title="class_name"
            item-value="id"
            label="Select Class"
            :disabled="loading"
          />
        </v-col>
        
        <v-col cols="12" md="6">
          <v-select
            v-model="selectedSubject"
            :items="subjects ?? []"
            item-title="name"
            item-value="id"
            label="Select Subject"
            :disabled="loading || !selectedKlass"
          />
        </v-col>
      </v-row>

      <!-- Remove the fetch button since we're fetching automatically -->
      
      <v-progress-linear
        v-if="loading"
        indeterminate
        color="primary"
        class="mb-4"
      />
      
      <v-data-table
        v-if="students.length > 0 && selectedSubject"
        :headers="[
          { title: 'Student Name', key: 'name' },
          { title: 'Mark', key: 'mark', align: 'center' }
        ]"
        :items="students"
        :loading="loading"
      >
        <template v-slot:item.mark="{ item }">
          <v-text-field
            v-model="studentMarks[item.id].mark"
            type="number"
            min="0"
            max="100"
            single-line
            hide-details
            density="compact"
            variant="outlined"
            class="mx-auto"
            style="max-width: 100px;"
          />
        </template>
      </v-data-table>
      
      <div v-else-if="selectedKlass && students.length === 0 && !loading" class="text-center py-4">
        <p>No students found for the selected class.</p>
      </div>
      
      <div v-else-if="selectedKlass && !selectedSubject" class="text-center py-4">
        <p>Please select a subject to enter marks.</p>
      </div>

      <div v-else class="text-center py-4">
        <p>Please select a class to view students.</p>
      </div>

      <!-- Add save button if you want to save marks -->
      <v-btn 
        v-if="students.length > 0 && selectedSubject"
        color="success" 
        @click="saveMarks" 
        class="mt-4"
        :disabled="loading"
      >
        Save Marks
      </v-btn>
    </div>
  </TeacherLayout>
</template>
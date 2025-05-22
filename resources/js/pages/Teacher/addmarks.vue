<script setup>
import { ref, watch } from 'vue';
import axios from 'axios';
import TeacherLayout from '@/layouts/TeacherLayout.vue';
import { usePage } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const form = useForm({
  klass_id: '',
  subject_id: '',
  marks:[],
  exam_type:'',
  comment:'',
  total_marks: [],
});

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

const saveMarks = () => {
  form.klass_id =   selectedKlass.value;
  form.subject_id = selectedSubject.value;
  form.marks = Object.values(studentMarks.value);

  form.post(route('teacher.storemarks'), {
    onSuccess: () => {
      Swal.fire('Success!', 'Marks Saved successfully.', 'success');
      // reset title and total marks after save 

      form.title = '';
      form.total_marks = '';
      Object.keys(studentMarks.value).forEach(studentId => {
        studentMarks.value[studentId].mark = '';
        studentMarks.value[studentId].comment = '';
        studentMarks.value[studentId].exam_type = '';
        studentMarks.value[studentId].total_marks = '';
      });
    },
    onError: (newErrors) => {
      errors.value = newErrors
    }
  })
}


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
                    exam_type: student.exam_type || '', 
                    total_marks: student.total_marks || '', 
                    comment: student.comment || '',
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
        studentMarks.value[studentId].exam_type  = '';
        studentMarks.value[studentId].comment = '';
        studentMarks.value[studentId].total_marks = '';
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
          {title: 'Title', key:'exam_type'},
          { title: 'Mark', key: 'mark', align: 'center' },
          {title: 'Total Marks', key:'total_marks'},
          {title: 'Comment', key: 'comment'},
          
        ]"
        :items="students"
        :loading="loading"
      >
      <template v-slot:item.exam_type="{item}">
        <v-text-field
            v-model="studentMarks[item.id].exam_type"
            single-line
            hide-details
            density="compact"
            variant="outlined"
            placeholder="Enter title..."

        />

      </template>

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
        <template v-slot:item.total_marks="{ item }">
          <v-text-field
            v-model="studentMarks[item.id].total_marks"
            type="number"
            min="0"
            single-line
            hide-details
            density="compact"
            variant="outlined"
            class="mx-auto"
            style="max-width: 100px;"
            placeholder="Total"
          />
        </template>

        <template v-slot:item.comment="{item}">
          <v-text-field
              v-model="studentMarks[item.id].comment"
              single-line
              hide-details
              density="compact"
              variant="outlined"
              class="mx-auto"
              placeholder="Enter comment..."
              style="min-width: 200px;"

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
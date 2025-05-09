<script setup>
import { ref, watch } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import TeacherLayout from '@/layouts/TeacherLayout.vue';
import Swal from 'sweetalert2';
import axios from 'axios';

const page = usePage();
const klasses = page.props.klasses;

const selectedKlass = ref('');
const selectedDate = ref(new Date().toISOString().substr(0, 10)); // today's date
const students = ref([]);
const loading = ref(false);

const attendance = ref({}); // student_id => { status, student_id }

const form = useForm({
  klass_id: '',
  date: '',
  records: []
});

const getStudents = async () => {
  if (selectedKlass.value) {
    loading.value = true;
    try {
      const response = await axios.get(route('teacher.getStudents'), {
        params: { klass_id: selectedKlass.value }
      });
      students.value = response.data;

      response.data.forEach(student => {
        attendance.value[student.id] = {
          student_id: student.id,
          status: 'present' // default
        };
      });
    } catch (e) {
      console.error(e);
    } finally {
      loading.value = false;
    }
  }
};

watch(selectedKlass, getStudents);

const saveAttendance = () => {
  form.klass_id = selectedKlass.value;
  form.date = selectedDate.value;
  form.records = Object.values(attendance.value);

  form.post(route('teacher.addAttendance'), {
    onSuccess: () => {
      Swal.fire('Success!', 'Attendance recorded.', 'success');
    },
    onError: errors => {
      console.log(errors);
    }
  });
};
</script>

<template>
  <TeacherLayout>
    <div class="p-6">
      <h2 class="text-h5 font-bold mb-4">Mark Register</h2>

      <v-row>
        <v-col cols="12" md="6">
          <v-select
            v-model="selectedKlass"
            :items="klasses"
            item-title="class_name"
            item-value="id"
            label="Select Class"
          />
        </v-col>

        <v-col cols="12" md="6">
          <v-text-field
            v-model="selectedDate"
            label="Date"
            type="date"
          />
        </v-col>
      </v-row>

      <v-data-table
        v-if="students.length"
        :headers="[
          { title: 'Student Name', key: 'name' },
          { title: 'Student Surname', key: 'surname' },
          { title: 'Status', key: 'status' }
        ]"untu
        :items="students"
      >
        <template v-slot:item.status="{ item }">
          <v-select
            v-model="attendance[item.id].status"
            :items="['present', 'absent', 'late', 'excused']"
            dense
            hide-details
            style="max-width: 150px;"
          />
        </template>
      </v-data-table>

      <v-btn
        color="primary"
        class="mt-4"
        :disabled="!students.length"
        @click="saveAttendance"
      >
        Save Attendance
      </v-btn>
    </div>
  </TeacherLayout>
</template>

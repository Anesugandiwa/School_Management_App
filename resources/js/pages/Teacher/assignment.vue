<script setup>
import { ref } from 'vue';
import TeacherLayout from '@/layouts/TeacherLayout.vue';
import { usePage, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const page = usePage();
const klasses = page.props.klasses;
const subjects = page.props.subjects;

const selectedKlass = ref('');
const selectedSubject = ref('');
const assignmentFile = ref(null);

const form = useForm({
  klass_id: '',
  subject_id: '',
  file: null,
});

const uploadAssignment = () => {
  form.klass_id = selectedKlass.value;
  form.subject_id = selectedSubject.value;
  form.file = assignmentFile.value;

  form.post(route('teacher.uploadAssignment'), {
    forceFormData: true,
    onSuccess: () => {
      Swal.fire('Success!', 'Assignment uploaded successfully.', 'success');
      assignmentFile.value = null;
    },
    onError: (errors) => {
      Swal.fire('Error!', 'Something went wrong.', 'error');
    }
  });
};
</script>

<template>
  <TeacherLayout>
    <div class="p-6">
      <h2 class="text-h5 font-bold mb-4">Upload Assignment</h2>

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
          <v-select
            v-model="selectedSubject"
            :items="subjects"
            item-title="name"
            item-value="id"
            label="Select Subject"
            :disabled="!selectedKlass"
          />
        </v-col>
      </v-row>

      <v-file-input
        label="Select Assignment File"
        accept=".pdf,.doc,.docx,.jpg,.png"
        v-model="assignmentFile"
        show-size
        class="my-4"
      />

      <v-btn
        color="primary"
        :disabled="!selectedKlass || !selectedSubject || !assignmentFile"
        @click="uploadAssignment"
      >
        Upload Assignment
      </v-btn>
    </div>
  </TeacherLayout>
</template>

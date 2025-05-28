<script setup>
import TeacherLayout from '@/layouts/TeacherLayout.vue';
import { ref } from 'vue';

const columns = ref([
    { key: 'id', title: 'ID' },
    { key: 'first_name', title: 'First Name' },
    { key: 'last_name', title: 'Last Name' },
    { key: 'department', title: 'Department' },
    { key: 'subjects', title: 'Subjects' },
]);
</script>

<template>
  <TeacherLayout>
    <div class="p-6">
      <h2 class="text-h5 font-bold mb-4">My Profile</h2>

      <!-- Check if $page.props.teacher exists -->
      <v-data-table
        v-if="$page.props.teacher"
        :headers="columns"
        :items="[$page.props.teacher]"
        class="elevation-1"
        hide-default-footer
      >
        <template #item.subjects="{ item }">
          <div>
            <v-chip
              v-for="subject in item.subjects ?? []"
              :key="subject.id"
              class="ma-1"
              color="primary"
              small
            >
              {{ subject.name }}
            </v-chip>
          </div>
        </template>

      </v-data-table>

      <!-- Optional: Loading State -->
      <div v-else>
        Loading profile...
      </div>

    </div>
  </TeacherLayout>
</template>

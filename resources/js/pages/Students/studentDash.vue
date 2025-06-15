<script setup>
import StudentLayout from '@/layouts/StudentLayout.vue';
import { ref } from 'vue';

// Dummy data
const stats = ref([
  { title: 'Upcoming Assignments', value: 3, icon: 'mdi-book-open-outline', color: 'primary' },
  { title: 'Attendance Rate', value: '92%', icon: 'mdi-calendar-check', color: 'success' },
  { title: 'Exams This Term', value: 2, icon: 'mdi-file-document-outline', color: 'warning' },
]);
</script>

<template>
  <StudentLayout>
    <v-container>
      <v-row class="mb-6">
        <v-col cols="12">
          <h2 class="text-h5 font-weight-bold">Welcome back, Student!</h2>
          <p class="text-subtitle-2 text-grey">Here's a quick overview of your progress.</p>
        </v-col>
      </v-row>

      <!-- Summary Cards -->
      <v-row>
        <v-col
          v-for="(stat, index) in stats"
          :key="index"
          cols="12"
          sm="4"
        >
          <v-card :color="stat.color" dark rounded="lg">
            <v-card-text class="d-flex justify-space-between align-center">
              <div>
                <h3 class="text-h6">{{ stat.title }}</h3>
                <p class="text-h4 font-weight-bold">{{ stat.value }}</p>
              </div>
              <v-icon size="48">{{ stat.icon }}</v-icon>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <!-- Recent Activity -->
      <v-row class="mt-6">
        <v-col cols="12">
          <v-card rounded="lg" outlined>
            <v-card-title>Recent Activity</v-card-title>
            <v-divider></v-divider>
            <v-list>
              <v-list-item>
                <v-list-item-content>
                  <v-list-item-title>Math Assignment due next Monday</v-list-item-title>
                  <v-list-item-subtitle>Submitted: No</v-list-item-subtitle>
                </v-list-item-content>
              </v-list-item>
              <v-list-item>
                <v-list-item-content>
                  <v-list-item-title>Science Project: Topic Approval</v-list-item-title>
                  <v-list-item-subtitle>Submitted: Yes</v-list-item-subtitle>
                </v-list-item-content>
              </v-list-item>
            </v-list>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </StudentLayout>
</template>

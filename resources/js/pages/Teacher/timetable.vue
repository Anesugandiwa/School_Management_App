<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import axios from 'axios';
import TeacherLayout from '@/layouts/TeacherLayout.vue';

const selectedTerm = ref(null);
const selectedAcademicYear = ref(null);
const timetable = ref({});
const loading = ref(true);

// Options for terms and years
const termOptions = ['Term 1', 'Term 2', 'Term 3'];
const academicYearOptions = ['2024', '2025', '2026'];

const daysOfWeek = [
  { name: 'Monday' },
  { name: 'Tuesday' },
  { name: 'Wednesday' },
  { name: 'Thursday' },
  { name: 'Friday' }
];

const hasData = computed(() => {
  return timetable.value && Object.keys(timetable.value).length > 0;
});

const fetchTimetable = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/teacher/timetable', {
      params: {
        term: selectedTerm.value,
        academic_year: selectedAcademicYear.value
      }
    });
    timetable.value = response.data;
  } catch (error) {
    console.error('Error fetching teacher timetable:', error);
    timetable.value = {};
  } finally {
    loading.value = false;
  }
};

watch([selectedTerm, selectedAcademicYear], () => {
  if (selectedTerm.value && selectedAcademicYear.value) {
    fetchTimetable();
  }
});

onMounted(() => {
  if (selectedTerm.value && selectedAcademicYear.value) {
    fetchTimetable();
  }
});
</script>
<template>
  <TeacherLayout>
    <div class="p-4">
      <h1 class="text-xl font-bold mb-2">My Timetable</h1>
      
      <div class="flex gap-4 mb-4">
        <v-select
          label="Select Term"
          v-model="selectedTerm"
          :items="termOptions"
        />
        <v-select
          label="Select Academic Year"
          v-model="selectedAcademicYear"
          :items="academicYearOptions"
        />
      </div>

      <v-skeleton-loader v-if="loading" type="table" />
      
      <div v-else>
        <div v-if="hasData">
          <div v-for="day in daysOfWeek" :key="day.name" class="mb-4">
            <h2 class="text-lg font-semibold">{{ day.name }}</h2>
            <v-row>
              <v-col
                v-for="(period, index) in timetable[day.name] || []"
                :key="index"
                cols="12" sm="6" md="4"
              >
                <v-card class="pa-2" :class="getPeriodCardColor(period)">
                  <div v-if="period.is_break">Break</div>
                  <div v-else>
                    <div><strong>Subject:</strong> {{ period.subject_name }}</div>
                    <div><strong>Time:</strong> {{ period.start_time }} - {{ period.end_time }}</div>
                    <div><strong>Class:</strong> {{ period.class_name }}</div>
                  </div>
                </v-card>
              </v-col>
            </v-row>
          </div>
        </div>
        <div v-else>
          <v-alert type="info">No timetable available for the selected term and academic year.</v-alert>
        </div>
      </div>
    </div>
  </TeacherLayout>
</template>


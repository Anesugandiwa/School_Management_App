<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import axios from 'axios';
import TeacherLayout from '@/layouts/TeacherLayout.vue';

const selectedTerm = ref('1st Term'); // Set default value
const selectedAcademicYear = ref('2025'); // Set default value
const timetable = ref({});
const loading = ref(false);

// Options for terms and years - match database format
const termOptions = ['1st Term', '2nd Term', '3rd Term'];
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
  if (!selectedTerm.value || !selectedAcademicYear.value) {
    return;
  }

  loading.value = true;
  try {
    const response = await axios.get(route('teacher.timetable.fetch'), {
      params: {
        term: selectedTerm.value,
        academic_year: selectedAcademicYear.value
      }
    });
    
    // The response.data should contain the timetable
    timetable.value = response.data.timetable || {};
    
  } catch (error) {
    console.error('Error fetching teacher timetable:', error);
    timetable.value = {};
    
    // Show error message if needed
    if (error.response?.status === 404) {
      console.log('No timetable found for selected period');
    }
  } finally {
    loading.value = false;
  }
};

const getPeriodCardColor = (period) => {
  if (period.is_break) return 'orange-lighten-4';
  return 'blue-lighten-5';
};

// Watch for changes in selection
watch([selectedTerm, selectedAcademicYear], () => {
  if (selectedTerm.value && selectedAcademicYear.value) {
    fetchTimetable();
  }
});

// Fetch initial data
onMounted(() => {
  fetchTimetable();
});
</script>

<template>
  <TeacherLayout>
    <v-container fluid>
      <!-- Header -->
      <v-row class="mb-6">
        <v-col cols="12">
          <h1 class="text-h4 font-weight-bold text-primary">My Teaching Schedule</h1>
          <p class="text-subtitle-1 text-medium-emphasis">View your weekly teaching timetable</p>
        </v-col>
      </v-row>

      <!-- Controls -->
      <v-card class="mb-6" elevation="2">
        <v-card-text>
          <v-row align="center">
            <v-col cols="12" md="4">
              <v-select
                v-model="selectedTerm"
                :items="termOptions"
                label="Select Term"
                variant="outlined"
                prepend-inner-icon="mdi-calendar-month"
              />
            </v-col>
            <v-col cols="12" md="4">
              <v-select
                v-model="selectedAcademicYear"
                :items="academicYearOptions"
                label="Academic Year"
                variant="outlined"
                prepend-inner-icon="mdi-calendar"
              />
            </v-col>
            <v-col cols="12" md="4">
              <v-btn 
                color="primary" 
                @click="fetchTimetable"
                :loading="loading"
                prepend-icon="mdi-refresh"
                block
              >
                Refresh Schedule
              </v-btn>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>

      <!-- Loading State -->
      <v-card v-if="loading" class="text-center py-12">
        <v-card-text>
          <v-progress-circular indeterminate size="64" color="primary" class="mb-4"></v-progress-circular>
          <h3 class="text-h6 mb-2">Loading Your Schedule</h3>
          <p class="text-body-2 text-medium-emphasis">Please wait...</p>
        </v-card-text>
      </v-card>

      <!-- No Data State -->
      <v-card v-else-if="!hasData" class="text-center py-12">
        <v-card-text>
          <v-icon size="80" color="grey-lighten-2" class="mb-4">mdi-calendar-blank</v-icon>
          <h3 class="text-h6 mb-2">No Schedule Available</h3>
          <p class="text-body-2 text-medium-emphasis">
            No teaching schedule found for {{ selectedTerm }} {{ selectedAcademicYear }}.
          </p>
        </v-card-text>
      </v-card>

      <!-- Timetable Display -->
      <v-card v-else elevation="3">
        <v-card-title class="bg-primary text-white">
          <v-icon class="mr-2">mdi-calendar-week</v-icon>
          Teaching Schedule - {{ selectedTerm }} {{ selectedAcademicYear }}
        </v-card-title>
        
        <v-card-text class="pa-4">
          <!-- Desktop View -->
          <div class="d-none d-sm-block">
            <v-row>
              <v-col 
                v-for="day in daysOfWeek" 
                :key="day.name"
                cols="12" 
                :lg="Math.floor(12/daysOfWeek.length)"
                :md="6"
                class="mb-4"
              >
                <v-card variant="outlined" class="h-100">
                  <v-card-title class="text-center bg-grey-lighten-4 py-2">
                    <div class="text-subtitle-1 font-weight-bold">{{ day.name }}</div>
                  </v-card-title>
                  
                  <v-card-text class="pa-2">
                    <div v-if="timetable[day.name] && timetable[day.name].length > 0">
                      <v-card
                        v-for="(period, index) in timetable[day.name]"
                        :key="index"
                        :color="getPeriodCardColor(period)"
                        variant="elevated"
                        class="mb-2"
                        elevation="1"
                      >
                        <v-card-text class="pa-3">
                          <div class="text-center">
                            <!-- Period Name and Time -->
                            <div class="text-caption font-weight-bold text-primary mb-1">
                              {{ period.period_name }}
                            </div>
                            <div class="text-caption text-medium-emphasis mb-2">
                              {{ period.start_time }} - {{ period.end_time }}
                            </div>
                            
                            <!-- Break periods -->
                            <div v-if="period.is_break" class="text-body-2 font-weight-bold text-orange">
                              Break Time
                            </div>
                            
                            <!-- Teaching periods -->
                            <div v-else>
                              <div class="text-body-2 font-weight-bold text-primary mb-1">
                                {{ period.subject_name }}
                              </div>
                              <div class="text-caption text-medium-emphasis">
                                Class: {{ period.class_name }}
                              </div>
                            </div>
                          </div>
                        </v-card-text>
                      </v-card>
                    </div>
                    
                    <!-- No classes for this day -->
                    <div v-else class="text-center py-4 text-medium-emphasis">
                      <v-icon class="mb-2">mdi-calendar-blank</v-icon>
                      <div class="text-caption">No classes</div>
                    </div>
                  </v-card-text>
                </v-card>
              </v-col>
            </v-row>
          </div>

          <!-- Mobile View -->
          <div class="d-sm-none">
            <v-expansion-panels multiple>
              <v-expansion-panel 
                v-for="day in daysOfWeek" 
                :key="day.name"
                :title="day.name"
              >
                <v-expansion-panel-text>
                  <div v-if="timetable[day.name] && timetable[day.name].length > 0">
                    <v-card
                      v-for="(period, index) in timetable[day.name]"
                      :key="index"
                      :color="getPeriodCardColor(period)"
                      variant="outlined"
                      class="mb-3"
                    >
                      <v-card-text>
                        <div class="d-flex justify-space-between align-center mb-2">
                          <div class="text-subtitle-2 font-weight-bold">{{ period.period_name }}</div>
                          <div class="text-caption">{{ period.start_time }} - {{ period.end_time }}</div>
                        </div>
                        
                        <!-- Break periods -->
                        <div v-if="period.is_break" class="text-body-1 font-weight-bold text-orange">
                          Break Time
                        </div>
                        
                        <!-- Teaching periods -->
                        <div v-else>
                          <div class="text-body-1 font-weight-bold text-primary">
                            {{ period.subject_name }}
                          </div>
                          <div class="text-body-2 text-medium-emphasis">
                            Class: {{ period.class_name }}
                          </div>
                        </div>
                      </v-card-text>
                    </v-card>
                  </div>
                  
                  <!-- No classes for this day -->
                  <div v-else class="text-center py-4 text-medium-emphasis">
                    <v-icon class="mb-2">mdi-calendar-blank</v-icon>
                    <div>No classes scheduled</div>
                  </div>
                </v-expansion-panel-text>
              </v-expansion-panel>
            </v-expansion-panels>
          </div>
        </v-card-text>
      </v-card>
    </v-container>
  </TeacherLayout>
</template>

<style scoped>
@media (max-width: 600px) {
  .text-h4 {
    font-size: 1.5rem !important;
  }
}
</style>
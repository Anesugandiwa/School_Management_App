<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';

const props = defineProps({
  klass: Object,
  currentTerm: String,
  academicYear: String,
  error: String
});

const loading = ref(false);
const timetable = ref({});
const selectedTerm = ref(props.currentTerm || '1st Term');
const selectedAcademicYear = ref(props.academicYear || new Date().getFullYear().toString());

const daysOfWeek = [
  { name: 'Monday', short: 'Mon' },
  { name: 'Tuesday', short: 'Tue' },
  { name: 'Wednesday', short: 'Wed' },
  { name: 'Thursday', short: 'Thu' },
  { name: 'Friday', short: 'Fri' }
];

const termOptions = [
  { value: '1st Term', title: '1st Term' },
  { value: '2nd Term', title: '2nd Term' },
  { value: '3rd Term', title: '3rd Term' }
];

const academicYearOptions = computed(() => {
  const currentYear = new Date().getFullYear();
  const years = [];
  for (let i = currentYear - 1; i <= currentYear + 3; i++) {
    years.push({ value: i.toString(), title: i.toString() });
  }
  return years;
});

const hasData = computed(() => {
  return Object.keys(timetable.value).length > 0 && 
         Object.values(timetable.value).some(dayPeriods => dayPeriods.length > 0);
});

const totalSubjects = computed(() => {
  if (!hasData.value) return 0;
  const subjects = new Set();
  Object.values(timetable.value).forEach(dayPeriods => {
    dayPeriods.forEach(period => {
      if (period.subject_name && !period.is_break) {
        subjects.add(period.subject_name);
      }
    });
  });
  return subjects.size;
});

const currentPeriod = computed(() => {
  const now = new Date();
  const currentDay = daysOfWeek.find(d => d.name === now.toLocaleDateString('en-US', { weekday: 'long' }));
  
  if (!currentDay || !hasData.value) return null;
  
  const currentTime = now.toTimeString().substring(0, 5);
  const todayPeriods = timetable.value[currentDay.name] || [];
  
  const currentPeriodData = todayPeriods.find(period => {
    return !period.is_break && 
           currentTime >= period.start_time && 
           currentTime <= period.end_time &&
           period.subject_name;
  });
  
  if (!currentPeriodData) return null;
  
  return {
    name: currentPeriodData.period_name,
    subject: currentPeriodData.subject_name,
    teacher: currentPeriodData.teacher_name,
    time: `${currentPeriodData.start_time} - ${currentPeriodData.end_time}`
  };
});

const fetchTimetable = async () => {
  try {
    loading.value = true;
    
    const response = await axios.get(route('student.timetable.fetch'), {
      params: {
        term: selectedTerm.value,
        academic_year: selectedAcademicYear.value
      }
    });

    if (response.data.timetable) {
      timetable.value = response.data.timetable;
    } else {
      timetable.value = {};
    }
  } catch (error) {
    console.error('Failed to fetch timetable:', error);
    
    if (error.response?.status === 404) {
      timetable.value = {};
    } else {
      Swal.fire({
        title: 'Error',
        text: 'Failed to load timetable.',
        icon: 'error'
      });
    }
  } finally {
    loading.value = false;
  }
};

const isCurrentPeriod = (period) => {
  const now = new Date();
  const currentTime = now.toTimeString().substring(0, 5);
  
  return !period.is_break && 
         currentTime >= period.start_time && 
         currentTime <= period.end_time;
};

const getPeriodCardColor = (period) => {
  if (period.is_break) return 'orange-lighten-4';
  if (!period.subject_name) return 'grey-lighten-4';
  if (isCurrentPeriod(period)) return 'success-lighten-4';
  return 'blue-lighten-5';
};

const downloadTimetable = () => {
  Swal.fire({
    title: 'Download Feature',
    text: 'PDF download functionality will be implemented soon.',
    icon: 'info'
  });
};

// Watch for changes in selection
watch([selectedTerm, selectedAcademicYear], async () => {
  if (selectedTerm.value && selectedAcademicYear.value) {
    await fetchTimetable();
  }
});

// Fetch initial data
onMounted(() => {
  if (props.klass && selectedTerm.value && selectedAcademicYear.value) {
    fetchTimetable();
  }
});
</script>

<template>
  <v-container fluid>
    <!-- Header -->
    <v-row class="mb-6">
      <v-col cols="12">
        <div class="d-flex justify-space-between align-center">
          <div>
            <h1 class="text-h4 font-weight-bold text-primary">My Timetable</h1>
            <p class="text-subtitle-1 text-medium-emphasis">Your weekly class schedule</p>
          </div>
          <v-btn 
            color="primary" 
            variant="outlined"
            prepend-icon="mdi-download"
            @click="downloadTimetable"
            v-if="hasData"
          >
            Download PDF
          </v-btn>
        </div>
      </v-col>
    </v-row>

    <!-- Class Information Card -->
    <v-card class="mb-6" elevation="2" v-if="klass">
      <v-card-title class="bg-blue-lighten-5">
        <v-icon class="mr-2">mdi-school</v-icon>
        Class Information
      </v-card-title>
      <v-card-text>
        <v-row>
          <v-col cols="12" md="3">
            <div class="text-subtitle-2 text-medium-emphasis">Class</div>
            <div class="text-h6 font-weight-bold">{{ klass.class_name }}</div>
          </v-col>
          <v-col cols="12" md="3">
            <div class="text-subtitle-2 text-medium-emphasis">Current Term</div>
            <div class="text-h6 font-weight-bold">{{ selectedTerm }}</div>
          </v-col>
          <v-col cols="12" md="3">
            <div class="text-subtitle-2 text-medium-emphasis">Academic Year</div>
            <div class="text-h6 font-weight-bold">{{ selectedAcademicYear }}</div>
          </v-col>
          <v-col cols="12" md="3">
            <div class="text-subtitle-2 text-medium-emphasis">Total Subjects</div>
            <div class="text-h6 font-weight-bold">{{ totalSubjects }}</div>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>

    <!-- Term Selector -->
    <v-card class="mb-6" elevation="2">
      <v-card-text>
        <v-row align="center">
          <v-col cols="12" md="4">
            <v-select
              v-model="selectedTerm"
              :items="termOptions"
              label="Select Term"
              item-title="title"
              item-value="value"
              variant="outlined"
              prepend-inner-icon="mdi-calendar-month"
              :loading="loading"
            />
          </v-col>
          <v-col cols="12" md="4">
            <v-select
              v-model="selectedAcademicYear"
              :items="academicYearOptions"
              label="Academic Year"
              item-title="title"
              item-value="value"
              variant="outlined"
              prepend-inner-icon="mdi-calendar"
              :loading="loading"
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
              Refresh Timetable
            </v-btn>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>

    <!-- Current Period Indicator -->
    <v-alert 
      v-if="currentPeriod && hasData"
      type="info" 
      variant="tonal" 
      class="mb-6"
      prominent
    >
      <v-row align="center">
        <v-col>
          <div class="d-flex align-center">
            <v-icon size="24" class="mr-3">mdi-clock-outline</v-icon>
            <div>
              <div class="text-subtitle-1 font-weight-bold">
                Current Period: {{ currentPeriod.name }}
              </div>
              <div class="text-body-2">
                {{ currentPeriod.subject }} with {{ currentPeriod.teacher }}
                ({{ currentPeriod.time }})
              </div>
            </div>
          </div>
        </v-col>
      </v-row>
    </v-alert>

    <!-- Loading State -->
    <v-card v-if="loading" class="text-center py-12">
      <v-card-text>
        <v-progress-circular indeterminate size="64" color="primary" class="mb-4"></v-progress-circular>
        <h3 class="text-h6 mb-2">Loading Your Timetable</h3>
        <p class="text-body-2 text-medium-emphasis">Please wait while we fetch your schedule...</p>
      </v-card-text>
    </v-card>

    <!-- Error State -->
    <v-card v-else-if="error" class="text-center py-12" color="error" variant="tonal">
      <v-card-text>
        <v-icon size="80" color="error" class="mb-4">mdi-alert-circle</v-icon>
        <h3 class="text-h6 mb-2">{{ error }}</h3>
        <p class="text-body-2">Please contact your administrator for assistance.</p>
      </v-card-text>
    </v-card>

    <!-- No Timetable State -->
    <v-card v-else-if="!hasData" class="text-center py-12">
      <v-card-text>
        <v-icon size="80" color="grey-lighten-2" class="mb-4">mdi-calendar-blank</v-icon>
        <h3 class="text-h6 mb-2">No Timetable Available</h3>
        <p class="text-body-2 text-medium-emphasis">
          No timetable has been created for your class in {{ selectedTerm }} {{ selectedAcademicYear }}.
        </p>
      </v-card-text>
    </v-card>

    <!-- Timetable Display -->
    <v-card v-else elevation="3">
      <v-card-title class="bg-primary text-white">
        <v-icon class="mr-2">mdi-calendar-week</v-icon>
        {{ klass?.class_name }} - {{ selectedTerm }} {{ selectedAcademicYear }}
        <v-spacer></v-spacer>
        <v-chip color="success" size="small">
          <v-icon start size="16">mdi-check</v-icon>
          {{ totalSubjects }} Subjects
        </v-chip>
      </v-card-title>
      
      <v-card-text class="pa-4">
        <!-- Desktop/Tablet View -->
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
              <v-card :color="day.name === new Date().toLocaleDateString('en-US', { weekday: 'long' }) ? 'blue-lighten-5' : ''" variant="outlined" class="h-100">
                <v-card-title class="text-center bg-grey-lighten-4 py-2">
                  <div class="text-subtitle-1 font-weight-bold">{{ day.name }}</div>
                </v-card-title>
                
                <v-card-text class="pa-2">
                  <div v-if="timetable[day.name] && timetable[day.name].length > 0">
                    <v-card
                      v-for="(period, index) in timetable[day.name]"
                      :key="index"
                      :color="getPeriodCardColor(period)"
                      :variant="period.is_break ? 'tonal' : 'elevated'"
                      :class="['mb-2', isCurrentPeriod(period) ? 'current-period' : '']"
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
                            {{ period.period_name }}
                          </div>
                          
                          <!-- Regular periods with subjects -->
                          <div v-else-if="period.subject_name">
                            <div class="text-body-2 font-weight-bold text-primary mb-1">
                              {{ period.subject_name }}
                            </div>
                            <div class="text-caption text-medium-emphasis" v-if="period.teacher_name">
                              {{ period.teacher_name }}
                            </div>
                          </div>
                          
                          <!-- Free periods -->
                          <div v-else class="text-body-2 text-medium-emphasis">
                            Free Period
                          </div>
                        </div>
                      </v-card-text>
                    </v-card>
                  </div>
                  
                  <!-- No periods for this day -->
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
              :value="day.name === new Date().toLocaleDateString('en-US', { weekday: 'long' })"
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
                        {{ period.period_name }}
                      </div>
                      
                      <!-- Regular periods -->
                      <div v-else-if="period.subject_name">
                        <div class="text-body-1 font-weight-bold text-primary">
                          {{ period.subject_name }}
                        </div>
                        <div class="text-body-2 text-medium-emphasis" v-if="period.teacher_name">
                          {{ period.teacher_name }}
                        </div>
                      </div>
                      
                      <!-- Free periods -->
                      <div v-else class="text-body-2 text-medium-emphasis">
                        Free Period
                      </div>
                    </v-card-text>
                  </v-card>
                </div>
                
                <!-- No periods for this day -->
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
</template>

<style scoped>
.current-period {
  animation: pulse 2s infinite;
  box-shadow: 0 0 15px rgba(76, 175, 80, 0.4) !important;
}

@keyframes pulse {
  0% {
    box-shadow: 0 0 15px rgba(76, 175, 80, 0.4);
  }
  50% {
    box-shadow: 0 0 25px rgba(76, 175, 80, 0.6);
  }
  100% {
    box-shadow: 0 0 15px rgba(76, 175, 80, 0.4);
  }
}

@media (max-width: 600px) {
  .text-h4 {
    font-size: 1.5rem !important;
  }
}
</style>
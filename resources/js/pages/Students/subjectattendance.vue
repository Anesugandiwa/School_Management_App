<script setup>
import StudentLayout from '@/layouts/StudentLayout.vue';
import { ref, computed, onMounted, watch } from 'vue';
import axios from 'axios';

const props = defineProps({
  student: Object,
  klass: Object,
  currentTerm: String,
  academicYear: String,
  error: String
});

const loading = ref(false);
const attendanceData = ref(null);
const selectedDateRange = ref('all');
const startDate = ref('');
const endDate = ref('');

const dateRangeOptions = [
  { title: 'All Time', value: 'all' },
  { title: 'This Week', value: 'week' },
  { title: 'This Month', value: 'month' },
  { title: 'Custom Range', value: 'custom' }
];

// Computed properties
const overallStats = computed(() => {
  return attendanceData.value?.overall_statistics || {};
});

const subjectStats = computed(() => {
  return attendanceData.value?.subject_statistics || [];
});

const studentInfo = computed(() => {
  return attendanceData.value?.student || {};
});

// Get attendance percentage color
const getAttendanceColor = (percentage) => {
  if (percentage >= 90) return 'success';
  if (percentage >= 75) return 'warning';
  return 'error';
};

// Get status color
const getStatusColor = (status) => {
  const colors = {
    present: 'success',
    absent: 'error', 
    late: 'warning',
    excused: 'info'
  };
  return colors[status] || 'default';
};

// Get status icon
const getStatusIcon = (status) => {
  const icons = {
    present: 'mdi-check-circle',
    absent: 'mdi-close-circle',
    late: 'mdi-clock-alert',
    excused: 'mdi-information'
  };
  return icons[status] || 'mdi-help-circle';
};

// Calculate date range
const calculateDateRange = () => {
  const today = new Date();
  let start = null;
  let end = null;

  switch (selectedDateRange.value) {
    case 'week':
      start = new Date(today.setDate(today.getDate() - 7));
      end = new Date();
      break;
    case 'month':
      start = new Date(today.getFullYear(), today.getMonth(), 1);
      end = new Date();
      break;
    case 'custom':
      start = startDate.value ? new Date(startDate.value) : null;
      end = endDate.value ? new Date(endDate.value) : null;
      break;
  }

  return { start, end };
};

// Fetch attendance data
const fetchAttendanceData = async () => {
  loading.value = true;
  
  try {
    const { start, end } = calculateDateRange();
    const params = {};
    
    if (start) params.start_date = start.toISOString().split('T')[0];
    if (end) params.end_date = end.toISOString().split('T')[0];

    const response = await axios.get(route('attendance.stats'), { params });
    attendanceData.value = response.data.data;
    
  } catch (error) {
    console.error('Error fetching attendance data:', error);
    attendanceData.value = null;
  } finally {
    loading.value = false;
  }
};

// Watch for date range changes
watch([selectedDateRange, startDate, endDate], () => {
  if (selectedDateRange.value !== 'custom' || (startDate.value && endDate.value)) {
    fetchAttendanceData();
  }
});

// Fetch data on mount
onMounted(() => {
  if (props.student && !props.error) {
    fetchAttendanceData();
  }
});
</script>

<template>
    <StudentLayout>
       <v-container fluid>
    <!-- Header -->
    <v-row class="mb-6">
      <v-col cols="12">
        <div class="d-flex justify-space-between align-center">
          <div>
            <h1 class="text-h4 font-weight-bold text-primary">My Attendance</h1>
            <p class="text-subtitle-1 text-medium-emphasis">Track your class attendance record</p>
          </div>
        </div>
      </v-col>
    </v-row>

    <!-- Student Information -->
    <v-card class="mb-6" elevation="2" v-if="student && !error">
      <v-card-title class="bg-blue-lighten-5">
        <v-icon class="mr-2">mdi-account</v-icon>
        Student Information
      </v-card-title>
      <v-card-text>
        <v-row>
          <v-col cols="12" md="3">
            <div class="text-subtitle-2 text-medium-emphasis">Student Name</div>
            <div class="text-h6 font-weight-bold">{{ student.name }} {{ student.surname }}</div>
          </v-col>
          <v-col cols="12" md="3">
            <div class="text-subtitle-2 text-medium-emphasis">Class</div>
            <div class="text-h6 font-weight-bold">{{ klass?.class_name }}</div>
          </v-col>
          <v-col cols="12" md="3">
            <div class="text-subtitle-2 text-medium-emphasis">Academic Year</div>
            <div class="text-h6 font-weight-bold">{{ academicYear }}</div>
          </v-col>
          <v-col cols="12" md="3">
            <div class="text-subtitle-2 text-medium-emphasis">Overall Attendance</div>
            <div class="text-h6 font-weight-bold" :class="`text-${getAttendanceColor(overallStats.overall_attendance_percentage)}`">
              {{ overallStats.overall_attendance_percentage || 0 }}%
            </div>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>

    <!-- Date Range Filter -->
    <v-card class="mb-6" elevation="2">
      <v-card-text>
        <v-row align="center">
          <v-col cols="12" md="4">
            <v-select
              v-model="selectedDateRange"
              :items="dateRangeOptions"
              label="Date Range"
              variant="outlined"
              prepend-inner-icon="mdi-calendar-range"
            />
          </v-col>
          <v-col cols="12" md="3" v-if="selectedDateRange === 'custom'">
            <v-text-field
              v-model="startDate"
              label="Start Date"
              type="date"
              variant="outlined"
            />
          </v-col>
          <v-col cols="12" md="3" v-if="selectedDateRange === 'custom'">
            <v-text-field
              v-model="endDate"
              label="End Date"
              type="date"
              variant="outlined"
            />
          </v-col>
          <v-col cols="12" md="2">
            <v-btn 
              color="primary" 
              @click="fetchAttendanceData"
              :loading="loading"
              prepend-icon="mdi-refresh"
              block
            >
              Refresh
            </v-btn>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>

    <!-- Error State -->
    <v-card v-if="error" class="text-center py-12" color="error" variant="tonal">
      <v-card-text>
        <v-icon size="80" color="error" class="mb-4">mdi-alert-circle</v-icon>
        <h3 class="text-h6 mb-2">{{ error }}</h3>
        <p class="text-body-2">Please contact your administrator for assistance.</p>
      </v-card-text>
    </v-card>

    <!-- Loading State -->
    <v-card v-else-if="loading" class="text-center py-12">
      <v-card-text>
        <v-progress-circular indeterminate size="64" color="primary" class="mb-4"></v-progress-circular>
        <h3 class="text-h6 mb-2">Loading Attendance Data</h3>
        <p class="text-body-2 text-medium-emphasis">Please wait...</p>
      </v-card-text>
    </v-card>

    <!-- No Data State -->
    <v-card v-else-if="!attendanceData || subjectStats.length === 0" class="text-center py-12">
      <v-card-text>
        <v-icon size="80" color="grey-lighten-2" class="mb-4">mdi-calendar-blank</v-icon>
        <h3 class="text-h6 mb-2">No Attendance Records</h3>
        <p class="text-body-2 text-medium-emphasis">
          No attendance records found for the selected period.
        </p>
      </v-card-text>
    </v-card>

    <!-- Overall Statistics -->
    <v-row v-else class="mb-6">
      <v-col cols="12" md="3">
        <v-card color="primary" variant="tonal">
          <v-card-text class="text-center">
            <v-icon size="48" color="primary" class="mb-2">mdi-school</v-icon>
            <div class="text-h4 font-weight-bold">{{ overallStats.total_subjects || 0 }}</div>
            <div class="text-subtitle-1">Total Subjects</div>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="12" md="3">
        <v-card color="success" variant="tonal">
          <v-card-text class="text-center">
            <v-icon size="48" color="success" class="mb-2">mdi-check-circle</v-icon>
            <div class="text-h4 font-weight-bold">{{ overallStats.total_classes_attended || 0 }}</div>
            <div class="text-subtitle-1">Classes Attended</div>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="12" md="3">
        <v-card color="info" variant="tonal">
          <v-card-text class="text-center">
            <v-icon size="48" color="info" class="mb-2">mdi-calendar</v-icon>
            <div class="text-h4 font-weight-bold">{{ overallStats.total_classes_held || 0 }}</div>
            <div class="text-subtitle-1">Total Classes</div>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="12" md="3">
        <v-card :color="getAttendanceColor(overallStats.overall_attendance_percentage)" variant="tonal">
          <v-card-text class="text-center">
            <v-icon size="48" :color="getAttendanceColor(overallStats.overall_attendance_percentage)" class="mb-2">mdi-chart-line</v-icon>
            <div class="text-h4 font-weight-bold">{{ overallStats.overall_attendance_percentage || 0 }}%</div>
            <div class="text-subtitle-1">Attendance Rate</div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Subject-wise Attendance -->
    <v-card elevation="3" v-if="subjectStats.length > 0">
      <v-card-title class="bg-primary text-white">
        <v-icon class="mr-2">mdi-book-multiple</v-icon>
        Subject-wise Attendance
      </v-card-title>
      
      <v-card-text class="pa-0">
        <v-data-table
          :headers="[
            { title: 'Subject', key: 'subject_name' },
            { title: 'Total Classes', key: 'total_classes' },
            { title: 'Present', key: 'present' },
            { title: 'Absent', key: 'absent' },
            { title: 'Late', key: 'late' },
            { title: 'Excused', key: 'excused' },
            { title: 'Attendance %', key: 'attendance_percentage' },
            { title: 'Recent Status', key: 'recent_status' }
          ]"
          :items="subjectStats"
          :items-per-page="10"
        >
          <template v-slot:item.subject_name="{ item }">
            <div class="font-weight-medium">{{ item.subject_name }}</div>
          </template>

          <template v-slot:item.present="{ item }">
            <v-chip color="success" size="small" variant="tonal">
              {{ item.present }}
            </v-chip>
          </template>

          <template v-slot:item.absent="{ item }">
            <v-chip color="error" size="small" variant="tonal">
              {{ item.absent }}
            </v-chip>
          </template>

          <template v-slot:item.late="{ item }">
            <v-chip color="warning" size="small" variant="tonal">
              {{ item.late }}
            </v-chip>
          </template>

          <template v-slot:item.excused="{ item }">
            <v-chip color="info" size="small" variant="tonal">
              {{ item.excused }}
            </v-chip>
          </template>

          <template v-slot:item.attendance_percentage="{ item }">
            <div class="d-flex align-center">
              <v-progress-linear
                :model-value="item.attendance_percentage"
                :color="getAttendanceColor(item.attendance_percentage)"
                height="8"
                class="mr-3"
                style="min-width: 80px;"
              ></v-progress-linear>
              <span :class="`text-${getAttendanceColor(item.attendance_percentage)} font-weight-bold`">
                {{ item.attendance_percentage }}%
              </span>
            </div>
          </template>

          <template v-slot:item.recent_status="{ item }">
            <div class="d-flex gap-1">
              <v-tooltip 
                v-for="(record, index) in item.recent_records.slice(0, 5)" 
                :key="index"
                :text="`Status: ${record.status}`"
                location="top"
              >
                <template v-slot:activator="{ props: tooltipProps }">
                  <v-icon 
                    v-bind="tooltipProps"
                    :color="getStatusColor(record.status)" 
                    size="small"
                  >
                    {{ getStatusIcon(record.status) }}
                  </v-icon>
                </template>
              </v-tooltip>
            </div>
          </template>
        </v-data-table>
      </v-card-text>
    </v-card>
  </v-container>

  </StudentLayout>
</template>

<style scoped>
.text-h4 {
  font-size: 1.5rem !important;
}

@media (max-width: 600px) {
  .text-h4 {
    font-size: 1.25rem !important;
  }
}
</style>
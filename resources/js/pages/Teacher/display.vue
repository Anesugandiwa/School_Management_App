<script setup>
import TeacherLayout from '@/layouts/TeacherLayout.vue';
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { usePage } from '@inertiajs/vue3';
import { format } from 'date-fns';


const props = defineProps({
  teacher: Object,
  stats: Object,
  todaySchedule: Array,
  recentActivity: Array,
  upcomingAssignments: Array,
  assignedClasses: Array,
  assignments: Array,
})

function formatDate(date) {
  return date ? format(new Date(date), 'MMM d, yyyy') : '—';
}
function getStatusColor(assignment) {
  const status = getStatus(assignment);
  switch (status) {
    case 'Overdue':
      return 'red';
    case 'Due Today':
      return 'orange';
    case 'Pending':
      return 'warning';
    case 'Draft':
      return 'grey';
    default:
      return 'primary';
  }
}
function getStatus(assignment) {
  const today = new Date();
  const due = new Date(assignment.due_date);
  if (!assignment.due_date) return 'Draft';
  if (due < today) return 'Overdue';
  if (due.toDateString() === today.toDateString()) return 'Due Today';
  return 'Pending';
}

const assignments = ref(props.assignments || []);


const loading = true;
const currentTeacher = computed(() => props.teacher);
const stats = ref({
  totalStudents: 0,
  attendance: 0,
  upcomingAssignments: props.stats?.upcomingAssignments || 0,
  pendingGrades: 0
});

const recentActivity = ref([]);

const todayClasses = ref([]);


const currentDate = computed(() => {
  const options = { 
    weekday: 'long', 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric' 
  };
  return new Date().toLocaleDateString('en-US', options);
});

onMounted(async () => {
  try{
      const response = await axios.get(route('teacher.percentage')) // adjust route name
      stats.value.attendance = response.data.attendance
  } catch (error){
    console.error('Error when fetching Up Coming Assignments', error)
  }

})
onMounted (async ()=> {
  try {
      const response = await axios.get(route('teacher.count'))
      stats.value.totalStudents = response.data.studentCount
  } catch (error){
    console.error('Error when fetching student count', error)
  }

})




</script>

<template>
  <TeacherLayout>
    <v-app>
      <v-container>
        <!-- Welcome Section -->
        <v-row class="my-4">
          <v-col>
            <h1 class="text-h4 font-weight-bold">Welcome back,{{ $page.props.auth.user.name }} </h1>
            <p class="text-subtitle-1">{{ currentDate }}</p>
          </v-col>
          <v-col cols="auto">
            <v-btn color="primary" prepend-icon="mdi-plus" :href="route('teacher.assignment')">New Assignment</v-btn>
          </v-col>
        </v-row>

        <!-- Stats Cards -->
        <v-row>
          <v-col cols="12" sm="6" md="3">
            <v-card elevation="2" height="100%">
              <v-card-text>
                <div class="text-subtitle-2 text-medium-emphasis">Total Students</div>
                <div class="text-h4 font-weight-bold mt-2">{{ stats.totalStudents }}</div>
              </v-card-text>
            </v-card>
          </v-col>
          <v-col cols="12" sm="6" md="3">
            <v-card elevation="2" height="100%">
              <v-card-text>
                <div class="text-subtitle-2 text-medium-emphasis">Attendance Rate</div>
                <div class="text-h4 font-weight-bold mt-2">{{ stats.attendance }}%</div>
              </v-card-text>
            </v-card>
          </v-col>
          <v-col cols="12" sm="6" md="3">
            <v-card elevation="2" height="100%">
              <v-card-text>
                <div class="text-subtitle-2 text-medium-emphasis">Upcoming Assignments</div>
                <div class="text-h4 font-weight-bold mt-2">{{ stats.upcomingAssignments }}</div>
              </v-card-text>
            </v-card>
          </v-col>
          <v-col cols="12" sm="6" md="3">
            <v-card elevation="2" height="100%">
              <v-card-text>
                <div class="text-subtitle-2 text-medium-emphasis">Pending Grades</div>
                <div class="text-h4 font-weight-bold mt-2">{{ stats.pendingGrades }}</div>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>

       

        
        <!-- Quick Actions -->
        <v-row class="mt-4">
          <v-col cols="12">
            <v-card elevation="2">
              <v-card-title class="text-h6 font-weight-bold">Quick Actions</v-card-title>
              <v-card-text>
                <v-row>
                  <v-col cols="12" sm="6" md="4">
                    <v-btn block color="primary" variant="tonal" height="100" class="flex-column" :href="route('teacher.get')">
                      <v-icon size="32" class="mb-2">mdi-clipboard-text</v-icon>
                      Take Attendance
                    </v-btn>
                  </v-col>
                  <v-col cols="12" sm="6" md="4">
                    <v-btn block color="primary" variant="tonal" height="100" class="flex-column" :href="route('teacher.addmarks')">
                      <v-icon size="32" class="mb-2">mdi-notebook-edit</v-icon>
                      Enter Marks
                    </v-btn>
                  </v-col>
                  <!-- <v-col cols="6" sm="4" md="2">
                    <v-btn block color="primary" variant="tonal" height="100" class="flex-column">
                      <v-icon size="32" class="mb-2">mdi-file-document</v-icon>
                      Lesson Plans
                    </v-btn>
                  </v-col> -->
                  <v-col cols="12" sm="6" md="4">
                    <v-btn block color="primary" variant="tonal" height="100" class="flex-column" :href="route('teacher.timetable.index')">
                      <v-icon size="32" class="mb-2">mdi-calendar</v-icon>
                      Schedule
                    </v-btn>
                  </v-col>
                  <!-- <v-col cols="6" sm="4" md="2">
                    <v-btn block color="primary" variant="tonal" height="100" class="flex-column">
                      <v-icon size="32" class="mb-2">mdi-message</v-icon>
                      Messages
                    </v-btn>
                  </v-col> -->
                  <!-- <v-col cols="6" sm="4" md="2">
                    <v-btn block color="primary" variant="tonal" height="100" class="flex-column">
                      <v-icon size="32" class="mb-2">mdi-chart-box</v-icon>
                      Reports
                    </v-btn>
                  </v-col> -->
                </v-row>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>
        
        <!-- Upcoming Assignments -->
        <v-row class="mt-4">
          <v-col cols="12">
            <v-card elevation="2">
              <v-card-title class="text-h6 font-weight-bold">
                Upcoming Assignments
                <v-spacer></v-spacer>
                <v-btn variant="text" size="small" color="primary">Manage All</v-btn>
              </v-card-title>
              <v-divider></v-divider>
              <v-table>
                <thead>
                  <tr>
                    <th>Assignment</th>
                    <th>Class</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="assignment in props.assignments"
                    :key ="assignment.id"
                  >
                  <td>{{ assignment.title }}</td>
                  <td>{{ assignment.klass?.class_name || '—' }}</td>
                  <td>{{ formatDate(assignment.due_date) }}</td>

                  <td>
                    <v-chip
                      :color="getStatusColor(assignment)"
                      size="small"
                      class="text-white"
                    >
                     {{ getStatus(assignment) }}
                    </v-chip>
                  </td>
                  <td>
                    <v-btn size="small" variant="text" icon="mdi-pencil" @click="editAssignment(assignment)"></v-btn>
                    <v-btn size="small" variant="text" icon="mdi-eye" @click="viewAssignment(assignment)"></v-btn>
                  </td>
 
                  </tr>
                  <tr v-if="!props.assignments || props.assignments.length === 0">
                    <td colspan="5" class="text-center">No upcoming assignments</td>
                  </tr>
                  
                  
                </tbody>
              </v-table>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-app>
  </TeacherLayout>
</template>
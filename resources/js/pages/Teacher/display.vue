<script setup>
import TeacherLayout from '@/layouts/TeacherLayout.vue';
import { ref, onMounted } from 'vue';

// Sample data - you would fetch this from your API
const stats = ref({
  totalStudents: 87,
  attendance: 94,
  upcomingAssignments: 3,
  pendingGrades: 12
});

const recentActivity = ref([
  { type: 'grade', description: 'Math quiz grades submitted', time: '2 hours ago' },
  { type: 'attendance', description: 'Marked attendance for Grade 10B', time: '4 hours ago' },
  { type: 'message', description: 'Parent message from Smith family', time: 'Yesterday' }
]);

const todayClasses = ref([
  { className: 'Grade 10A - Mathematics', time: '8:00 - 9:30', room: 'Room 15' },
  { className: 'Grade 9C - Mathematics', time: '10:00 - 11:30', room: 'Room 12' },
  { className: 'Grade 10B - Mathematics', time: '13:00 - 14:30', room: 'Room 15' }
]);

// Load data on mount
onMounted(() => {
  // fetchDashboardData()
});
</script>

<template>
  <TeacherLayout>
    <v-app>
      <v-container>
        <!-- Welcome Section -->
        <v-row class="my-4">
          <v-col>
            <h1 class="text-h4 font-weight-bold">Welcome back, Ms. Johnson</h1>
            <p class="text-subtitle-1">Tuesday, May 16, 2025</p>
          </v-col>
          <v-col cols="auto">
            <v-btn color="primary" prepend-icon="mdi-plus">New Assignment</v-btn>
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

        <!-- Today's Schedule and Recent Activity -->
        <v-row class="mt-4">
          <v-col cols="12" md="6">
            <v-card elevation="2">
              <v-card-title class="text-h6 font-weight-bold">
                Today's Schedule
                <v-spacer></v-spacer>
                <v-btn variant="text" size="small" color="primary">View All</v-btn>
              </v-card-title>
              <v-divider></v-divider>
              <v-list>
                <v-list-item v-for="(classItem, index) in todayClasses" :key="index" class="py-2">
                  <v-list-item-title class="font-weight-medium">{{ classItem.className }}</v-list-item-title>
                  <v-list-item-subtitle>
                    {{ classItem.time }} • {{ classItem.room }}
                  </v-list-item-subtitle>
                </v-list-item>
              </v-list>
            </v-card>
          </v-col>
          <v-col cols="12" md="6">
            <v-card elevation="2">
              <v-card-title class="text-h6 font-weight-bold">
                Recent Activity
                <v-spacer></v-spacer>
                <v-btn variant="text" size="small" color="primary">View All</v-btn>
              </v-card-title>
              <v-divider></v-divider>
              <v-list>
                <v-list-item v-for="(activity, index) in recentActivity" :key="index">
                  <template v-slot:prepend>
                    <v-avatar color="primary" variant="tonal" size="36">
                      <v-icon :icon="activity.type === 'grade' ? 'mdi-notebook' : activity.type === 'attendance' ? 'mdi-account-multiple' : 'mdi-email'"></v-icon>
                    </v-avatar>
                  </template>
                  <v-list-item-title>{{ activity.description }}</v-list-item-title>
                  <v-list-item-subtitle>{{ activity.time }}</v-list-item-subtitle>
                </v-list-item>
              </v-list>
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
                  <v-col cols="6" sm="4" md="2">
                    <v-btn block color="primary" variant="tonal" height="100" class="flex-column">
                      <v-icon size="32" class="mb-2">mdi-clipboard-text</v-icon>
                      Take Attendance
                    </v-btn>
                  </v-col>
                  <v-col cols="6" sm="4" md="2">
                    <v-btn block color="primary" variant="tonal" height="100" class="flex-column">
                      <v-icon size="32" class="mb-2">mdi-notebook-edit</v-icon>
                      Enter Grades
                    </v-btn>
                  </v-col>
                  <v-col cols="6" sm="4" md="2">
                    <v-btn block color="primary" variant="tonal" height="100" class="flex-column">
                      <v-icon size="32" class="mb-2">mdi-file-document</v-icon>
                      Lesson Plans
                    </v-btn>
                  </v-col>
                  <v-col cols="6" sm="4" md="2">
                    <v-btn block color="primary" variant="tonal" height="100" class="flex-column">
                      <v-icon size="32" class="mb-2">mdi-calendar</v-icon>
                      Schedule
                    </v-btn>
                  </v-col>
                  <v-col cols="6" sm="4" md="2">
                    <v-btn block color="primary" variant="tonal" height="100" class="flex-column">
                      <v-icon size="32" class="mb-2">mdi-message</v-icon>
                      Messages
                    </v-btn>
                  </v-col>
                  <v-col cols="6" sm="4" md="2">
                    <v-btn block color="primary" variant="tonal" height="100" class="flex-column">
                      <v-icon size="32" class="mb-2">mdi-chart-box</v-icon>
                      Reports
                    </v-btn>
                  </v-col>
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
                  <tr>
                    <td>Algebra Quiz</td>
                    <td>Grade 10A</td>
                    <td>May 20, 2025</td>
                    <td><v-chip color="warning" size="small">Pending</v-chip></td>
                    <td>
                      <v-btn size="small" variant="text" icon="mdi-pencil"></v-btn>
                      <v-btn size="small" variant="text" icon="mdi-eye"></v-btn>
                    </td>
                  </tr>
                  <tr>
                    <td>Geometry Project</td>
                    <td>Grade 9C</td>
                    <td>May 23, 2025</td>
                    <td><v-chip color="success" size="small">Ready</v-chip></td>
                    <td>
                      <v-btn size="small" variant="text" icon="mdi-pencil"></v-btn>
                      <v-btn size="small" variant="text" icon="mdi-eye"></v-btn>
                    </td>
                  </tr>
                  <tr>
                    <td>Statistics Test</td>
                    <td>Grade 10B</td>
                    <td>May 27, 2025</td>
                    <td><v-chip color="info" size="small">Draft</v-chip></td>
                    <td>
                      <v-btn size="small" variant="text" icon="mdi-pencil"></v-btn>
                      <v-btn size="small" variant="text" icon="mdi-eye"></v-btn>
                    </td>
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
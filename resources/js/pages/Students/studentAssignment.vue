<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const assignments = ref([]);
const upcomingAssignments = ref([]);
const loading = ref(true);
const error = ref(null);

// Fetch the assignments when component mounts
onMounted(async () => {
  try {
    loading.value = true;
    
    // You can use Promise.all to fetch multiple endpoints in parallel
    const [assignmentsResponse, upcomingResponse] = await Promise.all([
      axios.get('/api/student/assignments'),
      axios.get('/api/student/assignments/upcoming')
    ]);
    
    assignments.value = assignmentsResponse.data.assignments;
    upcomingAssignments.value = upcomingResponse.data.assignments;
  } catch (err) {
    error.value = 'Failed to load assignments. Please try again.';
    console.error(err);
  } finally {
    loading.value = false;
  }
});

// Format date helper function
const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
};

// Function to get status color based on due date
const getStatusColor = (dueDate) => {
  const now = new Date();
  const due = new Date(dueDate);
  
  if (due < now) return 'error'; // Past due
  
  // Due within 3 days
  const threeDaysFromNow = new Date();
  threeDaysFromNow.setDate(now.getDate() + 3);
  if (due <= threeDaysFromNow) return 'warning';
  
  return 'success'; // Due later
};

// Function to get status text
const getStatusText = (dueDate) => {
  const now = new Date();
  const due = new Date(dueDate);
  
  if (due < now) return 'Past Due';
  
  const threeDaysFromNow = new Date();
  threeDaysFromNow.setDate(now.getDate() + 3);
  if (due <= threeDaysFromNow) return 'Due Soon';
  
  return 'Upcoming';
};
</script>

<template>
  <v-container>
    <!-- Upcoming Assignments Section -->
    <v-row>
      <v-col cols="12">
        <v-card elevation="2">
          <v-card-title class="text-h6 font-weight-bold">
            Upcoming Assignments
            <v-spacer></v-spacer>
            <v-btn variant="text" color="primary" :to="{ name: 'all-assignments' }">
              View All
            </v-btn>
          </v-card-title>
          
          <v-divider></v-divider>
          
          <v-card-text v-if="loading">
            <v-progress-circular indeterminate color="primary"></v-progress-circular>
            <span class="ml-2">Loading assignments...</span>
          </v-card-text>
          
          <v-alert v-else-if="error" type="error" class="ma-3">
            {{ error }}
          </v-alert>
          
          <template v-else>
            <v-list v-if="upcomingAssignments.length > 0">
              <v-list-item
                v-for="assignment in upcomingAssignments"
                :key="assignment.id"
                :to="{ name: 'assignment-details', params: { id: assignment.id }}"
              >
                <template v-slot:prepend>
                  <v-avatar
                    :color="getStatusColor(assignment.due_date)"
                    variant="tonal"
                    size="36"
                  >
                    <v-icon>mdi-book-open-variant</v-icon>
                  </v-avatar>
                </template>
                
                <v-list-item-title class="font-weight-medium">
                  {{ assignment.title }}
                </v-list-item-title>
                
                <v-list-item-subtitle>
                  <div>Class: {{ assignment.class.name }}</div>
                  <div>Due: {{ formatDate(assignment.due_date) }}</div>
                </v-list-item-subtitle>
                
                <template v-slot:append>
                  <v-chip :color="getStatusColor(assignment.due_date)" size="small">
                    {{ getStatusText(assignment.due_date) }}
                  </v-chip>
                </template>
              </v-list-item>
            </v-list>
            
            <v-card-text v-else class="text-center py-4">
              <v-icon size="large" color="grey">mdi-book-check</v-icon>
              <div class="text-body-1 mt-2">No upcoming assignments</div>
            </v-card-text>
          </template>
        </v-card>
      </v-col>
    </v-row>

    <!-- Recent Assignments Section -->
    <v-row class="mt-4">
      <v-col cols="12">
        <v-card elevation="2">
          <v-card-title class="text-h6 font-weight-bold">
            Recent Assignments
          </v-card-title>
          
          <v-divider></v-divider>
          
          <template v-if="!loading && !error">
            <v-table v-if="assignments.length > 0">
              <thead>
                <tr>
                  <th>Assignment</th>
                  <th>Class</th>
                  <th>Teacher</th>
                  <th>Due Date</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="assignment in assignments.slice(0, 5)" :key="assignment.id">
                  <td>{{ assignment.title }}</td>
                  <td>{{ assignment.class.name }}</td>
                  <td>{{ assignment.teacher.name }}</td>
                  <td>{{ formatDate(assignment.due_date) }}</td>
                  <td>
                    <v-chip :color="getStatusColor(assignment.due_date)" size="small">
                      {{ getStatusText(assignment.due_date) }}
                    </v-chip>
                  </td>
                  <td>
                    <v-btn 
                      variant="text" 
                      icon="mdi-eye" 
                      size="small"
                      :to="{ name: 'assignment-details', params: { id: assignment.id }}"
                    ></v-btn>
                    <v-btn 
                      v-if="assignment.file_path" 
                      variant="text" 
                      icon="mdi-download" 
                      size="small"
                      :href="`/storage/${assignment.file_path}`"
                      target="_blank"
                    ></v-btn>
                  </td>
                </tr>
              </tbody>
            </v-table>
            
            <v-card-text v-else class="text-center py-4">
              <v-icon size="large" color="grey">mdi-notebook</v-icon>
              <div class="text-body-1 mt-2">No assignments yet</div>
            </v-card-text>
          </template>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>
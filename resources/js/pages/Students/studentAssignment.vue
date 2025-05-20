<script setup>
import { ref, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3'; // Import usePage from Inertia
import StudentLayout from '@/layouts/StudentLayout.vue';

// Get assignments directly from props instead of making an Axios call
const page = usePage();
const assignments = ref([]);
const upcomingAssignments = ref([]);
const pastAssignments = ref([]);
const loading = ref(false);
const error = ref(null);

onMounted(() => {
  try {
    // Get assignments directly from Inertia props
    assignments.value = page.props.assignments || [];
    
    const now = new Date();
    
    // Filter assignments
    upcomingAssignments.value = assignments.value.filter(a => new Date(a.due_date) >= now);
    pastAssignments.value = assignments.value.filter(a => new Date(a.due_date) < now);
    
  } catch (err) {
    error.value = 'Failed to process assignments. Please try again.';
    console.error(err);
  }
});

// Rest of your code remains the same
const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
};

const getStatusColor = (dueDate) => {
  const now = new Date();
  const due = new Date(dueDate);
  if (due < now) return 'error';
  const threeDaysFromNow = new Date();
  threeDaysFromNow.setDate(now.getDate() + 3);
  if (due <= threeDaysFromNow) return 'warning';
  return 'success';
};

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
  <studentLayout>
    <v-container>
      <!-- Upcoming Assignments -->
      <v-row>
        <v-col cols="12">
          <v-card elevation="2">
            <v-card-title class="text-h6 font-weight-bold">
              Upcoming Assignments
            </v-card-title>

            <v-divider></v-divider>

            <v-card-text v-if="loading">
              <v-progress-circular indeterminate color="primary" />
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
                  :to="{ name: 'assignment-details', params: { id: assignment.id } }"
                >
                  <template #prepend>
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
                    <div>Class: {{ assignment.klass.klass_id }}</div>
                    <div>Due: {{ formatDate(assignment.due_date) }}</div>
                  </v-list-item-subtitle>

                  <template #append>
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

      <!-- Past Assignments -->
      <v-row class="mt-4">
        <v-col cols="12">
          <v-card elevation="2">
            <v-card-title class="text-h6 font-weight-bold">
              Past Assignments
            </v-card-title>

            <v-divider></v-divider>

            <template v-if="!loading && !error">
              <v-list v-if="pastAssignments.length > 0">
                <v-list-item
                  v-for="assignment in pastAssignments"
                  :key="assignment.id"
                  :to="{ name: 'assignment-details', params: { id: assignment.id } }"
                >
                  <template #prepend>
                    <v-avatar color="error" variant="tonal" size="36">
                      <v-icon>mdi-book-remove</v-icon>
                    </v-avatar>
                  </template>

                  <v-list-item-title>
                    {{ assignment.title }}
                  </v-list-item-title>

                  <v-list-item-subtitle>
                    <div>Class: {{ assignment.klass.klass_id }}</div>
                    <div>Due: {{ formatDate(assignment.due_date) }}</div>
                  </v-list-item-subtitle>

                  <template #append>
                    <v-chip color="error" size="small">Past Due</v-chip>
                  </template>
                </v-list-item>
              </v-list>

              <v-card-text v-else class="text-center py-4">
                <v-icon size="large" color="grey">mdi-book-cancel</v-icon>
                <div class="text-body-1 mt-2">No past assignments</div>
              </v-card-text>
            </template>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </studentLayout>
</template>

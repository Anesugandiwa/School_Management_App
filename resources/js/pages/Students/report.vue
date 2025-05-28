<script setup>
import StudentLayout from '@/layouts/StudentLayout.vue';
import { computed } from 'vue';

const props = defineProps({
  student: {
    type: Object,
    default: null
  },
  reportData: {
    type: Array,
    default: () => []
  },
  summary: {
    type: Object,
    default: null
  },
  error: {
    type: String,
    default: null
  }
});

// Computed properties for better organization
const hasData = computed(() => {
  return props.student && props.reportData.length > 0;
});

const gradeColor = computed(() => {
  if (!props.summary) return 'grey';
  
  const grade = props.summary.overall_grade;
  if (grade === 'A+' || grade === 'A') return 'success';
  if (grade === 'B') return 'info';
  if (grade === 'C') return 'warning';
  return 'error';
});

const downloadPDF = () => {
  // You could integrate jsPDF or html2pdf here
  alert("Download feature coming soon!");
};

// Function to get color based on percentage
const getGradeColor = (percentage) => {
  if (percentage >= 80) return 'success';
  if (percentage >= 70) return 'info';
  if (percentage >= 60) return 'warning';
  return 'error';
};
</script>

<template>
  <StudentLayout>
    <v-container class="py-10">
      <!-- Error State -->
      <v-alert v-if="error" type="error" class="mb-4">
        {{ error }}
      </v-alert>
      
      <!-- No Data State -->
      <v-card v-else-if="!hasData" class="mx-auto elevation-4" max-width="600">
        <v-card-text class="text-center py-8">
          <v-icon size="64" color="grey">mdi-file-document-outline</v-icon>
          <h3 class="text-h5 mt-4 mb-2">No Report Data Available</h3>
          <p class="text-body-1">
            Your academic report will appear here once your teacher has entered your marks.
          </p>
        </v-card-text>
      </v-card>
      
      <!-- Report Content -->
      <v-card v-else class="mx-auto elevation-10" max-width="900">
        <!-- Header -->
        <v-card-title class="text-h5 font-weight-bold d-flex align-center">
          <v-icon class="mr-2">mdi-file-document</v-icon>
          Academic Report - {{ student.name }}
        </v-card-title>
        
        <v-card-subtitle class="text-subtitle-1 pb-4">
          Class: {{ student.class }} | 
          Term: {{ summary.term }} | 
          Year: {{ summary.year }}
        </v-card-subtitle>
        
        <v-divider></v-divider>
        
        <!-- Summary Stats -->
        <v-card-text>
          <v-row class="mb-4">
            <v-col cols="12" sm="4">
              <v-card variant="tonal" color="primary">
                <v-card-text class="text-center">
                  <div class="text-h4 font-weight-bold">{{ summary.total_subjects }}</div>
                  <div class="text-body-2">Subjects</div>
                </v-card-text>
              </v-card>
            </v-col>
            <v-col cols="12" sm="4">
              <v-card variant="tonal" :color="gradeColor">
                <v-card-text class="text-center">
                  <div class="text-h4 font-weight-bold">{{ summary.overall_average }}%</div>
                  <div class="text-body-2">Overall Average</div>
                </v-card-text>
              </v-card>
            </v-col>
            <v-col cols="12" sm="4">
              <v-card variant="tonal" :color="gradeColor">
                <v-card-text class="text-center">
                  <div class="text-h4 font-weight-bold">{{ summary.overall_grade }}</div>
                  <div class="text-body-2">Overall Grade</div>
                </v-card-text>
              </v-card>
            </v-col>
          </v-row>
        </v-card-text>
        
        <!-- Marks Table -->
        <v-table density="comfortable">
          <thead>
            <tr>
              <th class="text-left font-weight-bold">Subject</th>
              <th class="text-center font-weight-bold">Marks</th>
              <th class="text-center font-weight-bold">Percentage</th>
              <th class="text-center font-weight-bold">Grade</th>
              <th class="text-left font-weight-bold">Teacher's Comment</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(entry, index) in reportData" :key="index">
              <td class="font-weight-medium">{{ entry.subject }}</td>
              <td class="text-center">
                {{ Math.round(entry.marks) }}/{{ Math.round(entry.total_marks) }}
              </td>
              <td class="text-center">
                <v-chip 
                  :color="getGradeColor(entry.percentage)" 
                  size="small"
                  variant="tonal"
                >
                  {{ entry.percentage }}%
                </v-chip>
              </td>
              <td class="text-center">
                <v-chip 
                  :color="getGradeColor(entry.percentage)" 
                  size="small"
                >
                  {{ entry.grade }}
                </v-chip>
              </td>
              <td class="text-body-2">{{ entry.comment }}</td>
            </tr>
          </tbody>
        </v-table>
        
        <v-divider class="my-6"></v-divider>
        
        <!-- Head Teacher's Remarks -->
        <v-card-text>
          <div class="mb-3">
            <v-icon class="mr-2" color="primary">mdi-account-tie</v-icon>
            <span class="text-subtitle-1 font-weight-bold">Head Teacher's Remarks:</span>
          </div>
          <v-card variant="tonal" color="blue-grey">
            <v-card-text>
              <p class="text-body-1">{{ summary.head_remarks }}</p>
            </v-card-text>
          </v-card>
        </v-card-text>
        
        <!-- Actions -->
        <v-card-actions class="justify-space-between px-6 py-4">
          <div class="text-caption text-medium-emphasis">
            Generated on {{ new Date().toLocaleDateString() }}
          </div>
          <v-btn 
            color="primary" 
            @click="downloadPDF"
            prepend-icon="mdi-download"
            variant="elevated"
          >
            Download PDF
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-container>
  </StudentLayout>
</template>

<style scoped>
.v-card-title {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white !important;
}

.v-card-subtitle {
  background-color: #f8f9ff;
  color: #2c3e50 !important;
  border-bottom: 1px solid #e1e8ed;
}

.v-table th {
  background-color: #f5f7fa;
  font-size: 0.875rem;
}

.v-table tbody tr:nth-child(even) {
  background-color: #fafbfc;
}
</style>
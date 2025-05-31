<script setup>
import { ref, computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import Swal from 'sweetalert2';
import axios from 'axios';

const props = defineProps({
  klasses: Array,
  subjects: Array,
  teachers: Array,
  existingTimetable: Object
});

const selectedClass = ref('');
const loading = ref(false);
const loadingTimetable = ref(false); // Add loading state for fetching timetable
const editingSlot = ref(null);
const editDialog = ref(false);
const selectedTerm = ref('1st Term');
const selectedAcademicYear = ref(new Date().getFullYear().toString());

const daysOfWeek = [
  { name: 'Monday', short: 'Mon' },
  { name: 'Tuesday', short: 'Tue' },
  { name: 'Wednesday', short: 'Wed' },
  { name: 'Thursday', short: 'Thu' },
 { name: 'Friday', short: 'Fri' }
];

const timeSlots = [
  { id: 1, name: 'Period 1', start_time: '08:00', end_time: '08:40' },
  { id: 2, name: 'Period 2', start_time: '08:40', end_time: '09:20' },
  { id: 3, name: 'Break', start_time: '09:20', end_time: '09:40', isBreak: true },
  { id: 4, name: 'Period 3', start_time: '09:40', end_time: '10:20' },
  { id: 5, name: 'Period 4', start_time: '10:20', end_time: '11:00' },
  { id: 6, name: 'Period 5', start_time: '11:00', end_time: '11:40' },
  { id: 7, name: 'Lunch', start_time: '11:40', end_time: '12:20', isBreak: true },
  { id: 8, name: 'Period 6', start_time: '12:20', end_time: '13:00' },
  { id: 9, name: 'Period 7', start_time: '13:00', end_time: '13:40' }
];

// Term options
const termOptions = [
  { value: '1st Term', title: '1st Term' },
  { value: '2nd Term', title: '2nd Term' },
  { value: '3rd Term', title: '3rd Term' }
];

// Academic year options
const academicYearOptions = computed(() => {
  const currentYear = new Date().getFullYear();
  const years = [];
  for (let i = currentYear - 1; i <= currentYear + 3; i++) {
    years.push({ value: i.toString(), title: i.toString() });
  }
  return years;
});

const form = useForm({
  klass_id: '',
  timetable: {},
  academic_year: '',
  term: '',
  time_slots: []
});

const generateEmptyTimetable = () => {
  const timetableData = {};
  daysOfWeek.forEach(day => {
    timetableData[day.name] = {};
    timeSlots.forEach(slot => {
      if (!slot.isBreak) {
        timetableData[day.name][slot.id] = {
          subject_id: null,
          teacher_id: null,
          subject_name: '',
          teacher_name: ''
        };
      }
    });
  });
  return timetableData;
};

const timetable = ref(generateEmptyTimetable());
const selectedSubject = ref('');
const selectedTeacher = ref('');

// Method to fetch existing timetable
const fetchExistingTimetable = async () => {
  if (!selectedClass.value || !selectedTerm.value || !selectedAcademicYear.value) {
    return;
  }

  try {
    loadingTimetable.value = true;
    
    const response = await axios.get(route('admin.fetchez'), {
      params: {
        klass_id: selectedClass.value,
        term: selectedTerm.value,
        academic_year: selectedAcademicYear.value
      }
    });

    if (response.data.timetable) {
      // Populate the timetable with existing data
      timetable.value = formatFetchedTimetable(response.data.timetable);
      
      Swal.fire({
        title: 'Timetable Loaded',
        text: 'Existing timetable has been loaded successfully.',
        icon: 'info',
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
      });
    } else {
      // No existing timetable found, show empty timetable
      timetable.value = generateEmptyTimetable();
    }
  } catch (error) {
    console.error('Failed to fetch timetable:', error);
    
    if (error.response?.status === 404) {
      // No timetable exists for this selection
      timetable.value = generateEmptyTimetable();
      Swal.fire({
        title: 'No Existing Timetable',
        text: 'No timetable found for the selected parameters. You can create a new one.',
        icon: 'info',
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
      });
    } else {
      Swal.fire({
        title: 'Error',
        text: 'Failed to load existing timetable.',
        icon: 'error'
      });
    }
  } finally {
    loadingTimetable.value = false;
  }
};

// Format fetched timetable data to match component structure
const formatFetchedTimetable = (fetchedData) => {
  const formattedTimetable = generateEmptyTimetable();
  
  // If fetchedData is an array of timetable entries
  if (Array.isArray(fetchedData)) {
    fetchedData.forEach(entry => {
      if (formattedTimetable[entry.day] && formattedTimetable[entry.day][entry.period]) {
        formattedTimetable[entry.day][entry.period] = {
          subject_id: entry.subject_id,
          teacher_id: entry.teacher_id,
          subject_name: entry.subject?.name || '',
          teacher_name: entry.teacher?.name || ''
        };
      }
    });
  } 
  // If fetchedData is already in the format we expect
  else if (typeof fetchedData === 'object') {
    Object.keys(fetchedData).forEach(day => {
      if (formattedTimetable[day]) {
        Object.keys(fetchedData[day]).forEach(periodId => {
          if (formattedTimetable[day][periodId]) {
            formattedTimetable[day][periodId] = fetchedData[day][periodId];
          }
        });
      }
    });
  }
  
  return formattedTimetable;
};

// Watch for changes in selection to auto-fetch existing timetable
watch([selectedClass, selectedTerm, selectedAcademicYear], async ([newClass, newTerm, newYear], [oldClass, oldTerm, oldYear]) => {
  // Only fetch if all required fields are selected and at least one value changed
  if (newClass && newTerm && newYear && 
      (newClass !== oldClass || newTerm !== oldTerm || newYear !== oldYear)) {
    await fetchExistingTimetable();
  }
}, { immediate: false });

// Clear timetable when class is deselected
watch(selectedClass, (newClass) => {
  if (!newClass) {
    timetable.value = generateEmptyTimetable();
  }
});

const openEditDialog = (day, slotId) => {
  const slot = timetable.value[day][slotId];
  editingSlot.value = { day, slotId };
  selectedSubject.value = slot.subject_id || '';
  selectedTeacher.value = slot.teacher_id || '';
  editDialog.value = true;
};

const saveSlot = () => {
  if (editingSlot.value) {
    const { day, slotId } = editingSlot.value;
    const subject = props.subjects.find(s => s.id === selectedSubject.value);
    const teacher = props.teachers.find(t => t.id === selectedTeacher.value);

    timetable.value[day][slotId] = {
      subject_id: selectedSubject.value,
      teacher_id: selectedTeacher.value,
      subject_name: subject?.name || '',
      teacher_name: teacher?.name || ''
    };
  }
  editDialog.value = false;
  editingSlot.value = null;
};

const clearSlot = (day, slotId) => {
  timetable.value[day][slotId] = {
    subject_id: null,
    teacher_id: null,
    subject_name: '',
    teacher_name: ''
  };
};

const saveTimetable = () => {
  if (!selectedClass.value || !selectedTerm.value || !selectedAcademicYear.value) {
    Swal.fire({
      title: 'Missing Information',
      text: 'Please select class, term, and academic year before saving.',
      icon: 'warning'
    });
    return;
  }

  form.klass_id = selectedClass.value;
  form.timetable = timetable.value;
  form.academic_year = selectedAcademicYear.value;
  form.term = selectedTerm.value;
  form.time_slots = timeSlots;

  form.post(route('admin.timeTable.store'), {
    onSuccess: () => {
      Swal.fire({
        title: 'Success!',
        text: 'Timetable saved successfully.',
        icon: 'success'
      });
    },
    onError: () => {
      Swal.fire({
        title: 'Error!',
        text: 'Failed to save timetable. Please try again.',
        icon: 'error'
      });
    }
  });
};

const clearAllTimetable = () => {
  Swal.fire({
    title: 'Clear All Periods?',
    text: 'This will remove all subjects from the timetable. This action cannot be undone.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    confirmButtonText: 'Yes, clear all'
  }).then((result) => {
    if (result.isConfirmed) {
      timetable.value = generateEmptyTimetable();
      Swal.fire('Cleared!', 'All periods have been cleared.', 'success');
    }
  });
};

const getSlotColor = (day, slotId) => {
  const slot = timetable.value[day][slotId];
  if (slot.subject_id) return 'blue-lighten-5';
  return 'grey-lighten-4';
};

// Manual refresh button
const refreshTimetable = () => {
  fetchExistingTimetable();
};

// Check if timetable has any data
const hasExistingData = computed(() => {
  return daysOfWeek.some(day => 
    timeSlots.some(slot => 
      !slot.isBreak && timetable.value[day.name]?.[slot.id]?.subject_id
    )
  );
});
</script>

<template>
  <AdminLayout>
    <v-container fluid>
      <!-- Header -->
      <v-row class="mb-6">
        <v-col cols="12">
          <div class="d-flex justify-space-between align-center">
            <div>
              <h1 class="text-h4 font-weight-bold">Create Timetable</h1>
              <p class="text-subtitle-1 text-medium-emphasis">Design weekly schedule for classes</p>
            </div>
          </div>
        </v-col>
      </v-row>

      <!-- Controls -->
      <v-card class="mb-6" elevation="2">
        <v-card-title class="text-h6 bg-blue-lighten-5">
          <v-icon class="mr-2">mdi-cog</v-icon>
          Timetable Configuration
        </v-card-title>
        
        <v-card-text>
          <v-row align="center">
            <v-col cols="12" md="4">
              <v-select
                v-model="selectedClass"
                :items="klasses"
                label="Select Class"
                item-title="class_name"
                item-value="id"
                variant="outlined"
                prepend-inner-icon="mdi-school"
                :loading="loadingTimetable"
              />
            </v-col>
            
            <v-col cols="12" md="4">
              <v-select
                v-model="selectedTerm"
                :items="termOptions"
                label="Select Term"
                item-title="title"
                item-value="value"
                variant="outlined"
                prepend-inner-icon="mdi-calendar-month"
                :loading="loadingTimetable"
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
                :loading="loadingTimetable"
              />
            </v-col>
          </v-row>

          <!-- Action Buttons -->
          <v-row class="mt-4">
            <v-col cols="12" md="3">
              <v-btn 
                color="primary" 
                @click="saveTimetable" 
                :disabled="!selectedClass || !selectedTerm || !selectedAcademicYear || loading"
                :loading="loading"
                size="large"
                prepend-icon="mdi-content-save"
                block
              >
                Save Timetable
              </v-btn>
            </v-col>
            
            <v-col cols="12" md="3">
              <v-btn 
                color="info" 
                variant="outlined"
                @click="refreshTimetable"
                :disabled="!selectedClass || !selectedTerm || !selectedAcademicYear"
                :loading="loadingTimetable"
                prepend-icon="mdi-refresh"
                block
              >
                Refresh
              </v-btn>
            </v-col>
            
            <v-col cols="12" md="3">
              <v-btn 
                color="warning" 
                variant="outlined"
                @click="clearAllTimetable"
                prepend-icon="mdi-delete-sweep"
                block
              >
                Clear All
              </v-btn>
            </v-col>

            <v-col cols="12" md="3">
              <v-chip 
                v-if="hasExistingData"
                color="success" 
                prepend-icon="mdi-check-circle"
                block
              >
                Has Data
              </v-chip>
              <v-chip 
                v-else
                color="grey" 
                prepend-icon="mdi-plus-circle"
                block
              >
                New Timetable
              </v-chip>
            </v-col>
          </v-row>

          <!-- Loading indicator -->
          <v-row v-if="loadingTimetable" class="mt-4">
            <v-col cols="12">
              <v-alert type="info" variant="tonal">
                <div class="d-flex align-center">
                  <v-progress-circular indeterminate size="20" class="mr-3"></v-progress-circular>
                  Loading existing timetable...
                </div>
              </v-alert>
            </v-col>
          </v-row>

          <!-- Info Display -->
          <v-row v-if="selectedClass && selectedTerm && selectedAcademicYear && !loadingTimetable" class="mt-4">
            <v-col cols="12">
              <v-alert type="info" variant="tonal">
                <div class="d-flex align-center">
                  <v-icon class="mr-2">mdi-information</v-icon>
                  <span>
                    {{ hasExistingData ? 'Editing' : 'Creating' }} timetable for: 
                    <strong>{{ klasses.find(k => k.id === selectedClass)?.class_name }}</strong> - 
                    <strong>{{ selectedTerm }}</strong> - 
                    <strong>{{ selectedAcademicYear }}</strong>
                  </span>
                </div>
              </v-alert>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>

      <!-- Rest of your existing template remains the same -->
      <!-- Timetable Grid -->
      <v-card elevation="3" v-if="selectedClass && selectedTerm && selectedAcademicYear">
        <v-card-title class="bg-primary text-white">
          <v-icon class="mr-2">mdi-calendar-week</v-icon>
          Weekly Timetable - {{ selectedTerm }} {{ selectedAcademicYear }}
          <v-spacer></v-spacer>
          <v-chip v-if="hasExistingData" color="success" size="small">
            <v-icon start size="16">mdi-check</v-icon>
            Existing Data
          </v-chip>
        </v-card-title>
        
        <!-- Rest of your timetable grid code remains the same -->
        <v-card-text class="pa-0">
          <!-- Time Slots Header -->
          <v-row no-gutters class="bg-grey-lighten-4">
            <v-col cols="2" class="pa-3 text-center font-weight-bold border-e">
              Day / Time
            </v-col>
            <v-col 
              v-for="slot in timeSlots" 
              :key="slot.id"
              :class="[
                'pa-2 text-center border-e',
                slot.isBreak ? 'bg-orange-lighten-4' : ''
              ]"
            >
              <div class="text-subtitle-2 font-weight-bold">{{ slot.name }}</div>
              <div class="text-caption">{{ slot.start_time }} - {{ slot.end_time }}</div>
            </v-col>
          </v-row>

          <!-- Days and Periods -->
          <div v-for="(day, dayIndex) in daysOfWeek" :key="day.name">
            <v-row no-gutters :class="dayIndex % 2 === 0 ? 'bg-grey-lighten-5' : ''">
              <!-- Day Column -->
              <v-col cols="2" class="pa-4 border-e border-b d-flex align-center">
                <div class="text-h6 font-weight-bold text-primary">
                  {{ day.name }}
                </div>
              </v-col>

              <!-- Period Slots -->
              <v-col
                v-for="slot in timeSlots"
                :key="`${day.name}-${slot.id}`"
                class="pa-1 border-e border-b"
                style="min-height: 120px;"
              >
                <!-- Break Periods -->
                <div v-if="slot.isBreak" class="h-100 d-flex align-center justify-center">
                  <v-chip color="orange" variant="tonal" size="small">
                    {{ slot.name }}
                  </v-chip>
                </div>
                
                <!-- Regular Periods -->
                <v-card
                  v-else
                  :color="getSlotColor(day.name, slot.id)"
                  :variant="timetable[day.name][slot.id].subject_id ? 'elevated' : 'outlined'"
                  class="h-100 d-flex flex-column cursor-pointer slot-card"
                  @click="openEditDialog(day.name, slot.id)"
                  hover
                >
                  <v-card-text class="pa-2 flex-grow-1 d-flex flex-column justify-center">
                    <div v-if="timetable[day.name][slot.id].subject_id" class="text-center">
                      <div class="text-subtitle-2 font-weight-bold text-primary mb-1">
                        {{ timetable[day.name][slot.id].subject_name }}
                      </div>
                      <div class="text-caption text-medium-emphasis">
                        {{ timetable[day.name][slot.id].teacher_name }}
                      </div>
                    </div>
                    <div v-else class="text-center text-medium-emphasis">
                      <v-icon size="20" class="mb-1">mdi-plus-circle-outline</v-icon>
                      <div class="text-caption">Add Subject</div>
                    </div>
                  </v-card-text>
                  
                  <!-- Quick Actions -->
                  <div v-if="timetable[day.name][slot.id].subject_id" class="slot-actions">
                    <v-btn
                      icon="mdi-close"
                      size="x-small"
                      color="error"
                      variant="elevated"
                      @click.stop="clearSlot(day.name, slot.id)"
                    ></v-btn>
                  </div>
                </v-card>
              </v-col>
            </v-row>
          </div>
        </v-card-text>
      </v-card>

      <!-- Placeholder when no class/term selected -->
      <v-card v-else class="text-center py-12" elevation="2">
        <v-card-text>
          <v-icon size="80" color="grey-lighten-2" class="mb-4">mdi-calendar-blank</v-icon>
          <h3 class="text-h6 mb-2">Select Class, Term, and Academic Year</h3>
          <p class="text-body-2 text-medium-emphasis">
            Please select a class, term, and academic year to start creating the timetable.
          </p>
        </v-card-text>
      </v-card>

      <!-- Edit Dialog -->
      <v-dialog v-model="editDialog" max-width="500px">
        <v-card>
          <v-card-title class="text-h6 bg-primary text-white">
            <v-icon class="mr-2">mdi-pencil</v-icon>
            Edit Period
            <v-spacer></v-spacer>
            <v-btn icon="mdi-close" variant="text" @click="editDialog = false"></v-btn>
          </v-card-title>
          
          <v-card-text class="pa-6">
            <v-row>
              <v-col cols="12">
                <v-select
                  v-model="selectedSubject"
                  :items="subjects"
                  label="Select Subject"
                  item-title="name"
                  item-value="id"
                  variant="outlined"
                  prepend-inner-icon="mdi-book"
                  clearable
                />
              </v-col>
              <v-col cols="12">
                <v-select
                  v-model="selectedTeacher"
                  :items="teachers"
                  label="Select Teacher"
                  item-title="first_name"
                  item-value="id"
                  variant="outlined"
                  prepend-inner-icon="mdi-account"
                  clearable
                />
              </v-col>
            </v-row>
          </v-card-text>
          
          <v-card-actions class="px-6 pb-6">
            <v-spacer></v-spacer>
            <v-btn color="grey" variant="text" @click="editDialog = false">
              Cancel
            </v-btn>
            <v-btn color="primary" @click="saveSlot" prepend-icon="mdi-check">
              Save
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-container>
  </AdminLayout>
</template>

<!-- Styles remain the same -->
<style scoped>
.slot-card {
  position: relative;
  transition: all 0.2s ease-in-out;
  min-height: 100px;
}

.slot-card:hover {
  transform: translateY(-2px);
}

.slot-actions {
  position: absolute;
  top: 4px;
  right: 4px;
  opacity: 0;
  transition: opacity 0.2s ease-in-out;
}

.slot-card:hover .slot-actions {
  opacity: 1;
}

.border-e {
  border-right: 1px solid rgba(0, 0, 0, 0.12);
}

.border-b {
  border-bottom: 1px solid rgba(0, 0, 0, 0.12);
}

.cursor-pointer {
  cursor: pointer;
}
</style>
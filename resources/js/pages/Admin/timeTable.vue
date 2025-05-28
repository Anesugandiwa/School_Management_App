<script setup>
import { ref, computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';

const props = defineProps({
  classes: Array,
  subjects: Array,
  teachers: Array,
  existingTimetable: Object
});

const selectedClass = ref('');
const loading = ref(false);
const editingSlot = ref(null);
const editDialog = ref(false);

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
  { id: 9, name: 'Period 7', start_time: '13:00', end_time: '13:40' },
  { id: 9, name: 'Period 7', start_time: '13:00', end_time: '13:40' },
  { id: 9, name: 'Period 7', start_time: '13:00', end_time: '13:40' },
];

const form = useForm({
  class_id: '',
  timetable: {}
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
  form.class_id = selectedClass.value;
  form.timetable = timetable.value;
  
  form.post(route('admin.timetable.store'), {
    onSuccess: () => {
      // Handle success
    }
  });
};

const getSlotColor = (day, slotId) => {
  const slot = timetable.value[day][slotId];
  if (slot.subject_id) return 'blue-lighten-5';
  return 'grey-lighten-4';
};

const getSlotBorderColor = (day, slotId) => {
  const slot = timetable.value[day][slotId];
  if (slot.subject_id) return 'blue-lighten-2';
  return 'grey-lighten-2';
};
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
        <v-card-text>
          <v-row align="center">
            <v-col cols="12" md="4">
              <v-select
                v-model="selectedClass"
                :items="classes"
                label="Select Class"
                item-title="class_name"
                item-value="id"
                variant="outlined"
                prepend-inner-icon="mdi-school"
              />
            </v-col>
            <v-col cols="12" md="4">
              <v-btn 
                color="primary" 
                @click="saveTimetable" 
                :disabled="!selectedClass || loading"
                :loading="loading"
                size="large"
                prepend-icon="mdi-content-save"
              >
                Save Timetable
              </v-btn>
            </v-col>
            <v-col cols="12" md="4">
              <v-btn 
                color="warning" 
                variant="outlined"
                @click="timetable = generateEmptyTimetable()"
                prepend-icon="mdi-refresh"
              >
                Clear All
              </v-btn>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>

      <!-- Timetable Grid -->
      <v-card elevation="3">
        <v-card-title class="bg-primary text-white">
          <v-icon class="mr-2">mdi-calendar-week</v-icon>
          Weekly Timetable
        </v-card-title>
        
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

      <!-- Edit Dialog -->
      <v-dialog v-model="editDialog" max-width="500px">
        <v-card>
          <v-card-title class="text-h6">
            Edit Period
            <v-spacer></v-spacer>
            <v-btn icon="mdi-close" variant="text" @click="editDialog = false"></v-btn>
          </v-card-title>
          
          <v-card-text>
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
                  item-title="name"
                  item-value="id"
                  variant="outlined"
                  prepend-inner-icon="mdi-account"
                  clearable
                />
              </v-col>
            </v-row>
          </v-card-text>
          
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="grey" variant="text" @click="editDialog = false">
              Cancel
            </v-btn>
            <v-btn color="primary" @click="saveSlot">
              Save
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-container>
  </AdminLayout>
</template>

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
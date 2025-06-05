<script setup>
import TeacherLayout from '@/layouts/TeacherLayout.vue';
import {ref, watch} from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';
import Swal from 'sweetalert2';

const form = useForm({
    klass_id: '',
    subject_id: '',
    date: '',
    attendance: {}
});

const page = usePage();
const klasses = page.props.klasses;
const subjects = page.props.subjects;

const selectedKlass = ref('');
const selectedSubject = ref('');
const selectedDate = ref(new Date().toISOString().substr(0, 10));
const students = ref([]); // Keep as array for data table
const attendance = ref({}); // Separate object for attendance status
const loading = ref(false);

const saveAttendance = () => {
    form.klass_id = selectedKlass.value;
    form.subject_id = selectedSubject.value;
    form.date = selectedDate.value;
    form.attendance = attendance.value;
    
    form.post(route('teacher.store_attendance'), {
        onSuccess: () => {
            Swal.fire('Success!', 'Subject Attendance Saved successfully', 'success');
        },
        onError: (newErrors) => {
            console.error('Attendance save errors:', newErrors);
            Swal.fire('Error!', 'Failed to save attendance', 'error');
        }
    });
};

const getStudents = async () => {
    // Only fetch students if both class and subject are selected
    if (selectedKlass.value && selectedSubject.value) {
        loading.value = true;
        try {
            const response = await axios.get(route('teacher.getStudents'), {
                params: {
                    klass_id: selectedKlass.value,
                    subject_id: selectedSubject.value // Include subject_id in the request
                }
            });
            
            // Keep students as array for data table
            students.value = response.data;
            
            // Initialize attendance object with default status
            attendance.value = {};
            response.data.forEach(student => {
                attendance.value[student.id] = 'present'; // Default to present
            });
            
            console.log('Students:', response.data);
            console.log('Attendance object:', attendance.value);
            
        } catch (error) {
            console.error('Error fetching students:', error);
            students.value = [];
            attendance.value = {};
        } finally {
            loading.value = false;
        }
    } else {
        // Clear students if either class or subject is not selected
        students.value = [];
        attendance.value = {};
    }
};

// Watch for changes in both class and subject
watch([selectedKlass, selectedSubject], getStudents);
</script>

<template>
    <TeacherLayout>
        <v-container fluid>
            <div class="p-6">
                <h1 class="text-h5 font-bold mb-4">Mark Subject Attendance</h1>
            </div>
            
            <v-row>
                <v-col cols="12" md="4">
                    <v-select
                        v-model="selectedKlass"
                        :items="klasses"
                        item-title="class_name"
                        item-value="id"
                        label="Select Class"
                        variant="outlined"
                    />
                </v-col>
                <v-col cols="12" md="4">
                    <v-select
                        v-model="selectedSubject"
                        :items="subjects"
                        item-title="name"
                        item-value="id"
                        label="Select Subject"
                        variant="outlined"
                    />
                </v-col>
                <v-col cols="12" md="4">
                    <v-text-field
                        v-model="selectedDate"
                        label="Date"
                        type="date"
                        variant="outlined"
                    />
                </v-col>
            </v-row>

            <!-- Loading State -->
            <v-card v-if="loading" class="text-center py-12 mb-4">
                <v-card-text>
                    <v-progress-circular indeterminate size="64" color="primary" class="mb-4"></v-progress-circular>
                    <h3 class="text-h6 mb-2">Loading Students</h3>
                    <p class="text-body-2 text-medium-emphasis">Please wait...</p>
                </v-card-text>
            </v-card>

            <!-- No Students State -->
            <v-card v-else-if="students.length === 0 && selectedKlass" class="text-center py-12 mb-4">
                <v-card-text>
                    <v-icon size="80" color="grey-lighten-2" class="mb-4">mdi-account-group</v-icon>
                    <h3 class="text-h6 mb-2">Please Select Subject</h3>
                    <p class="text-body-2 text-medium-emphasis">
                        Select subject .
                    </p>
                </v-card-text>
            </v-card>

            <!-- Students Data Table -->
            <v-card v-else-if="students.length > 0" elevation="2">
                <v-card-title class="bg-blue-lighten-5">
                    <v-icon class="mr-2">mdi-account-group</v-icon>
                    Students Attendance ({{ students.length }} students)
                </v-card-title>
                
                <v-data-table
                    :headers="[
                        { title: 'Student Name', key: 'name' },
                        { title: 'Student Surname', key: 'surname' },
                        { title: 'Attendance Status', key: 'status', sortable: false }
                    ]"
                    :items="students"
                    :items-per-page="10"
                >
                    <template v-slot:item.status="{ item }">
                        <v-select
                            v-model="attendance[item.id]"
                            :items="[
                                { title: 'Present', value: 'present' },
                                { title: 'Absent', value: 'absent' },
                                { title: 'Late', value: 'late' },
                                { title: 'Excused', value: 'excused' }
                            ]"
                            item-title="title"
                            item-value="value"
                            variant="outlined"
                            density="compact"
                            hide-details
                            style="min-width: 120px;"
                        />
                    </template>
                </v-data-table>
                
                <v-card-actions class="px-6 pb-6">
                    <v-spacer></v-spacer>
                    <v-btn
                        color="primary"
                        :disabled="students.length === 0 || !selectedSubject || !selectedDate || form.processing"
                        :loading="form.processing"
                        @click="saveAttendance"
                        prepend-icon="mdi-content-save"
                        variant="flat"
                    >
                        Save Attendance
                    </v-btn>
                </v-card-actions>
            </v-card>

            <!-- No Class Selected -->
            <v-card v-else class="text-center py-12">
                <v-card-text>
                    <v-icon size="80" color="grey-lighten-2" class="mb-4">mdi-school</v-icon>
                    <h3 class="text-h6 mb-2">Select a Class</h3>
                    <p class="text-body-2 text-medium-emphasis">
                        Please select a class to view students.
                    </p>
                </v-card-text>
            </v-card>
        </v-container>
    </TeacherLayout>
</template>
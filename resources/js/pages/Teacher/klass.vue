<script setup>
import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import TeacherLayout from '@/layouts/TeacherLayout.vue' 

const { props } = usePage()

const search = ref('')

const columns = [
    { key: 'class_name', title: 'Class Name' },
    { key: 'year', title: 'Year' },
    { key: 'department', title: 'Department' },
    { key: 'subjects', title: 'Subjects Assigned' },
    { key: 'subject_count', title: 'Total Subjects' },
]

// Helper function for department colors (moved inside setup)
const getDepartmentColor = (department) => {
    const colors = {
        'Science': 'blue',
        'Arts': 'purple',
        'Languages': 'green',
        'commercials': 'orange'
    }
    return colors[department] || 'grey'
}

// Computed properties for dashboard stats
const klasses = computed(() => props.klasses || [])
const totalClasses = computed(() => props.totalClasses || 0)
const totalSubjects = computed(() => props.totalSubjects || 0)
const teacher = computed(() => props.teacher || {})

// Debug computed property
const debugInfo = computed(() => props.debug_info || null)
</script>

<template>
    <TeacherLayout>
        <v-container>
            <!-- Debug Information -->
            <v-row v-if="debugInfo" class="mb-4">
                <v-col>
                    <v-card color="red-lighten-4" class="pa-4">
                        <h3 class="text-h6 mb-2">🐛 Debug Information</h3>
                        <p><strong>Your User ID:</strong> {{ debugInfo.your_user_id }}</p>
                        <p><strong>Teacher ID with Data:</strong> {{ debugInfo.teacher_id_with_data }}</p>
                        <p><strong>Problem:</strong> {{ debugInfo.message }}</p>
                        
                        <v-alert type="error" class="mt-3">
                            <strong>SOLUTION:</strong> Either:
                            <ol class="mt-2">
                                <li>Assign your user ID ({{ debugInfo.your_user_id }}) to classes in the admin panel, OR</li>
                                <li>Login as the user with ID 1, OR</li>
                                <li>Temporarily change the controller to use teacher_id = 1 for testing</li>
                            </ol>
                        </v-alert>
                    </v-card>
                </v-col>
            </v-row>

            <!-- Header Section -->
            <v-row class="mb-4">
                <v-col>
                    <v-card elevation="2" class="pa-4">
                        <v-row align="center" justify="space-between">
                            <v-col>
                                <h1 class="text-h5 font-weight-bold text-primary">
                                    My Classes & Subjects
                                </h1>
                                <p class="text-subtitle-1 text-grey-600 mt-1">
                                    Welcome, {{ teacher.first_name }} {{ teacher.last_name }}
                                </p>
                                <p class="text-caption text-grey-500">
                                    Teacher ID: {{ teacher.id }}
                                </p>
                            </v-col>
                        </v-row>
                    </v-card>
                </v-col>
            </v-row>

            <!-- Stats Cards -->
            <v-row class="mb-4">
                <v-col cols="12" md="6">
                    <v-card elevation="1" class="pa-4 text-center">
                        <v-icon size="48" color="primary" class="mb-2">mdi-school</v-icon>
                        <h2 class="text-h4 font-weight-bold text-primary">{{ totalClasses }}</h2>
                        <p class="text-subtitle-1 text-grey-600">Classes Assigned</p>
                    </v-card>
                </v-col>
                <v-col cols="12" md="6">
                    <v-card elevation="1" class="pa-4 text-center">
                        <v-icon size="48" color="success" class="mb-2">mdi-book-open-variant</v-icon>
                        <h2 class="text-h4 font-weight-bold text-success">{{ totalSubjects }}</h2>
                        <p class="text-subtitle-1 text-grey-600">Subjects Teaching</p>
                    </v-card>
                </v-col>
            </v-row>

            <!-- Search Bar (only show if there are classes) -->
            <v-row class="mb-4" v-if="klasses.length > 0">
                <v-col>
                    <v-card elevation="1" class="pa-4">
                        <v-text-field
                            v-model="search"
                            label="Search classes..."
                            prepend-inner-icon="mdi-magnify"
                            variant="outlined"
                            hide-details
                            clearable
                        />
                    </v-card>
                </v-col>
            </v-row>

            <!-- Classes Data Table -->
            <v-card elevation="2" v-if="klasses.length > 0">
                <v-card-title class="pa-4">
                    <v-icon class="mr-2">mdi-view-list</v-icon>
                    Your Teaching Schedule ({{ klasses.length }} classes)
                </v-card-title>
                <v-divider />
                <v-data-table
                    :headers="columns"
                    :items="klasses"
                    :search="search"
                    class="elevation-0"
                    :items-per-page="10"
                >
                    <!-- Custom slot for subjects display -->
                    <template v-slot:item.subjects="{ item }">
                        <div class="d-flex flex-wrap ga-1">
                            <v-chip 
                                v-for="subject in item.subjects" 
                                :key="subject.id"
                                size="small"
                                color="primary"
                                variant="outlined"
                                class="ma-1"
                            >
                                {{ subject.name }}
                            </v-chip>
                        </div>
                    </template>

                    <!-- Custom slot for subject count -->
                    <template v-slot:item.subject_count="{ item }">
                        <v-chip 
                            :color="item.subject_count > 3 ? 'warning' : 'success'"
                            size="small"
                        >
                            {{ item.subject_count }}
                        </v-chip>
                    </template>

                    <!-- Custom slot for class name -->
                    <template v-slot:item.class_name="{ item }">
                        <div class="d-flex align-center">
                            <v-icon class="mr-2" color="primary">mdi-account-group</v-icon>
                            <span class="font-weight-medium">{{ item.class_name }}</span>
                        </div>
                    </template>

                    <!-- Custom slot for year -->
                    <template v-slot:item.year="{ item }">
                        <v-chip size="small" color="info" variant="outlined">
                            {{ item.year }}
                        </v-chip>
                    </template>

                    <!-- Custom slot for department -->
                    <template v-slot:item.department="{ item }">
                        <v-chip 
                            size="small" 
                            :color="getDepartmentColor(item.department)"
                            variant="outlined"
                        >
                            {{ item.department }}
                        </v-chip>
                    </template>
                </v-data-table>
            </v-card>

            <!-- No Data State -->
            <v-card v-else elevation="2" class="pa-8 text-center">
                <v-icon size="64" color="grey-lighten-1">mdi-school-outline</v-icon>
                <h3 class="text-h6 text-grey-lighten-1 mt-4">No Classes Assigned</h3>
                <p class="text-body-2 text-grey-lighten-1 mb-4">
                    You haven't been assigned to any classes yet.
                </p>
                <v-btn color="primary" variant="outlined" href="/admin" v-if="teacher.role === 'admin'">
                    Go to Admin Panel
                </v-btn>
            </v-card>

            <!-- Additional Info Card (only show if there are classes) -->
            <v-row class="mt-4" v-if="klasses.length > 0">
                <v-col>
                    <v-card elevation="1" class="pa-4">
                        <v-card-title class="text-h6">
                            <v-icon class="mr-2">mdi-information</v-icon>
                            Quick Info
                        </v-card-title>
                        <v-card-text>
                            <v-row>
                                <v-col cols="12" md="4">
                                    <div class="text-center">
                                        <v-icon size="24" color="primary">mdi-calendar</v-icon>
                                        <p class="text-caption text-grey-600 mt-1">Academic Year</p>
                                        <p class="font-weight-medium">{{ new Date().getFullYear() }}</p>
                                    </div>
                                </v-col>
                                <v-col cols="12" md="4">
                                    <div class="text-center">
                                        <v-icon size="24" color="success">mdi-check-circle</v-icon>
                                        <p class="text-caption text-grey-600 mt-1">Status</p>
                                        <p class="font-weight-medium text-success">Active</p>
                                    </div>
                                </v-col>
                                <v-col cols="12" md="4">
                                    <div class="text-center">
                                        <v-icon size="24" color="info">mdi-clock</v-icon>
                                        <p class="text-caption text-grey-600 mt-1">Last Updated</p>
                                        <p class="font-weight-medium">{{ new Date().toLocaleDateString() }}</p>
                                    </div>
                                </v-col>
                            </v-row>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </TeacherLayout>
</template>

<style scoped>
.glass {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.2);
}
</style>
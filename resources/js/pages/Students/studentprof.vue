<script setup>
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import StudentLayout from '@/layouts/StudentLayout.vue'

const student = computed(() => usePage().props.student)
</script>

<template>
    <StudentLayout>
        <v-container class="pa-6">
            <v-card 
                elevation="8" 
                class="mx-auto rounded-lg" 
                max-width="800"
                color="surface"
            >
                <!-- Header with Lock Icon (Indicates Non-Editable) -->
                <v-card-title class="text-h5 font-weight-bold text-primary d-flex align-center">
                    <v-avatar color="primary" size="40" class="mr-3">
                        <v-icon color="white">mdi-account-lock</v-icon> <!-- Changed to "lock" icon -->
                    </v-avatar>
                    Student Profile (Read-Only)
                </v-card-title>
                
                <v-divider class="my-2"></v-divider>
                
                <v-card-text class="pa-6">
                    <!-- Profile Picture (No Upload Option) -->
                    <v-row class="mb-4">
                        <v-col cols="12" class="text-center">
                            <v-avatar size="120" color="primary" class="elevation-6">
                                <span class="text-h3 white--text">
                                    {{ student.name.charAt(0).toUpperCase() }}
                                </span>
                            </v-avatar>
                        </v-col>
                    </v-row>
                    
                    <!-- Read-Only Fields -->
                    <v-row>
                        <v-col cols="12" md="6" class="py-3">
                            <v-card variant="outlined" class="pa-4 rounded-lg">
                                <div class="text-subtitle-1 text-medium-emphasis">Full Name</div>
                                <div class="text-h6">{{ student.name }}</div>
                            </v-card>
                        </v-col>
                        
                        <v-col cols="12" md="6" class="py-3">
                            <v-card variant="outlined" class="pa-4 rounded-lg">
                                <div class="text-subtitle-1 text-medium-emphasis">Email Address</div>
                                <div class="text-h6">{{ student.email }}</div>
                                <v-chip small color="green-lighten-5" class="mt-2">
                                    <v-icon left size="16">mdi-check-decagram</v-icon>
                                    Verified
                                </v-chip>
                            </v-card>
                        </v-col>
                        
                        <v-col cols="12" md="6" class="py-3">
                            <v-card variant="outlined" class="pa-4 rounded-lg">
                                <div class="text-subtitle-1 text-medium-emphasis">Registration Date</div>
                                <div class="text-h6">
                                    {{ new Date(student.created_at).toLocaleDateString('en-US', { 
                                        year: 'numeric', 
                                        month: 'long', 
                                        day: 'numeric' 
                                    }) }}
                                </div>
                            </v-card>
                        </v-col>
                        
                        <v-col cols="12" md="6" class="py-3">
                            <v-card variant="outlined" class="pa-4 rounded-lg">
                                <div class="text-subtitle-1 text-medium-emphasis">Account Role</div>
                                <div class="text-h6">{{ student.role ?? 'Student' }}</div>
                                <v-chip small color="blue-lighten-5" class="mt-2">
                                    <v-icon left size="16">mdi-account-school</v-icon>
                                    Active
                                </v-chip>
                            </v-card>
                        </v-col>
                    </v-row>
                </v-card-text>
                
                <v-divider class="my-2"></v-divider>
                
                <!-- Admin Contact Instructions (Instead of Edit Buttons) -->
                <v-card-actions class="px-6 py-4">
                    <v-alert type="info" variant="tonal" class="w-100">
                        <template v-slot:prepend>
                            <v-icon>mdi-alert-circle</v-icon>
                        </template>
                        <strong>Need changes?</strong> Contact your school administrator to update your profile.
                    </v-alert>
                </v-card-actions>
            </v-card>
        </v-container>
    </StudentLayout>
</template>

<style scoped>
/* Optional: Make it visually clear this is non-editable */
.v-card {
    background-color: #f9f9f9; /* Slightly gray to indicate "locked" state */
}
</style>
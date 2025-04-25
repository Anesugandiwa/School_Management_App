<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue';

// const getCount = (value) => {
//     return typeof value === 'number' ? value : value.length;
// };
</script>

<template>
    <AdminLayout>
        <v-app>
            <v-row no-gutters class="d-flex justify-space-between">
                <!-- Subjects Card -->
                <v-col class="flex-grow-1 flex-shrink-0" style="max-width: 23%">
                    <v-card class="pa-2" height="120" color="blue" dark>
                        <v-card-title class="text-subtitle-2 pa-1">
                            <v-icon left x-small>mdi-book-education</v-icon>
                            Subjects
                        </v-card-title>
                        <v-card-text class="text-h5 font-weight-bold text-center pa-0">
                            {{ $page.props.subjects }}
                        </v-card-text>
                    </v-card>
                </v-col>

                <!-- Teachers Card -->
                <v-col class="flex-grow-1 flex-shrink-0" style="max-width: 23%">
                    <v-card class="pa-2" height="120" color="blue" dark>
                        <v-card-title class="text-subtitle-2 pa-1">
                            <v-icon left x-small>mdi-account-tie</v-icon>
                            Teachers
                        </v-card-title>
                        <v-card-text class="text-h5 font-weight-bold text-center pa-0">
                            {{ $page.props.teachers }}
                        </v-card-text>
                    </v-card>
                </v-col>

                <!-- Students Card -->
                <v-col class="flex-grow-1 flex-shrink-0" style="max-width: 23%">
                    <v-card class="pa-2" height="120" color="blue" dark>
                        <v-card-title class="text-subtitle-2 pa-1">
                            <v-icon left x-small>mdi-account-group</v-icon>
                            Students
                        </v-card-title>
                        <v-card-text class="text-h5 font-weight-bold text-center pa-0">
                            {{  $page.props.students }}
                        </v-card-text>
                    </v-card>
                </v-col>

                <!-- Classes Card -->
                <v-col class="flex-grow-1 flex-shrink-0" style="max-width: 23%">
                    <v-card class="pa-2" height="120" color="blue" dark>
                        <v-card-title class="text-subtitle-2 pa-1">
                            <v-icon left x-small>mdi-school</v-icon>
                            Classes
                        </v-card-title>
                        <v-card-text class="text-h5 font-weight-bold text-center pa-0">
                            {{ $page.props.classes }}
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-app>
    </AdminLayout>
</template>
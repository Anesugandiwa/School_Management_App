<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue';

// const getCount = (value) => {
//     return typeof value === 'number' ? value : value.length;
// };

const props = defineProps({
    students: {
        type: [Array, Number],
        default: () => [],
    },
    subjects: {
        type: [Array, Number],
        default: () => [],
    },
    labels: {
        type: Array,
        default: () => [],
    },
    dataset: {
        type: Array,
        default: () => [],
    },
    studentLabels: {
        type: Array,
        default: () => [],
    },
    studentDataset: {
        type: Array,
        default: () => [],
    },
});

// First chart options (area chart)
const options = {
    chart: {
        id: 'students-chart',
        toolbar: {
            show: true,
        },
    },
    xaxis: {
        categories: props.labels, // FIXED: changed 'students' to 'categories'
    },
    stroke: {
        curve: 'smooth',
        width: 2,
    },
    dataLabels: {
        enabled: false,
    },
    tooltip: {
        enabled: true,
    },
};
const series = [
    {
        name: 'Subjects',
        data: props.dataset,
    },
];

// Bar chart options
const studentOptions = {
    chart: {
        id: 'student-bar',
        toolbar: {
            show: true, // Show toolbar for debugging
        },
    },
    xaxis: {
        categories: props.studentLabels, // FIXED: changed 'subjects' to 'categories'
        labels: {
            show: true,
            rotate: -45,
            rotateAlways: false,
        },
    },
    plotOptions: {
        bar: {
            borderRadius: 5,
            horizontal: false,
            columnWidth: '60%',
            distributed: false,
        },
    },
    dataLabels: {
        enabled: true, // Enable data labels to see the values
        style: {
            fontSize: '12px',
        },
    },
    colors: ['#2E93fA'], // Add explicit colors
    title: {
        text: 'Students per Subject',
        align: 'center',
    },
    grid: {
        show: true,
    },
    tooltip: {
        enabled: true,
    },
};

const studentSeries = [
    {
        name: 'Students',
        data: props.studentDataset,
    }
];


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
            <v-row>
                 <v-col cols="12" md="6">
                    <v-card elevation="2">
                        <v-card-title>Subject Trends</v-card-title>
                        <v-card-text>
                            <apexchart width="100%" height="350" type="area" :options="options" :series="series"></apexchart>
                        </v-card-text>
                    </v-card>
                </v-col>
                <v-col cols="12" md="6">
                    <v-card elevation="2">
                        <v-card-title>Number of Satudents</v-card-title>
                        <v-card-text>
                            <apexchart 
                                width="100%" 
                                height="350" 
                                type="bar" 
                                :options="studentOptions" 
                                :series="studentSeries"
                            ></apexchart>
                        </v-card-text>
                    </v-card>
                </v-col>


            </v-row>
        </v-app>
    </AdminLayout>
</template>
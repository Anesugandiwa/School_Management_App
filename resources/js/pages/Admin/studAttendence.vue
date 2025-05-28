<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue';
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    date: {
        type: String,
        default: () => new Date().toISOString().split('T')[0]
    },
    classLabels: {
        type: Array,
        default: () => []
    },
    presentCounts: {
        type: Array,
        default: () => []
    },
    absentCounts: {
        type: Array,
        default: () => []
    },
    lateCounts: {
        type: Array,
        default: () => []
    },
    stats: {
        type: Object,
        default: () => ({
            total: 0,
            present: 0,
            absent: 0,
            late: 0,
            attendanceRate: 0
        })
    }
});

const selectedDate = ref(props.date);

// Watch for date changes
const dateChanged = () => {
    router.get(route('admin.attendance'), {
        date: selectedDate.value
    }, {
        preserveState: true,
        preserveScroll: true
    });
};

// Chart options for the attendance bar chart
const attendanceOptions = {
    chart: {
        id: 'attendance-bar',
        stacked: true,
        toolbar: {
            show: true
        }
    },
    plotOptions: {
        bar: {
            horizontal: false,
            columnWidth: '60%',
            borderRadius: 5,
        },
    },
    xaxis: {
        categories: props.classLabels,
        labels: {
            style: {
                fontWeight: 'bold'
            },
            rotate: -45,
            trim: false
        }
    },
    yaxis: {
        title: {
            text: 'Number of Students'
        }
    },
    legend: {
        position: 'top',
    },
    colors: ['#4CAF50', '#F44336', '#FFC107'], // Green for present, Red for absent, Yellow for late
    dataLabels: {
        enabled: true
    },
    title: {
        text: 'Class Attendance Overview',
        align: 'center',
        style: {
            fontSize: '18px',
            fontWeight: 'bold'
        }
    },
    tooltip: {
        y: {
            formatter: function(val) {
                return val + " students";
            }
        }
    }
};

// Series data for the chart
const attendanceSeries = [
    {
        name: 'Present',
        data: props.presentCounts
    },
    {
        name: 'Absent',
        data: props.absentCounts
    },
    {
        name: 'Late',
        data: props.lateCounts
    }
];

// For a pie chart showing overall attendance distribution
const pieOptions = {
    chart: {
        type: 'pie',
    },
    labels: ['Present', 'Absent', 'Late'],
    colors: ['#4CAF50', '#F44336', '#FFC107'],
    legend: {
        position: 'bottom'
    },
    responsive: [{
        breakpoint: 480,
        options: {
            chart: {
                width: 300
            },
            legend: {
                position: 'bottom'
            }
        }
    }],
    dataLabels: {
        enabled: true,
        formatter: function(val) {
            return Math.round(val) + "%";
        }
    }
};

const pieSeries = [
    props.stats.present, 
    props.stats.absent, 
    props.stats.late
];
</script>

<template>
    <AdminLayout>
        <v-container>
            <!-- Header with date picker -->
            <v-card class="mb-4">
                <v-card-title class="d-flex justify-space-between align-center">
                    <h2>Student Attendance Dashboard</h2>
                    <v-text-field
                        v-model="selectedDate"
                        type="date"
                        label="Select Date"
                        dense
                        hide-details
                        class="date-picker"
                        style="max-width: 200px;"
                        @change="dateChanged"
                    ></v-text-field>
                </v-card-title>
            </v-card>
            
            <!-- Stats summary cards -->
            <v-row class="mb-4">
                <v-col cols="12" sm="3">
                    <v-card color="primary" dark>
                        <v-card-text class="text-center">
                            <div class="text-h5 mb-0">{{ stats.total }}</div>
                            <div>Total Students</div>
                        </v-card-text>
                    </v-card>
                </v-col>
                <v-col cols="12" sm="3">
                    <v-card color="success" dark>
                        <v-card-text class="text-center">
                            <div class="text-h5 mb-0">{{ stats.present }}</div>
                            <div>Present</div>
                        </v-card-text>
                    </v-card>
                </v-col>
                <v-col cols="12" sm="3">
                    <v-card color="error" dark>
                        <v-card-text class="text-center">
                            <div class="text-h5 mb-0">{{ stats.absent }}</div>
                            <div>Absent</div>
                        </v-card-text>
                    </v-card>
                </v-col>
                <v-col cols="12" sm="3">
                    <v-card color="info" dark>
                        <v-card-text class="text-center">
                            <div class="text-h5 mb-0">{{ stats.attendanceRate }}%</div>
                            <div>Attendance Rate</div>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
            
            <!-- Charts -->
            <v-row>
                <!-- Bar chart showing attendance by class -->
                <v-col cols="12" lg="8">
                    <v-card elevation="2">
                        <v-card-text>
                            <apexchart
                                width="100%"
                                height="400"
                                type="bar"
                                :options="attendanceOptions"
                                :series="attendanceSeries"
                            ></apexchart>
                        </v-card-text>
                    </v-card>
                </v-col>
                
                <!-- Pie chart showing overall attendance distribution -->
                <v-col cols="12" lg="4">
                    <v-card elevation="2">
                        <v-card-title class="text-center">Overall Attendance</v-card-title>
                        <v-card-text>
                            <apexchart
                                width="100%"
                                height="320"
                                type="pie"
                                :options="pieOptions"
                                :series="pieSeries"
                            ></apexchart>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
            
            <!-- No data message if needed -->
            <v-card v-if="stats.total === 0" class="mt-4">
                <v-card-text class="text-center">
                    <v-icon size="large" color="grey">mdi-alert-circle-outline</v-icon>
                    <div class="text-h6 mt-2">No attendance data available for the selected date</div>
                    <div class="text-body-1">Please select a different date or make sure attendance has been recorded</div>
                </v-card-text>
            </v-card>
        </v-container>
    </AdminLayout>
</template>

<style scoped>
.date-picker :deep(.v-field__input) {
    padding-top: 6px;
    padding-bottom: 6px;
}
</style>
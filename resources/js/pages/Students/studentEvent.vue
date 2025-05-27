<script setup>
import { ref, onMounted } from 'vue';
import StudentLayout from '@/layouts/StudentLayout.vue';
import axios from 'axios';


const activityList = ref([]);

onMounted( async() => {
    try{
        const response = await axios.get(route('activities'))
        activityList.value = response.data

    } catch (error){
        console.error('error whilist fetching events ', error)
    }
})

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};
</script>

<template>
  <StudentLayout>
    <v-container>
      <h1 class="text-h4 mb-6">School Activities & Events</h1>
      
      <v-row>
        <v-col 
            v-for="activity in activityList" 
            :key="activity.id" 
            cols="12" 
            md="4"
        >
          <v-card class="mb-4">
            <v-img
              v-if="activity.image_path"
              :src="`/storage/${activity.image_path}`"
              height="200"
            ></v-img>
            
            <v-card-title>{{ activity.title }}</v-card-title>
            
            <v-card-text>
              <p class="mb-2">{{ activity.description }}</p>
              
              <div class="text-caption">
                <div><strong>Date:</strong> {{ formatDate(activity.start_date) }}</div>
                <div v-if="activity.location"><strong>Location:</strong> {{ activity.location }}</div>
                <div><strong>Type:</strong> {{ activity.type }}</div>
              </div>
            </v-card-text>
            
            <v-card-actions>
              <!-- <v-btn 
                :to="route('activities.show', activity.id)" 
                color="primary"
              >
                View Details
              </v-btn> -->
              
              <v-chip 
                :color="activity.requires_registration ? 'info' : 'success'" 
                size="small"
              >
                {{ activity.requires_registration ? 'Registration Required' : 'Open Event' }}
              </v-chip>
            </v-card-actions>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </StudentLayout>
</template>
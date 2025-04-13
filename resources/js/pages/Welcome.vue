<script setup>
import DefaultLayout from '@/layouts/DefaultLayout.vue';
</script>

<template>
  <DefaultLayout>
    <v-container 
        fluid 
        class="fill-height"
        :style="{ backgroundImage: 'url(/images/school-bg.jpg)', backgroundSize: 'cover', backgroundPosition: 'center' }"
    >
      <v-row align="center" justify="center">
        <v-col cols="12" sm="8" md="6" lg="4">
          <v-card class="pa-4 text-center" elevation="6">
            <!-- School Logo -->
            <v-img
              src="/images/school.jpg"
              alt="Cheunje High School Logo"
              contain
              height="150"
              class="mb-4"
            ></v-img>
            
            <v-card-title class="text-h5 font-weight-bold justify-center primary--text">
              Cheunje High School Portal
            </v-card-title>
            
            <v-card-subtitle class="text-subtitle-1 py-2">
              Excellence in Education Since 1985
            </v-card-subtitle>
            
            <v-card-text class="px-4 pb-4">
              <v-divider class="my-4"></v-divider>
              
              <p class="text-body-1 mb-4">
                Access your academic records, assignments, and school resources
              </p>
              
              <!-- Role Selection -->
              <v-row class="mb-4">
                <v-col cols="12" sm="6"  class="d-flex justify-center">
                 
                    <v-btn
                      color="primary"
                      block
                      class="mb-2"

                    >
                      Get started 
                      <v-icon end>mdi-account-school</v-icon>
                    </v-btn>
                  
                </v-col>

              </v-row>
              
              <v-divider class="my-4"></v-divider>
              
              <!-- Quick Links -->
              <div class="d-flex justify-space-around">
                
                  <v-icon small>mdi-calendar-clock</v-icon> Timetable
                
                
                  <v-icon small>mdi-poll</v-icon> Exam Results
                
                
                  <v-icon small>mdi-calendar</v-icon> School Calendar
                
              </div>
            </v-card-text>
            
            <!-- Footer Notice -->
            <v-card-actions class="justify-center">
              <span class="text-caption grey--text">
                © 2023 Cheunje High School. All rights reserved.
              </span>
            </v-card-actions>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </DefaultLayout>
</template>
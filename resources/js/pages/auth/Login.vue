<script setup>
import DefaultLayout from '@/layouts/DefaultLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import ResetPassword from './ResetPassword.vue';


const form = useForm({
    email:'',
    password: '',
    remember: false,

});

const submitForm =() =>{
    form.post(route('login'), {
        onFinish: () => form.reset('Password'),
    });
}



</script>

<template>
  <DefaultLayout>
    <v-container fluid class="bg-gradient">
      <v-form @submit.prevent="submitForm">
        <v-card 
          class="mx-auto auth-card" 
          max-width="500" 
          elevation="10"
          rounded="lg"
        >
          <!-- Header -->
          <v-card-title class="text-center pt-8 text-h4 font-weight-bold text-primary">
            school Portal
            <v-icon end color="primary" class="ml-2">mdi-shield-account</v-icon>
          </v-card-title>

          <v-card-subtitle class="text-center text-medium-emphasis mb-6">
            Restricted access - authorized personnel only
          </v-card-subtitle>

          <v-card-text>
            <!-- Email Field -->
            <v-text-field
              variant="outlined"
              color="primary"
              v-model="form.email"
              prepend-inner-icon="mdi-email-outline"
              label="Enter Email"
              type="email"
              class="mb-4"
              :rules="[v => !!v || 'Email is required']"
              hide-details="auto"
              placeholder="admin@yourdomain.com"
            ></v-text-field>

            <!-- Password Field -->
            <v-text-field
              variant="outlined"
              v-model="form.password"
              color="primary"
              prepend-inner-icon="mdi-lock-outline"
              :append-inner-icon="showPassword ? 'mdi-eye-outline' : 'mdi-eye-off-outline'"
              label="Password"
              :type="showPassword ? 'text' : 'password'"
              @click:append-inner="showPassword = !showPassword"
              class="mb-2"
              :rules="[v => !!v || 'Password is required']"
              hide-details="auto"
            ></v-text-field>

            <!-- Remember Me & Forgot Password -->
            <div class="d-flex align-center mb-6">
              <v-checkbox
                color="primary"
                label="Remember this device"
                density="compact"
                hide-details
              ></v-checkbox>
              <v-spacer></v-spacer>
              <a 
                href="#forgot-password" 
                class="text-caption text-primary text-decoration-none"
              >
                Forgot password?
              </a>
            </div>

            <!-- Login Button -->
            <v-btn
              block
              size="large"
              color="primary"
              rounded="lg"
              class="text-white font-weight-bold py-6"
              type="submit"
            >
              Log In
              <v-icon end>mdi-arrow-right-circle</v-icon>
            </v-btn>
          </v-card-text>

          <!-- Security Notice -->
          <v-card-actions class="justify-center pa-2">
            <span class="text-caption text-medium-emphasis">
              <v-icon small color="warning">mdi-security</v-icon>
              All access attempts are logged
            </span>
          </v-card-actions>
        </v-card>
      </v-form>
    </v-container>
  </DefaultLayout>
</template>
<template>
    <div>
      <h2>My Subjects</h2>
      <ul>
        <li v-for="subject in subjects" :key="subject.id">
          {{ subject.name }}
        </li>
      </ul>
    </div>
  </template>
  
  <script setup>
  defineProps({
    subjects: Array
  });
  </script>
  
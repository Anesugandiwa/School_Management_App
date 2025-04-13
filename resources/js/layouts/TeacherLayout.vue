<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3';

const drawer = ref(null)

const teacherMenus = ref([
    {
        name: "Dashboard",
        
        icon: "mdi-monitor-dashboard",
        id: 1,
    },
    {
        name: "My Classes",
        
        icon: "mdi-account-group",
        id: 2,
    },
    {
        name: "Assignments",
        
        icon: "mdi-notebook-edit",
        id: 3,
    },
    {
        name: "Create Assignment",
        
        icon: "mdi-plus-box",
        id: 4,
    },
    {
        name: "Grades",
        
        icon: "mdi-numeric",
        id: 5,
    },
]);

// function logout() {
//     router.post('/logout', {}, {
//         onSuccess: () => {
//             window.location.reload()
//         },
//     });
// }
</script>

<template>
    <v-app id="teacher-app">
        <v-navigation-drawer v-model="drawer">
            <v-list density="compact">
                <div class="mb-1 mx-1 d-flex align-center">
                    <div>
                        <img height="55" src="/images/school.jpg" alt="Logo"/>
                    </div>
                    <div class="ml-2">
                        <b>{{ $page.props.auth.user.name }}</b> <br />
                        <small class="text-caption">
                            {{ $page.props.auth.user.role }} • {{ $page.props.auth.user.email }}
                        </small>
                    </div>
                </div>
                <v-divider />
                <v-list-subheader class="my-3">Teacher Portal</v-list-subheader>

                <!-- Teacher Menu Items -->
                <InertiaLink
                    v-for="(item, i) in teacherMenus"
                    :key="i"
                    :href="item.path"
                    class="InertiaLink"
                >
                    <v-list-item nav class="mx-3">
                        <template v-slot:prepend>
                            <v-icon :icon="item.icon"></v-icon>
                        </template>
                        <v-list-item-title>&nbsp; {{ item.name }}</v-list-item-title>
                    </v-list-item>
                </InertiaLink>

                <!-- User Profile Section -->
                <div class="pa-6 px-2 userbottom">
                    <v-sheet class="bg-secondary rounded-lg pa-2">
                        <div class="d-flex align-center justify-space-between">
                            <div>
                                <h4 class="ml-1 d-flex align-center font-weight-semibold">
                                    <v-icon class="mr-2">mdi-teach</v-icon>
                                    Teacher Account
                                </h4>
                            </div>
                            <div>
                                <v-btn
                                    @click="logout()"
                                    color="secondary"
                                    icon="mdi-logout"
                                    variant="text"
                                    size="small"
                                />
                            </div>
                        </div>
                    </v-sheet>
                </div>
            </v-list>
        </v-navigation-drawer>

        <v-app-bar color="secondary">
            <v-app-bar-nav-icon @click="drawer = !drawer"></v-app-bar-nav-icon>
            <v-app-bar-title>Teacher Portal</v-app-bar-title>
            <v-spacer/>

            <!-- Quick Actions -->
            <v-btn 
                :href="route('teacher.assignments.create')" 
                prepend-icon="mdi-plus"
                variant="tonal"
                class="mr-2"
            >
                New Assignment
            </v-btn>
            
            <v-btn 
                :href="route('home')" 
                variant="text"
                prepend-icon="mdi-web"
            >
                School Website
            </v-btn>
        </v-app-bar>

        <v-main class="bg-grey-lighten-4">
           <v-container fluid class="mt-3">
               <slot></slot>
           </v-container>
        </v-main>
    </v-app>
</template>
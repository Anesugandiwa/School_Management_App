<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3';

const drawer = ref(null)

const menues = ref([
    {
        name: "Dashboard",
        
        icon: "mdi-monitor-dashboard",
        id: 1,
    },
    {
        name: "Add Staff",
        path: route('admin.AddTeacher.index'),
        icon: "mdi-account-cog",
        id: 2,
    },
    {
        name: "Add Students",
        path: route('admin.AddStudent.index'),
        icon:"mdi-account-multiple",
        id: 3,
    },
    {
        name: "Manage Classes",
        path:route('admin.Klass.index'),
        icon: "mdi-google-classroom",
        id: 4,
    },
    {
        name: "Manage Subjects",
        path: route('admin.Subject.index'),
        icon: "mdi-book-open-page-variant",
        id: 5,
    },
    {
        name: "Attendance",
        icon: "mdi-calendar-check",
        id: 6,
    },
    {
        name: "Reports",
        icon: "mdi-chart-box-outline",
        id: 7,
    },
    {
        name: "Activities & Events",
        icon: "mdi-calendar-multiselect",
        id: 8,
    },
    {
        name: "Manage Fees",
        icon: "mdi-cash-multiple",
        id: 10,
    }



]);

function logout() {
    router.post('/logout', {}, {
        onSuccess: () => {
            window.location.reload()
        },
    });
}
</script>



<template>
    <v-app id="inspire">
        <v-navigation-drawer v-model="drawer">
            <v-list density="compact">
                <div class="mb-1 mx-1  d-flex">
                    <div>
                        <img  height="55" src="/images/school.jpg"  alt="Logo"/>
                    </div>
                   <div>
                       <b>{{ $page.props.auth.user.name }} </b> <br />
                       <small>
                           {{ $page.props.auth.user.email }}
                       </small>
                   </div>
                </div>
                <v-divider />
                <v-list-subheader class="my-3"> Admin Portal </v-list-subheader>

                <!--list-->

                <InertiaLink
                    v-for="(item, i) in menues"
                    :key="i"
                    :href="item.path"
                    class="InertiaLink"
                >
                    <v-list-item nav class="mx-3">
                        <template v-slot:prepend>
                            <v-icon :icon="item.icon"></v-icon>
                        </template>
                        <v-list-item-title>&nbsp; {{ item.name }}</v-list-item-title
                        >
                    </v-list-item>
                </InertiaLink>

                <div class="pa-6 px-2 userbottom">
                    <v-sheet class="bg-primary rounded-lg pa-1">
                        <div class="d-flex align-center justify-space-between">
                            <div>
                                <h4
                                    class="ml-1 d-flex align-center font-weight-semibold"
                                >
                                    {{ $page.props.auth.user.name }}
                                </h4>
                            </div>
                            <div>

                                   <v-btn
                                       @click="logout()"
                                       color="primary"
                                       icon="mdi-location-exit"
                                       variant="elevated"
                                   />
                            </div>
                        </div>
                    </v-sheet>
                </div>
            </v-list>
        </v-navigation-drawer>

        <v-app-bar>
            <v-app-bar-nav-icon @click="drawer = !drawer"></v-app-bar-nav-icon>
            <v-app-bar-title>Application</v-app-bar-title>
            <v-spacer/>

            <v-btn :href="route('home')">
                Website
            </v-btn>
        </v-app-bar>

        <v-main class="bg-grey-lighten-3">
           <v-container fluid class="mt-3">
               <slot></slot>
           </v-container>
        </v-main>
    </v-app>
</template>
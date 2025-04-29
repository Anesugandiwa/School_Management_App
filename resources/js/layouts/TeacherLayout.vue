<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const drawer = ref(null)

const menues = ref([
    {
        name: "Dashboard",
        icon: "mdi-monitor-dashboard",
        path: route('teacher.dis'),
    },
    {
        name: "Profile",
        icon: "mdi-chart-line",
        path: route('teacher.profile'),
    },
    {
        name: "My Subjects",
        icon: "mdi-book-open-page-variant",
        path: route('teacher.klass'),
    },
    {
        name: "Add Marks",
        icon: "mdi-book-open-page-variant",
        path: route('teacher.addmarks'),
    },
    {
        name: "Exams",
        icon: "mdi-file-document-edit",
        // path: route('teacher.exams'),
    },
    {
        name: "Reports",
        icon: "mdi-chart-line",
        path: route('teacher.teachersub'),
    },
])

function logout() {
    router.post('/logout', {}, {
        onSuccess: () => {
            window.location.reload()
        },
    })
}
</script>

<template>
    <v-app id="inspire">
        <v-navigation-drawer v-model="drawer">
            <v-list density="compact">
                <div class="mb-1 mx-1 d-flex">
                    <div>
                        <img height="55" src="/images/school.jpg" alt="Logo"/>
                    </div>
                    <div>
                        <b>{{ $page.props.auth.user.name }} </b><br />
                        <small>{{ $page.props.auth.user.email }}</small>
                    </div>
                </div>

                <v-divider />
                <v-list-subheader class="my-3"> Teacher Portal </v-list-subheader>

                <InertiaLink
                    v-for="(item, i) in menues"
                    :key="i"
                    :href="item.path"
                    class="InertiaLink"
                >
                    <v-list-item nav class="mx-3">
                        <template v-slot:prepend>
                            <v-icon :icon="item.icon" />
                        </template>
                        <v-list-item-title>&nbsp; {{ item.name }}</v-list-item-title>
                    </v-list-item>
                </InertiaLink>

                <div class="pa-6 px-2 userbottom">
                    <v-sheet class="bg-primary rounded-lg pa-1">
                        <div class="d-flex align-center justify-space-between">
                            <div>
                                <h4 class="ml-1 d-flex align-center font-weight-semibold">
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
            <v-app-bar-nav-icon @click="drawer = !drawer" />
            <v-app-bar-title>Teacher Portal</v-app-bar-title>
            <v-spacer />

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

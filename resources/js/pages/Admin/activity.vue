<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue';
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const form = useForm({
    title:'',
    description: '',
    category:'',
    type:'',
    start_date:'',
    end_date:'',
    location:'',
    target_classes:['all'],
    max_participants:null,
    requires_registration:false,
    registration_deadline:'',
    is_mandatory:'',
    requirements:'',
    status:'',

})
const columns = [
    {key:'title', title:'Title'},
    {key: 'description', title: 'Description'},
    {key:'location', title:'Location'},
    {key: 'start_date', title:'Start Date'},
    {key: 'end_date', title:'End Date'},
    {key: 'actions', title:'Actions'},
]
const categories = [
  { value: 'activity', title: 'Activity' },
  { value: 'event', title: 'Event' }
];

const activityTypes = [
  { value: 'academic', title: 'Academic' },
  { value: 'sports', title: 'Sports' },
  { value: 'cultural', title: 'Cultural' },
  { value: 'meeting', title: 'Meeting' },
  { value: 'ceremony', title: 'Ceremony' },
  { value: 'club', title: 'Club' },
  { value: 'other', title: 'Other' }
];

const submitForm =()=> {
    form.post(route('admin.activity.store'));
}

const isDailogopen =ref(false);
const isEditing = ref(false);

const openDialog = () =>{
    isEditing.value = false;
    form.reset();
    isDailogopen.value = true;
}

const closeDialog =() =>{
    isDailogopen.value = false;
    form.reset()


}
const deleteActivity = (activity) => {
    Swal.fire({
        title: 'Are you sure you want to delete this Activity!',
        text: "You won't be able to revert this",
        icon:'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            form.delete(route('admin.activity.destroy', activity.id), {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire(
                        'Deleted!',
                        'Activity has been deleted.',
                        'success'
                    );
                },
                onError: () => {
                    Swal.fire(
                        'Error!',
                        'Something went wrong.',
                        'error'
                    );
                }
            });
        }
    });
};
</script>
<template>
    <AdminLayout>
        <v-container>
            <v-row class="mb-4">
                <v-col>
                    <v-card elevation="1" class="pa-4">
                        <v-row align="center" justify="space-between">
                            <h1 class="text-h6 font-weight-bold">Events And Activities</h1>
                            <v-btn color="success" @click="openDialog">Add New Event and Activity</v-btn>
                        </v-row>
                    </v-card>
                </v-col>

            </v-row>
 
            <v-dialog v-model="isDailogopen" max-width="800">
            <v-card class="mx-auto" max-width="900">
                <v-card-title  class="text-h6">
                        {{ isEditing? 'Edit Event': 'Add new Event' }}
                </v-card-title>
                <v-card-text class="pa-6">
                    <v-form @submit.prevent="submitForm">
                    
                        <v-row>
                            <!-- Basic Information -->
                             <v-col cols="12">
                                <h3 class="text-h6 mb-4 text-primary">Basic Information</h3>
                            </v-col>
                            <v-col cols="12" md="8">
                                <v-text-field
                                    v-model="form.title"
                                    label="Title *"
                                    variant="outlined"
                                    :error-messages="form.errors.title"
                                    required
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12" md="4">
                                <v-select
                                    v-model="form.category"
                                    :items="categories"
                                    label="Category *"
                                    variant="outlined"
                                    :error-messages="form.errors.category"
                                ></v-select>
                            </v-col>
                            <v-col cols="12">
                                <v-textarea
                                    v-model="form.description"
                                    label="Description"
                                    variant="outlined"
                                    rows="4"
                                    :error-messages="form.errors.description"
                                ></v-textarea>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-select
                                    v-model="form.type"
                                    :items="activityTypes"
                                    label="Type *"
                                    variant="outlined"
                                    :error-messages="form.errors.type"
                                ></v-select>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-text-field
                                    v-model="form.location"
                                    label="Location"
                                    variant="outlined"
                                    :error-messages="form.errors.location"
                                    prepend-inner-icon="mdi-map-marker"
                                ></v-text-field>
                            </v-col>
                            <!-- Date and Time -->
                            <v-col cols="12">
                                <v-divider class="my-4"></v-divider>
                                <h3 class="text-h6 mb-4 text-primary">Date & Time</h3>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-text-field
                                    v-model="form.start_date"
                                    label="Start Date & Time *"
                                    type="datetime-local"
                                    variant="outlined"
                                    :error-messages="form.errors.start_date"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-text-field
                                    v-model="form.end_date"
                                    label="End Date & Time"
                                    type="datetime-local"
                                    variant="outlined"
                                    :error-messages="form.errors.end_date"
                                ></v-text-field>
                            </v-col>
                            <!-- Target Classes -->
                            <v-col cols="12">
                                <v-divider class="my-4"></v-divider>
                                <h3 class="text-h6 mb-4 text-primary">Participants</h3>
                            </v-col>
                            <v-col cols="12">
                                <v-select
                                    v-model="form.target_classes"
                                    :items="targetClassOptions"
                                    label="Target Classes *"
                                    variant="outlined"
                                    multiple
                                    chips
                                    :error-messages="form.errors.target_classes"
                                ></v-select>
                            </v-col>
                            <v-col cols="12">
                                <v-switch
                                    v-model="form.is_mandatory"
                                    label="Mandatory Attendance"
                                    color="primary"
                                    hint="Students must attend this activity/event"
                                    persistent-hint
                                ></v-switch>
                            </v-col>
                            <!-- Registration Settings -->
                             <v-col cols="12">
                                <v-divider class="my-4"></v-divider>
                                <h3 class="text-h6 mb-4 text-primary">Registration Settings</h3>
                            </v-col>
                            <v-col cols="12">
                                <v-switch
                                    v-model="form.requires_registration"
                                    label="Requires Registration"
                                    color="primary"
                                    hint="Students need to register for this activity"
                                    persistent-hint
                                ></v-switch>
                            </v-col>
                            <template v-if="form.requires_registration">
                                <v-col cols="12" md="6">
                                    <v-text-field
                                        v-model="form.max_participants"
                                        label="Max Participants"
                                        type="number"
                                        variant="outlined"
                                        :error-messages="form.errors.max_participants"
                                        hint="Leave empty for unlimited"
                                        persistent-hint
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-text-field
                                        v-model="form.registration_deadline"
                                        label="Registration Deadline"
                                        type="datetime-local"
                                        variant="outlined"
                                        :error-messages="form.errors.registration_deadline"
                                    ></v-text-field>
                                </v-col>
                            </template>
                            <!-- Requirements -->
                            <v-col cols="12">
                                <v-textarea
                                    v-model="form.requirements"
                                    label="Requirements"
                                    variant="outlined"
                                    rows="3"
                                    :error-messages="form.errors.requirements"
                                    hint="What students need to bring or prepare"
                                    persistent-hint
                                ></v-textarea>
                            </v-col>
                            <!-- Image Upload -->
                            <v-col cols="12">
                                <v-divider class="my-4"></v-divider>
                                <h3 class="text-h6 mb-4 text-primary">Image</h3>
                            </v-col>
                        </v-row>
                    </v-form>
                </v-card-text>
                <v-card-actions class="px-6 pb-6">
                    <v-btn 
                        variant="flat"
                        size="large"
                        color="warning"
                        @click="closeDialog"
                    >
                      Cancel
                    </v-btn>
                    <v-spacer></v-spacer>
                    <v-btn 
                        @click="submitForm"
                        type="submit" 
                        color="primary"
                        size="large"
                        :loading="form.processing"
                        :disabled="form.processing"
                        prepend-icon="mdi-check"
                        variant="flat"
                    >
                        Create {{ form.category === 'activity' ? 'Activity' : 'Event' }}
                    </v-btn>
                </v-card-actions>
        </v-card>
        </v-dialog>
        <div class="glass pa-3">
            <v-data-table
                :headers="columns"
                :items="$page.props.activities"
                :search="search"
            >
            <template v-slot:item.actions="{item}">
                <div class="d-flex">
                    <v-btn color="info" class="mx-1 no-uppercase" @click="viewActivity(item)">
                        View
                    </v-btn>
                    <v-btn class="mx-1 no-uppercase" @click="editActivity(item)">
                        Edit
                    </v-btn>
                    <v-btn color="error" class="mx-1 no-uppercase" @click="deleteActivity(item)">
                        Delete
                    </v-btn>

                </div>

                </template>

            </v-data-table>

        </div>
    </v-container>
</AdminLayout>

</template>
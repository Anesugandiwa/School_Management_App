<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue';
import { useForm } from '@inertiajs/vue3'
import { ref } from 'vue';
import Swal from 'sweetalert2';

const isDialogOpen = ref(false)
const isEditing = ref(false)

const form = useForm({
    id: null,
    name: '',
    teacher_id: '',
    is_optional: false,
    department: '',
})

const errors = ref({})



const openDialog = () => {
    form.reset()
    isEditing.value = false
    isDialogOpen.value = true
}

const closeDialog = () => {
    form.reset()
    isDialogOpen.value = false
}

const submitForm = () => {
    if (isEditing.value) {
        form.put(route('admin.subjects.update', form.id), {
            onSuccess: () => {
                closeDialog()
                Swal.fire('Updated!', 'Subject updated successfully.', 'success')
            },
            onError: (newErrors) => {
                errors.value = newErrors
            }
        })
    } else {
        form.post(route('admin.subjects.store'), {
            onSuccess: () => {
                closeDialog()
                Swal.fire('Success!', 'Subject added successfully.', 'success')
            },
            onError: (newErrors) => {
                errors.value = newErrors
            }
        })
    }
}

const editSubject = (subject) => {
    form.id = subject.id
    form.name = subject.name
    form.teacher_id = subject.teacher_id
    form.is_optional = subject.is_optional
    form.department = subject.department
    isEditing.value = true
    isDialogOpen.value = true
}
</script>

<template>
    <AdminLayout>
        <v-container>
            <v-row>
                <v-col>
                    <v-card class="pa-4">
                        <v-row justify="space-between" align="center">
                            <h2 class="text-h6 font-weight-bold">Subjects</h2>
                            <v-btn color="primary" @click="openDialog">Add Subject</v-btn>
                        </v-row>
                    </v-card>
                </v-col>
            </v-row>

            <v-dialog v-model="isDialogOpen" max-width="600">
                <v-card class="pa-4">
                    <v-card-title>
                        {{ isEditing ? 'Edit Subject' : 'Add New Subject' }}
                    </v-card-title>

                    <v-card-text>
                        <v-form @submit.prevent="submitForm">
                            <v-text-field
                                v-model="form.name"
                                label="Subject Name"
                                placeholder="Mathematics"
                                :error-messages="errors.value?.name"
                                prepend-inner-icon="mdi-book"
                                dense
                                outlined
                            />

                            <v-select
                                v-model="form.teacher_id"
                                :items="teachers"
                                item-value="id"
                                item-title="name"
                                label="Assign Teacher"
                                :error-messages="errors.value?.teacher_id"
                                prepend-inner-icon="mdi-account"
                                dense
                                outlined
                            />

                            <v-select
                                v-model="form.department"
                                :items="['Science', 'Arts', 'Commerce', 'Languages']"
                                label="Department"
                                placeholder="Select department"
                                :error-messages="errors.value?.department"
                                dense
                                outlined
                            />

                            <v-switch
                                v-model="form.is_optional"
                                label="Is Optional?"
                                inset
                            />
                        </v-form>
                    </v-card-text>

                    <v-card-actions>
                        <v-btn color="warning" @click="closeDialog">Cancel</v-btn>
                        <v-btn color="primary" @click="submitForm">
                            {{ isEditing ? 'Update Subject' : 'Add Subject' }}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <!-- You can add a v-data-table below here to list subjects -->
        </v-container>
    </AdminLayout>
</template>

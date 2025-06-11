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
    // teacher_id: '',
    teacher: '', 
    is_optional: false,
    department: '',
})

const errors = ref({
    name: '',
    // teacher_id: '',
    teacher: '',
    is_optional: '',
    department: '',


})

const columns = [
    {key: 'id', title:'ID'},
    {key: 'name', title: 'Subject Name'},
    {key:'department', title: 'Department'},
    {key:'actions', title: 'Actions'},
]



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
        form.put(route('admin.Subject.update', form.id), {
            onSuccess: () => {
                closeDialog()
                Swal.fire('Updated!', 'Subject updated successfully.', 'success')
            },
            onError: (newErrors) => {
                errors.value = newErrors
            }
        })
    } else {
        form.post(route('admin.Subject.store'), {
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
const deleteSubject = (subject) =>{
    Swal.fire({
        title: 'Are you sure you want to delete this Student!',
        text: "You won't be able to revert this",
        icon:'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((resultg) => {
        if (resultg.isConfirmed){
            form.delete(route('admin.Subject.destroy', subject.id),{
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire(
                        'Deleted!',
                        'Student has been deleted.',
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

            })
        
        }
    })

    
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

                            <!-- <v-select
                                v-model="form.teacher_id"
                                :items="teachers"
                                item-value="id"
                                item-title="name"
                                label="Assign Teacher"
                                :error-messages="errors.value?.teacher_id"
                                prepend-inner-icon="mdi-account"
                                dense
                                outlined
                            /> -->
                            <v-text-field
                            v-model="form.teacher"
                            label="Teacher"
                            placeholder="teacher"
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
                        <v-btn color="warning" @click="closeDialog" variant="flat">Cancel</v-btn>
                        <v-btn color="primary" @click="submitForm" variant="flat">
                            {{ isEditing ? 'Update Subject' : 'Add Subject' }}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <v-data-table
                :headers="columns"
                :items="$page.props.subjects"
                :search
            >
            <template v-slot:item.actions ="{item}">
                <div class="d-flex">
                    <!-- <v-btn color="info" class="mx-1 no-uppercase" @click="viewStudent(item)">
                        View

                    </v-btn> -->
                    <v-btn class="mx-1 no-uppercase" @click="editSubject(item)">
                        Edit

                    </v-btn>
                    <v-btn color="error" class="mx-1 no-uppercase" @click="deleteSubject(item)">
                        Delete
                    </v-btn>

                </div>

            </template>

            </v-data-table>
        </v-container>
    </AdminLayout>
</template>

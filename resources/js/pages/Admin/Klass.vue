<script setup>
import { ref, onMounted} from 'vue'
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import Swal from 'sweetalert2'
import axios from 'axios'

const teachersList = ref([])
const subjectsList = ref([])
onMounted(async() => {
    try {
        const response = await axios.get(route('admin.AddTeacher.index'))
        teachersList.value = response.data
    } catch (error){
        console.error('error while fetching teachers', error)
    }
})
onMounted(async()=> {
    try {
        const res = await axios.get(route('admin.Subject.index'))
        subjectsList.value = res.data
    } catch (error){
        console.error('erro while fetching subjects', error)
    }
})
const columns = [
    { key: 'id', title: 'ID' },
    { key: 'class_name', title: 'Class Name' },
    { key: 'year', title: 'Year'  },
    { key: 'department',  title: 'Department'},
    { key: 'teacher',  title: 'Teacher'},
    {key: 'subjects', title: 'Subjects'},
    { key: 'actions',  title: 'Actions'  },
]
const form = useForm({
    class_name: '',
    year: new Date().getFullYear(),
    department: '',
    teachers: null,
    subjects: null,
})

const errors = ref({
    class_name: '',
    year: '',
    department: '',
    teachers: '',
    subjects:''
})

const isDialogOpen = ref(false)
const isEditing = ref(false)
// const teachers = $page.props.teachers 

const openDialog = () => {
    isEditing.value = false
    form.reset()
    isDialogOpen.value = true
}

const closeDialog = () => {
    isDialogOpen.value = false
    form.reset()
}

const submitForm = () => {
    if (isEditing.value) {
        form.put(route('admin.Klass.update', form.id), {
            onSuccess: () => {
                closeDialog()
                Swal.fire('Success!', 'Class updated successfully.', 'success')
            },
            onError: (newErrors) => {
                errors.value = newErrors
            }
        })
    } else {
        form.post(route('admin.Klass.store'), {
            onSuccess: () => {
                closeDialog()
                Swal.fire('Success!', 'Class added successfully.', 'success')
            },
            onError: (newErrors) => {
                errors.value = newErrors
            }
        })
    }
}
const deleteClass = (klass) => {
    Swal.fire({
        title: 'Are you sure you want to delete this Class!',
        text: "You won't be able to revert this",
        icon:'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'

    }).then((result)=> {
        if(result.isConfirmed){
            form.delete(route('admin.Klass.destroy', klass.id), {
                onSuccess: () => {
                    Swal.fire(
                        'Deleted!',
                        'Class has been deleted.',
                        'success'
                    )
                },
                onError: () =>{
                    Swal.fire(
                        'Error!',
                        'Something went wrong.',
                        'error'
                    )
                }
            })
        }
    })


}


const editClass = (klass) =>{
  
  isEditing.value       = true
  form.id               = klass.id
  form.class_name       = klass.class_name
  form.year             = klass.year
  form.department       = klass.department
  form.teachers         = klass.teachers
  isDialogOpen.value =        true
}
const viewClass = (klass) => {
    router.visit(route('admin.Klass.show', klass.id));
}
</script>

<template>
    <AdminLayout>
        <v-container>
            <v-row class="mb-4">
                <v-col>
                    <v-card elevation="1" class="pa-4">
                        <v-row align="center" justify="space-between">
                            <h1 class="text-h6 font-weight-bold"> Classes </h1>
                            <v-btn color="success" @click="openDialog">Add Class</v-btn>
                        </v-row>
                    </v-card>
                </v-col>
            </v-row>

            <v-dialog v-model="isDialogOpen" max-width="600">
                <v-card class="pa-4" elevation="2">
                    <v-card-title class="text-h6">
                        {{ isEditing ? 'Edit Class' : 'Add New Class' }}
                    </v-card-title>
                    <v-card-text>
                        <v-form @submit.prevent="submitForm">
                            <v-row dense>
                                <v-col cols="12">
                                    <v-text-field
                                        v-model="form.class_name"
                                        label="Class Name"
                                        :error-messages="errors.class_name"
                                        required
                                        outlined
                                    />
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-text-field
                                        v-model="form.year"
                                        label="Year"
                                        type="number"
                                        :error-messages="errors.year"
                                        outlined
                                    />
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-select
                                        v-model="form.department"
                                        :items="['Science', 'Arts', 'Languages', 'commercials']"
                                        label="Department"
                                        :error-messages="errors.department"
                                        required
                                        outlined
                                    />
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-select
                                        v-model="form.teachers"
                                        :items="$page.props.teachers"
                                        item-title="first_name"
                                        item-value="id"
                                        label="Class Teacher"
                                        :error-messages="errors.teacher_id"
                                        outlined
                                        clearable
                                    />
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-select
                                        v-model="form.subjects"
                                        :items ="$page.props.subjects"
                                        item-title="name"
                                        item-value="id"
                                        label="select subject(s)"
                                        :error-messages="errors.subjects"
                                        outlined
                                        clearable
                                        multiple
                                    />

                                </v-col>
                            </v-row>
                        </v-form>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer />
                        <v-btn color="red" @click="closeDialog">Cancel</v-btn>
                        <v-btn color="green" @click="submitForm">
                            {{ isEditing ? 'Update Class' : 'Save Class' }}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
            <div class="glass pa-3">
                <v-data-table
                    :headers="columns"
                    :items="$page.props.klasses"
                    :search="search"
                >
                <template v-slot:item.teacher="{ item }">
                    <v-chip v-if="item.teacher">
                        {{ item.teacher.first_name }}
                    </v-chip>
                </template>
                <template v-slot:item.subjects="{item}">
                    <v-chip v-if="item.subjects" v-for="subject in item.subjects" :key="subject.id">
                        {{ subject.name }}
                    </v-chip>

                </template>



                <template v-slot:item.actions="{ item }">
                    <div class="d-flex">
                        <v-btn color="info" class="mx-1 no-uppercase" @click="viewClass(item)">
                            View
                        </v-btn>
                        <v-btn class="mx-1 no-uppercase" @click="editClass(item)">
                            Edit
                        </v-btn>
                        <v-btn color="error" class="mx-1 no-uppercase" @click="deleteClass(item)">
                            Delete
                        </v-btn>
                    </div>
                </template>
            </v-data-table>
        </div>
        </v-container>
    </AdminLayout>
</template>

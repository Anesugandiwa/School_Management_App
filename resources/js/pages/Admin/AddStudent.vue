<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue';
import { useForm } from '@inertiajs/vue3'
import Swal from 'sweetalert2';
import { ref, onMounted } from 'vue';
import axios from 'axios';

const classList = ref([]);

onMounted( async() => {
    try{
        const response = await axios.get(route('admin.Klass.index'))
        classList.value = response.data
    } catch (error) {
        console.error('error whilist fetching the class', error)

    }
})

const form = useForm({
    name: '',
    surname: '',
    gender:'',
    date_of_birth:'',
    address:'',
    contact:'',
    nationality:'',
    klasses: null,

})
const errors = ref({
        name: '',
        surname: '',
        gender:'',
        date_of_birth: '',
        address:'',
        contact:'',
        nationality:'',
        klasses:'',
    })
const columns = [
    {key:'id', title: 'ID'},
    {key:'name' , title:'First Name'},
    {key:'surname' , title:'Surname'},
    {key:'klass' , title:'Class'},
    {key:'actions' , title:'Actions'},
]
// initialising the   dialog
const isDialogOpen = ref(false)
const isEditing = ref(false)
// opening the dialog
const openDialog = () =>{
    isEditing.value = false
    form.reset()
    isDialogOpen.value = true
    
}
// closing the dialog
const closeDialog = () =>{
    isDialogOpen.value =false
    form.reset()
}

const submitForm =() =>{
    if (isEditing.value){
        form.put(route('admin.AddStudent.update', form.id), {
            onSuccess: () =>{
                closeDialog()
                Swal.fire('success!', 'Student has been updated successfully', 'success')
            },
            onError: (newErrors) => {
                errors.value = newErrors

            }
        })

    } else {
        form.post(route('admin.AddStudent.store'),{
            onSuccess: ()=> {
                closeDialog()
                Swal.fire('success!', 'student has been added successfully.', 'success')
            },
            onError:(newErrors) => {
                errors.value = newErrors
            }
        })
    }

}
const deleteStudent = (student) => {
    Swal.fire({
        title: 'Are you sure you want to delete this Student!',
        text: "You won't be able to revert this",
        icon:'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            form.delete(route('admin.AddStudent.destroy', student.id), {
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
            });
        }
    });
};

const viewStudent= (student) => {
    router.visit(route('admin.AddStudent.show', student.id));
}

const editStudent = (student) => {
    isEditing.value       = true
  form.id               = student.id
  form.name       = student.name
  form.surname             = student.surname
  form.gender       = student.gender
  form.date_of_birth         = student.date_of_birth
  form.address         = student.address
  form.contact         = student.contact
  form.nationality         = student.nationality
  form.klass         = student.klass
  isDialogOpen.value =        true
}

</script>
<template>
    <AdminLayout>
        <v-container>
            <v-row class="mb-4">
                <v-col>
                    <v-card elevation="1" class="pa-4">
                        <v-row align="center" justify="space-between">
                            <h1 class="text-h6 font-weight-bold">Students</h1>
                            <v-btn color="success" @click="openDialog">Add students</v-btn>
                        </v-row>
                    </v-card>
                </v-col>

            </v-row>
            <v-dialog   v-model="isDialogOpen" max-width="800">
                <v-card class="pa-4" elevation="2">
                    <v-card-title  class="text-h6">
                        {{ isEditing? 'Edit student': 'Add new Student' }}
                    </v-card-title>
                    <v-card-text>
                        <v-form @submit.prevent="submitForm">
                            <v-row>
                                <v-col cols="12" md="6">
                                    <v-text-field
                                        v-model="form.name"
                                        label="First Name"
                                        placeholder="Anesu"
                                        :error-messages="errors.name"
                                        outlined
                                        dense
                                        clearable
                                        prepend-inner-icon="mdi-account"
                                        required
                                    />
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-text-field
                                        v-model="form.surname"
                                        label="Surname"
                                        placeholder="Gandiwa"
                                        :error-messages="errors.surname"
                                        outlined
                                        dense
                                        clearable
                                        prepend-inner-icon="mdi-account"
                                        required
                                    />
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-select
                                        v-model="form.gender"
                                        label="Gender"
                                        :items="['Male', 'Female', 'Other']"
                                        placeholder="Select Gender"
                                        :error-messages="errors.gender"
                                        outlined
                                        dense
                                        prepend-inner-icon="mdi-gender-male-female"
                                        required
                                    />
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-text-field
                                        v-model="form.date_of_birth"
                                        label="Date of Birth"
                                        placeholder="YYYY-MM-DD"
                                        :error-messages="errors.date_of_birth"
                                        outlined
                                        dense
                                        prepend-inner-icon="mdi-calendar"
                                        required
                                        hint="Enter date in YYYY-MM-DD format"
                                        persistent-hint
                                    />
                                </v-col>

                                <v-col cols="12">
                                    <v-text-field
                                        v-model="form.address"
                                        label="Address"
                                        placeholder="123 Main Street, Harare"
                                        :error-messages="errors.address"
                                        outlined
                                        dense
                                        clearable
                                        prepend-inner-icon="mdi-home-map-marker"
                                        required
                                    />
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-text-field
                                        v-model="form.contact"
                                        label="Parent Contact"
                                        placeholder="+1234567890"
                                        :error-messages="errors.contact"
                                        outlined
                                        dense
                                        clearable
                                        prepend-inner-icon="mdi-phone"
                                        required
                                    />
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-text-field
                                        v-model="form.nationality"
                                        label="Nationality"
                                        placeholder="e.g. American"
                                        :error-messages="errors.nationality"
                                        outlined
                                        dense
                                        clearable
                                        prepend-inner-icon="mdi-earth"
                                        required
                                    />
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-select
                                        v-model="form.klasses"
                                        label="Class"
                                        :items="$page.props.klasses"
                                        item-title="class_name"
                                        item-value="id"
                                        placeholder="Select Class"
                                        :error-messages="errors.klasses"
                                        outlined
                                        dense
                                        prepend-inner-icon="mdi-school"
                                        clearable
                                    />
                                </v-col>
                            </v-row>
                        </v-form>
                    </v-card-text>
                    
                    <v-card-actions>
                        <v-btn color="warning" @click="closeDialog">cancel</v-btn>
                        <v-btn color="primary" @click="submitForm">
                            {{ isEditing? 'Update Student Details' : 'Add Student' }}
                        </v-btn>
                    </v-card-actions>
                </v-card>

            </v-dialog>
            <div class="glass pa-3">
                <v-data-table
                    :headers="columns"
                    :items="$page.props.students"
                    :search="search"
                >
                <template v-slot:item.klass="{item}">
                    <v-chip v-if="item.klass">
                        {{ item.klass.class_name }}

                    </v-chip>

                </template>
                <template v-slot:item.actions="{item}">
                    <div class="d-flex">
                        <v-btn color="info" class="mx-1 no-uppercase" @click="viewStudent(item)">
                            View
                        </v-btn>
                        <v-btn class="mx-1 no-uppercase" @click="editStudent(item)">
                            Edit
                        </v-btn>
                        <v-btn color="error" class="mx-1 no-uppercase" @click="deleteStudent(item)">
                            Delete
                        </v-btn>

                    </div>

                </template>

                </v-data-table>


            </div>
        </v-container>
    </AdminLayout>
</template>
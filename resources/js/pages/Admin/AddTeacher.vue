<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'


const columns = [
    { key: 'id', title: 'ID' },
    { key: 'first_name', title: 'First Name' },
    { key: 'last_name', title: 'Last Name'  },
    { key: 'department',  title: 'Department'},
    { key: 'subject',  title: 'Subject'},
    { key: 'actions',  title: 'Actions'  },
]

const form =useForm({
    first_name: '',
    last_name: '',
    gender: [],
    Date_Of_Birth: '',
    national_id: '',
    email: '',
    phone: '',
    address: '',
    department: '',
    password:'',
    user_id: '',
    subject: '',
    klass_id: ''
});
const errors = ref({
    first_name: '',
    last_name: '',
    gender:'',
    Date_Of_Birth: '',
    national_id: '',
    email: '',
    phone: '',
    address: '',
    department: '',
    password: '',
    subject: '',

    
})
// initialising the state of the Dialog
const isDialogOpen = ref(false)
const isEditing   =ref(false)

// opening Dialog
const openDialog = () =>{
    isEditing.value = false
    form.reset()
    isDialogOpen.value =true
    
}

// closing the Dialog

const closeDialog = () =>{
    isDialogOpen.value = false

    form.reset()
}
const submitForm = () => {
    if (isEditing.value){
        form.put(route('', form.id), {
            onSuccess: () =>{
                closeDialog()
                Swal.fire('Success!', 'Teacher updated successfully.','success')
            },
            onError: (newErrors) => {
                errors.value = newErrors
            }
        })
    } else {
        form.post(route(''), {
            onSuccess: ()  => {
                closeDialog()
                Swal.fire('Success!', 'Teacher Added succefully.', 'success')
            },
            onError: (newErrors) => {
                errors.value =newErrors
            }
        })
    }
    const deletTeacher = (teacher) => {
    Swal.fire({
        title: 'Are you sure you want to delete this teacher!',
        text: "You won't be able to revert this",
        icon:'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'

    }).then((result)=> {
        if(result.isConfirmed){
            form.delete(route(), {
                onSuccess: () => {
                    Swal.fire(
                        'Deleted!',
                        'Teacher has been deleted.',
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
const editTeacher = (teacher) =>{
  
  isEditing.value          =       true
  form.id             =   teacher.id
  form.first_name          =        teacher.first_name
  form.last_name =          teacher.last_name
 
  form.gender =           teacher.gender
  form.Date_Of_Birth =             teacher.Date_Of_Birth
  form.national_id =             teacher.national_id
  form.email  =      teacher.email
  form.phone =           teacher.phone
  form.address =           teacher.address
  form.department =           teacher.department
  form.password =           teacher.password
  form.subject =           teacher.subject
  
  
  isDialogOpen.value =        true
}
}

</script>
<template>
    
        <v-container>
            <v-row class="mb-4">
                <v-col>
                    <v-card elevation="1" class="pa-4">
                        <v-row align="center" justify="space-between">
                            <h1 class ="text-h6 font-weight-bold"> Teachers</h1>
                            <v-btn color="success" @click="isDialogOpen = !isDialogOpen">Add Teacher</v-btn>

                        </v-row>

                    </v-card>
                </v-col>
                <v-dialog v-model="isDialogOpen" max-width="800">
            <v-card class="pa-4" elevation="2">
                <v-card-title class="text-h6" >
                    {{ isEditing? 'Edit Teacher' : 'Add New Teacher' }}
                </v-card-title>
                <v-card-text>
                    <v-form @submit.prevent="submitForm">
                        <v-row>
                            <v-col cols="12" md="6" lg="4" sm="3">
                                <v-text-field
                                    v-model="form.first_name"
                                    label="First Name"
                                    required
                                    :error-messages="errors.first_name"
                                />
                            </v-col>
                            <v-col cols="12" md="6" lg="4" sm="3">
                                <v-text-field
                                    v-model="form.last_name"
                                    label="Last Name"
                                    required
                                    :error-messages="errors.last_name"
                                />
                            </v-col>
                            <v-col cols="12" md="9" lg="4" sm="3">
                                <v-text-field
                                    v-model="form.Date_Of_Birth"
                                    label="Date Of Birth"
                                    required
                                    :error-messages="errors.Date_Of_Birth"
                                />
                            </v-col>
                            <v-col cols="12" md="6" lg="4" sm="3">
                                <v-text-field
                                    v-model="form.national_id"
                                    label="National Id "
                                    required
                                    :error-messages="errors.national_id"
                                
                                />
                            </v-col>
                            <v-col cols="12" md="6" lg="4" sm="3">
                                <v-text-field
                                    v-model="form.email"
                                    label="Email "
                                    required
                                    :error-messages="errors.email"
                                
                                />
                            </v-col>
                            <v-col cols="12" md="6" lg="4" sm="3">
                                <v-text-field
                                    v-model="form.phone"
                                    label="Phone Number "
                                    required
                                    :error-messages="errors.phone"
                                
                                />
                            </v-col>
                            <v-col cols="12" md="6" lg="4" sm="3">
                                <v-textarea
                                    v-model="form.address" 
                                    label="Address"
                                    required
                                    :error-messages="errors.address"
                                
                                />
                            </v-col>
                            <v-col cols="12" md="6" lg="4" sm="3">
                                <v-select v-model="form.gender"  multiple></v-select>
                                    <option>Male</option>
                                    <option>Female</option>
                                    <option>Other</option>
                            </v-col>
                            <v-col cols="12" md="6" lg="4" sm="3">
                                <v-select v-model="form.department" multiple></v-select>
                                    <option>Science</option>
                                    <option>Arts</option>
                                    <option>Languages</option>
                                    <option>commercials</option>
                            </v-col>
                            <v-col cols="12" md="6" lg="4" sm="3">
                                <v-text-field
                                    v-model="form.subject"
                                    label="Subjects"
                                    required
                                    :error-messages="errors.subject"
                                />

                            </v-col>
                        </v-row>
                    </v-form>

                </v-card-text>
                <v-card-actions>
                    <v-spacer />
                    <v-btn color="red" @click="closeDialog">Cancel</v-btn>
                    <v-btn color="green" @click="submitForm">
                        {{ isEditing? 'Update Teacher Details' : 'Save Teacher' }}
                    </v-btn>
                </v-card-actions>

            </v-card>
                </v-dialog>
        </v-row>
        <div class="glass pa-3">
                <v-data-table
                    :headers="columns"
                    :items="$page.props.teachers"
                    :search="search"
                >


                <template v-slot:item.actions="{ item }">
                    <div class="d-flex">
                        <v-btn color="info" class="mx-1 no-uppercase" @click="viewTeacher(item)">
                            View
                        </v-btn>
                        <v-btn class="mx-1 no-uppercase" @click="editTeacher(item)">
                            Edit
                        </v-btn>
                        <v-btn color="error" class="mx-1 no-uppercase" @click="deletTeacher(item)">
                            Delete
                        </v-btn>
                    </div>
                </template>
            </v-data-table>
        </div>
        </v-container>
    

</template>
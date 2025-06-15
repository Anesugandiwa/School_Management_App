<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue';
import axios from 'axios';
import { useForm, usePage  } from '@inertiajs/vue3';
import {ref, onMounted, computed} from 'vue';
import Swal from 'sweetalert2';

const { props } = usePage();

const teachersList = ref([])
const subjectList = ref([])
const klassList = ref([])

const editingItem = ref(null)

const form = useForm({
    teachers: null,
    subjects: null,
    klasses: null,
})
const errors = ref({
    teachers: '',
    subjects: '',
    klasses: '',

})

onMounted(async()=> {
    try {
        const response = await axios.get(route('admin.AddTeacher.index'))
        teachersList.value = response.data
    } catch (error){
        console.error('error while fetching teachers', error)
    }
})

onMounted( async()=> {
    try {
        const respo = await axios.get(route('admin.Klass.index'))
        klassList.value = respo.data
    } catch (error){
        console.error('error while fetching subjects', error)
    }
})
onMounted( async()=> {
    try {
        const resp = await axios.get(route('admin.Subject.index'))
        subjectList.value = resp.data
    } catch (error){
        console.error('error while fetching subjects', error)
    }
})
const columns = ref([
  { key: 'teacher_name', title: 'Teacher' },
  { key: 'class_name', title: 'Class' },
  { key: 'subject_name', title: 'Subjects' },
  
  { key: 'actions', title: 'Actions' },
]);




const isDialogOpen = ref(false)
const isEditing = ref(false)

const openDialog = () =>{
    isEditing.value = false
    
    isDialogOpen.value =true
}

const closeDialog = () => {
    isDialogOpen.value = false
    form.reset()
}

const saveData =() =>{
    if (isEditing.value) {
        form.put(route('admin.subjectTeacher.update', form.id), {
            onSuccess: () => {
                closeDialog()
                Swal.fire('Success!', ' updated successfully.', 'success')
                Inertia.reload({ only: ['klassSubjectTeacher'] }); 
            },
            onError: (newErrors) => {
                errors.value = newErrors
            }
        })
    } else {
        form.post(route('admin.subjectTeacher.store'), {
            onSuccess: () => {
                closeDialog()
                Swal.fire('Success!', ' successfully assigned to the class .', 'success')
                Inertia.reload({ only: ['klassSubjectTeacher'] }); 
            },
            onError: (newErrors) => {
                errors.value = newErrors
            }
        })
    }
}
const editItem = (subjectTeacher) =>{
    isEditing.value = true
    editingItem.value = subjectTeacher

    form.teachers =subjectTeacher.teacher_id
    form.subjects = [subjectTeacher.subject_id]
    form.klasses = [subjectTeacher.klass_id]
    form.id = subjectTeacher.id

    isDialogOpen.value = true
}
const deleteItem =  (subjectTeacher) => {
    Swal.fire({
        title: 'Are you sure?',
        text: 'Do you want to delete this assignment?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
    if (result.isConfirmed) {
      form.delete(route('admin.subjectTeacher.destroy', subjectTeacher.id), {
        onSuccess: () => {
          Swal.fire('Deleted!', 'Assignment has been deleted.', 'success');
          Inertia.reload({ only: ['klassSubjectTeacher'] }); 
        },
      });
    }
  });
    
    
}



</script>
<template>
    <AdminLayout>
        <v-container>
            <v-row>
                <v-col>
                    <v-card class="pa-4">
                        <v-row justify="space-between" align="center">
                            <h2 class="text-h6 font-weight-bold">Assign Teachers to Subjects</h2>
                            <v-btn color="primary" @click="openDialog">Assign Teacher</v-btn>
                        </v-row>
                    </v-card>
                </v-col>
            </v-row>
            <v-dialog v-model="isDialogOpen" max-width="600">
                <v-card class="pa-4">
                    <v-card-title>
                        {{ isEditing ? 'Edit ' : 'Assign Subject' }}
                    </v-card-title>
                    <v-card-text>
                        <v-form @submit.prevent="saveData">
                            <v-row dense>
                                <v-col cols="12">
                                    <v-select
                                        v-model="form.teachers"
                                        :items="$page.props.teachers"
                                        item-title="first_name"
                                        item-value="id"
                                        label="Teacher"
                                        :error-messages="errors.teachers"
                                        outlined
                                        clearable
                                    />

                                </v-col>
                                <v-col cols="12">
                                    <v-select
                                        v-model="form.subjects"
                                        :items="$page.props.subjects"
                                        item-title="name"
                                        item-value="id"
                                        label="Subjects Taught"
                                        :error-messages="errors.subjects"
                                        outlined
                                        clearable
                                        multiple
                                    />

                                </v-col>
                                <v-col cols="12">
                                    <v-select
                                        v-model="form.klasses"
                                        :items="$page.props.klasses"
                                        item-title="class_name"
                                        item-value="id"
                                        label="Classes Taught"
                                        :error-messages="errors.klasses"
                                        outlined
                                        clearable
                                        multiple
                                    />

                                </v-col>
                            </v-row>
                            
                        </v-form>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn 
                            color="red" 
                            @click="closeDialog"
                            variant="flat"
                        >
                            Cancel
                    </v-btn>
                    <v-btn color="green" @click="saveData" variant="flat">
                            {{ isEditing ? 'Update Teacher' : ' Save' }}
                        </v-btn>
                    </v-card-actions>
                </v-card>

            </v-dialog>
             <v-data-table
                :headers="columns"
                :items="props.klassSubjectTeacher"
                class="elevation-1 text-body-3"
                
             >
             <template v-slot:item.actions="{ item }">
                <v-btn
                    icon="mdi-pencil"
                    size="small"
                    @click="editItem(item)"
                    color="red"
                ></v-btn>
                <v-btn
                    icon="mdi-delete"
                    size="small"
                    color="red"
                    @click="deleteItem(item)"
                ></v-btn>
             </template>

             </v-data-table>
        </v-container>

    </AdminLayout>

</template>
<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue';
import axios from 'axios';
import { useForm } from '@inertiajs/vue3';
import {ref, onMounted} from 'vue';
import Swal from 'sweetalert2';

const teachersList = ref([])
const subjectList = ref([])
const klassList = ref([])

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
            },
            onError: (newErrors) => {
                errors.value = newErrors
            }
        })
    }
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
                            <v-btn color="primary" @click="openDialog">Assign Teacher to the class</v-btn>
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
        </v-container>

    </AdminLayout>

</template>
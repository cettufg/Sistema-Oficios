<template>
    <Head title="Usuários" />

    <AuthenticatedLayout>
        <div class="tw-py-12 md:tw-mx-20">
            <div class="">
                <div class="tw-overflow-hidden">
                    <div class="tw-flex tw-justify-between tw-mx-4">
                        <span class="tw-text-3xl">Usuários</span>
                    </div>
                    <div class="q-pa-md">
                        <q-table
                            :rows="props.usuarios"
                            :columns="columns"
                            row-key="id"
                            :pagination="initialPagination"
                            :filter="filter"
                            no-data-label="Sem dados cadastrados"
                            no-results-label="A sua pesquisa não retornou dados"
                        >

                            <template v-slot:body-cell="props">
                                <q-td :props="props">
                                    <div v-if="props.col.name == 'status'">
                                        <q-chip :label="props.value == 1 ? 'Ativo' : 'Inativo'" text-color="white" :color="props.value == 1 ? 'green' : 'red'" />
                                    </div>
                                    <div v-else-if="props.col.name == 'actions'">
                                        <div class="tw-inline-flex tw-items-center tw-rounded-md tw-shadow-sm">
                                            <button @click="openModalAction(1, props.row)" class="tw-text-slate-800 hover:tw-text-green-500 tw-text-sm tw-bg-white hover:tw-bg-slate-100 tw-border tw-border-slate-200 tw-rounded-l-lg tw-font-medium tw-px-4 tw-py-2 tw-inline-flex tw-space-x-1 tw-items-center">
                                                <Icon icon="tabler:edit" />
                                            </button>
                                            <button @click="openModalAction(2, props.row)" class="tw-text-slate-800 hover:tw-text-red-500 tw-text-sm tw-bg-white hover:tw-bg-slate-100 tw-border tw-border-slate-200 tw-rounded-r-lg tw-font-medium tw-px-4 tw-py-2 tw-inline-flex tw-space-x-1 tw-items-center">
                                                <Icon icon="uil:trash" />
                                            </button>
                                        </div>
                                    </div>
                                    <div v-else>
                                        {{props.value}}
                                    </div>

                                </q-td>
                            </template>

                            <template v-slot:top-right>
                                <q-input v-model="filter" outlined type="search" placeholder="Pesquisar">
                                    <template v-slot:append>
                                        <q-icon name="search" />
                                    </template>
                                </q-input>
                            </template>

                            <template v-slot:no-data="{ icon, message, filter }">
                                <div class="tw-w-full tw-flex tw-justify-center tw-items-center">
                                    <span>
                                        {{ message }}
                                    </span>
                                    <q-icon size="2em" name="sentiment_dissatisfied" />
                                </div>
                            </template>
                        </q-table>
                    </div>
                </div>
            </div>
            <q-dialog
                v-model="openModal"
            >
                <q-card style="width: 1400px; max-width: 80vw;">
                    <q-card-section>
                        <div class="text-h6">Editar usuário</div>
                    </q-card-section>

                    <q-card-section class="">
                        <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-4 tw-gap-4">
                            <div class="tw-col-span-2">
                                <InputLabel required value="Nome" />
                                <q-input
                                    v-model="form.name"
                                    outlined
                                    clearable
                                />
                            </div>

                            <div class="tw-col-span-2">
                                <InputLabel required value="E-mail" />
                                <q-input
                                    v-model="form.email"
                                    outlined
                                    clearable
                                />
                            </div>

                            <div class="tw-col-span-2" v-if="$page.props.auth.user.is_admin">
                                <InputLabel value="Permissão de admin" />
                                <q-select
                                    v-model="form.is_admin"
                                    outlined
                                    :options="[
                                        { label: 'Sim', value: 1 },
                                        { label: 'Não', value: 0 },
                                    ]"
                                />
                            </div>

                            <div class="tw-col-span-2">
                                <InputLabel required value="Resetar senha" />
                                <q-select
                                    v-model="form.password_reset"
                                    outlined
                                    :options="[
                                        { label: 'Sim', value: 1 },
                                        { label: 'Não', value: 0 },
                                    ]"
                                />
                            </div>

                            <div class="tw-col-span-2">
                                <InputLabel required value="Status" />
                                <q-select
                                    v-model="form.status"
                                    outlined
                                    :options="[
                                        { label: 'Ativo', value: 1 },
                                        { label: 'Inativo', value: 0 },
                                    ]"
                                />
                            </div>
                        </div>
                    </q-card-section>

                    <q-card-actions align="left" class="tw-space-x-4">
                        <button @click="saveData()" class="tw-flex tw-items-center tw-rounded-md tw-bg-green-500 tw-text-white tw-px-2 tw-py-1">
                            <span class="tw-text-md">Salvar</span>
                            <Icon class="tw-text-md tw-ml-1" icon="ic:round-save" />
                        </button>
                        <button class="tw-flex tw-items-center tw-rounded-md tw-bg-cyan-500 tw-text-white tw-px-2 tw-py-1" v-close-popup>
                            <span class="tw-text-md">Cancelar</span>
                            <Icon class="tw-text-md tw-ml-1" icon="material-symbols:cancel-outline" />
                        </button>
                    </q-card-actions>
                </q-card>
            </q-dialog>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Button from '@/Components/Button.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Icon } from '@iconify/vue';
import { Notify } from 'quasar';


const props = defineProps({
    usuarios:{
        default: '',
        type: Object
    }
})
const filter = ref('')
const initialPagination = ref({
    sortBy: 'desc',
    descending: false,
    page: 1,
    rowsPerPage: 10
})

const columns = [
  { name: 'id', label: '#', align: 'left', field: 'id', sortable: true },
  { name: 'name', label: 'Nome', align: 'left', field: 'name', sortable: true },
  { name: 'status', label: 'Status', align: 'left', field: 'status', sortable: true },
  { name: 'actions', label: 'Ações', align: 'center', field: 'actions', sortable: true },
]
const openModal = ref(false);

const form = useForm({
    id: '',
    name: '',
    email: '',
    status: '',
    is_admin: '',
    password_reset: ''
});

function openModalAction(type, data = []){
    form.reset()

    if(type == 1){
        form.id = data.id;
        form.name = data.name;
        form.email = data.email;
        form.status = {
            label: data.status == 1 ? 'Ativo' : 'Inativo',
            value: data.status
        };
        form.is_admin = {
            label: data.is_admin == 1 ? 'Sim' : 'Não',
            value: data.is_admin
        };
        form.password_reset = {
            label: data.password_reset == 1 ? 'Sim' : 'Não',
            value: data.password_reset
        };
        openModal.value = true;
    }else{
        openModalDelete.value = true;
        form.id = data.id;
    }
}

function saveData(){
    form.put(route('usuario.update', form.id), {
        preserveScroll: true,
        onSuccess: () => {
            openModal.value = false;
            Notify.create({
                message: 'Usuário atualizado com sucesso!',
                type: 'positive',
                position: 'top-right',
                timeout: 2000
            })
        },
        onError: () => {
            Notify.create({
                message: 'Erro ao atualizar usuário!',
                type: 'negative',
                position: 'top-right',
                timeout: 2000
            })
        }
    })
}
</script>

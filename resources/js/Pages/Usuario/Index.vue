<template>
    <Head title="Usuários" />

    <AuthenticatedLayout>
        <div class="tw-py-12 md:tw-mx-20">
            <div class="tw-flex tw-justify-between tw-mb-10">
                <span class="tw-text-3xl">Usuários</span>
            </div>

            <q-table
                :rows="props.usuarios"
                :columns="columns"
                :pagination="initialPagination"
                :filter="filter"
                :visible-columns="visibleColumns"
                no-data-label="Sem dados cadastrados"
                no-results-label="A sua pesquisa não retornou dados"
            >

                <template v-slot:body-cell="props">
                    <q-td :props="props">
                        <div v-if="props.col.name == 'status'">
                            <span class="tw-text-white tw-rounded-2xl tw-p-2" :class="{
                                'tw-bg-positive': props.row.status == 1,
                                'tw-bg-negative': props.row.status == 2,
                                'tw-bg-gray-500': props.row.status == 0,
                            }">
                                {{props.row.status == 1 ? 'Ativo' : props.row.status == 2 ? 'Inativo' : 'Lixeira'}}
                            </span>
                        </div>
                        <div v-else-if="props.col.name == 'actions'">
                            <div class="tw-flex tw-items-center tw-justify-center tw-gap-2">
                                <PrimaryButton
                                    @click="openModalAction(1, props.row)"
                                    class="tw-p-2"
                                    background="info"
                                    icon="tabler:edit"
                                />
                                <PrimaryButton
                                    v-if="$page.props.auth.user.is_admin == 1"
                                    @click="openModalAction(2, props.row)"
                                    class="tw-p-2"
                                    background="negative"
                                    icon="uil:trash"
                                />
                            </div>
                        </div>
                        <div v-else>
                            {{props.value}}
                        </div>

                    </q-td>
                </template>

                <template v-slot:top-right>
                    <div class="tw-grid tw-grid-cols-1 sm:tw-grid-cols-2 tw-gap-3">
                        <q-input v-model="filter" outlined type="search" placeholder="Pesquisar">
                            <template v-slot:append>
                                <q-icon name="search" />
                            </template>
                        </q-input>
                        <q-select
                            v-model="visibleColumns"
                            multiple
                            outlined
                            options-dense
                            display-value="Selecionar colunas"
                            emit-value
                            map-options
                            :options="columns"
                            option-value="name"
                            options-cover
                        >
                            <template v-slot:option="{ itemProps, opt, selected, toggleOption }">
                                <q-item v-bind="itemProps">
                                    <q-item-section>
                                        <q-item-label v-html="opt.label" />
                                    </q-item-section>
                                    <q-item-section side>
                                        <q-toggle :model-value="selected" @update:model-value="toggleOption(opt)" />
                                    </q-item-section>
                                </q-item>
                            </template>
                        </q-select>
                    </div>
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

            <q-dialog v-model="openModal">
                <q-card style="width: 700px; max-width: 80vw;">
                    <q-card-section>
                        <div class="tw-text-2xl">Editar usuário</div>
                    </q-card-section>

                    <q-card-section>
                        <div v-if="!form.processing" class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4">
                            <div>
                                <InputLabel required value="Nome" />
                                <q-input
                                    v-model="form.name"
                                    outlined
                                    clearable
                                />
                            </div>

                            <div>
                                <InputLabel required value="E-mail" />
                                <q-input
                                    v-model="form.email"
                                    outlined
                                    clearable
                                />
                            </div>

                            <div v-if="$page.props.auth.user.is_admin">
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

                            <div>
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

                            <div>
                                <InputLabel required value="Status" />
                                <q-select
                                    v-model="form.status"
                                    outlined
                                    :options="optionsStatus"
                                    :error="!!form.errors.status"
                                    :error-message="form.errors.status"
                                />
                            </div>
                        </div>
                        <div v-if="form.processing" class="tw-flex tw-items-center tw-justify-center tw-py-2">
                            <q-spinner
                                color="primary"
                                size="3em"
                            />
                        </div>
                    </q-card-section>

                    <q-card-actions align="left" class="tw-ml-2 tw-mb-3 tw-space-x-4">
                        <PrimaryButton
                            @click="saveData()"
                            :disabled="form.processing"
                            background="positive"
                            class="tw-px-3 tw-py-2"
                            text="Salvar"
                            icon="ic:round-save"
                        />
                        <PrimaryButton
                            v-close-popup
                            :disabled="form.processing"
                            background="info"
                            class="tw-px-3 tw-py-2"
                            text="Cancelar"
                            icon="material-symbols:cancel-outline"
                        />
                    </q-card-actions>
                </q-card>
            </q-dialog>

            <q-dialog v-model="openModalDelete">
                <q-card style="width: 700px; max-width: 80vw;">
                    <q-card-section>
                        <div class="tw-text-2xl">Deletar Dados</div>
                    </q-card-section>

                    <q-card-section>
                        <div v-if="!form.processing" class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4">
                            <div>
                                Você quer mesmo excluir esses dados?
                            </div>
                        </div>
                        <div v-else class="tw-flex tw-items-center tw-justify-center tw-py-2">
                            <q-spinner
                                color="primary"
                                size="3em"
                            />
                        </div>
                    </q-card-section>

                    <q-card-actions align="left" class="tw-ml-2 tw-mb-3 tw-space-x-4">
                        <PrimaryButton
                            @click="destroyData()"
                            :disabled="form.processing"
                            background="negative"
                            class="tw-px-3 tw-py-2"
                            text="Excluir"
                            icon="uil:trash"
                        />
                        <PrimaryButton
                            v-close-popup
                            :disabled="form.processing"
                            background="info"
                            class="tw-px-3 tw-py-2"
                            text="Cancelar"
                            icon="material-symbols:cancel-outline"
                        />
                    </q-card-actions>
                </q-card>
            </q-dialog>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Notify } from 'quasar';


const props = defineProps({
    usuarios:{
        type: Array,
        default: []
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
const visibleColumns = ref([
    'id',
    'name',
    'status',
    'actions',
]);
const openModal = ref(false);
const openModalDelete = ref(false);
const optionsStatus = [
    {
        label: 'Ativo',
        value: 1
    },
    {
        label: 'Inativo',
        value: 2
    },
    {
        label: 'Lixeira',
        value: 0
    }
]

const form = useForm({
    id: '',
    name: '',
    email: '',
    status: '',
    is_admin: '',
    password_reset: ''
});

function openModalAction(type, data = []){
    form.reset();
    if(type == 1){
        form.id = data.id;
        form.name = data.name;
        form.email = data.email;
        form.status = {
            label: data.status == 1 ? 'Ativo' : data.status == 2 ? 'Inativo' : 'Lixeira',
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
            form.reset();
            Notify.create({
                message: 'Dados atualizados com sucesso!',
                type: 'positive',
            })
        },
        onError: () => {
            Notify.create({
                message: 'Erro ao atualizar dados!',
                type: 'negative',
            })
        }
    })
}

function destroyData(){
    form.delete(route('usuario.destroy', form.id), {
        preserveScroll: true,
        onSuccess: () => {
            openModalDelete.value = false;
            form.reset();
            Notify.create({
                message: 'Dados excluídos com sucesso!',
                type: 'positive',
            })
        },
        onError: () => {
            Notify.create({
                message: 'Erro ao excluir dados!',
                type: 'negative',
            })
        }
    })
}
</script>

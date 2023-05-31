<template>
    <Head title="Destinatários" />

    <AuthenticatedLayout>
        <div class="tw-py-12 md:tw-mx-20">
            <div class="">
                <div class="tw-overflow-hidden">
                    <div class="tw-flex tw-justify-between tw-mx-4">
                        <span class="tw-text-3xl">Destinatários</span>
                        <Link :href="route('destinatario.create')" class="tw-px-3 tw-py-2 tw-text-white tw-rounded-lg tw-bg-primary hover:tw-bg-[#031447] tw-flex tw-items-center tw-justify-center">
                            <span class="tw-mr-1">Adicionar</span> <Icon icon="material-symbols:add-circle-outline" />
                        </Link>
                    </div>
                    <div class="q-pa-md">
                        <q-table
                            :rows="rows"
                            :columns="columns"
                            row-key="id"
                            :pagination="initialPagination"
                            selection="multiple"
                            :filter="filter"
                            v-model:selected="selected"
                            :visible-columns="visibleColumns"
                            no-data-label="Sem dados cadastrados"
                            no-results-label="A sua pesquisa não retornou dados"
                        >

                            <template v-slot:body-cell="props">
                                <q-td :props="props">
                                    <div v-if="props.col.name == 'actions'">
                                        <div class="tw-inline-flex tw-items-center tw-rounded-md tw-shadow-sm">
                                            <Link :href="route('destinatario.edit', props.row.id)" class="tw-text-slate-800 hover:tw-text-green-500 tw-text-sm tw-bg-white hover:tw-bg-slate-100 tw-border tw-border-slate-200 tw-rounded-l-lg tw-font-medium tw-px-4 tw-py-2 tw-inline-flex tw-space-x-1 tw-items-center">
                                                <Icon icon="tabler:edit" />
                                            </Link>
                                            <button @click="destroyItem(props.row)" v-if="props.row.user_created == 1" class="tw-text-slate-800 hover:tw-text-red-500 tw-text-sm tw-bg-white hover:tw-bg-slate-100 tw-border tw-border-slate-200 tw-rounded-r-lg tw-font-medium tw-px-4 tw-py-2 tw-inline-flex tw-space-x-1 tw-items-center">
                                                <Icon icon="uil:trash" />
                                            </button>
                                        </div>
                                    </div>
                                    <div v-else>
                                        {{props.value}}
                                    </div>

                                </q-td>
                            </template>

                            <template v-slot:top-left>
                                <button v-if="$page.props.auth.user.id == 1" :disabled="selected.length == 0" class="tw-rounded-full tw-bg-red-500 tw-text-white tw-text-xl tw-p-2 hover:tw-bg-red-200 hover:tw-text-black" @click="destroy(selected)">
                                    <Icon icon="uil:trash" />
                                </button>
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

                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Icon } from '@iconify/vue';
import axios from 'axios';


const props = defineProps({
    destinatarios:{
        default: '',
        type: Object
    }
})
const selected = ref([])
const filter = ref('')
const initialPagination = ref({
    sortBy: 'desc',
    descending: false,
    page: 1,
    rowsPerPage: 10
})

const columns = [
  { name: 'id', label: '#', align: 'left', field: 'id', sortable: true },
  { name: 'nome', label: 'Nome', align: 'left', field: 'nome', sortable: true },
  { name: 'status', label: 'Status', align: 'left', field: 'status', sortable: true },
  { name: 'actions', label: 'Ações', align: 'center', field: 'actions', sortable: true },
]

const visibleColumns = ref([
    'id',
    'nome',
    'status',
    'actions',
]);

const form = useForm({
    id: '',
})

const rows = ref(props.destinatarios);

async function destroy(items)
{
    await axios.delete(route('destinatario.destroyselected'), {data: items})
    .then((response) => {
        if(response.status == 200){
            selected.value = [];
            rows.value = response.data;
        }
    });
}

function destroyItem(item)
{
    form.id = item.id;

    form.delete(route('destinatario.destroy'),{
        preserveScroll: true,
        onSuccess: (response) => {
            rows.value = response.props.destinatarios;
        },
        onError: () => '',
        onFinish: () => '',
    });
}
</script>

<template>
    <Head title="Ofícios" />

    <AuthenticatedLayout>
        <div class="tw-my-5 sm:tw-my-10">
            <div class="">
                <div class="tw-overflow-hidden">
                    <div class="tw-flex tw-justify-between tw-mx-4">
                        <span class="tw-text-3xl">Ofícios</span>
                        <Link :href="route('oficio.create')" class="tw-px-3 tw-py-2 tw-text-white tw-rounded-lg tw-bg-primary hover:tw-bg-[#031447] tw-flex tw-items-center tw-justify-center">
                            <span class="tw-mr-1">Adicionar</span> <Icon icon="material-symbols:add-circle-outline" />
                        </Link>
                    </div>

                    <!-- =======  Filtros =============-->
                    <div class="tw-mx-4 tw-w-[95%] tw-mt-8 tw-grid tw-grid-cols-1 sm:tw-grid-cols-2 lg:tw-grid-cols-4 tw-gap-4">
                        <div class="tw-w-full">
                            <label for="" class="tw-flex tw-items-center">Tipo de oficio</label>
                            <q-select
                                outlined
                                dense
                                clearable
                                @update:model-value="val => filtrar(val, 'tipo_oficio')"
                                v-model="filters.tipo_oficio"
                                :options="optionsFilters.tipo_oficio"
                            />
                        </div>

                        <div class="tw-w-full">
                            <label for="" class="tw-flex tw-items-center">Data</label>
                            <q-input
                                outlined
                                dense
                                clearable
                                v-model="filterDataFormatted"
                                @update:model-value="val => filtrar(val, 'data')"
                            >
                                <template v-slot:append>
                                    <q-icon name="event" class="cursor-pointer">
                                        <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                                            <q-date range v-model="filters.data" @update:model-value="val => filtrar(val, 'data')">
                                                <div class="row items-center justify-end">
                                                    <q-btn v-close-popup label="Close" color="primary" flat />
                                                </div>
                                            </q-date>
                                        </q-popup-proxy>
                                    </q-icon>
                                </template>
                            </q-input>
                        </div>

                        <div class="tw-w-full">
                            <label for="" class="tw-flex tw-items-center">Tipo de documento</label>
                            <q-select
                                outlined
                                dense
                                clearable
                                @update:model-value="val => filtrar(val, 'tipo_documento')"
                                v-model="filters.tipo_documento"
                                :options="optionsFilters.tipo_documento"
                            />
                        </div>

                        <div class="tw-w-full">
                            <label for="" class="tw-flex tw-items-center">Número do documento</label>
                            <q-select
                                outlined
                                dense
                                clearable
                                use-input
                                @filter="filterNumeroOficio"
                                @update:model-value="val => filtrar(val, 'numero_oficio')"
                                v-model="filters.numero_oficio"
                                :options="optionsFiltred.numero_oficio"
                            />
                        </div>

                        <div class="tw-w-full">
                            <label for="" class="tw-flex tw-items-center">Origem/Destinatário</label>
                            <q-select
                                outlined
                                dense
                                clearable
                                @update:model-value="val => filtrar(val, 'destinatario')"
                                v-model="filters.destinatario"
                                :options="optionsFilters.destinatario"
                            />
                        </div>

                        <div class="tw-w-full">
                            <label for="" class="tw-flex tw-items-center">Responsável</label>
                            <q-select
                                outlined
                                dense
                                clearable
                                use-input
                                @filter="filterResponsavel"
                                @update:model-value="val => filtrar(val, 'responsavel')"
                                v-model="filters.responsavel"
                                :options="optionsFiltred.responsavel"
                            >
                                <template v-slot:no-option>
                                    <q-item>
                                        <q-item-section class="tw-text-gray-500">
                                        Sem dados
                                        </q-item-section>
                                    </q-item>
                                </template>
                            </q-select>
                        </div>

                        <div class="tw-w-full">
                            <label for="" class="tw-flex tw-items-center">Prazo</label>
                            <q-select
                                outlined
                                dense
                                clearable
                                use-input
                                @filter="filterPrazo"
                                @update:model-value="val => filtrar(val, 'prazo')"
                                v-model="filters.prazo"
                                :options="optionsFiltred.prazo"
                            >
                                <template v-slot:no-option>
                                    <q-item>
                                        <q-item-section class="tw-text-gray-500">
                                        Sem dados
                                        </q-item-section>
                                    </q-item>
                                </template>
                            </q-select>
                        </div>

                        <div class="tw-w-full">
                            <label for="" class="tw-flex tw-items-center">Etapa</label>
                            <q-select
                                outlined
                                dense
                                clearable
                                @update:model-value="val => filtrar(val, 'etapa')"
                                v-model="filters.etapa"
                                :options="optionsFilters.etapa"
                            />
                        </div>

                    </div>

                    <!-- =======  Tabela =============-->
                    <div class="q-pa-md tw-w-full tw-mt-6">
                        <q-table
                            :rows="rows"
                            :columns="columns"
                            row-key="id"
                            :pagination="initialPagination"
                            selection="multiple"
                            virtual-scroll
                            v-model:selected="selected"
                            :filter="filter"
                            :visible-columns="visibleColumns"
                            no-data-label="Sem dados cadastrados"
                            no-results-label="A sua pesquisa não retornou dados"
                        >

                            <template v-slot:body="props">
                                <q-tr :props="props" :class="{'tw-bg-red-500 tw-text-white': props.row.etapa == 'Atrasado',
                                                              'tw-bg-[#10b981] tw-text-white': props.row.etapa == 'Finalizado',
                                                              'tw-bg-gray-400/50 tw-text-black': props.row.recente == false && props.row.etapa != 'Atrasado' && props.row.etapa != 'Finalizado'}">
                                    <q-td>
                                        <q-checkbox
                                        v-model="props.selected"
                                         />
                                    </q-td>
                                    <q-td key="tipo_oficio" :props="props">
                                        {{ props.row.tipo_oficio }}
                                    </q-td>
                                    <q-td key="data" :props="props">
                                        {{ props.row.data }}
                                    </q-td>
                                    <q-td key="tipo_documento" :props="props">
                                        {{ props.row.tipo_documento }}
                                    </q-td>
                                    <q-td key="numero_oficio" :props="props">
                                        {{ props.row.numero_oficio }}
                                    </q-td>
                                    <q-td key="destinatario" :props="props">
                                        {{ props.row.destinatario }}
                                    </q-td>
                                    <q-td key="status_inicial" :props="props">
                                        {{ props.row.status_inicial }}
                                    </q-td>
                                    <q-td key="responsaveis" :props="props">
                                        <div v-for="responsavel in props.row.responsaveis" class="tw-flex tw-flex-col tw-w-full tw-items-start tw-justify-start">
                                            {{ responsavel }}
                                        </div>
                                    </q-td>
                                    <q-td key="prazo" :props="props">
                                        {{ props.row.prazo }}
                                    </q-td>
                                    <q-td key="etapa" :props="props">
                                        {{ props.row.etapa }}
                                    </q-td>
                                    <q-td key="status_final" :props="props">
                                        {{ props.row.status_final }}
                                    </q-td>
                                    <q-td key="actions" :props="props">
                                        <div class="tw-inline-flex tw-items-center tw-rounded-md tw-shadow-sm">
                                            <Link :href="route('oficio.edit', props.row.id)" class="tw-text-slate-800 hover:tw-text-green-500 tw-text-sm tw-bg-white hover:tw-bg-slate-100 tw-border tw-border-slate-200 tw-rounded-l-lg tw-font-medium tw-px-4 tw-py-2 tw-inline-flex tw-space-x-1 tw-items-center">
                                                <Icon icon="tabler:edit" />
                                            </Link>
                                            <Link :href="route('oficio.detail', props.row.id)" class="tw-text-slate-800 hover:tw-text-blue-500 tw-text-sm tw-bg-white hover:tw-bg-slate-100 tw-border-y tw-border-slate-200 tw-font-medium tw-px-4 tw-py-2 tw-inline-flex tw-space-x-1 tw-items-center">
                                                <Icon icon="mdi:eye-outline" />
                                            </Link>
                                            <button @click="destroyItem(props.row)" v-if="props.row.user_created == usuario.id" class="tw-text-slate-800 hover:tw-text-red-500 tw-text-sm tw-bg-white hover:tw-bg-slate-100 tw-border tw-border-slate-200 tw-rounded-r-lg tw-font-medium tw-px-4 tw-py-2 tw-inline-flex tw-space-x-1 tw-items-center">
                                                <Icon icon="uil:trash" />
                                            </button>
                                        </div>
                                    </q-td>
                                </q-tr>
                            </template>

                            <template v-slot:top-left>
                                <div class="tw-flex tw-my-5 sm:tw-my-0">
                                    <button v-if="$page.props.auth.user.is_admin" :disabled="selected.length == 0" class="tw-rounded-full tw-bg-red-500 tw-text-white tw-text-xl tw-p-2 hover:tw-bg-red-200 hover:tw-text-black" @click="destroy(selected)">
                                        <Icon icon="uil:trash" />
                                    </button>

                                    <button @click="getPDF(selected)" :disabled="selected.length == 0" class="tw-rounded-full tw-ml-3 tw-bg-cyan-500 tw-text-white tw-text-xl tw-p-2 hover:tw-bg-cyan-200 hover:tw-text-black">
                                        <Icon icon="teenyicons:pdf-outline" />
                                    </button>
                                </div>
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
import { ref, onMounted } from 'vue';
import { Icon } from '@iconify/vue';
import axios from 'axios';

const props = defineProps({
    oficios:{
        default: '',
        type: Array
    },
    usuario: {
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
const optionsFilters = ref({
    tipo_oficio: [],
    data: [],
    tipo_documento: [],
    numero_oficio: [],
    destinatario: [],
    responsavel: [],
    prazo: [],
    etapa: [],
})
const optionsFiltred = ref({
    data: optionsFilters.value.data,
    responsavel: optionsFilters.value.responsavel,
    numero_oficio: optionsFilters.value.numero_oficio,
    prazo: optionsFilters.value.prazo,
})
const filterDataFormatted = ref('');

onMounted(() => {
    loadList();

    if(sessionStorage.getItem('filters.tipo_oficio') && sessionStorage.getItem('filters.tipo_oficio') != 'null'){
        filters.value.tipo_oficio = sessionStorage.getItem('filters.tipo_oficio');
        filtrar(filters.value.tipo_oficio, 'tipo_oficio');
    }
    if(sessionStorage.getItem('filters.data') && sessionStorage.getItem('filters.data') != 'null'){
        filters.value.data = sessionStorage.getItem('filters.data');
        filterDataFormatted.value = sessionStorage.getItem('filters.data').from.split('/').reverse().join('/') + ' - ' + sessionStorage.getItem('filters.data').to.split('/').reverse().join('/');
        filtrar(filters.value.data, 'data');
    }
    if(sessionStorage.getItem('filters.tipo_documento') && sessionStorage.getItem('filters.tipo_documento') != 'null'){
        filters.value.tipo_documento = sessionStorage.getItem('filters.tipo_documento');
        filtrar(filters.value.tipo_documento, 'tipo_documento');
    }
    if(sessionStorage.getItem('filters.numero_documento') && sessionStorage.getItem('filters.numero_documento') != 'null'){
        filters.value.numero_documento = sessionStorage.getItem('filters.numero_documento');
        filtrar(filters.value.numero_documento, 'numero_documento');
    }
    if(sessionStorage.getItem('filters.destinatario') && sessionStorage.getItem('filters.destinatario') != 'null'){
        filters.value.destinatario = sessionStorage.getItem('filters.destinatario');
        filtrar(filters.value.destinatario, 'destinatario');
    }
    if(sessionStorage.getItem('filters.responsavel') && sessionStorage.getItem('filters.responsavel') != 'null'){
        filters.value.responsavel = sessionStorage.getItem('filters.responsavel');
        filtrar(filters.value.responsavel, 'responsavel');
    }
    if(sessionStorage.getItem('filters.prazo') && sessionStorage.getItem('filters.prazo') != 'null'){
        filters.value.prazo = sessionStorage.getItem('filters.prazo');
        filtrar(filters.value.prazo, 'prazo');
    }
    if(sessionStorage.getItem('filters.etapa') && sessionStorage.getItem('filters.etapa') != 'null'){
        filters.value.etapa = sessionStorage.getItem('filters.etapa');
        filtrar(filters.value.etapa, 'etapa');
    }
});

function loadList()
{
    rows.value = props.oficios;

    loadFilters()
}

function loadFilters()
{
    optionsFilters.value.tipo_oficio = [];
    optionsFilters.value.tipo_documento = [];
    optionsFilters.value.numero_oficio = [];
    optionsFilters.value.destinatario = [];
    optionsFilters.value.responsavel = [];
    optionsFilters.value.prazo = [];
    optionsFilters.value.etapa = [];

    rows.value.map((oficio) => {
        if(!optionsFilters.value.tipo_oficio.includes(oficio.tipo_oficio)){
            if(oficio.tipo_oficio){
                optionsFilters.value.tipo_oficio.push(oficio.tipo_oficio)
            }
        }

        if(!optionsFilters.value.tipo_documento.includes(oficio.tipo_documento)){
            if(oficio.tipo_documento){
                optionsFilters.value.tipo_documento.push(oficio.tipo_documento)
            }
        }

        if(!optionsFilters.value.numero_oficio.includes(oficio.numero_oficio)){
            if(oficio.numero_oficio){
                optionsFilters.value.numero_oficio.push(oficio.numero_oficio)
            }
        }

        if(!optionsFilters.value.destinatario.includes(oficio.destinatario)){
            if(oficio.destinatario){
                optionsFilters.value.destinatario.push(oficio.destinatario)
            }
        }

        oficio.responsaveis.map((responsavel) => {
            if(!optionsFilters.value.responsavel.includes(responsavel)){
                optionsFilters.value.responsavel.push(responsavel)
            }
        })


        if(!optionsFilters.value.prazo.includes(oficio.prazo)){
            if(oficio.prazo){
                optionsFilters.value.prazo.push(oficio.prazo)
            }
        }

        if(!optionsFilters.value.etapa.includes(oficio.etapa)){
            if(oficio.etapa){
                optionsFilters.value.etapa.push(oficio.etapa)
            }
        }

    });

}

const columns = [
    { name: 'tipo_oficio', label: 'Tipo de ofício', align: 'left', field: 'tipo_oficio', sortable: true },
    { name: 'data', label: 'Data', align: 'left', field: 'data', sortable: true },
    { name: 'tipo_documento', label: 'Documento', align: 'left', field: 'tipo_documento', sortable: true },
    { name: 'numero_oficio', label: 'Número', align: 'left', field: 'numero_oficio', sortable: true },
    { name: 'destinatario', label: 'Origem', align: 'left', field: 'destinatario', sortable: true },
    { name: 'status_inicial', label: 'Status Inicial', align: 'left', field: 'status_inicial', sortable: true },
    { name: 'responsaveis', label: 'Responsavel', align: 'left', field: 'responsaveis', sortable: true },
    { name: 'prazo', label: 'Prazo', align: 'left', field: 'prazo', sortable: true },
    { name: 'etapa', label: 'Etapa', align: 'left', field: 'etapa', sortable: true },
    { name: 'status_final', label: 'Status Final', align: 'left', field: 'status_final', sortable: true },
    { name: 'actions', label: 'Ações', align: 'center', field: 'actions', sortable: true },
]
const visibleColumns = ref([
    'tipo_oficio',
    'data',
    'tipo_documento',
    'numero_oficio',
    'destinatario',
    'responsaveis',
    'prazo',
    'etapa',
    'actions',
]);
const form = useForm({
    id: '',
})

const filters = ref({
    tipo_oficio: null,
    data: null,
    tipo_documento: null,
    numero_oficio: null,
    destinatario: null,
    responsavel: null,
    prazo: null,
    etapa: null,
});

const rows = ref();

async function destroy(items)
{
    await axios.delete(route('oficio.destroyselected'), {data: items})
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

    form.delete(route('oficio.destroy'),{
        preserveScroll: true,
        onSuccess: (response) => {
            rows.value = response.props.oficios;
        },
        onError: () => '',
        onFinish: () => '',
    });
}

async function getPDF(items){
    await items.map((item) => {
        window.open(route('oficio.generatepdf', item.id));
    });
}

function filterResponsavel(val, update){
    if (val === '') {
        update(() => {
            optionsFiltred.value.responsavel = optionsFilters.value.responsavel
        })
        return
    }

    update(() => {
        const needle = val.toLowerCase()
        optionsFiltred.value.responsavel = optionsFilters.value.responsavel.filter(v => v.toLowerCase().indexOf(needle) > -1)
    })
}

function filterPrazo(val, update){
    if (val === '') {
        update(() => {
            optionsFiltred.value.prazo = optionsFilters.value.prazo
        })
        return
    }

    update(() => {
        const needle = val.toLowerCase()
        optionsFiltred.value.prazo = optionsFilters.value.prazo.filter(v => v.toLowerCase().indexOf(needle) > -1)
    })
}

function filterNumeroOficio(val, update){
    if (val === '') {
        update(() => {
            optionsFiltred.value.numero_oficio = optionsFilters.value.numero_oficio
        })
        return
    }

    update(() => {
        const needle = val.toLowerCase()
        optionsFiltred.value.numero_oficio = optionsFilters.value.numero_oficio.filter(v => v.toLowerCase().indexOf(needle) > -1)
    })
}

function filtrar(value, type){
    filters.value[type] = value;


    if(filters.value.data != null){
        filterDataFormatted.value = value.from.split('/').reverse().join('/') + ' - ' + value.to.split('/').reverse().join('/');
    }

    if(sessionStorage.getItem('filters.' + type) != null){
        sessionStorage.removeItem('filters.' + type);
        sessionStorage.setItem('filters.' + type, value);
    }else{
        sessionStorage.setItem('filters.' + type, value);
    }

    loadList();

    if(filters.value.tipo_oficio != null){
        rows.value = rows.value.filter((item, index) => {
            return (filters.value.tipo_oficio != null && item.tipo_oficio == filters.value.tipo_oficio);
        });
    }

    if(filters.value.data != null){
        rows.value = rows.value.filter((item, index) => {
            if(filters.value.data != null){
                let dataCompare = new Date(item.data.split('/').reverse().join('-'));
                let dataFromCompare = new Date(filters.value.data.from.split('/').join('-'));
                let dataToCompare = new Date(filters.value.data.to.split('/').join('-'));
                return (dataCompare >= dataFromCompare && dataCompare <= dataToCompare);
            }
        });
    }

    if(filters.value.tipo_documento != null){
        rows.value = rows.value.filter((item, index) => {
            return (filters.value.tipo_documento != null && item.tipo_documento == filters.value.tipo_documento);
        });
    }

    if(filters.value.numero_oficio != null){
        rows.value = rows.value.filter((item, index) => {
            return (filters.value.numero_oficio != null && item.numero_oficio == filters.value.numero_oficio);
        });
    }

    if(filters.value.destinatario != null){
        rows.value = rows.value.filter((item, index) => {
            return (filters.value.destinatario != null && item.destinatario == filters.value.destinatario);
        });
    }

    if(filters.value.responsavel != null){
        rows.value = rows.value.filter((item, index) => {
            return (filters.value.responsavel != null && item.responsaveis.includes(filters.value.responsavel));
        });
    }

    if(filters.value.prazo != null){
        rows.value = rows.value.filter((item, index) => {
            return (filters.value.prazo != null && item.prazo == filters.value.prazo);
        });
    }

    if(filters.value.etapa != null){
        rows.value = rows.value.filter((item, index) => {
            return (filters.value.etapa != null && item.etapa == filters.value.etapa);
        });
    }

    loadFilters();
}

</script>

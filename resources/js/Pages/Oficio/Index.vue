<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import PrimaryLink from '@/Components/PrimaryLink.vue';
    import {Head, Link, router, useForm} from '@inertiajs/vue3';
    import {onMounted, ref} from 'vue';
    import {Icon} from '@iconify/vue';

    const props = defineProps({
        oficios: {
            type: Object,
            default: () => {
            }
        },
        tipo_oficio: {
            type: Array,
            default: () => []
        },
        tipo_documento: {
            type: Array,
            default: () => []
        },
        numero_oficio: {
            type: Array,
            default: () => []
        },
        destinatario: {
            type: Array,
            default: () => []
        },
        responsavel: {
            type: Array,
            default: () => []
        },
        prazo: {
            type: Array,
            default: () => []
        },
        etapa: {
            type: Array,
            default: () => []
        }
    })
    const selected = ref([]);
    const openModalDelete = ref(false);
    const openModalDeleteAll = ref(false);
    const initialPagination = ref({
        sortBy: 'desc',
        descending: false,
        page: 1,
        rowsPerPage: 10
    });
    const columns = [
        {name: 'tipo_oficio', label: 'Tipo de ofício', align: 'left', field: 'tipo_oficio', sortable: false},
        {name: 'data', label: 'Data', align: 'left', field: 'data', sortable: false},
        {name: 'tipo_documento', label: 'Documento', align: 'left', field: 'tipo_documento', sortable: false},
        {name: 'numero_oficio', label: 'Número', align: 'left', field: 'numero_oficio', sortable: false},
        {
            name: 'destinatario',
            label: 'Origem',
            align: 'left',
            field: 'destinatario',
            sortable: false,
            format: val => val.nome
        },
        {name: 'status_inicial', label: 'Status Inicial', align: 'left', field: 'status_inicial', sortable: false},
        {name: 'responsavel', label: 'Responsavel', align: 'left', field: 'responsavel', sortable: false},
        {name: 'prazo', label: 'Prazo', align: 'left', field: 'prazo', sortable: false},
        {name: 'etapa', label: 'Etapa', align: 'left', field: 'etapa', sortable: false},
        {name: 'status_final', label: 'Status Final', align: 'left', field: 'status_final', sortable: false},
        {name: 'actions', label: 'Ações', align: 'center', field: 'actions', sortable: false},
    ]
    const visibleColumns = ref([
        'tipo_oficio',
        'data',
        'tipo_documento',
        'numero_oficio',
        'destinatario',
        'responsavel',
        'prazo',
        'etapa',
        'actions',
    ]);
    const filterVisible = ref(false);
    const filters = ref({
        tipo_oficio: null,
        data: null,
        dateFormatted: null,
        tipo_documento: null,
        numero_oficio: null,
        destinatario: null,
        responsavel: null,
        prazo: null,
        etapa: null,
    });
    const optionsTipoOficio = ref([]);
    const optionsTipoDocumento = ref([]);
    const optionsNumeroOficio = ref([]);
    const optionsDestinatario = ref([]);
    const optionsResponsavel = ref([]);
    const optionsPrazo = ref([]);
    const optionsEtapa = ref([]);

    const form = useForm({
        id: '',
        selecteds: [],
    })

    onMounted(() => {
        props.tipo_oficio.forEach((item) => {
            if (item.tipo_oficio) {
                optionsTipoOficio.value.push({label: item.tipo_oficio, value: item.tipo_oficio})
            }
        });

        props.numero_oficio.forEach((item) => {
            if (item.numero_oficio) {
                optionsNumeroOficio.value.push({label: item.numero_oficio, value: item.numero_oficio})
            }
        });

        props.tipo_documento.forEach((item) => {
            if (item.tipo_documento) {
                optionsTipoDocumento.value.push({label: item.tipo_documento, value: item.tipo_documento})
            }
        });

        props.destinatario.forEach((item) => {
            if (item.nome) {
                optionsDestinatario.value.push({label: item.nome, value: item.nome})
            }
        });

        props.responsavel.forEach((item) => {
            if (item.name) {
                optionsResponsavel.value.push({label: item.name, value: item.name})
            }
        });

        props.prazo.forEach((item) => {
            if (item.prazo) {
                optionsPrazo.value.push({label: item.prazo, value: item.prazo})
            }
        });

        props.etapa.forEach((item) => {
            if (item.etapa) {
                optionsEtapa.value.push({label: item.etapa, value: item.etapa})
            }
        });


        loadFilter();
    });

    function destroyAll () {
        form.delete(route('oficio.destroySelected'), {
            preserveScroll: true,
            onSuccess: (response) => {


            },
            onError: () => {

            }
        });
    }

    function destroyItem (data = []) {
        form.id = data.id;
        openModalDelete.value = true;
    }

    function openDestroyAll (data = []) {
        form.selecteds = data;
        openModalDeleteAll.value = true;
    }

    function destroyData () {
        form.delete(route('oficio.destroy', form.id), {
            preserveScroll: true,
            onSuccess: (response) => {

            },
            onError: () => {

            }
        });
    }

    async function getPDF (items) {
        await items.map((item) => {
            window.open(route('oficio.generatepdf', item.id));
        });
    }

    function runFilter () {
        if (filters.value.data) {
            filters.value.dateFormatted = filters.value.data;
        }
        let getFilter = mountFilter();

        router.visit(route('oficio.index', getFilter), {
            preserveScroll: true,
        });
    }

    function mountFilter () {
        let getFilter = {}

        if (filters.value.tipo_oficio) {
            getFilter.tipo_oficio = filters.value.tipo_oficio.value
        }

        if (filters.value.dateFormatted) {
            if (typeof filters.value.data === 'object') {
                getFilter.data = filters.value.data.from.split('/').reverse().join('/') + ' - ' + filters.value.data.to.split('/').reverse().join('/')
                filters.value.dateFormatted = getFilter.data
            } else {
                getFilter.data = filters.value.data.split('/').reverse().join('/') + ' - ' + filters.value.data.split('/').reverse().join('/')
                filters.value.dateFormatted = getFilter.data
            }
        }

        if (filters.value.tipo_documento) {
            getFilter.tipo_documento = filters.value.tipo_documento.value
        }

        if (filters.value.numero_oficio) {
            getFilter.numero_oficio = filters.value.numero_oficio.value
        }

        if (filters.value.destinatario) {
            getFilter.destinatario = filters.value.destinatario.value
        }

        if (filters.value.responsavel) {
            getFilter.responsavel = filters.value.responsavel.value
        }

        if (filters.value.prazo) {
            getFilter.prazo = filters.value.prazo.value
        }

        if (filters.value.etapa) {
            getFilter.etapa = filters.value.etapa.value
        }

        return getFilter;
    }

    function loadFilter () {
        let url = new URL(window.location.href);

        if (url.searchParams.get('tipo_oficio')) {
            filters.value.tipo_oficio = {
                label: url.searchParams.get('tipo_oficio'),
                value: url.searchParams.get('tipo_oficio')
            }

            filterVisible.value = true;
        }

        if (url.searchParams.get('data')) {
            let filterDate = url.searchParams.get('data');

            filterDate = filterDate.split(' - ');

            if (filterDate[0] === filterDate[1]) {
                filterDate = filterDate[0].split('/').reverse().join('/')
            } else {
                filterDate = {
                    from: filterDate[0].split('/').reverse().join('/'),
                    to: filterDate[1].split('/').reverse().join('/')
                }
            }

            filters.value.data = filterDate;
            filters.value.dateFormatted = url.searchParams.get('data');
            filterVisible.value = true;
        }

        if (url.searchParams.get('tipo_documento')) {
            filters.value.tipo_documento = {
                label: url.searchParams.get('tipo_documento'),
                value: url.searchParams.get('tipo_documento')
            }

            filterVisible.value = true;
        }

        if (url.searchParams.get('numero_oficio')) {
            filters.value.numero_oficio = {
                label: url.searchParams.get('numero_oficio'),
                value: url.searchParams.get('numero_oficio')
            }

            filterVisible.value = true;
        }

        if (url.searchParams.get('destinatario')) {
            filters.value.destinatario = {
                label: url.searchParams.get('destinatario'),
                value: url.searchParams.get('destinatario')
            }

            filterVisible.value = true;
        }

        if (url.searchParams.get('responsavel')) {
            filters.value.responsavel = {
                label: url.searchParams.get('responsavel'),
                value: url.searchParams.get('responsavel')
            }

            filterVisible.value = true;
        }

        if (url.searchParams.get('prazo')) {
            filters.value.prazo = {
                label: url.searchParams.get('prazo'),
                value: url.searchParams.get('prazo')
            }

            filterVisible.value = true;
        }

        if (url.searchParams.get('etapa')) {
            filters.value.etapa = {
                label: url.searchParams.get('etapa'),
                value: url.searchParams.get('etapa')
            }

            filterVisible.value = true;
        }
    }

    function updateClearDate () {
        filters.value.data = null;
        filters.value.dateFormatted = null;
        runFilter();
    }

    function formatDate (date) {
        if (date) {
            return new Date(date).toLocaleDateString('pt-BR');
        }

        return '';
    }

    function getResponsaveis (value) {
        if (value.length === 0) {
            return 'Sem Responsável';
        }

        let responsaveis = '';

        value.forEach((item, index) => {
            if (index === 0) {
                responsaveis += item.user.name;
            } else {
                responsaveis += ', ' + item.user.name;
            }
        });

        return responsaveis;
    }

</script>

<template>
<Head title="Ofícios"/>

<AuthenticatedLayout>
    <div class="tw-my-5 sm:tw-my-10">
        <div class="tw-flex tw-justify-between tw-px-4">
            <span class="tw-text-3xl">Ofícios</span>
            <div class="tw-flex tw-items-center tw-gap-2">
                <PrimaryButton
                    background="info"
                    class="tw-px-3 tw-py-3"
                    icon="material-symbols:filter-list"
                    text="Filtrar"
                    @click="filterVisible = !filterVisible"
                />
                <PrimaryLink
                    :href="route('oficio.create')"
                    background="primary"
                    class="tw-px-4 tw-py-3"
                    icon="material-symbols:add-circle-outline"
                    text="Adicionar"
                />
            </div>
        </div>

        <!-- =======  Filtros =============-->
        <div v-if="filterVisible"
             class="tw-px-4 tw-w-full tw-mt-8 tw-grid tw-grid-cols-1 sm:tw-grid-cols-2 lg:tw-grid-cols-4 tw-gap-4">
            <div class="tw-w-full">
                <label class="tw-flex tw-items-center" for="">Tipo de oficio</label>
                <q-select
                    v-model="filters.tipo_oficio"
                    :options="optionsTipoOficio"
                    clearable
                    dense
                    outlined
                    @update:model-value="val => runFilter(val)"
                />
            </div>

            <div class="tw-w-full">
                <label class="tw-flex tw-items-center" for="">Data</label>
                <q-input
                    v-model="filters.dateFormatted"
                    clearable
                    dense
                    outlined
                    @clear="updateClearDate()"
                    @update:model-value="val => runFilter(val)"
                >
                    <template v-slot:append>
                        <q-icon class="cursor-pointer" name="event">
                            <q-popup-proxy cover transition-hide="scale" transition-show="scale">
                                <q-date
                                    v-model="filters.data"
                                    range
                                    @update:model-value="val => runFilter(val)"
                                >
                                    <div class="row items-center justify-end">
                                        <q-btn v-close-popup color="primary" flat label="Close"/>
                                    </div>
                                </q-date>
                            </q-popup-proxy>
                        </q-icon>
                    </template>
                </q-input>
            </div>

            <div class="tw-w-full">
                <label class="tw-flex tw-items-center" for="">Tipo de documento</label>
                <q-select
                    v-model="filters.tipo_documento"
                    :options="optionsTipoDocumento"
                    clearable
                    dense
                    outlined
                    @update:model-value="val => runFilter(val)"
                />
            </div>

            <div class="tw-w-full">
                <label class="tw-flex tw-items-center" for="">Número do documento</label>
                <q-select
                    v-model="filters.numero_oficio"
                    :options="optionsNumeroOficio"
                    clearable
                    dense
                    outlined
                    @update:model-value="val => runFilter(val)"
                />
            </div>

            <div class="tw-w-full">
                <label class="tw-flex tw-items-center" for="">Origem/Destinatário</label>
                <q-select
                    v-model="filters.destinatario"
                    :options="optionsDestinatario"
                    clearable
                    dense
                    outlined
                    @update:model-value="val => runFilter(val)"
                />
            </div>

            <div class="tw-w-full">
                <label class="tw-flex tw-items-center" for="">Responsável</label>
                <q-select
                    v-model="filters.responsavel"
                    :options="optionsResponsavel"
                    clearable
                    dense
                    outlined
                    @update:model-value="val => runFilter(val)"
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
                <label class="tw-flex tw-items-center" for="">Prazo</label>
                <q-select
                    v-model="filters.prazo"
                    :options="optionsPrazo"
                    clearable
                    dense
                    outlined
                    @update:model-value="val => runFilter(val)"
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
                <label class="tw-flex tw-items-center" for="">Etapa</label>
                <q-select
                    v-model="filters.etapa"
                    :options="optionsEtapa"
                    clearable
                    dense
                    outlined
                    @update:model-value="val => runFilter(val)"
                />
            </div>

        </div>

        <!-- =======  Tabela =============-->
        <div class="tw-w-full tw-mt-6">
            <q-table
                v-model:selected="selected"
                :columns="columns"
                :pagination="initialPagination"
                :rows="props.oficios.data"
                :visible-columns="visibleColumns"
                no-data-label="Sem dados cadastrados"
                no-results-label="A sua pesquisa não retornou dados"
                row-key="id"
                selection="multiple"
                virtual-scroll
            >

                <template v-slot:body="props">
                    <q-tr
                        :class="{
                            'tw-bg-red-500 tw-text-white': props.row.etapa === 'Atrasado',
                            'tw-bg-[#10b981] tw-text-white': props.row.etapa === 'Finalizado',
                            'tw-bg-gray-400/50 tw-text-black': props.row.recente === false && props.row.etapa !== 'Atrasado' && props.row.etapa !== 'Finalizado'
                        }"
                        :props="props"
                    >
                        <q-td>
                            <q-checkbox v-model="props.selected"/>
                        </q-td>
                        <q-td v-for="column in props.cols" :key="column.name" :props="props">
                            <div class="tw-flex tw-flex-wrap">
                                {{ column.value }}
                            </div>
                            <div v-if="column.name === 'data'">
                                {{
                                    props.row.tipo_oficio === 'Recebido' ? formatDate(props.row.data_recebimento) : formatDate(props.row.data_emissao)
                                }}
                            </div>
                            <div v-if="column.name === 'responsavel'" class="tw-inline-flex tw-flex-wrap">
                                {{
                                    getResponsaveis(props.row.responsaveis)
                                }}
                            </div>
                            <div v-if="column.name === 'actions'">
                                <div class="tw-inline-flex tw-items-center tw-rounded-md tw-shadow-sm">
                                    <Link :href="route('oficio.edit', props.row.id)"
                                          class="tw-text-slate-800 hover:tw-text-green-500 tw-text-sm tw-bg-white hover:tw-bg-slate-100 tw-border tw-border-slate-200 tw-rounded-l-lg tw-font-medium tw-px-4 tw-py-2 tw-inline-flex tw-space-x-1 tw-items-center">
                                        <Icon icon="tabler:edit"/>
                                    </Link>
                                    <Link :href="route('oficio.detail', props.row.id)"
                                          class="tw-text-slate-800 hover:tw-text-blue-500 tw-text-sm tw-bg-white hover:tw-bg-slate-100 tw-border-y tw-border-slate-200 tw-font-medium tw-px-4 tw-py-2 tw-inline-flex tw-space-x-1 tw-items-center">
                                        <Icon icon="mdi:eye-outline"/>
                                    </Link>
                                    <button
                                        v-if="props.row.user_created === $page.props.auth.user.id || $page.props.auth.user.is_admin === 1"
                                        class="tw-text-slate-800 hover:tw-text-red-500 tw-text-sm tw-bg-white hover:tw-bg-slate-100 tw-border tw-border-slate-200 tw-rounded-r-lg tw-font-medium tw-px-4 tw-py-2 tw-inline-flex tw-space-x-1 tw-items-center"
                                        @click="destroyItem(props.row)">
                                        <Icon icon="uil:trash"/>
                                    </button>
                                </div>
                            </div>
                        </q-td>
                    </q-tr>
                </template>

                <template v-slot:top-left>
                    <div class="tw-flex tw-my-5 sm:tw-my-0">
                        <button v-if="$page.props.auth.user.is_admin && selected.length > 0"
                                class="tw-rounded-full tw-bg-red-500 tw-text-white tw-text-xl tw-p-2 hover:tw-bg-red-200 hover:tw-text-black"
                                @click="openDestroyAll(selected)">
                            <Icon icon="uil:trash"/>
                        </button>

                        <button v-if="selected.length > 0"
                                class="tw-rounded-full tw-ml-3 tw-bg-cyan-500 tw-text-white tw-text-xl tw-p-2 hover:tw-bg-cyan-200 hover:tw-text-black"
                                @click="getPDF(selected)">
                            <Icon icon="teenyicons:pdf-outline"/>
                        </button>
                    </div>
                </template>

                <template v-slot:top-right>
                    <q-select
                        v-model="visibleColumns"
                        :options="columns"
                        dense
                        display-value="Colunas Visíveis"
                        emit-value
                        map-options
                        multiple
                        option-value="name"
                        options-cover
                        options-dense
                        outlined
                    >
                        <template v-slot:option="{ itemProps, opt, selected, toggleOption }">
                            <q-item v-bind="itemProps">
                                <q-item-section>
                                    <q-item-label v-html="opt.label"/>
                                </q-item-section>
                                <q-item-section side>
                                    <q-toggle :model-value="selected" @update:model-value="toggleOption(opt)"/>
                                </q-item-section>
                            </q-item>
                        </template>
                    </q-select>
                </template>

                <template v-slot:no-data="{ icon, message }">
                    <div class="tw-w-full tw-flex tw-justify-center tw-items-center">
                            <span>
                                {{ message }}
                            </span>
                        <q-icon name="sentiment_dissatisfied" size="2em"/>
                    </div>
                </template>

            </q-table>
        </div>

        <!-- =======  Modal Excluir =============-->
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
                        :disabled="form.processing"
                        background="negative"
                        class="tw-px-3 tw-py-2"
                        icon="uil:trash"
                        text="Excluir"
                        @click="destroyData()"
                    />
                    <PrimaryButton
                        v-close-popup
                        :disabled="form.processing"
                        background="info"
                        class="tw-px-3 tw-py-2"
                        icon="material-symbols:cancel-outline"
                        text="Cancelar"
                    />
                </q-card-actions>
            </q-card>
        </q-dialog>

        <q-dialog v-model="openModalDeleteAll">
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
                        :disabled="form.processing"
                        background="negative"
                        class="tw-px-3 tw-py-2"
                        icon="uil:trash"
                        text="Excluir"
                        @click="destroyAll()"
                    />
                    <PrimaryButton
                        v-close-popup
                        :disabled="form.processing"
                        background="info"
                        class="tw-px-3 tw-py-2"
                        icon="material-symbols:cancel-outline"
                        text="Cancelar"
                    />
                </q-card-actions>
            </q-card>
        </q-dialog>
    </div>
</AuthenticatedLayout>
</template>

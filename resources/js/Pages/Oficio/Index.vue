<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import PrimaryLink from '@/Components/PrimaryLink.vue';
    import {Head, Link, useForm} from '@inertiajs/vue3';
    import {onMounted, ref} from 'vue';
    import {Icon} from '@iconify/vue';

    const props = defineProps({
        oficios: {
            default: [],
            type: Array
        }
    })
    const selected = ref([]);
    const filter = ref('');
    const rows = ref([]);
    const openModalDelete = ref(false);
    const openModalDeleteAll = ref(false);
    const initialPagination = ref({
        sortBy: 'desc',
        descending: false,
        page: 1,
        rowsPerPage: 10
    });
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
    const columns = [
        {name: 'tipo_oficio', label: 'Tipo de ofício', align: 'left', field: 'tipo_oficio', sortable: true},
        {name: 'data', label: 'Data', align: 'left', field: 'data', sortable: true},
        {name: 'tipo_documento', label: 'Documento', align: 'left', field: 'tipo_documento', sortable: true},
        {name: 'numero_oficio', label: 'Número', align: 'left', field: 'numero_oficio', sortable: true},
        {name: 'destinatario', label: 'Origem', align: 'left', field: 'destinatario', sortable: true},
        {name: 'status_inicial', label: 'Status Inicial', align: 'left', field: 'status_inicial', sortable: true},
        {name: 'responsavel', label: 'Responsavel', align: 'left', field: 'responsavel', sortable: true},
        {name: 'prazo', label: 'Prazo', align: 'left', field: 'prazo', sortable: true},
        {name: 'etapa', label: 'Etapa', align: 'left', field: 'etapa', sortable: true},
        {name: 'status_final', label: 'Status Final', align: 'left', field: 'status_final', sortable: true},
        {name: 'actions', label: 'Ações', align: 'center', field: 'actions', sortable: true},
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

    onMounted(() => {
        loadList();

        if (sessionStorage.getItem('filters.tipo_oficio') && sessionStorage.getItem('filters.tipo_oficio') != 'null') {
            filters.value.tipo_oficio = sessionStorage.getItem('filters.tipo_oficio');
            filtrar(filters.value.tipo_oficio, 'tipo_oficio');
        }

        if (sessionStorage.getItem('filters.data') && sessionStorage.getItem('filters.data') != 'null') {
            filters.value.data = sessionStorage.getItem('filters.data');
            filterDataFormatted.value = filters.value.data.split('/').reverse().join('/');
            filtrar(filters.value.data, 'data');
        }

        if (sessionStorage.getItem('filters.data.from') && sessionStorage.getItem('filters.data.from') != 'null') {
            filters.value.data = {
                from: sessionStorage.getItem('filters.data.from'),
                to: sessionStorage.getItem('filters.data.to'),
            };
            filterDataFormatted.value = filters.value.data.from.split('/').reverse().join('/') + ' - ' + filters.value.data.to.split('/').reverse().join('/');
            filtrar(filters.value.data, 'data');
        }

        if (sessionStorage.getItem('filters.tipo_documento') && sessionStorage.getItem('filters.tipo_documento') != 'null') {
            filters.value.tipo_documento = sessionStorage.getItem('filters.tipo_documento');
            filtrar(filters.value.tipo_documento, 'tipo_documento');
        }
        if (sessionStorage.getItem('filters.numero_oficio') && sessionStorage.getItem('filters.numero_oficio') != 'null') {
            filters.value.numero_oficio = sessionStorage.getItem('filters.numero_oficio');
            filtrar(filters.value.numero_oficio, 'numero_documento');
        }
        if (sessionStorage.getItem('filters.destinatario') && sessionStorage.getItem('filters.destinatario') != 'null') {
            filters.value.destinatario = sessionStorage.getItem('filters.destinatario');
            filtrar(filters.value.destinatario, 'destinatario');
        }
        if (sessionStorage.getItem('filters.responsavel') && sessionStorage.getItem('filters.responsavel') != 'null') {
            filters.value.responsavel = sessionStorage.getItem('filters.responsavel');
            filtrar(filters.value.responsavel, 'responsavel');
        }
        if (sessionStorage.getItem('filters.prazo') && sessionStorage.getItem('filters.prazo') != 'null') {
            filters.value.prazo = sessionStorage.getItem('filters.prazo');
            filtrar(filters.value.prazo, 'prazo');
        }
        if (sessionStorage.getItem('filters.etapa') && sessionStorage.getItem('filters.etapa') != 'null') {
            filters.value.etapa = sessionStorage.getItem('filters.etapa');
            filtrar(filters.value.etapa, 'etapa');
        }
    });

    function loadList () {
        rows.value = props.oficios;

        rows.value.forEach((oficio) => {
            if (oficio.tipo_oficio == 'Recebido') {
                oficio.data = oficio.data_recebimento ? oficio.data_recebimento.split('-').reverse().join('/') : oficio.data_recebimento;
            } else {
                oficio.data = oficio.data_emissao ? oficio.data_emissao.split('-').reverse().join('/') : oficio.data_emissao;
            }

            oficio.responsavel = '';
            oficio.responsaveis.forEach((responsavel, index) => {
                if (oficio.responsaveis.length > 1 && index < oficio.responsaveis.length - 1) {
                    oficio.responsavel += responsavel.user.name + ', ';
                } else {
                    oficio.responsavel += responsavel.user.name;
                }
            });

            if (oficio.destinatario.nome) {
                oficio.destinatario = oficio.destinatario.nome;
            } else {
                oficio.destinatario = oficio.destinatario;
            }
        });

        loadFilters();
    }

    function loadFilters () {
        optionsFilters.value.tipo_oficio = [];
        optionsFilters.value.tipo_documento = [];
        optionsFilters.value.numero_oficio = [];
        optionsFilters.value.destinatario = [];
        optionsFilters.value.responsavel = [];
        optionsFilters.value.prazo = [];
        optionsFilters.value.etapa = [];

        rows.value.map((oficio) => {
            if (!optionsFilters.value.tipo_oficio.includes(oficio.tipo_oficio)) {
                if (oficio.tipo_oficio) {
                    optionsFilters.value.tipo_oficio.push(oficio.tipo_oficio)
                }
            }

            if (!optionsFilters.value.tipo_documento.includes(oficio.tipo_documento)) {
                if (oficio.tipo_documento) {
                    optionsFilters.value.tipo_documento.push(oficio.tipo_documento)
                }
            }

            if (!optionsFilters.value.numero_oficio.includes(oficio.numero_oficio)) {
                if (oficio.numero_oficio) {
                    optionsFilters.value.numero_oficio.push(oficio.numero_oficio)
                }
            }

            if (!optionsFilters.value.destinatario.includes(oficio.destinatario)) {
                if (oficio.destinatario) {
                    optionsFilters.value.destinatario.push(oficio.destinatario)
                }
            }

            oficio.responsaveis.map((responsavel) => {
                if (!optionsFilters.value.responsavel.includes(responsavel.user.name)) {
                    if (responsavel.user.name) {
                        optionsFilters.value.responsavel.push(responsavel.user.name)
                    }
                }
            })


            if (!optionsFilters.value.prazo.includes(oficio.prazo)) {
                if (oficio.prazo) {
                    optionsFilters.value.prazo.push(oficio.prazo)
                }
            }

            if (!optionsFilters.value.etapa.includes(oficio.etapa)) {
                if (oficio.etapa) {
                    optionsFilters.value.etapa.push(oficio.etapa)
                }
            }

        });

    }

    const form = useForm({
        id: '',
        selecteds: [],
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

    function destroyAll () {
        form.delete(route('oficio.destroySelected'), {
            preserveScroll: true,
            onSuccess: (response) => {
                props.oficios = response.props.oficios;
                openModalDeleteAll.value = false;
                loadList();
                Notify.create({
                    message: 'Ofícios excluídos com sucesso!',
                    type: 'positive',
                });
            },
            onError: () => {
                Notify.create({
                    message: 'Erro ao excluir ofícios!',
                    type: 'negative',
                });
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
                props.oficios = response.props.oficios;
                loadList();
                openModalDelete.value = false;
                Notify.create({
                    message: 'Ofício excluído com sucesso!',
                    type: 'positive',
                });
            },
            onError: () => {
                Notify.create({
                    message: 'Erro ao excluir o ofício!',
                    type: 'negative',
                });
            }
        });
    }

    async function getPDF (items) {
        await items.map((item) => {
            window.open(route('oficio.generatepdf', item.id));
        });
    }

    function filterResponsavel (val, update) {
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

    function filterPrazo (val, update) {
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

    function filterNumeroOficio (val, update) {
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

    function filtrar (value, type) {
        filters.value[type] = value;

        if (filters.value.data != null) {
            if (value.from) {
                filterDataFormatted.value = value.from.split('/').reverse().join('/') + ' - ' + value.to.split('/').reverse().join('/');
            } else {
                filterDataFormatted.value = value.split('/').reverse().join('/');
            }
        }

        sessionStorage.removeItem('filters.' + type);
        if (type == 'data') {
            sessionStorage.removeItem('filters.data.from');
            sessionStorage.removeItem('filters.data.to');
            sessionStorage.removeItem('filters.data');

            if (value) {
                if (value.from) {
                    sessionStorage.setItem('filters.data.from', value.from);
                    sessionStorage.setItem('filters.data.to', value.to);
                } else {
                    sessionStorage.setItem('filters.data', value);
                }
            }
        } else {
            sessionStorage.setItem('filters.' + type, value);
        }

        loadList();

        if (filters.value.tipo_oficio != null) {
            rows.value = rows.value.filter((item, index) => {
                return (filters.value.tipo_oficio != null && item.tipo_oficio == filters.value.tipo_oficio);
            });
        }


        if (filters.value.data != null) {
            rows.value = rows.value.filter((item, index) => {
                if (filters.value.data != null && item.data) {
                    if (filters.value.data.from) {
                        let dataCompare = new Date(item.data.split('/').reverse().join('-'));
                        let dataFromCompare = new Date(filters.value.data.from.split('/').join('-'));
                        let dataToCompare = new Date(filters.value.data.to.split('/').join('-'));
                        return (dataCompare >= dataFromCompare && dataCompare <= dataToCompare);
                    } else {
                        let dataCompare = item.data.split('/').reverse().join('-');
                        let dataToCompare = filters.value.data.split('/').join('-');
                        return (dataCompare == dataToCompare);
                    }
                }
            });
        }


        if (filters.value.tipo_documento != null) {
            rows.value = rows.value.filter((item, index) => {
                return (filters.value.tipo_documento != null && item.tipo_documento == filters.value.tipo_documento);
            });
        }

        if (filters.value.numero_oficio != null) {
            rows.value = rows.value.filter((item, index) => {
                return (filters.value.numero_oficio != null && item.numero_oficio == filters.value.numero_oficio);
            });
        }

        if (filters.value.destinatario != null) {
            rows.value = rows.value.filter((item, index) => {
                return (filters.value.destinatario != null && item.destinatario == filters.value.destinatario);
            });
        }

        if (filters.value.responsavel != null) {
            rows.value = rows.value.filter((item, index) => {
                return (filters.value.responsavel != null && item.responsavel.includes(filters.value.responsavel));
            });
        }

        if (filters.value.prazo != null) {
            rows.value = rows.value.filter((item, index) => {
                return (filters.value.prazo != null && item.prazo == filters.value.prazo);
            });
        }

        if (filters.value.etapa != null) {
            rows.value = rows.value.filter((item, index) => {
                return (filters.value.etapa != null && item.etapa == filters.value.etapa);
            });
        }

        loadFilters();
    }

</script>

<template>
<Head title="Ofícios"/>

<AuthenticatedLayout>
    <div class="tw-my-5 sm:tw-my-10">
        <div class="tw-flex tw-justify-between tw-px-4">
            <span class="tw-text-3xl">Ofícios</span>
            <PrimaryLink
                :href="route('oficio.create')"
                background="primary"
                class="tw-px-4 tw-py-3"
                icon="material-symbols:add-circle-outline"
                text="Adicionar"
            />
        </div>

        <!-- =======  Filtros =============-->
        <div class="tw-px-4 tw-w-full tw-mt-8 tw-grid tw-grid-cols-1 sm:tw-grid-cols-2 lg:tw-grid-cols-4 tw-gap-4">
            <div class="tw-w-full">
                <label class="tw-flex tw-items-center" for="">Tipo de oficio</label>
                <q-select
                    v-model="filters.tipo_oficio"
                    :options="optionsFilters.tipo_oficio"
                    clearable
                    dense
                    outlined
                    @update:model-value="val => filtrar(val, 'tipo_oficio')"
                />
            </div>

            <div class="tw-w-full">
                <label class="tw-flex tw-items-center" for="">Data</label>
                <q-input
                    v-model="filterDataFormatted"
                    clearable
                    dense
                    outlined
                    @update:model-value="val => filtrar(val, 'data')"
                >
                    <template v-slot:append>
                        <q-icon class="cursor-pointer" name="event">
                            <q-popup-proxy cover transition-hide="scale" transition-show="scale">
                                <q-date v-model="filters.data" range @update:model-value="val => filtrar(val, 'data')">
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
                    :options="optionsFilters.tipo_documento"
                    clearable
                    dense
                    outlined
                    @update:model-value="val => filtrar(val, 'tipo_documento')"
                />
            </div>

            <div class="tw-w-full">
                <label class="tw-flex tw-items-center" for="">Número do documento</label>
                <q-select
                    v-model="filters.numero_oficio"
                    :options="optionsFiltred.numero_oficio"
                    clearable
                    dense
                    outlined
                    use-input
                    @filter="filterNumeroOficio"
                    @update:model-value="val => filtrar(val, 'numero_oficio')"
                />
            </div>

            <div class="tw-w-full">
                <label class="tw-flex tw-items-center" for="">Origem/Destinatário</label>
                <q-select
                    v-model="filters.destinatario"
                    :options="optionsFilters.destinatario"
                    clearable
                    dense
                    outlined
                    @update:model-value="val => filtrar(val, 'destinatario')"
                />
            </div>

            <div class="tw-w-full">
                <label class="tw-flex tw-items-center" for="">Responsável</label>
                <q-select
                    v-model="filters.responsavel"
                    :options="optionsFiltred.responsavel"
                    clearable
                    dense
                    outlined
                    use-input
                    @filter="filterResponsavel"
                    @update:model-value="val => filtrar(val, 'responsavel')"
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
                    :options="optionsFiltred.prazo"
                    clearable
                    dense
                    outlined
                    use-input
                    @filter="filterPrazo"
                    @update:model-value="val => filtrar(val, 'prazo')"
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
                    :options="optionsFilters.etapa"
                    clearable
                    dense
                    outlined
                    @update:model-value="val => filtrar(val, 'etapa')"
                />
            </div>

        </div>

        <!-- =======  Tabela =============-->
        <div class="tw-w-full tw-mt-6">
            <q-table
                v-model:selected="selected"
                :columns="columns"
                :filter="filter"
                :pagination="initialPagination"
                :rows="rows"
                :visible-columns="visibleColumns"
                no-data-label="Sem dados cadastrados"
                no-results-label="A sua pesquisa não retornou dados"
                row-key="id"
                selection="multiple"
                virtual-scroll
            >

                <template v-slot:body="props">
                    <q-tr :class="{'tw-bg-red-500 tw-text-white': props.row.etapa == 'Atrasado',
                                                        'tw-bg-[#10b981] tw-text-white': props.row.etapa == 'Finalizado',
                                                        'tw-bg-gray-400/50 tw-text-black': props.row.recente == false && props.row.etapa != 'Atrasado' && props.row.etapa != 'Finalizado'}"
                          :props="props">
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
                            <div class="tw-flex tw-flex-wrap">
                                {{ props.row.destinatario }}
                            </div>
                        </q-td>
                        <q-td key="status_inicial" :props="props">
                            {{ props.row.status_inicial }}
                        </q-td>
                        <q-td key="responsavel" :props="props">
                            {{ props.row.responsavel }}
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
                                <Link :href="route('oficio.edit', props.row.id)"
                                      class="tw-text-slate-800 hover:tw-text-green-500 tw-text-sm tw-bg-white hover:tw-bg-slate-100 tw-border tw-border-slate-200 tw-rounded-l-lg tw-font-medium tw-px-4 tw-py-2 tw-inline-flex tw-space-x-1 tw-items-center">
                                    <Icon icon="tabler:edit"/>
                                </Link>
                                <Link :href="route('oficio.detail', props.row.id)"
                                      class="tw-text-slate-800 hover:tw-text-blue-500 tw-text-sm tw-bg-white hover:tw-bg-slate-100 tw-border-y tw-border-slate-200 tw-font-medium tw-px-4 tw-py-2 tw-inline-flex tw-space-x-1 tw-items-center">
                                    <Icon icon="mdi:eye-outline"/>
                                </Link>
                                <button v-if="props.row.user_created == $page.props.auth.user.id"
                                        class="tw-text-slate-800 hover:tw-text-red-500 tw-text-sm tw-bg-white hover:tw-bg-slate-100 tw-border tw-border-slate-200 tw-rounded-r-lg tw-font-medium tw-px-4 tw-py-2 tw-inline-flex tw-space-x-1 tw-items-center"
                                        @click="destroyItem(props.row)">
                                    <Icon icon="uil:trash"/>
                                </button>
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
                    <div class="tw-grid tw-grid-cols-1 sm:tw-grid-cols-2 tw-gap-3">
                        <q-input v-model="filter" dense outlined placeholder="Pesquisar" type="search">
                            <template v-slot:append>
                                <q-icon name="search"/>
                            </template>
                        </q-input>
                        <q-select
                            v-model="visibleColumns"
                            :options="columns"
                            dense
                            display-value="Selecionar colunas"
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
                    </div>
                </template>

                <template v-slot:no-data="{ icon, message, filter }">
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

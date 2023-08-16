<template>
    <Head :title="typeAction == 'edit' ? 'Editar Ofício' : 'Adicionar Ofício'" />

    <AuthenticatedLayout>
        <div class="tw-py-12 tw-px-5 md:tw-px-20">
            <!-- =========== HEADER  ======== -->
            <div class="tw-flex tw-items-center tw-mb-10">
                <span class="tw-text-3xl tw-mr-7">Ofício</span>
                <Link class="tw-flex tw-items-center tw-justify-center tw-gap-1" :href="route('oficio.index')">
                    <Icon icon="ic:baseline-keyboard-arrow-left" /> <span>Voltar</span>
                </Link>
            </div>
            <!-- =========== FORM  ======== -->
            <q-tabs
                v-model="tab"
                dense
                class="tw-text-gray-500"
                active-color="primary"
                indicator-color="primary"
                align="justify"
                narrow-indicator
                >
                    <q-tab name="oficio" label="Ofícios" />
                    <q-tab name="oficio_relacionado" label="Ofícios relacionados" />
            </q-tabs>

            <q-tab-panels v-model="tab" animated>
                <q-tab-panel name="oficio">
                    <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4">
                        <div>
                            <InputLabel required value="Tipo de Ofício" />
                            <q-select v-model="form.tipo_oficio"
                                outlined
                                :options="options_tipo_oficio"
                                :error-message="form.errors['tipo_oficio']" :error="!!form.errors['tipo_oficio']"
                            />
                        </div>

                        <div v-if="form.tipo_oficio == 'Recebido'">
                            <InputLabel required value="Tipo de Documento" />
                            <q-select v-model="form.tipo_documento"
                                outlined
                                :options="['OFÍCIO', 'NOTIFICAÇÃO', 'MANIFESTAÇÃO', 'OFÍCIO CIRCULAR', 'DESPACHO']"
                                :error-message="form.errors['tipo_documento']" :error="!!form.errors['tipo_documento']"
                            />
                        </div>

                        <div v-if="form.tipo_oficio == 'Expedido'">
                            <InputLabel required value="Diretoria" />
                            <q-select
                                v-model="form.diretoria"
                                use-input
                                clearable
                                multiple
                                use-chips
                                outlined
                                stack-label
                                counter
                                input-debounce="0"
                                :options="options_filter_diretoria"
                                @filter="filterFnDiretoria"
                                :error-message="form.errors['diretoria']" :error="!!form.errors['diretoria']"
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
                    </div>
                    <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4">
                        <div>
                            <InputLabel required value="Número do ofício" />
                            <q-input v-model="form.numero_oficio"
                                counter
                                outlined
                                maxlength="255"
                                :error-message="form.errors['numero_oficio']" :error="!!form.errors['numero_oficio']"
                            />
                        </div>

                        <div>
                            <InputLabel required value="Origem" v-if="form.tipo_oficio == 'Recebido'" />
                            <InputLabel required value="Destinatário" v-if="form.tipo_oficio == 'Expedido'" />
                            <q-select
                                v-model="form.destinatario_id"
                                use-input
                                clearable
                                hide-selected
                                fill-input
                                outlined
                                input-debounce="0"
                                :options="options_filter_destinatario"
                                @filter="filterFn"
                                :error-message="form.errors['destinatario_id']" :error="!!form.errors['destinatario_id']"
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

                        <div>
                            <InputLabel required value="Assunto do ofício recebido" v-if="form.tipo_oficio == 'Recebido'" />
                            <InputLabel required value="Assunto do ofício expedido" v-if="form.tipo_oficio == 'Expedido'" />
                            <q-input v-model="form.assunto_oficio"
                                counter
                                autogrow
                                outlined
                                :error-message="form.errors['assunto_oficio']" :error="!!form.errors['assunto_oficio']"
                            />
                        </div>

                        <div>
                            <InputLabel required value="Dias corridos" />
                            <q-select v-model="form.dias_corridos"
                                outlined
                                :options="['Sim', 'Não']"
                                :error-message="form.errors['dias_corridos']" :error="!!form.errors['dias_corridos']"
                            />
                        </div>

                        <div v-if="form.tipo_oficio == 'Recebido'">
                            <InputLabel required value="Data de recebimento" />
                            <q-input v-model="form.data_recebimento"
                                outlined
                                type="date"
                                :error-message="form.errors['data_recebimento']" :error="!!form.errors['data_recebimento']"
                            />
                        </div>

                        <div v-if="form.tipo_oficio == 'Expedido'">
                            <InputLabel required value="Data de emissão" />
                            <q-input v-model="form.data_emissao"
                                outlined
                                type="date"
                                :error-message="form.errors['data_emissao']" :error="!!form.errors['data_emissao']"
                            />
                        </div>

                        <div>
                            <InputLabel required value="Prazo final" />
                            <q-input v-model="form.data_prazo"
                                outlined
                                type="date"
                                :error-message="form.errors['data_prazo']" :error="!!form.errors['data_prazo']"
                            />
                        </div>

                        <div>
                            <InputLabel required value="Responsáveis" />
                            <q-select
                                v-model="form.responsaveis"
                                use-input
                                clearable
                                multiple
                                use-chips
                                outlined
                                stack-label
                                counter
                                input-debounce="0"
                                :options="options_filter_responsaveis"
                                @filter="filterFnResponsaveis"
                                :error-message="form.errors['responsaveis']" :error="!!form.errors['responsaveis']"
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

                        <div>
                            <InputLabel required value="Interessados" />
                            <q-select
                                v-model="form.interessados"
                                use-input
                                clearable
                                multiple
                                outlined
                                use-chips
                                counter
                                stack-label
                                input-debounce="0"
                                :options="options_filter_interessados"
                                @filter="filterFnInteressados"
                                :error-message="form.errors['interessados']" :error="!!form.errors['interessados']"
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

                        <div v-if="form.tipo_oficio == 'Expedido'">
                            <InputLabel value="Número da notificação" />
                            <q-input v-model="form.numero_notificacao"
                                counter
                                outlined
                                maxlength="255"
                                :error-message="form.errors['numero_notificacao']" :error="!!form.errors['numero_notificacao']"
                            />
                        </div>

                        <div>
                            <InputLabel value="Status inicial" />
                            <q-input v-model="form.status_inicial"
                                outlined
                                autogrow
                                counter
                                :error-message="form.errors['status_inicial']" :error="!!form.errors['status_inicial']"
                            />
                        </div>

                        <div>
                            <InputLabel value="Status Final" />
                            <q-input v-model="form.status_final"
                                outlined
                                autogrow
                                counter
                                :error-message="form.errors['status_final']" :error="!!form.errors['status_final']"
                            />
                        </div>

                        <div>
                            <InputLabel value="Anexos" />
                            <q-file
                                v-model="form.anexos"
                                multiple
                                append
                                outlined
                                use-chips
                                :error-message="form.errors['anexos']" :error="!!form.errors['anexos']"
                            >
                                <template v-slot:prepend>
                                    <q-icon name="attach_file" />
                                </template>
                            </q-file>
                        </div>

                        <div class="tw-col-span-2" v-if="anexos_novos.length > 0">
                            <q-list bordered separator>
                                <q-item v-for="(anexo, index) in anexos_novos" :key="index">
                                    <div class="tw-w-full tw-flex tw-items-center tw-justify-between">
                                        {{ anexo.nome }}
                                        <div class="tw-flex tw-gap-2">
                                            <a
                                                :href="url + '/storage/' + anexo.caminho"
                                                target="_blank"
                                                class="tw-p-2 tw-text-info"
                                            >
                                                Visualizar
                                            </a>
                                            <PrimaryButton
                                                @click="destroyArquivo(index, anexo.id)"
                                                class="tw-p-2 tw-text-negative"
                                                icon="ic:baseline-delete"
                                            />
                                        </div>
                                    </div>
                                </q-item>
                            </q-list>
                        </div>

                        <div>
                            <InputLabel value="Observação" />
                            <q-input v-model="form.observacao"
                                outlined
                                autogrow
                                counter
                                :error-message="form.errors['observacao']" :error="!!form.errors['observacao']"
                            />
                        </div>

                        <div>
                            <InputLabel required value="Etapa" />
                            <q-select v-model="form.etapa"
                                outlined
                                :options="['Ciente', 'Responder', 'Atrasado', 'Em aberto', 'Finalizado']"
                                :error-message="form.errors['etapa']" :error="!!form.errors['etapa']"
                            />
                        </div>

                    </div>
                </q-tab-panel>

                <q-tab-panel name="oficio_relacionado">
                    <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4">
                        <div>
                            <InputLabel value="Ofícios relacionados" />
                            <q-select
                                v-model="form.oficio_relacionado"
                                use-input
                                clearable
                                multiple
                                outlined
                                use-chips
                                counter
                                stack-label
                                input-debounce="0"
                                :options="options_filter_oficio_relacionado"
                                @filter="filterFnOficioRelacionado"
                                :error-message="form.errors['oficio_relacionado']" :error="!!form.errors['oficio_relacionado']"
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

                        <div>
                            <InputLabel value="Ofícios externos" />
                            <q-select v-model="form.oficio_externo"
                                use-input
                                clearable
                                multiple
                                outlined
                                use-chips
                                stack-label
                                input-debounce="0"
                                new-value-mode="add-unique"
                                :error-message="form.errors['oficio_externo']" :error="!!form.errors['oficio_externo']"
                            />
                        </div>
                    </div>
                </q-tab-panel>
            </q-tab-panels>

            <div class="tw-flex tw-items-center">
                <PrimaryButton
                    v-if="typeAction == 'create'"
                    @click="storeData()"
                    :disabled="form.processing"
                    class="tw-px-4 tw-py-3"
                    background="primary"
                    text="Adicionar"
                    icon="material-symbols:add-circle-outline"
                />
                <PrimaryButton
                    v-if="typeAction == 'edit'"
                    @click="saveData()"
                    :disabled="form.processing"
                    class="tw-px-4 tw-py-3"
                    background="primary"
                    text="Salvar"
                    icon="ic:baseline-save"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { Icon } from '@iconify/vue';
import { Notify } from 'quasar';

const props = defineProps({
    oficio: {
        default: {},
        type: Object
    },
    oficios: {
        default: [],
        type: Array
    },
    usuarios: {
        default: [],
        type: Array
    },
    destinatarios: {
        default: [],
        type: Array
    },
    diretorias: {
        default: [],
        type: Array
    }
})

onMounted(() => {
    if(props.oficio.tipo_oficio){
        typeAction.value = 'edit';
    }else{
        typeAction.value = 'create';
        form.tipo_oficio = 'Recebido';
    }

    props.usuarios.map((item) => {
        options_responsaveis.value.push({
            label: item.name,
            id: item.id
        });

        options_interessados.value.push({
            label: item.name,
            id: item.id
        });
    });

    props.destinatarios.map((item) => {
        options_destinatario.value.push({
            label: item.nome,
            id: item.id
        })
    });

    props.diretorias.map((item) => {
        if(typeAction.value == 'edit'){
            props.oficio.diretorias.map((diretoria) => {
                if(diretoria.diretoria_id == item.id){
                    form.diretoria.push({label: item.nome, id: item.id});
                }
            });
        }

        options_diretoria.value.push({
            label: item.nome,
            id: item.id
        })
    });

    props.oficios.map((item) => {
        options_oficio_relacionado.value.push({
            label: item.tipo_oficio + ' - ' + item.numero_oficio,
            id: item.id
        })
    });

    //Preenche todos os dados do formulário
    if(typeAction.value == 'edit'){
        form.id = props.oficio.id;
        form.tipo_oficio = props.oficio.tipo_oficio;
        form.destinatario_id = {label: props.oficio.destinatario.nome, id: props.oficio.destinatario.id};
        form.numero_oficio = props.oficio.numero_oficio;
        form.prazo = props.oficio.prazo;
        form.dias_corridos = props.oficio.dias_corridos == 0 ? 'Não' : 'Sim' ;
        form.observacao = props.oficio.observacao;
        form.tipo_documento = props.oficio.tipo_documento;
        form.status_inicial = props.oficio.status_inicial;
        form.status_final = props.oficio.status_final;
        form.data_prazo = props.oficio.data_prazo;
        form.etapa = props.oficio.etapa;
        form.assunto_oficio = props.oficio.assunto_oficio;
        form.data_recebimento = props.oficio.data_recebimento;
        form.data_emissao = props.oficio.data_emissao;
        form.numero_notificacao = props.oficio.numero_notificacao;

        props.oficio.responsaveis.map((item) => {
            form.responsaveis.push({label: item.user.name, id: item.user.id});
        });

        props.oficio.interessados.map((item) => {
            form.interessados.push({label: item.user.name, id: item.user.id});
        });

        props.oficio.anexos.map((anexo) => {
            anexos_novos.value.push(anexo);
        });

        props.oficio.oficios_externos.map((item) => {
            form.oficio_externo.push({label: item.descricao, id: item.id});
        });

        props.oficio.oficios_relacionados.map((item) => {
            form.oficio_relacionado.push({label: item.oficio_filho.tipo_oficio + ' - ' + item.oficio_filho.numero_oficio, id: item.oficio_filho.id});
        });
    }
});

const options_tipo_oficio = ['Recebido', 'Expedido'];
const options_responsaveis = ref([]);
const options_interessados = ref([]);
const options_destinatario = ref([]);
const options_diretoria = ref([]);
const options_oficio_relacionado = ref([]);
const url = window.location.protocol + '//' + window.location.host;
const typeAction = ref('');

const options_filter_destinatario = ref(options_destinatario.value)
const options_filter_responsaveis = ref(options_responsaveis.value)
const options_filter_interessados = ref(options_interessados.value)
const options_filter_diretoria = ref(options_diretoria.value)
const options_filter_oficio_relacionado = ref(options_oficio_relacionado.value)
const anexos_novos = ref([]);

const form = useForm({
    id: '',
    tipo_oficio: '',
    responsaveis: [],
    interessados: [],
    destinatario_id: '',
    diretoria: [],
    numero_oficio: '',
    prazo: '',
    dias_corridos: '',
    anexos: [],
    numero_notificacao: '',
    tipo_documento: '',
    status_inicial: '',
    status_final: '',
    etapa: '',
    observacao: '',
    assunto_oficio: '',
    data_emissao: '',
    data_recebimento: '',
    data_prazo: '',
    oficio_relacionado: [],
    oficio_externo: [],
})

const tab = ref('oficio')

function storeData(){
    form.post(route('oficio.store'),{
        preserveScroll: true,
        onSuccess: () => {
            Notify.create({
                message: 'Ofício cadastrado com sucesso!',
                type: 'positive',
            })
        },
        onError: () => {
            Notify.create({
                message: 'Erro ao cadastrar ofício!',
                type: 'negative',
            })
        },
    });
}

function saveData(){
    form.post(route('oficio.update', form.id),{
        preserveScroll: true,
        onSuccess: (response) => {
            Notify.create({
                message: 'Ofício atualizado com sucesso!',
                type: 'positive',
            })
        },
        onError: () => {
            Notify.create({
                message: 'Erro ao atualizar ofício!',
                type: 'negative',
            })
        },
    });
}

function filterFn (val, update) {
    if (val === '') {
        update(() => {
        options_filter_destinatario.value = options_destinatario.value

        // here you have access to "ref" which
        // is the Vue reference of the QSelect
        })
        return
    }
    update(() => {
        const needle = val.toLowerCase()
        options_filter_destinatario.value = options_destinatario.value.filter(v => v.label.toLowerCase().indexOf(needle) > -1)
    })
}

function filterFnResponsaveis (val, update) {
    if (val === '') {
        update(() => {
            options_filter_responsaveis.value = options_responsaveis.value

            // here you have access to "ref" which
            // is the Vue reference of the QSelect
        })
        return
    }
    update(() => {
        const needle = val.toLowerCase()
        options_filter_responsaveis.value = options_responsaveis.value.filter(v => v.label.toLowerCase().indexOf(needle) > -1)
    })
}
function filterFnInteressados (val, update) {
    if (val === '') {
        update(() => {
        options_filter_interessados.value = options_interessados.value

        // here you have access to "ref" which
        // is the Vue reference of the QSelect
        })
        return
    }
    update(() => {
        const needle = val.toLowerCase()
        options_filter_interessados.value = options_interessados.value.filter(v => v.label.toLowerCase().indexOf(needle) > -1)
    })
}
function filterFnDiretoria (val, update) {
    if (val === '') {
        update(() => {
        options_filter_diretoria.value = options_diretoria.value

        // here you have access to "ref" which
        // is the Vue reference of the QSelect
        })
        return
    }
    update(() => {
        const needle = val.toLowerCase()
        options_filter_diretoria.value = options_diretoria.value.filter(v => v.label.toLowerCase().indexOf(needle) > -1)
    })
}
function filterFnOficioRelacionado (val, update) {
    if (val === '') {
        update(() => {
        options_filter_oficio_relacionado.value = options_oficio_relacionado.value

        // here you have access to "ref" which
        // is the Vue reference of the QSelect
        })
        return
    }
    update(() => {
        const needle = val.toLowerCase()
        options_filter_oficio_relacionado.value = options_oficio_relacionado.value.filter(v => v.label.toLowerCase().indexOf(needle) > -1)
    })
}

function destroyArquivo(index, id)
{
    if(index != null && id != null){
        form.delete(route('oficio.destroyAnexo', id),{
            preserveScroll: true,
            onSuccess: (response) => {
                anexos_novos.value.splice(index, 1);
                Notify.create({
                    message: 'Anexo excluído com sucesso!',
                    type: 'positive',
                });
            },
            onError: () => {
                Notify.create({
                    message: 'Erro ao excluir anexo!',
                    type: 'negative',
                });
            }
        });
    }
}
</script>

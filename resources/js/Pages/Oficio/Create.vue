<template>
    <Head title="Adicionar Ofício" />

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
                                :error-message="form.errors['dados_recebidos.tipo_oficio']" :error="!!form.errors['dados_recebidos.tipo_oficio']"
                            />
                        </div>

                        <div v-if="form.tipo_oficio == 'Recebido'">
                            <InputLabel required value="Tipo de Documento" />
                            <q-select v-model="form.dados_recebidos.tipo_documento"
                                outlined
                                :options="['Ofício', 'Notificação', 'Manifestação', 'Ofício Circular', 'Despacho']"
                                :error-message="form.errors['dados_recebidos.tipo_documento']" :error="!!form.errors['dados_recebidos.tipo_documento']"
                            />
                        </div>
                    </div>
                    <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4" v-if="form.tipo_oficio == 'Recebido'">
                        <div>
                            <InputLabel required value="Número do ofício" />
                            <q-input v-model="form.dados_recebidos.numero_oficio"
                                counter
                                outlined
                                maxlength="255"
                                :error-message="form.errors['dados_recebidos.numero_oficio']" :error="!!form.errors['dados_recebidos.numero_oficio']"
                            />
                        </div>

                        <div>
                            <InputLabel required value="Origem" />
                            <q-select
                                v-model="form.dados_recebidos.destinatario_id"
                                use-input
                                clearable
                                hide-selected
                                fill-input
                                outlined
                                input-debounce="0"
                                :options="options_filter_destinatario"
                                @filter="filterFn"
                                :error-message="form.errors['dados_recebidos.destinatario_id']" :error="!!form.errors['dados_recebidos.destinatario_id']"
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
                            <InputLabel required value="Assunto do ofício recebido" />
                            <q-input v-model="form.dados_recebidos.assunto_oficio"
                                counter
                                autogrow
                                outlined
                                :error-message="form.errors['dados_recebidos.assunto_oficio']" :error="!!form.errors['dados_recebidos.assunto_oficio']"
                            />
                        </div>

                        <div>
                            <InputLabel required value="Dias corridos" />
                            <q-select v-model="form.dados_recebidos.dias_corridos"
                                outlined
                                :options="['Sim', 'Não']"
                                :error-message="form.errors['dados_recebidos.dias_corridos']" :error="!!form.errors['dados_recebidos.dias_corridos']"
                            />
                        </div>

                        <div>
                            <InputLabel required value="Data de recebimento" />
                            <q-input v-model="form.dados_recebidos.data_recebimento"
                                outlined
                                type="date"
                                :error-message="form.errors['dados_recebidos.data_recebimento']" :error="!!form.errors['dados_recebidos.data_recebimento']"
                            />
                        </div>

                        <div>
                            <InputLabel required value="Prazo final" />
                            <q-input v-model="form.dados_recebidos.data_prazo"
                                outlined
                                type="date"
                                :error-message="form.errors['dados_recebidos.data_prazo']" :error="!!form.errors['dados_recebidos.data_prazo']"
                            />
                        </div>

                        <div>
                            <InputLabel required value="Responsáveis" />
                            <q-select
                                v-model="form.dados_recebidos.responsaveis"
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
                                :error-message="form.errors['dados_recebidos.responsaveis']" :error="!!form.errors['dados_recebidos.responsaveis']"
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
                                v-model="form.dados_recebidos.interessados"
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
                                :error-message="form.errors['dados_recebidos.interessados']" :error="!!form.errors['dados_recebidos.interessados']"
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
                            <InputLabel value="Status inicial" />
                            <q-input v-model="form.dados_recebidos.status_inicial"
                                outlined
                                autogrow
                                counter
                                :error-message="form.errors['dados_recebidos.status_inicial']" :error="!!form.errors['dados_recebidos.status_inicial']"
                            />
                        </div>

                        <div>
                            <InputLabel value="Status Final" />
                            <q-input v-model="form.dados_recebidos.status_final"
                                outlined
                                autogrow
                                counter
                                :error-message="form.errors['dados_recebidos.status_final']" :error="!!form.errors['dados_recebidos.status_final']"
                            />
                        </div>

                        <div>
                            <InputLabel value="Anexos" />
                            <q-file
                                v-model="form.dados_recebidos.anexos"
                                multiple
                                append
                                outlined
                                use-chips
                                :error-message="form.errors['dados_recebidos.anexos']" :error="!!form.errors['dados_recebidos.anexos']"
                            >
                                <template v-slot:prepend>
                                    <q-icon name="attach_file" />
                                </template>
                            </q-file>
                        </div>

                        <div>
                            <InputLabel value="Observação" />
                            <q-input v-model="form.dados_recebidos.observacao"
                                outlined
                                autogrow
                                counter
                                :error-message="form.errors['dados_recebidos.observacao']" :error="!!form.errors['dados_recebidos.observacao']"
                            />
                        </div>

                        <div>
                            <InputLabel required value="Etapa" />
                            <q-select v-model="form.dados_recebidos.etapa"
                                outlined
                                :options="['Ciente', 'Responder', 'Atrasado', 'Em aberto', 'Finalizado']"
                                :error-message="form.errors['dados_recebidos.etapa']" :error="!!form.errors['dados_recebidos.etapa']"
                            />
                        </div>

                    </div>

                    <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4" v-if="form.tipo_oficio == 'Expedido'">
                        <div>
                            <InputLabel required value="Diretoria" />
                            <q-select
                                v-model="form.dados_expedidos.diretoria"
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
                                :error-message="form.errors['dados_expedidos.diretoria']" :error="!!form.errors['dados_expedidos.diretoria']"
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
                            <InputLabel required value="Destinatário" />
                            <q-select
                                v-model="form.dados_expedidos.destinatario_id"
                                use-input
                                clearable
                                hide-selected
                                fill-input
                                outlined
                                input-debounce="0"
                                :options="options_filter_destinatario"
                                @filter="filterFn"
                                :error-message="form.errors['dados_expedidos.destinatario_id']" :error="!!form.errors['dados_expedidos.destinatario_id']"
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
                            <InputLabel required value="Responsáveis" />
                            <q-select
                                v-model="form.dados_expedidos.responsaveis"
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
                                :error-message="form.errors['dados_expedidos.responsaveis']" :error="!!form.errors['dados_expedidos.responsaveis']"
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
                                v-model="form.dados_expedidos.interessados"
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
                                :error-message="form.errors['dados_expedidos.interessados']" :error="!!form.errors['dados_expedidos.interessados']"
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
                            <InputLabel required value="Número do ofício" />
                            <q-input v-model="form.dados_expedidos.numero_oficio"
                                counter
                                outlined
                                maxlength="255"
                                :error-message="form.errors['dados_expedidos.numero_oficio']" :error="!!form.errors['dados_expedidos.numero_oficio']"
                            />
                        </div>

                        <div>
                            <InputLabel required value="Data de emissão" />
                            <q-input v-model="form.dados_expedidos.data_emissao"
                                outlined
                                type="date"
                                :error-message="form.errors['dados_expedidos.data_emissao']" :error="!!form.errors['dados_expedidos.data_emissao']"
                            />
                        </div>

                        <div>
                            <InputLabel required value="Prazo final" />
                            <q-input v-model="form.dados_expedidos.data_prazo"
                                outlined
                                type="date"
                                :error-message="form.errors['dados_expedidos.data_prazo']" :error="!!form.errors['dados_expedidos.data_prazo']"
                            />
                        </div>

                        <div>
                            <InputLabel required value="Dias corridos" />
                            <q-select v-model="form.dados_expedidos.dias_corridos"
                                outlined
                                :options="['Sim', 'Não']"
                            />
                        </div>

                        <div>
                            <InputLabel value="Status Inicial" />
                            <q-input v-model="form.dados_expedidos.status_inicial"
                                outlined
                                autogrow
                                counter
                                :error-message="form.errors['dados_expedidos.status_inicial']" :error="!!form.errors['dados_expedidos.status_inicial']"
                            />
                        </div>

                        <div>
                            <InputLabel value="Status Final" />
                            <q-input v-model="form.dados_expedidos.status_final"
                                outlined
                                autogrow
                                counter
                                :error-message="form.errors['dados_expedidos.status_final']" :error="!!form.errors['dados_expedidos.status_final']"
                            />
                        </div>

                        <div>
                            <InputLabel value="Número da notificação" />
                            <q-input v-model="form.dados_expedidos.numero_notificacao"
                                counter
                                outlined
                                maxlength="255"
                                :error-message="form.errors['dados_expedidos.numero_notificacao']" :error="!!form.errors['dados_expedidos.numero_notificacao']"
                            />
                        </div>

                        <div>
                            <InputLabel value="Assunto do ofício" />
                            <q-input v-model="form.dados_expedidos.assunto_oficio"
                                outlined
                                autogrow
                                counter
                                :error-message="form.errors['dados_expedidos.assunto_oficio']" :error="!!form.errors['dados_expedidos.assunto_oficio']"
                            />
                        </div>

                        <div>
                            <InputLabel value="Anexos" />
                            <q-file
                                v-model="form.dados_expedidos.anexos"
                                multiple
                                append
                                outlined
                                use-chips
                                :error-message="form.errors['dados_expedidos.anexos']" :error="!!form.errors['dados_expedidos.anexos']"
                            >
                                <template v-slot:prepend>
                                    <q-icon name="attach_file" />
                                </template>
                            </q-file>
                        </div>

                        <div>
                            <InputLabel value="Observação" />
                            <q-input v-model="form.dados_expedidos.observacao"
                                outlined
                                autogrow
                                counter
                                :error-message="form.errors['dados_expedidos.observacao']" :error="!!form.errors['dados_expedidos.observacao']"
                            />
                        </div>

                        <div>
                            <InputLabel required value="Etapa" />
                            <q-select v-model="form.dados_expedidos.etapa"
                                outlined
                                :options="['Responder', 'Enviar', 'Retificar', 'Atrasado', 'Finalizado']"
                                :error-message="form.errors['dados_expedidos.etapa']" :error="!!form.errors['dados_expedidos.etapa']"
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
                    @click="storeData()"
                    :disabled="form.processing"
                    class="tw-px-4 tw-py-3"
                    background="primary"
                    text="Cadastrar"
                    icon="material-symbols:add-circle-outline"
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
    },
})

onMounted(() => {
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
        console.log(item);
        options_destinatario.value.push({
            label: item.nome,
            id: item.id
        })
    });

    props.diretorias.map((item) => {
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
});

const options_tipo_oficio = ['Recebido', 'Expedido'];
const options_responsaveis = ref([]);
const options_interessados = ref([]);
const options_destinatario = ref([]);
const options_diretoria = ref([]);
const options_oficio_relacionado = ref([]);

const options_filter_destinatario = ref(options_destinatario.value)
const options_filter_responsaveis = ref(options_responsaveis.value)
const options_filter_interessados = ref(options_interessados.value)
const options_filter_diretoria = ref(options_diretoria.value)
const options_filter_oficio_relacionado = ref(options_oficio_relacionado.value)

const form = useForm({
    tipo_oficio: 'Recebido',
    dados_recebidos: {
        responsaveis: [],
        interessados: [],
        destinatario_id: '',
        numero_oficio: '',
        prazo: 0,
        dias_corridos: 'Não',
        observacao: '',
        anexos: '',
        tipo_documento: '',
        status_inicial: '',
        status_final: '',
        etapa: 'Ciente',
        assunto_oficio: '',
        data_recebimento: '',
        data_prazo: '',
    },
    dados_expedidos: {
        responsaveis: [],
        interessados: [],
        diretoria: [],
        destinatario_id: '',
        numero_oficio: '',
        prazo: 0,
        dias_corridos: 'Não',
        anexos: '',
        numero_notificacao: '',
        status_inicial: '',
        status_final: '',
        etapa: 'Responder',
        observacao: '',
        assunto_oficio: '',
        data_emissao: '',
        data_prazo: '',
    },
    oficio_relacionado: [],
    oficio_externo: [],
})
const tab = ref('oficio')

function storeData(){
    form.post(route('oficio.store'),{
        preserveScroll: true,
        onSuccess: (response) => {
            Notify.create({
                message: 'Ofício cadastrado com sucesso!',
                type: 'positive',
            });
        },
        onError: () => {
            Notify.create({
                message: 'Erro ao cadastrar ofício!',
                type: 'negative',
            });
        }
    });
}

function filterFn (val, update) {
    if (val === '') {
        update(() => {
            options_filter_destinatario.value = options_destinatario.value;
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
            options_filter_responsaveis.value = options_responsaveis.value;
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
            options_filter_interessados.value = options_interessados.value;
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
            options_filter_diretoria.value = options_diretoria.value;
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
            options_filter_oficio_relacionado.value = options_oficio_relacionado.value;
        })
        return
    }
    update(() => {
        const needle = val.toLowerCase()
        options_filter_oficio_relacionado.value = options_oficio_relacionado.value.filter(v => v.label.toLowerCase().indexOf(needle) > -1)
    })
}

</script>

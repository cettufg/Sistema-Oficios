<template>
    <Head title="Editar Ofício" />

    <AuthenticatedLayout>
        <div class="tw-py-12">
            <div class="md:tw-mx-20">
                <div class="tw-overflow-hidden tw-px-3">
                    <div class="tw-flex tw-items-center tw-mb-10">
                        <span class="tw-text-3xl tw-mr-7">Ofício</span>
                        <Link class="tw-flex tw-items-center tw-justify-center tw-mt-1" :href="route('oficio.index')">
                            <Icon icon="ic:baseline-keyboard-arrow-left" /> <span class="tw-ml-1">Voltar</span>
                        </Link>
                    </div>
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
                                    <label for="" class="tw-flex tw-items-center">Tipo de ofício <Icon class="tw-text-red-500 tw-text-xs" icon="mdi:required" /></label>
                                    <q-select v-model="form.tipo_oficio"
                                        outlined
                                        :options="options_tipo_oficio"
                                        :error-message="form.errors['dados_recebidos.tipo_oficio']" :error="!!form.errors['dados_recebidos.tipo_oficio']"
                                    />
                                </div>

                                <div v-if="form.tipo_oficio == 'Recebido'">
                                    <label for="" class="tw-flex tw-items-center">Tipo de documento <Icon class="tw-text-red-500 tw-text-xs" icon="mdi:required" /></label>
                                    <q-select v-model="form.dados_recebidos.tipo_documento"
                                        outlined
                                        :options="['Ofício', 'Notificação', 'Manifestação']"
                                        :error-message="form.errors['dados_recebidos.tipo_documento']" :error="!!form.errors['dados_recebidos.tipo_documento']"
                                    />
                                </div>
                            </div>
                            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4" v-if="form.tipo_oficio == 'Recebido'">
                                <div>
                                    <label for="" class="tw-flex tw-items-center">Número do ofício <Icon class="tw-text-red-500 tw-text-xs" icon="mdi:required" /></label>
                                    <q-input v-model="form.dados_recebidos.numero_oficio"
                                        counter
                                        outlined
                                        maxlength="255"
                                        :error-message="form.errors['dados_recebidos.numero_oficio']" :error="!!form.errors['dados_recebidos.numero_oficio']"
                                    />
                                </div>

                                <div>
                                    <label for="" class="tw-flex tw-items-center">Destinatário <Icon class="tw-text-red-500 tw-text-xs" icon="mdi:required" /></label>
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
                                    <label for="">Assunto do ofício recebido</label>
                                    <q-input v-model="form.dados_recebidos.assunto_oficio"
                                        counter
                                        outlined
                                        autogrow
                                        :error-message="form.errors['dados_recebidos.assunto_oficio']" :error="!!form.errors['dados_recebidos.assunto_oficio']"
                                    />
                                </div>

                                <div>
                                    <label for="" class="tw-flex tw-items-center">Dias corridos <Icon class="tw-text-red-500 tw-text-xs" icon="mdi:required" /></label>
                                    <q-select v-model="form.dados_recebidos.dias_corridos"
                                        outlined
                                        :options="['Sim', 'Não']"
                                        :error-message="form.errors['dados_recebidos.dias_corridos']" :error="!!form.errors['dados_recebidos.dias_corridos']"
                                    />
                                </div>

                                <div>
                                    <label for="" class="tw-flex tw-items-center">Data de recebimento <Icon class="tw-text-red-500 tw-text-xs" icon="mdi:required" /></label>
                                    <q-input v-model="form.dados_recebidos.data_recebimento"
                                        outlined
                                        type="date"
                                        :error-message="form.errors['dados_recebidos.data_recebimento']" :error="!!form.errors['dados_recebidos.data_recebimento']"
                                    />
                                </div>

                                <div>
                                    <label for="" class="tw-flex tw-items-center">Prazo final <Icon class="tw-text-red-500 tw-text-xs" icon="mdi:required" /></label>
                                    <q-input v-model="form.dados_recebidos.data_prazo"
                                        outlined
                                        type="date"
                                        :error-message="form.errors['dados_recebidos.data_prazo']" :error="!!form.errors['dados_recebidos.data_prazo']"
                                    />
                                </div>

                                <div>
                                    <label for="" class="tw-flex tw-items-center">Responsáveis <Icon class="tw-text-red-500 tw-text-xs" icon="mdi:required" /></label>
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
                                    <label for="" class="tw-flex tw-items-center">Interessados <Icon class="tw-text-red-500 tw-text-xs" icon="mdi:required" /></label>
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
                                    <label for="">Status inicial</label>
                                    <q-input v-model="form.dados_recebidos.status_inicial"
                                        outlined
                                        autogrow
                                        counter
                                        :error-message="form.errors['dados_recebidos.status_inicial']" :error="!!form.errors['dados_recebidos.status_inicial']"
                                    />
                                </div>

                                <div>
                                    <label for="">Status Final</label>
                                    <q-input v-model="form.dados_recebidos.status_final"
                                        outlined
                                        autogrow
                                        counter
                                        :error-message="form.errors['dados_recebidos.status_final']" :error="!!form.errors['dados_recebidos.status_final']"
                                    />
                                </div>

                                <div>
                                    <label for="">Anexos</label>
                                    <q-file
                                        v-model="form.dados_recebidos.anexos"
                                        multiple
                                        append
                                        outlined
                                        use-chips
                                    >
                                        <template v-slot:prepend>
                                            <q-icon name="attach_file" />
                                        </template>
                                    </q-file>

                                    <q-list bordered separator v-if="anexos_novos.length > 0">
                                        <q-item clickable clearable v-for="(anexo,index) in anexos_novos">
                                            <q-item-section>
                                                <div class="tw-flex tw-items-center tw-justify-between">
                                                    <a :href="route('index') + '/storage/' +  anexo.caminho" target="_blank">{{ anexo.nome }}</a>
                                                    <button @click="deleteArquivo(index, anexo.id)"><Icon icon="ic:baseline-clear" /></button>
                                                </div>
                                            </q-item-section>
                                        </q-item>
                                    </q-list>
                                </div>

                                <div>
                                    <label for="">Observação</label>
                                    <q-input v-model="form.dados_recebidos.observacao"
                                        outlined
                                        autogrow
                                        counter
                                        :error-message="form.errors['dados_recebidos.observacao']" :error="!!form.errors['dados_recebidos.observacao']"
                                    />
                                </div>

                                <div>
                                    <label for="" class="tw-flex tw-items-center">Etapa <Icon class="tw-text-red-500 tw-text-xs" icon="mdi:required" /></label>
                                    <q-select v-model="form.dados_recebidos.etapa"
                                        outlined
                                        :options="['Ciente', 'Responder', 'Atrasado', 'Em aberto', 'Finalizado']"
                                        :error-message="form.errors['dados_recebidos.etapa']" :error="!!form.errors['dados_recebidos.etapa']"
                                    />
                                </div>

                            </div>

                            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4" v-if="form.tipo_oficio == 'Expedido'">
                                <div>
                                    <label for="" class="tw-flex tw-items-center">Diretoria <Icon class="tw-text-red-500 tw-text-xs" icon="mdi:required" /></label>
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
                                    <label for="" class="tw-flex tw-items-center">Destinatário <Icon class="tw-text-red-500 tw-text-xs" icon="mdi:required" /></label>
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
                                    <label for="" class="tw-flex tw-items-center">Responsáveis <Icon class="tw-text-red-500 tw-text-xs" icon="mdi:required" /></label>
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
                                    <label for="" class="tw-flex tw-items-center">Interessados <Icon class="tw-text-red-500 tw-text-xs" icon="mdi:required" /></label>
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
                                    <label for="">Número do ofício</label>
                                    <q-input v-model="form.dados_expedidos.numero_oficio"
                                        counter
                                        outlined
                                        maxlength="255"
                                        :error-message="form.errors['dados_expedidos.numero_oficio']" :error="!!form.errors['dados_expedidos.numero_oficio']"
                                    />
                                </div>

                                <div>
                                    <label for="" class="tw-flex tw-items-center">Data de emissão <Icon class="tw-text-red-500 tw-text-xs" icon="mdi:required" /></label>
                                    <q-input v-model="form.dados_expedidos.data_emissao"
                                        outlined
                                        type="date"
                                        :error-message="form.errors['dados_expedidos.data_emissao']" :error="!!form.errors['dados_expedidos.data_emissao']"
                                    />
                                </div>

                                <div>
                                    <label for="" class="tw-flex tw-items-center">Prazo final <Icon class="tw-text-red-500 tw-text-xs" icon="mdi:required" /></label>
                                    <q-input v-model="form.dados_expedidos.data_prazo"
                                        outlined
                                        type="date"
                                        :error-message="form.errors['dados_expedidos.data_prazo']" :error="!!form.errors['dados_expedidos.data_prazo']"
                                    />
                                </div>

                                <div>
                                    <label for="" class="tw-flex tw-items-center">Dias corridos <Icon class="tw-text-red-500 tw-text-xs" icon="mdi:required" /></label>
                                    <q-select v-model="form.dados_expedidos.dias_corridos"
                                        outlined
                                        :options="['Sim', 'Não']"
                                    />
                                </div>

                                <div>
                                    <label for="">Status inicial</label>
                                    <q-input v-model="form.dados_expedidos.status_inicial"
                                        outlined
                                        autogrow
                                        counter
                                        :error-message="form.errors['dados_expedidos.status_inicial']" :error="!!form.errors['dados_expedidos.status_inicial']"
                                    />
                                </div>

                                <div>
                                    <label for="">Status Final</label>
                                    <q-input v-model="form.dados_expedidos.status_final"
                                        outlined
                                        autogrow
                                        counter
                                        :error-message="form.errors['dados_expedidos.status_final']" :error="!!form.errors['dados_expedidos.status_final']"
                                    />
                                </div>

                                <div>
                                    <label for="">Número da notificação</label>
                                    <q-input v-model="form.dados_expedidos.numero_notificacao"
                                        counter
                                        outlined
                                        maxlength="255"
                                        :error-message="form.errors['dados_expedidos.numero_notificacao']" :error="!!form.errors['dados_expedidos.numero_notificacao']"
                                    />
                                </div>

                                <div>
                                    <label for="">Assunto do ofício</label>
                                    <q-input v-model="form.dados_expedidos.assunto_oficio"
                                        outlined
                                        autogrow
                                        counter
                                        :error-message="form.errors['dados_expedidos.assunto_oficio']" :error="!!form.errors['dados_expedidos.assunto_oficio']"
                                    />
                                </div>

                                <div>
                                    <label for="">Anexos</label>
                                    <q-file
                                        v-model="form.dados_expedidos.anexos"
                                        multiple
                                        append
                                        outlined
                                        use-chips
                                    >
                                        <template v-slot:prepend>
                                            <q-icon name="attach_file" />
                                        </template>
                                    </q-file>

                                    <q-list bordered separator v-if="anexos_novos.length > 0">
                                        <q-item clickable clearable v-for="(anexo,index) in anexos_novos">
                                            <q-item-section>
                                                <div class="tw-flex tw-items-center tw-justify-between">
                                                    <a :href="route('index') + '/storage/' +  anexo.caminho" target="_blank">{{ anexo.nome }}</a>
                                                    <button @click="deleteArquivo(index, anexo.id)"><Icon icon="ic:baseline-clear" /></button>
                                                </div>
                                            </q-item-section>
                                        </q-item>
                                    </q-list>
                                </div>

                                <div>
                                    <label for="">Observação</label>
                                    <q-input v-model="form.dados_expedidos.observacao"
                                        outlined
                                        autogrow
                                        counter
                                        :error-message="form.errors['dados_expedidos.observacao']" :error="!!form.errors['dados_expedidos.observacao']"
                                    />
                                </div>

                                <div>
                                    <label for="" class="tw-flex tw-items-center">Etapa <Icon class="tw-text-red-500 tw-text-xs" icon="mdi:required" /></label>
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
                                    <label for="">Ofícios relacionados</label>
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
                                    <label for="">Ofícios externos</label>
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

                    <q-btn color="primary"
                        :disable="enviando"
                        text-color="white"
                        class="tw-mt-5 tw-w-40"
                        @click="save()"
                    >
                        <q-spinner
                            v-if="enviando"
                            color="white"
                            size="1em"
                        />
                        <span v-if="!enviando" class="tw-flex tw-items-center">Salvar <Icon class="tw-ml-1 tw-text-xl" icon="material-symbols:save-outline" /></span>
                    </q-btn>

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
    usuarios: {
        default: '',
        type: Object
    },
    destinatarios: {
        default: '',
        type: Object
    },
    diretorias: {
        default: '',
        type: Object
    },
    oficios: {
        default: '',
        type: Object
    },
    oficio: {
        default: '',
        type: Object
    },
    oficios_relacionados: {
        default: '',
        type: Object
    }
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
        options_destinatario.value.push({
            label: item.nome,
            id: item.id
        })
    });

    props.diretorias.map((item) => {
        props.oficio[0].diretorias.map((diretoria) => {
            if(diretoria.diretoria_id == item.id){
                form.dados_expedidos.diretoria.push({label: item.nome, id: item.id});
            }
        });

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
    form.id = props.oficio[0].id;
    form.tipo_oficio = props.oficio[0].tipo_oficio;
    if(form.tipo_oficio == 'Recebido'){
        form.dados_recebidos.destinatario_id = {label: props.oficio[0].destinatario.nome, id: props.oficio[0].destinatario.id};
        form.dados_recebidos.numero_oficio = props.oficio[0].numero_oficio;
        form.dados_recebidos.prazo = props.oficio[0].prazo;
        form.dados_recebidos.dias_corridos = props.oficio[0].dias_corridos == 0 ? 'Não' : 'Sim' ;
        form.dados_recebidos.observacao = props.oficio[0].observacao;
        form.dados_recebidos.tipo_documento = props.oficio[0].tipo_documento;
        form.dados_recebidos.status_inicial = props.oficio[0].status_inicial;
        form.dados_recebidos.status_final = props.oficio[0].status_final;
        form.dados_recebidos.data_prazo = props.oficio[0].data_prazo;
        form.dados_recebidos.etapa = props.oficio[0].etapa;
        form.dados_recebidos.assunto_oficio = props.oficio[0].assunto_oficio;
        form.dados_recebidos.data_recebimento = props.oficio[0].data_recebimento;

        props.oficio[0].responsaveis.map((item) => {
            form.dados_recebidos.responsaveis.push({label: getNameUser(item.user_id)[0].name, id: item.user_id});
        });

        props.oficio[0].interessados.map((item) => {
            form.dados_recebidos.interessados.push({label: getNameUser(item.user_id)[0].name, id: item.user_id});
        });
    }else{

        form.dados_expedidos.destinatario_id = {label: props.oficio[0].destinatario.nome, id: props.oficio[0].destinatario.id};
        form.dados_expedidos.numero_oficio = props.oficio[0].numero_oficio;
        form.dados_expedidos.prazo = props.oficio[0].prazo;
        form.dados_expedidos.dias_corridos = props.oficio[0].dias_corridos == 0 ? 'Não' : 'Sim' ;
        form.dados_expedidos.observacao = props.oficio[0].observacao;
        form.dados_expedidos.numero_notificacao = props.oficio[0].numero_notificacao;
        form.dados_expedidos.status_inicial = props.oficio[0].status_inicial;
        form.dados_expedidos.status_final = props.oficio[0].status_final;
        form.dados_expedidos.etapa = props.oficio[0].etapa;
        form.dados_expedidos.assunto_oficio = props.oficio[0].assunto_oficio;
        form.dados_expedidos.data_emissao = props.oficio[0].data_emissao;
        form.dados_expedidos.data_prazo = props.oficio[0].data_prazo;

        props.oficio[0].responsaveis.map((item) => {
            form.dados_expedidos.responsaveis.push({label: getNameUser(item.user_id)[0].name, id: item.user_id});
        });

        props.oficio[0].interessados.map((item) => {
            form.dados_expedidos.interessados.push({label: getNameUser(item.user_id)[0].name, id: item.user_id});
        });
    }


    //Precisa de atenção
    props.oficio[0].anexos.map((anexo) => {
        anexos_novos.value.push(anexo);
    });

    props.oficio[0].oficios_externos.map((item) => {
        form.oficio_externo.push({label: item.descricao, id: item.id});
    });

    props.oficios_relacionados.map((item) => {
        form.oficio_relacionado.push({label: getOficioDescription(item.oficio_filho)[0].tipo_oficio + ' - ' + getOficioDescription(item.oficio_filho)[0].numero_oficio, id: item.oficio_filho});
    });

});

const enviando = ref(false);
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
const anexos_novos = ref([]);

const form = useForm({
    id: '',
    tipo_oficio: '',
    dados_recebidos: {
        responsaveis: [],
        interessados: [],
        destinatario_id: '',
        numero_oficio: '',
        prazo: '',
        dias_corridos: '',
        observacao: '',
        anexos: [],
        tipo_documento: '',
        status_inicial: '',
        status_final: '',
        etapa: '',
        assunto_oficio: '',
        data_recebimento: '',
    },
    dados_expedidos: {
        responsaveis: [],
        interessados: [],
        diretoria: [],
        destinatario_id: '',
        numero_oficio: '',
        prazo: '',
        dias_corridos: '',
        anexos: [],
        numero_notificacao: '',
        status_inicial: '',
        status_final: '',
        etapa: '',
        observacao: '',
        assunto_oficio: '',
        data_emissao: '',
    },
    oficio_relacionado: [],
    oficio_externo: [],
})

const tab = ref('oficio')

function save(){
    enviando.value = true;
    form.post(route('oficio.update', form.id),{
        preserveScroll: true,
        onSuccess: (response) => {
            enviando.value = false;
        },
        onError: () => {
            enviando.value = false;
        },
        onFinish: () => '',
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
function getNameUser(id){
    if(id){
        return props.usuarios.filter((item) => {
            if(item.id == id){
                return item;
            }
        });
    }
}
function getOficioDescription(id){
    if(id){
        return props.oficios.filter((item) => {
            if(item.id == id){
                return item;
            }
        });
    }
}

async function deleteArquivo(index, id)
{
    await axios.delete(route('oficio.removeanexo', anexos_novos.value[index].id))
    .then((response) => {
        if(response.status == 200){
            anexos_novos.value.splice(index, 1);
        }
    });
}

</script>

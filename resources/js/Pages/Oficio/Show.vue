<template>
    <Head title="Detalhes do ofício" />

    <AuthenticatedLayout>
        <div class="tw-overflow-hidden tw-mx-10 tw-my-10">
            <div class="tw-flex tw-items-center tw-mb-10">
                <span class="tw-text-3xl tw-mr-7">Ofício</span>
                <Link class="tw-flex tw-items-center tw-justify-center tw-mt-1" :href="route('oficio.index')">
                    <Icon icon="ic:baseline-keyboard-arrow-left" /> <span class="tw-ml-1">Voltar</span>
                </Link>
            </div>

            <div class="tw-w-full md:tw-w-[80%] tw-border tw-border-gray-300 tw-p-6 tw-rounded-lg">
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4">
                    <div>
                        <span class="tw-font-bold">Tipo de Ofício</span>
                        <q-input
                            outlined
                            readonly
                            v-model="oficio.tipo_oficio"
                        />
                    </div>

                    <div v-if="oficio.tipo_oficio == 'Recebido'">
                        <span class="tw-font-bold">Tipo de documento</span>
                        <q-input
                            outlined
                            readonly
                            v-model="oficio.tipo_documento"
                        />
                    </div>

                    <div v-if="oficio.tipo_oficio == 'Expedido'">
                        <span class="tw-font-bold">Diretoria</span>
                        <q-select
                            outlined
                            readonly
                            v-model="optionsDiretoria"
                        />
                    </div>

                    <div>
                        <span class="tw-font-bold">Número do ofício</span>
                        <q-input
                            outlined
                            readonly
                            v-model="oficio.numero_oficio"
                        />
                    </div>

                    <div>
                        <span class="tw-font-bold">Destinatário</span>
                        <q-input
                            outlined
                            readonly
                            v-model="oficio.destinatario.nome"
                        />
                    </div>

                    <div v-if="oficio.tipo_oficio == 'Recebido'">
                        <span class="tw-font-bold">Assunto do ofício recebido</span>
                        <q-input
                            outlined
                            readonly
                            v-model="oficio.assunto_oficio"
                        />
                    </div>

                    <div>
                        <span class="tw-font-bold">Prazo</span>
                        <q-input
                            outlined
                            readonly
                            v-model="oficio.prazo"
                        />
                    </div>

                    <div>
                        <span class="tw-font-bold">Dias corridos?</span>
                        <q-input
                            outlined
                            readonly
                            v-model="oficio.dias_corridos"
                        />
                    </div>

                    <div v-if="oficio.tipo_oficio == 'Recebido'">
                        <span class="tw-font-bold">Data de recebimento</span>
                        <q-input
                            outlined
                            readonly
                            v-model="oficio.data_recebimento"
                        />
                    </div>

                    <div v-if="oficio.tipo_oficio == 'Expedido'">
                        <span class="tw-font-bold">Data de emissão</span>
                        <q-input
                            outlined
                            readonly
                            v-model="oficio.data_emissao"
                        />
                    </div>

                    <div>
                        <span class="tw-font-bold">Responsáveis</span>
                        <q-select
                            outlined
                            readonly
                            v-model="optionsResponsaveis"
                        />
                    </div>

                    <div>
                        <span class="tw-font-bold">Interessados</span>
                        <q-select
                            outlined
                            readonly
                            v-model="optionsInteressados"
                        />
                    </div>

                    <div>
                        <span class="tw-font-bold">Status inicial</span>
                        <q-input
                            outlined
                            readonly
                            autogrow
                            v-model="oficio.status_inicial"
                        />
                    </div>

                    <div>
                        <span class="tw-font-bold">Status final</span>
                        <q-input
                            outlined
                            readonly
                            autogrow
                            v-model="oficio.status_final"
                        />
                    </div>

                    <div>
                        <span class="tw-font-bold">Observação</span>
                        <q-input
                            outlined
                            readonly
                            autogrow
                            v-model="oficio.observacao"
                        />
                    </div>

                    <div>
                        <span class="tw-font-bold">Etapa</span>
                        <q-input
                            outlined
                            readonly
                            autogrow
                            v-model="oficio.etapa"
                        />
                    </div>

                </div>

                <div class="tw-w-full tw-mt-5 tw-flex tw-flex-col tw-gap-2" v-if="oficio.anexos.length > 0">
                    <span class="tw-text-2xl tw-font-bold">Anexos</span>
                    <q-list bordered separator>
                        <q-item v-for="(anexo, index) in oficio.anexos" :key="index" clickable v-ripple>
                            <div class="tw-w-full tw-flex tw-items-center tw-justify-between">
                                {{ anexo.nome }}
                                <a :href="url + '/storage/' + anexo.caminho" target="_blank" class="tw-text-blue-500 tw-text-sm">Visualizar</a>
                            </div>
                        </q-item>
                    </q-list>
                </div>

                <div class="tw-w-full tw-mt-5 tw-flex tw-flex-col tw-gap-4" v-if="oficio.oficios_relacionados.length > 0 || oficio.oficios_externos.length > 0">
                    <span class="tw-text-2xl tw-font-bold">Ofícios Relacionados</span>
                    <div class="tw-w-full tw-flex tw-items-center tw-justify-between">
                        <div class="tw-w-full" v-if="oficio.oficios_relacionados.length > 0">
                            <span class="tw-text-lg tw-font-bold">Internos</span>
                            <q-list bordered separator>
                                <q-item v-for="(oficio, index) in oficio.oficios_relacionados" :key="index" clickable v-ripple>
                                    <div class="tw-w-full tw-flex tw-items-center tw-justify-between">
                                        <div class="tw-w-full tw-flex tw-flex-col tw-items-start tw-justify-center">
                                            <div><span class="tw-font-bold">Tipo:</span> {{ oficio.oficio_filho.tipo_oficio }}</div>
                                            <div><span class="tw-font-bold">Número do ofício:</span> {{ oficio.oficio_filho.numero_oficio }}</div>
                                        </div>
                                        <a :href="url + '/oficio/detail/' + oficio.oficio_filho.id" target="_blank" class="tw-text-blue-500 tw-text-sm">Visualizar</a>
                                    </div>
                                </q-item>
                            </q-list>
                        </div>

                        <div class="tw-w-full" v-if="oficio.oficios_externos.length > 0">
                            <span class="tw-text-lg tw-font-bold">Externos</span>
                            <q-list bordered separator>
                                <q-item v-for="(oficio, index) in oficio.oficios_externos" :key="index" clickable v-ripple>
                                    <div class="tw-w-full tw-flex tw-flex-col tw-items-start tw-justify-center">
                                        <div><span class="tw-font-bold">Descrição:</span> {{ oficio.descricao }}</div>
                                    </div>
                                </q-item>
                            </q-list>
                        </div>
                    </div>
                </div>

                <div class="tw-w-full tw-mt-5 tw-flex tw-flex-col tw-gap-2">
                    <div class="tw-w-full tw-flex tw-items-center tw-justify-between">
                        <span class="tw-text-2xl tw-font-bold">Ciência</span>
                        <q-btn
                            color="info"
                            label="Dar ciência"
                            v-if="!ciente"
                            @click="darCiencia()"
                        />
                    </div>
                    <q-list bordered separator v-if="oficio.cientes.length > 0">
                        <q-item clickable v-ripple v-for="(ciencia, index) in oficio.cientes" :key="index">
                            <div class="tw-w-full tw-flex tw-items-center tw-justify-between">
                                <div class="tw-w-full tw-flex tw-flex-col tw-items-start tw-justify-center">
                                    <div><span class="tw-font-bold">Ciência:</span> {{ ciencia.user.name }}</div>
                                    <div><span class="tw-font-bold">Data da ciência:</span> {{ formatarDataBrasileira(new Date(ciencia.created_at)) }}</div>
                                </div>
                            </div>
                        </q-item>
                    </q-list>
                    <div v-else>
                        <span class="tw-text-lg">Ninguém deu ciência.</span>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { Icon } from '@iconify/vue';
import { Notify } from 'quasar';

const url = window.location.href.split('/oficio/detail')[0];
const props = defineProps({
    oficio:{
        default: '',
        type: Object
    }
})
const oficio = ref(props.oficio[0]);
const ciente = ref(false);
const page = usePage();

onMounted(() => {
    if(oficio.value.tipo_oficio == 'Expedido'){
        oficio.value.diretorias.forEach((diretoria) => {
            optionsDiretoria.value.push(diretoria.diretoria.nome)
        })

        oficio.value.data_emissao = oficio.value.data_emissao.replace(/(\d*)-(\d*)-(\d*).*/, '$3/$2/$1');
    }else{
        oficio.value.data_recebimento = oficio.value.data_recebimento.replace(/(\d*)-(\d*)-(\d*).*/, '$3/$2/$1');
    }

    oficio.value.responsaveis.forEach((responsavel) => {
        optionsResponsaveis.value.push(responsavel.user.name)
    })

    oficio.value.interessados.forEach((interessado) => {
        optionsInteressados.value.push(interessado.user.name)
    })

    oficio.value.dias_corridos = oficio.value.dias_corridos == 0 ? 'Não' : 'Sim';

    form.id = oficio.value.id;

    oficio.value.cientes.forEach((item) => {
        if(item.user_id == page.props.auth.user.id){
            ciente.value = true;
        }
    })
});

const form = useForm({
    id: ''
});
const optionsDiretoria = ref([]);
const optionsResponsaveis = ref([]);
const optionsInteressados = ref([]);

function darCiencia(){
    form.post(route('oficio.ciencia', form.id), {
        preserveScroll: true,
        onSuccess: (response) => {
            oficio.value = response.props.oficio[0];
            ciente.value = true;
            Notify.create({
                message: 'Ciência dada com sucesso!',
                type: 'positive',
            })
        }, onError: () => {
            Notify.create({
                message: 'Erro ao dar ciência!',
                type: 'negative',
            })
        }
    });
}

function formatarDataBrasileira(data) {
  var dia = data.getDate().toString().padStart(2, '0');
  var mes = (data.getMonth() + 1).toString().padStart(2, '0');
  var ano = data.getFullYear().toString();
  return dia + '/' + mes + '/' + ano;
}
</script>

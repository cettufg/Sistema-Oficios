<template>
    <Head title="Detalhes do ofício" />

    <AuthenticatedLayout>
        <div class="tw-overflow-hidden tw-mx-20 tw-my-10">
            <div class="tw-flex tw-items-center tw-mb-10">
                <span class="tw-text-3xl tw-mr-7">Ofício</span>
                <Link class="tw-flex tw-items-center tw-justify-center tw-mt-1" :href="route('oficio.index')">
                    <Icon icon="ic:baseline-keyboard-arrow-left" /> <span class="tw-ml-1">Voltar</span>
                </Link>
            </div>

            <div class="tw-w-full md:tw-w-[60%] tw-border tw-border-gray-300 tw-p-6 tw-rounded-lg">
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4">
                    <div>
                        <span class="tw-font-bold">Tipo de Ofício</span>
                        <div>{{ oficio.tipo_oficio }}</div>
                    </div>

                    <div v-if="oficio.tipo_oficio == 'Recebido'">
                        <span class="tw-font-bold">Tipo de documento</span>
                        <div>{{ oficio.tipo_documento }}</div>
                    </div>

                    <div v-if="oficio.tipo_oficio == 'Expedido'">
                        <span class="tw-font-bold">Diretoria</span>
                        <div v-for="diretoria in oficio.diretorias">{{ getDiretoria(diretoria.diretoria_id)[0].nome }}</div>
                    </div>

                    <div>
                        <span class="tw-font-bold">Número do ofício</span>
                        <div>{{ oficio.numero_oficio }}</div>
                    </div>

                    <div>
                        <span class="tw-font-bold">Destinatário</span>
                        <div>{{ oficio.destinatario.nome }}</div>
                    </div>

                    <div v-if="oficio.tipo_oficio == 'Recebido'">
                        <span class="tw-font-bold">Assunto do ofício recebido</span>
                        <div>{{ oficio.assunto_oficio }}</div>
                    </div>

                    <div>
                        <span class="tw-font-bold">Prazo</span>
                        <div>{{ oficio.prazo }}</div>
                    </div>

                    <div>
                        <span class="tw-font-bold">Dias corridos?</span>
                        <div>{{ oficio.dias_corridos == 0 ? 'Não' : 'Sim' }}</div>
                    </div>

                    <div v-if="oficio.tipo_oficio == 'Recebido'">
                        <span class="tw-font-bold">Data de recebimento</span>
                        <div>{{ oficio.data_recebimento.replace(/(\d*)-(\d*)-(\d*).*/, '$3/$2/$1') }}</div>
                    </div>

                    <div v-if="oficio.tipo_oficio == 'Expedido'">
                        <span class="tw-font-bold">Data de emissão</span>
                        <div>{{ oficio.data_emissao.replace(/(\d*)-(\d*)-(\d*).*/, '$3/$2/$1') }}</div>
                    </div>

                    <div>
                        <span class="tw-font-bold">Responsáveis</span>
                        <div v-for="responsavel in oficio.responsaveis">{{ getNameUser(responsavel.user_id)[0].name }}</div>
                    </div>

                    <div>
                        <span class="tw-font-bold">Interessados</span>
                        <div v-for="interessado in oficio.interessados">{{ getNameUser(interessado.user_id)[0].name }}</div>
                    </div>

                    <div>
                        <span class="tw-font-bold">Status inicial</span>
                        <div>{{ oficio.status_inicial }}</div>
                    </div>

                    <div>
                        <span class="tw-font-bold">Status final</span>
                        <div>{{ oficio.status_final }}</div>
                    </div>

                    <div>
                        <span class="tw-font-bold">Anexos</span>
                        <div v-for="anexo in oficio.anexos">{{ anexo.nome }}</div>
                    </div>

                    <div>
                        <span class="tw-font-bold">Observação</span>
                        <div>{{ oficio.observacao }}</div>
                    </div>

                    <div>
                        <span class="tw-font-bold">Etapa</span>
                        <div>{{ oficio.etapa }}</div>
                    </div>

                </div>

                <div class="tw-mt-8 tw-text-xl" v-if="props.oficios_relacionados.length > 0 || oficio.oficios_externos.length > 0">
                    Ofícios relacionados
                </div>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4 tw-mt-4" v-if="props.oficios_relacionados.length > 0 || oficio.oficios_externos.length > 0">
                    <div>
                        <span class="tw-font-bold">Ofícios relacionados</span>
                        <div v-for="relacionado in props.oficios_relacionados">{{ getOficioDescription(relacionado.oficio_filho)[0].tipo_oficio}} -  {{ getOficioDescription(relacionado.oficio_filho)[0].numero_oficio }}</div>
                    </div>

                    <div v-if="oficio.oficios_externos">
                        <span class="tw-font-bold">Ofícios externos</span>
                        <div v-for="externo in oficio.oficios_externos">{{ externo.descricao }}</div>
                    </div>
                </div>

                <div class="tw-mt-8 tw-text-xl" v-if="oficio.tipo_oficio == 'Recebido'">
                    Ciência
                </div>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4 tw-mt-4" v-if="oficio.tipo_oficio == 'Recebido'">
                    <div>
                        <button v-if="!ciente" class="tw-bg-green-500 tw-px-3 tw-py-2 tw-rounded-lg tw-text-white" @click="darCiencia()">Dar ciência</button>
                    </div>

                    <div>
                        <span class="tw-font-bold">Deram ciência</span>
                        <div v-for="ciente in cientes">{{ ciente.name }}</div>
                        <div v-if="oficio.cientes.length == 0">Niguém deu ciência ainda</div>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { Icon } from '@iconify/vue';

const props = defineProps({
    oficio:{
        default: '',
        type: Object
    },
    oficios_relacionados:{
        default: '',
        type: Object
    },
    usuarios:{
        default: '',
        type: Object
    },
    oficios:{
        default: '',
        type: Object
    },
    diretorias: {
        default: '',
        type: Object
    },
    usuario: {
        default: '',
        type: Object
    }
})

const ciente = ref(false);
const cientes = ref([]);

const oficio = ref(props.oficio[0])
onMounted(() => {
    geraCiente()
})

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
function geraCiente()
{
    cientes.value = [];
    oficio.value.cientes.map((item) => {
        if(item.user_id == props.usuario.id){
            ciente.value = true;
            cientes.value.push({name: 'Você deu ciência'})
        }else{
            cientes.value.push({name: getNameUser(item.user_id)[0].name })
        }
    })
}
function getDiretoria(id){
    return props.diretorias.filter((diretoria) => {
        if(diretoria.id == id){
            return true;
        }
    });
}
function darCiencia()
{
    axios.post(route('oficio.ciencia', oficio.value.id))
    .then((response) => {
        if(response.status == 200){
            ciente.value = true;
            oficio.value = response.data[0];
            geraCiente()
        }
    });
}
</script>

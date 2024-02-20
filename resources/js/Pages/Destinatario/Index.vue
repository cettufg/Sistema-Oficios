<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import {Head, useForm} from '@inertiajs/vue3';
    import {ref} from 'vue';
    import {Notify} from 'quasar';

    const props = defineProps({
        destinatarios: {
            default: () => [],
            type: Array
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
        {name: 'id', label: '#', align: 'left', field: 'id', sortable: true},
        {name: 'nome', label: 'Nome', align: 'left', field: 'nome', sortable: true},
        {name: 'status', label: 'Status', align: 'left', field: 'status', sortable: true},
        {name: 'actions', label: 'Ações', align: 'center', field: 'actions', sortable: true},
    ]

    const form = useForm({
        id: '',
        nome: '',
        status: {
            label: 'Ativo',
            value: 1
        },
        selected: [],
    })
    const openModal = ref(false);
    const openModalDelete = ref(false);
    const openModalDeleteAll = ref(false);
    const typeAction = ref('');
    const optionsStatus = [
        {
            label: 'Ativo',
            value: 1
        },
        {
            label: 'Inativo',
            value: 2
        }
    ]

    function openModalAction (type, data = []) {
        typeAction.value = type;
        form.reset();
        form.clearErrors();
        if (type == 1) {
            openModal.value = true;
        } else if (type == 2) {
            form.id = data.id;
            form.nome = data.nome;
            form.status = {
                label: data.status == 1 ? 'Ativo' : 'Inativo',
                value: data.status
            }
            openModal.value = true;
        } else if (type == 3) {
            form.id = data.id;
            openModalDelete.value = true;
        } else if (type == 4) {
            form.selected = selected.value;
            openModalDeleteAll.value = true;
        }
    }

    function storeData () {
        form.post(route('destinatario.store'), {
            preserveScroll: true,
            onSuccess: (response) => {
                openModal.value = false;
                form.reset();
                Notify.create({
                    message: 'Dados adicionados com sucesso!',
                    type: 'positive'
                });
            },
            onError: () => {
                Notify.create({
                    message: 'Erro ao adicionar dados!',
                    type: 'negative'
                });
            },
        });
    }

    function saveData () {
        form.put(route('destinatario.update', form.id), {
            preserveScroll: true,
            onSuccess: (response) => {
                openModal.value = false;
                form.reset();
                Notify.create({
                    message: 'Dados atualizados com sucesso!',
                    type: 'positive'
                });
            },
            onError: (error) => {
                Notify.create({
                    message: 'Erro ao atualizar dados!',
                    type: 'negative'
                });
            },
        });
    }

    function destroyData () {
        form.delete(route('destinatario.destroy', form.id), {
            preserveScroll: true,
            onSuccess: (response) => {
                openModalDelete.value = false;
                form.reset();
                Notify.create({
                    message: 'Dados excluídos com sucesso!',
                    type: 'positive'
                });
            },
            onError: (error) => {
                Notify.create({
                    message: 'Erro ao excluir dados!',
                    type: 'negative'
                });
            },
        });
    }

    function destroySelected () {
        form.post(route('destinatario.destroySelected'), {
            preserveScroll: true,
            onSuccess: (response) => {
                openModalDeleteAll.value = false;
                form.reset();
                Notify.create({
                    message: 'Dados excluídos com sucesso!',
                    type: 'positive'
                });
            },
            onError: (error) => {
                Notify.create({
                    message: 'Erro ao excluir dados!',
                    type: 'negative'
                });
            },
        })
    }
</script>

<template>
<Head title="Destinatários"/>

<AuthenticatedLayout>
    <div class="tw-py-12 md:tw-mx-20">
        <div class="tw-flex tw-justify-between tw-mb-10">
            <span class="tw-text-3xl">Destinatários</span>

            <PrimaryButton
                background="primary"
                class="tw-px-4 tw-py-3"
                icon="material-symbols:add-circle-outline"
                text="Adicionar"
                @click="openModalAction(1)"
            />
        </div>

        <q-table
            v-model:selected="selected"
            :columns="columns"
            :filter="filter"
            :pagination="initialPagination"
            :rows="props.destinatarios"
            no-data-label="Sem dados cadastrados"
            no-results-label="A sua pesquisa não retornou dados"
            selection="multiple"
        >

            <template v-slot:body-cell="props">
                <q-td :props="props">
                    <div v-if="props.col.name == 'status'">
                            <span :class="{
                                'tw-bg-positive': props.row.status == 1,
                                'tw-bg-negative': props.row.status == 2,
                            }" class="tw-text-white tw-rounded-2xl tw-p-2">
                                {{ props.row.status == 1 ? 'Ativo' : 'Inativo' }}
                            </span>
                    </div>
                    <div v-else-if="props.col.name == 'actions'">
                        <div class="tw-flex tw-items-center tw-justify-center tw-gap-2">
                            <PrimaryButton
                                background="info"
                                class="tw-p-2"
                                icon="tabler:edit"
                                @click="openModalAction(2, props.row)"
                            />
                            <PrimaryButton
                                v-if="props.row.user_created == $page.props.auth.user.id || $page.props.auth.user.is_admin == 1"
                                background="negative"
                                class="tw-p-2"
                                icon="uil:trash"
                                @click="openModalAction(3, props.row)"
                            />
                        </div>
                    </div>
                    <div v-else>
                        {{ props.value }}
                    </div>

                </q-td>
            </template>

            <template v-slot:top-left>
                <PrimaryButton
                    v-if="$page.props.auth.user.is_admin == 1"
                    :disabled="selected.length == 0"
                    background="negative"
                    class="tw-p-2"
                    icon="uil:trash"
                    rounded="full"
                    @click="openModalAction(4, selected)"
                />
            </template>

            <template v-slot:top-right>
                <q-input v-model="filter" dense outlined placeholder="Pesquisar" type="search">
                    <template v-slot:append>
                        <q-icon name="search"/>
                    </template>
                </q-input>
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

        <q-dialog v-model="openModal">
            <q-card style="width: 700px; max-width: 80vw;">
                <q-card-section>
                    <div v-if="typeAction == 1" class="tw-text-2xl">Adicionar destinatário</div>
                    <div v-if="typeAction == 2" class="tw-text-2xl">Editar destinatário</div>
                </q-card-section>

                <q-card-section>
                    <div v-if="!form.processing" class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4">
                        <div>
                            <InputLabel required value="Destinatário"/>
                            <q-input
                                v-model="form.nome"
                                :error="!!form.errors.nome"
                                :error-message="form.errors.nome"
                                clearable
                                outlined
                            />
                        </div>

                        <div>
                            <InputLabel required value="Status"/>
                            <q-select
                                v-model="form.status"
                                :error="!!form.errors.status"
                                :error-message="form.errors.status"
                                :options="optionsStatus"
                                outlined
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
                        v-if="typeAction == 1"
                        :disabled="form.processing"
                        background="positive"
                        class="tw-px-3 tw-py-2"
                        icon="material-symbols:add-circle-outline"
                        text="Cadastrar"
                        @click="storeData()"
                    />
                    <PrimaryButton
                        v-if="typeAction == 2"
                        :disabled="form.processing"
                        background="positive"
                        class="tw-px-3 tw-py-2"
                        icon="ic:round-save"
                        text="Salvar"
                        @click="saveData()"
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
                        @click="destroySelected()"
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

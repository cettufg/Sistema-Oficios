<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import {Head, useForm} from '@inertiajs/vue3';
    import {ref} from 'vue';
    import {Notify} from 'quasar';

    const props = defineProps({
        usuarios: {
            type: Array,
            default: () => []
        }
    })
    const filter = ref('')
    const initialPagination = ref({
        sortBy: 'desc',
        descending: false,
        page: 1,
        rowsPerPage: 10
    })

    const columns = [
        {name: 'id', label: '#', align: 'left', field: 'id', sortable: true},
        {name: 'name', label: 'Nome', align: 'left', field: 'name', sortable: true},
        {name: 'email', label: 'E-mail', align: 'left', field: 'email', sortable: true},
        {name: 'status', label: 'Status', align: 'left', field: 'status', sortable: true},
        {name: 'actions', label: 'Ações', align: 'center', field: 'actions', sortable: true},
    ];
    const openModal = ref(false);
    const openModalDelete = ref(false);
    const optionsStatus = [
        {
            label: 'Ativo',
            value: 1
        },
        {
            label: 'Inativo',
            value: 2
        },
        {
            label: 'Lixeira',
            value: 0
        }
    ]

    const form = useForm({
        id: '',
        name: '',
        email: '',
        status: '',
        is_admin: '',
        password_reset: ''
    });

    function openModalAction (type, data = []) {
        form.reset();
        if (type == 1) {
            form.id = data.id;
            form.name = data.name;
            form.email = data.email;
            form.status = {
                label: data.status == 1 ? 'Ativo' : data.status == 2 ? 'Inativo' : 'Lixeira',
                value: data.status
            };
            form.is_admin = {
                label: data.is_admin == 1 ? 'Sim' : 'Não',
                value: data.is_admin
            };
            form.password_reset = {
                label: data.password_reset == 1 ? 'Sim' : 'Não',
                value: data.password_reset
            };
            openModal.value = true;
        } else {
            openModalDelete.value = true;
            form.id = data.id;
        }
    }

    function saveData () {
        form.put(route('usuario.update', form.id), {
            preserveScroll: true,
            onSuccess: () => {
                openModal.value = false;
                form.reset();
                Notify.create({
                    message: 'Dados atualizados com sucesso!',
                    type: 'positive',
                })
            },
            onError: () => {
                Notify.create({
                    message: 'Erro ao atualizar dados!',
                    type: 'negative',
                })
            }
        })
    }

    function destroyData () {
        form.delete(route('usuario.destroy', form.id), {
            preserveScroll: true,
            onSuccess: () => {
                openModalDelete.value = false;
                form.reset();
                Notify.create({
                    message: 'Dados excluídos com sucesso!',
                    type: 'positive',
                })
            },
            onError: () => {
                Notify.create({
                    message: 'Erro ao excluir dados!',
                    type: 'negative',
                })
            }
        })
    }
</script>

<template>
<Head title="Usuários"/>

<AuthenticatedLayout>
    <div class="tw-py-12 md:tw-mx-20">
        <div class="tw-flex tw-justify-between tw-mb-10">
            <span class="tw-text-3xl">Usuários</span>
        </div>

        <q-table
            :columns="columns"
            :filter="filter"
            :pagination="initialPagination"
            :rows="props.usuarios"
            no-data-label="Sem dados cadastrados"
            no-results-label="A sua pesquisa não retornou dados"
        >

            <template v-slot:body-cell="props">
                <q-td :props="props">
                    <div v-if="props.col.name === 'status'">
                            <span :class="{
                                'tw-bg-positive': props.row.status == 1,
                                'tw-bg-negative': props.row.status == 2,
                                'tw-bg-gray-500': props.row.status == 0,
                            }" class="tw-text-white tw-rounded-2xl tw-p-2">
                                {{ props.row.status == 1 ? 'Ativo' : props.row.status == 2 ? 'Inativo' : 'Lixeira' }}
                            </span>
                    </div>
                    <div v-else-if="props.col.name === 'actions'">
                        <div class="tw-flex tw-items-center tw-justify-center tw-gap-2">
                            <PrimaryButton
                                background="info"
                                class="tw-p-2"
                                icon="tabler:edit"
                                @click="openModalAction(1, props.row)"
                            />
                            <PrimaryButton
                                v-if="$page.props.auth.user.is_admin === 1"
                                background="negative"
                                class="tw-p-2"
                                icon="uil:trash"
                                @click="openModalAction(2, props.row)"
                            />
                        </div>
                    </div>
                    <div v-else>
                        {{ props.value }}
                    </div>

                </q-td>
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
                    <div class="tw-text-2xl">Editar usuário</div>
                </q-card-section>

                <q-card-section>
                    <div v-if="!form.processing" class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4">
                        <div>
                            <InputLabel required value="Nome"/>
                            <q-input
                                v-model="form.name"
                                clearable
                                outlined
                            />
                        </div>

                        <div>
                            <InputLabel required value="E-mail"/>
                            <q-input
                                v-model="form.email"
                                clearable
                                outlined
                            />
                        </div>

                        <div v-if="$page.props.auth.user.is_admin">
                            <InputLabel value="Permissão de admin"/>
                            <q-select
                                v-model="form.is_admin"
                                :options="[
                                        { label: 'Sim', value: 1 },
                                        { label: 'Não', value: 0 },
                                    ]"
                                outlined
                            />
                        </div>

                        <div>
                            <InputLabel required value="Resetar senha"/>
                            <q-select
                                v-model="form.password_reset"
                                :options="[
                                        { label: 'Sim', value: 1 },
                                        { label: 'Não', value: 0 },
                                    ]"
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
    </div>
</AuthenticatedLayout>
</template>

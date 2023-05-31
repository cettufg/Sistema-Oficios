<template>
    <div class="tw-min-h-screen tw-flex tw-flex-col tw-justify-between">
        <div class="tw-text-primary">
            <div class="tw-flex tw-justify-between tw-py-10 tw-px-20 tw-border-2">
                <div>
                    <Link :href="route('index')">
                        <ApplicationLogo class="tw-border-2 tw-w-48" />
                    </Link>
                </div>
                <div class="tw-text-lg tw-flex tw-items-center">
                    <div class="tw-hidden md:tw-block">
                        <div v-if="!$page.props.auth.user">
                            <Link :href="route('login')" class="tw-mr-5 tw-bg-primary tw-text-white tw-px-3 tw-py-1 tw-text-md tw-rounded-lg">Entrar</Link>
                        </div>
                        <div v-if="$page.props.auth.user">
                            <Dropdown class="tw-mr-5" width="48">
                                <template #trigger>
                                    <span class="tw-inline-flex tw-rounded-md">
                                        <button
                                            type="button"
                                            class="tw-inline-flex tw-items-center tw-px-3 tw-py-2 tw-border tw-border-transparent tw-leading-4 tw-font-medium tw-rounded-md tw-bg-transparent focus:tw-outline-none tw-transition tw-ease-in-out tw-duration-150"
                                        >
                                            {{ $page.props.auth.user.name }}

                                            <svg
                                                class="tw-ml-2 -tw-mr-0.5 tw-h-4 tw-w-4"
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                        </button>
                                    </span>
                                </template>

                                <template #content>
                                    <DropdownLink :href="route('profile.edit')">
                                        Perfil
                                    </DropdownLink>
                                    <DropdownLink :href="route('logout')" method="post" as="button">
                                        Sair
                                    </DropdownLink>
                                </template>
                            </Dropdown>
                        </div>
                    </div>
                    <button class="tw-block md:tw-hidden tw-mr-5" @click="activeMenu = !activeMenu">
                        <Icon icon="lucide:menu" class="tw-text-3xl" />
                    </button>
                    <img src="@/images/logo_ufg.png" alt="Logo UFG" class="logoufg">
                </div>
            </div>

            <div class="tw-hidden md:tw-flex tw-py-2 tw-px-20 tw-bg-secondary">
                <NavLink v-for="item in itemsMenu" :href="item.href"
                    :submenu="item.submenu"
                    :name="item.name"
                    :validate="menuValidate(item, $page.props.auth.user)"
                    />
            </div>

            <div class="tw-flex md:tw-hidden tw-justify-between tw-w-full">
                <div v-if="activeMenu" class="tw-w-full tw-border tw-border-b-1">
                    <q-list separator v-if="$page.props.auth.user">
                        <q-item class="tw-pl-5 tw-py-3">
                            <q-item-section class="tw-text-gray-600">
                                <q-item-label>Nome: {{ $page.props.auth.user.name }}</q-item-label>
                                <q-item-label>E-mail: {{ $page.props.auth.user.email }}</q-item-label>
                            </q-item-section>
                        </q-item>

                        <q-item clickable v-ripple class="tw-pl-5">
                            <q-item-section>
                                <Link :href="route('oficio.index')">Ofícios</Link>
                            </q-item-section>
                        </q-item>

                        <q-item clickable v-ripple class="tw-pl-5">
                            <q-item-section>
                                <Link :href="route('destinatario.index')">Destinatários</Link>
                            </q-item-section>
                        </q-item>

                        <q-item clickable v-ripple class="tw-pl-5">
                            <q-item-section>
                                <Link :href="route('diretoria.index')">Diretorias</Link>
                            </q-item-section>
                        </q-item>

                        <q-item clickable v-ripple class="tw-pl-5">
                            <q-item-section>
                                <Link :href="route('usuario.index')">Usuários</Link>
                            </q-item-section>
                        </q-item>

                        <q-item clickable v-ripple class="tw-pl-5">
                            <q-item-section>
                                <a href="https://chat.whatsapp.com/LqtmsTWU72JHXT5FTt5wLb" target="_blank">Suporte</a>
                            </q-item-section>
                        </q-item>

                        <q-item clickable v-ripple class="tw-pl-5">
                            <q-item-section>
                                <Link :href="route('profile.edit')">Perfil</Link>
                            </q-item-section>
                        </q-item>

                        <q-item clickable v-ripple class="tw-pl-5">
                            <q-item-section>
                                <Link class="tw-text-left" :href="route('logout')" method="post" as="button">Sair</Link>
                            </q-item-section>
                        </q-item>

                    </q-list>

                    <q-list separator v-if="!$page.props.auth.user">
                        <q-item clickable v-ripple class="tw-pl-5">
                            <q-item-section>
                                <Link :href="route('login')">Entrar</Link>
                            </q-item-section>
                        </q-item>

                        <q-item clickable v-ripple class="tw-pl-5">
                            <q-item-section>
                                <a href="https://chat.whatsapp.com/LqtmsTWU72JHXT5FTt5wLb" target="_blank">Suporte</a>
                            </q-item-section>
                        </q-item>

                    </q-list>
                </div>
            </div>
        </div>

        <div class="">
            <slot />
        </div>

        <div class="tw-bg-primary tw-flex tw-justify-between tw-items-center tw-text-white tw-px-20 tw-py-10">
            <div class="tw-text-md">
                <p>
                    © Centro de Educação, Trabalho e Tecnologia - CETT. Todos os Direitos Reservados
                </p>
                <p>
                    Suporte: (62) 99185-3242 (COTEC) / (62) 99679-9525 (EFG)
                </p>
            </div>

            <div>
                <img src="@/images/logo_cett_ufg.png" alt="Logo CETT UFG" class="logocettufg">
            </div>
        </div>

    </div>
</template>

<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';
import { Icon } from '@iconify/vue';


function menuValidate(item, user){
    if(item.validate == 'register'){
        if(item.name == 'Configurações' && user){
            if(user.is_admin == true){
                return true;
            }else{
                return false;
            }

        }else{
            if(!!user){
                return true;
            }else{
                return false;
            }
        }
    }else{
        return true;
    }
}

const itemsMenu = [
    {
        name: 'Oficios',
        href: 'oficio.index',
        validate: 'register'
    },
    {
        name: 'Configurações',
        href: '',
        validate: 'register',
        submenu: [
            {
                name: 'Destinatários',
                href: 'destinatario.index',
            },
            {
                name: 'Diretorias',
                href: 'diretoria.index',
            },
            {
                name: 'Usuários',
                href: 'usuario.index',
            }
        ]
    },
    {
        name: 'Suporte',
        href: 'https://chat.whatsapp.com/LqtmsTWU72JHXT5FTt5wLb',
        validate: 'nullable'
    }
]
const activeMenu = ref(false);

</script>

<style scoped>
.logotipo{
    width: 170px;
}
.logoufg{
    width: 100px;
}
.logocettufg{
    width: 200px;
}
</style>

<style>
[type='text']:focus, [type='email']:focus, [type='url']:focus, [type='password']:focus,
[type='number']:focus, [type='date']:focus, [type='datetime-local']:focus, [type='month']:focus,
[type='search']:focus, [type='tel']:focus, [type='time']:focus, [type='week']:focus, [multiple]:focus,
textarea:focus, select:focus{
    --tw-ring-shadow: 0;
}
</style>

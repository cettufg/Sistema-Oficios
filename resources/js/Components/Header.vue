<template>
    <div class="tw-text-primary">
        <div class="tw-flex tw-justify-between tw-py-10 tw-px-20 tw-border-2">
            <Link :href="route('index')">
                <ApplicationLogo class="tw-w-48" />
            </Link>
            <div class="tw-text-lg tw-flex tw-items-center tw-gap-5">
                <div class="tw-hidden md:tw-block">
                    <div v-if="!$page.props.auth.user">
                        <PrimaryLink
                            :href="route('login')"
                            background="primary"
                            class="tw-px-4 tw-py-2"
                            text="Entrar"
                            icon="majesticons:login-line"
                        />
                    </div>
                    <div v-if="$page.props.auth.user">
                        <Dropdown class="tw-mr-5" width="48">
                            <template #trigger>
                                <span class="tw-inline-flex tw-rounded-md">
                                    <button type="button" class="tw-flex tw-items-center tw-gap-2 focus:tw-outline-none tw-transition tw-ease-in-out tw-duration-150">
                                        <Icon class="tw-text-2xl" icon="iconoir:verified-user" />
                                        <span>{{ $page.props.auth.user.name }}</span>
                                        <Icon class="tw-text-xl" icon="ep:arrow-down-bold" />
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
                <img src="@/images/logo_ufg.png" alt="Logo UFG" class="tw-w-32">
            </div>
        </div>

        <div class="tw-hidden md:tw-flex tw-py-2 tw-px-20 tw-bg-secondary">
            <NavLink v-for="(item, index) in itemsMenu"
                :key="index"
                :href="item.href"
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
</template>

<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import PrimaryLink from '@/Components/PrimaryLink.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';
import { Icon } from '@iconify/vue';
import { ref } from 'vue';


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

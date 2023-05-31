<script setup>
import { computed, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

const props = defineProps(['href', 'submenu', 'name', 'validate']);
const target = ref(false);

function link(){
    if(props.href){
        if(props.href.includes('http')){
            target.value = true;
            return props.href;
        }else{
            return route(props.href);
        }
    }
}

function active(){
    if(props.href){
        return route().current(props.href);
    }else{
        return false;
    }
}

function classes(){
    if(active()){
        return 'tw-inline-flex tw-text-[1.1rem] tw-items-center tw-border-b-2 tw-border-indigo-400 tw-font-medium tw-text-gray-900 focus:tw-outline-none focus:tw-border-indigo-700 tw-transition tw-duration-150 tw-ease-in-out'
    }else{
        return 'tw-inline-flex tw-text-[1.1rem] tw-items-center tw-border-b-2 tw-border-transparent tw-font-medium tw-text-gray-900 focus:tw-outline-none focus:tw-border-gray-300 tw-transition tw-duration-150 tw-ease-in-out hover:tw-text-gray-700 hover:tw-border-gray-300 focus:tw-text-gray-700'
    }
}

const submenu = props.submenu ? props.submenu.length : 0;

</script>

<template>
    <div class="tw-mr-5" v-if="props.validate">
        <a :href="link()" :class="classes()" target="_blank" v-if="target">
            {{ props.name }}
        </a>
        <Link :href="link()" :class="classes()" :target="target" v-if="submenu == 0 && !target">
            {{ props.name }}
        </Link>

        <Dropdown align="left" v-if="submenu > 0">
            <template #trigger>
                <span class="tw-inline-flex">
                    <button type="button" :class="classes()">
                        {{ props.name }}

                        <svg
                            class="tw-ml-1 -tw-mr-0.5 tw-h-4 tw-w-4"
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
                <div class="tw-flex tw-flex-col">
                    <Link v-for="item in props.submenu" :href="route(item.href)" :class="route().current(props.href) ? 'tw-w-full tw-py-2 tw-text-white tw-bg-[#0d39ba] hover:tw-bg-[#07102a]' : 'tw-w-full tw-py-2 tw-text-white tw-bg-[#07102a] hover:tw-bg-[#0d39ba]'">
                        <span class="tw-px-2">{{ item.name }}</span>
                    </Link>
                </div>
            </template>
        </Dropdown>
    </div>
</template>

<script setup>
    import {Icon} from '@iconify/vue';
    import {computed} from 'vue';

    const props = defineProps({
        type: {
            type: String,
            default: 'submit',
        },
        background: {
            type: String,
            default: 'gray',
        },
        rounded: {
            type: String,
            default: 'md',
        },
        outlined: {
            type: Boolean,
            default: false,
        },
        icon: {
            type: String,
            default: '',
        },
        text: {
            type: String,
            default: '',
        },
        fontSize: {
            type: String,
            default: 'sm',
        },
        iconSize: {
            type: String,
            default: 'lg',
        }
    });
    const rounded = computed(() => {
        if (props.rounded === 'sm') {
            return 'tw-rounded-sm';
        } else if (props.rounded === 'md') {
            return 'tw-rounded-md';
        } else if (props.rounded === 'lg') {
            return 'tw-rounded-lg';
        } else if (props.rounded === 'xl') {
            return 'tw-rounded-xl';
        } else if (props.rounded === '2xl') {
            return 'tw-rounded-2xl';
        } else if (props.rounded === '3xl') {
            return 'tw-rounded-3xl';
        } else if (props.rounded === 'full') {
            return 'tw-rounded-full';
        }
    });
    const fontSize = computed(() => {
        if (props.fontSize === 'xs') {
            return 'tw-text-xs';
        } else if (props.fontSize === 'sm') {
            return 'tw-text-sm';
        } else if (props.fontSize === 'base') {
            return 'tw-text-base';
        } else if (props.fontSize === 'lg') {
            return 'tw-text-lg';
        } else if (props.fontSize === 'xl') {
            return 'tw-text-xl';
        } else if (props.fontSize === '2xl') {
            return 'tw-text-2xl';
        } else if (props.fontSize === '3xl') {
            return 'tw-text-3xl';
        } else if (props.fontSize === '4xl') {
            return 'tw-text-4xl';
        } else if (props.fontSize === '5xl') {
            return 'tw-text-5xl';
        } else if (props.fontSize === '6xl') {
            return 'tw-text-6xl';
        } else if (props.fontSize === '7xl') {
            return 'tw-text-7xl';
        } else if (props.fontSize === '8xl') {
            return 'tw-text-8xl';
        } else if (props.fontSize === '9xl') {
            return 'tw-text-9xl';
        }
    });
    const iconSize = computed(() => {
        if (props.iconSize === 'xs') {
            return 'tw-text-xs';
        } else if (props.iconSize === 'sm') {
            return 'tw-text-sm';
        } else if (props.iconSize === 'base') {
            return 'tw-text-base';
        } else if (props.iconSize === 'lg') {
            return 'tw-text-lg';
        } else if (props.iconSize === 'xl') {
            return 'tw-text-xl';
        } else if (props.iconSize === '2xl') {
            return 'tw-text-2xl';
        } else if (props.iconSize === '3xl') {
            return 'tw-text-3xl';
        } else if (props.iconSize === '4xl') {
            return 'tw-text-4xl';
        } else if (props.iconSize === '5xl') {
            return 'tw-text-5xl';
        } else if (props.iconSize === '6xl') {
            return 'tw-text-6xl';
        } else if (props.iconSize === '7xl') {
            return 'tw-text-7xl';
        } else if (props.iconSize === '8xl') {
            return 'tw-text-8xl';
        } else if (props.iconSize === '9xl') {
            return 'tw-text-9xl';
        }
    });
    const background = computed(() => {
        if (props.background === 'none' && props.outlined === false) {
            return 'tw-text-gray-500 hover:tw-text-gray-700 focus:tw-text-gray-700 active:tw-text-gray-700';
        } else if (props.background === 'none' && props.outlined === true) {
            return 'tw-text-gray-500 hover:tw-text-gray-700 focus:tw-text-gray-700 active:tw-text-gray-700 tw-border tw-border-gray-300 hover:tw-bg-gray-50 focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-offset-2 focus:tw-ring-indigo-500';
        } else if (props.background === 'positive' && props.outlined === false) {
            return 'tw-text-white tw-bg-positive hover:tw-bg-positive_hover focus:tw-bg-positive_hover active:tw-bg-positive_hover';
        } else if (props.background === 'positive' && props.outlined === true) {
            return 'tw-text-positive tw-border tw-border-positive hover:tw-bg-positive_hover focus:tw-bg-positive_hover active:tw-bg-positive_hover hover:tw-text-positive focus:tw-text-white active:tw-text-white';
        } else if (props.background === 'negative' && props.outlined === false) {
            return 'tw-text-white tw-bg-negative hover:tw-bg-negative_hover focus:tw-bg-negative_hover active:tw-bg-negative_hover';
        } else if (props.background === 'negative' && props.outlined === true) {
            return 'tw-text-negative tw-border tw-border-negative hover:tw-bg-negative_hover focus:tw-bg-negative_hover active:tw-bg-negative_hover hover:tw-text-negative focus:tw-text-white active:tw-text-white';
        } else if (props.background === 'info' && props.outlined === false) {
            return 'tw-text-white tw-bg-info hover:tw-bg-info_hover focus:tw-bg-info_hover active:tw-bg-info_hover';
        } else if (props.background === 'info' && props.outlined === true) {
            return 'tw-text-info tw-border tw-border-info hover:tw-bg-info_hover focus:tw-bg-info_hover active:tw-bg-info_hover hover:tw-text-white focus:tw-text-info active:tw-text-white';
        } else if (props.background === 'warning' && props.outlined === false) {
            return 'tw-text-white tw-bg-warning hover:tw-bg-warning_hover focus:tw-bg-warning_hover active:tw-bg-warning_hover';
        } else if (props.background === 'warning' && props.outlined === true) {
            return 'tw-text-warning tw-border tw-border-warning hover:tw-bg-warning_hover focus:tw-bg-warning_hover active:tw-bg-warning_hover hover:tw-text-warning focus:tw-text-white active:tw-text-white';
        } else if (props.background === 'primary' && props.outlined === false) {
            return 'tw-text-white tw-bg-primary hover:tw-bg-primary_hover focus:tw-bg-primary_hover active:tw-bg-primary_hover';
        } else if (props.background === 'primary' && props.outlined === true) {
            return 'tw-text-primary tw-border tw-border-primary hover:tw-bg-primary_hover focus:tw-bg-primary_hover active:tw-bg-primary_hover hover:tw-text-primary focus:tw-text-white active:tw-text-white';
        } else if (props.background === 'secondary' && props.outlined === false) {
            return 'tw-text-white tw-bg-secondary hover:tw-bg-secondary_hover focus:tw-bg-secondary_hover active:tw-bg-secondary_hover';
        } else if (props.background === 'secondary' && props.outlined === true) {
            return 'tw-text-secondary tw-border tw-border-secondary hover:tw-bg-secondary_hover focus:tw-bg-secondary_hover active:tw-bg-secondary_hover hover:tw-text-secondary focus:tw-text-white active:tw-text-white';
        }
    });
</script>

<template>
<button
    :class="background + ' ' + rounded"
    :type="props.type"
    class="tw-flex tw-gap-2 tw-items-center tw-font-semibold tw-transition tw-ease-in-out tw-duration-150"
>
    <span v-if="props.text" :class="fontSize">{{ props.text }}</span>
    <Icon v-if="props.icon" :class="iconSize" :icon="props.icon"/>
    <slot/>
</button>
</template>

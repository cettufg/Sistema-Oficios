<template>
    <div class="tw-min-h-screen tw-flex tw-flex-col tw-justify-between">
        <div>
            <Header />

            <slot />
        </div>

        <Footer />
    </div>
</template>

<script setup>
import Header from '@/Components/Header.vue';
import Footer from '@/Components/Footer.vue';

</script>

<style>
[type='text']:focus, [type='email']:focus, [type='url']:focus, [type='password']:focus,
[type='number']:focus, [type='date']:focus, [type='datetime-local']:focus, [type='month']:focus,
[type='search']:focus, [type='tel']:focus, [type='time']:focus, [type='week']:focus, [multiple]:focus,
textarea:focus, select:focus{
    --tw-ring-shadow: 0;
}
</style>

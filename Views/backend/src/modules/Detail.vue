<template>
    <div class="detail">
        <div v-if="showLogo" class="centered-logo">
            <img src="@/assets/logo.svg" alt="">
        </div>
        <div v-else-if="showLoader" class="loader">
            <fa icon="spinner" spin></fa>
        </div>
        <slot v-else></slot>
    </div>
</template>

<script>
export default {
    props: {
        disabled: Boolean,
        loading: Boolean
    },
    computed: {
        showLogo () {
            let me = this
            
            return me.disabled && !me.loading
        },
        showLoader () {
            let me = this
            
            return me.loading
        }
    }
}
</script>
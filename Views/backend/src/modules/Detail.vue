<template>
    <div class="detail">
        <div v-if="disabled" class="centered-logo">
            <img src="@/assets/logo.svg" alt="">
        </div>
        <slot v-else></slot>
    </div>
</template>

<script>
export default {
    props: {
        disabled: Boolean,
    }
}
</script>
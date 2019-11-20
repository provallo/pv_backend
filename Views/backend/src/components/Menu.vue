<template>
    <ul v-if="items.length > 0">
        <li v-for="item in items">
            
            <router-link v-if="item.route" :to="item.route">
                {{ item.label }}
                
                <template v-if="item.children && item.children.length > 0">
                    <fa icon="chevron-up" class="menu-toggle closed"></fa>
                    <fa icon="chevron-down" class="menu-toggle opened"></fa>
                </template>
            </router-link>
            <a href="#" v-else @click.prevent="item.click">
                {{ item.label }}
            </a>
            
            <template v-if="item.children && item.children.length > 0">
                <v-menu :items="item.children"></v-menu>
            </template>
        </li>
    </ul>
</template>

<script>
export default {
    name: 'v-menu',
    props: [
        'items'
    ]
}
</script>
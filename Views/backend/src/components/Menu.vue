<template>
    <ul v-if="items.length > 0">
        <li v-for="item in items">
            
            <router-link v-if="item.route" :to="item.route">{{ item.name }}</router-link>
            <a href="#" v-else @click.prevent="item.click">{{ item.name }}</a>
            
            <!-- <fa icon="chevron-up" class="menu-toggle" v-if="item.items"></fa>
            <v-menu v-if="item.items" :items="item.items"></v-menu> -->
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
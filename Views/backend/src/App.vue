<template>
    <div id="app">
        <!-- Define different layouts -->
        <div v-if="isReady && $route.name === 'login'" class="layout-simple">
            <router-view></router-view>
        </div>
        <div v-else-if="isReady && $route.name !== 'login'" class="layout-sidebar">
            <v-sidebar></v-sidebar>
            <router-view></router-view>
        </div>
        <div v-else class="layout-simple">
            <v-loading></v-loading>
        </div>
    </div>
</template>

<script>
import VLoading from '@/views/Loading'

export default {
    name: 'App',
    data: () => ({
        isReady: false
    }),
    computed: {
        isLoggedIn () {
            return this.$store.state.currentUser !== null
        }
    },
    mounted () {
        let me = this
        
        me.$http.get('backend/user/status').then(({ data }) => {
            me.isReady = true
            
            if (data.success === true) {
                me.$store.state.currentUser = {
                    username: data.username
                }
            } else {
                me.$store.state.currentUser = false
                me.$router.push('login')
            }
        })
    },
    components: {
        VLoading
    }
}
</script>

<style>

</style>

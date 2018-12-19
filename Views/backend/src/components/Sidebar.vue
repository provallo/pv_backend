<template>
    <div class="sidebar">
        <div class="logo">
            <img src="@/assets/logo.svg" alt="ProVallo Logo">
        </div>
        <div class="menu">
            <v-menu :items="menu"></v-menu>
        </div>
        <div class="version-info">
            <div class="application-version">
                v0.0.1
            </div>
        </div>
        <div class="login-info">
            <div class="username">
                {{ username }}
            </div>
            <span class="menu-toggle" @click="$refs.userMenu.show()">
                <fa icon="chevron-up"></fa>
            </span>
            
            <v-context-menu :items="contextMenu" :pos="{ left: '160px', bottom: '55px' }" ref="userMenu"></v-context-menu>
        </div>
    </div>
</template>

<script>
export default {
    computed: {
        username () {
            return this.$store.state.currentUser.username
        }
    },
    data () {
        return {
            menu: [
                {
                    active: true,
                    name: 'Dashboard',
                    route: '/'
                },
                {
                    name: 'Users',
                    route: '/users'
                },
                {
                    name: 'Pages',
                    route: '/pages'
                },
                {
                    name: 'Forms',
                    route: '/forms'
                },
                {
                    name: 'Media',
                    route: '/medias'
                }
            ],
            contextMenu: [
                {
                    name: 'Logout',
                    click: this.logout.bind(this)
                }
            ]
        }
    },
    methods: {
        logout () {
            let me = this
            
            me.$store.state.currentUser = null
            me.$router.push({ name: 'login' })
            
            me.$http.get('backend/user/logout')
        }
    }
}
</script>
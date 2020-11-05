<template>
    <div class="sidebar-container">
        <div class="sidebar-content">
            <div class="logo">
                <img src="@/assets/logo.svg" alt="ProVallo Logo">
            </div>

            <div class="login-info">
                <div class="username">
                    Hi, {{ username }}!
                </div>
                <span class="menu-toggle" @click="$refs.userMenu.show()">
                    <fa icon="ellipsis-v"></fa>
                </span>

                <v-context-menu :items="contextMenu" :pos="{ top: '103px', left: '235px' }" ref="userMenu"></v-context-menu>
            </div>

            <div class="menu">
                <v-menu v-if="menu.length > 0" :items="menu"></v-menu>
                <div v-else class="menu-loader">
                    <fa icon="spinner" spin></fa>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    computed: {
        username () {
            return this.$store.state.currentUser.username
        },
        currentVersion() {
            return ProVallo.version;
        }
    },
    data () {
        return {
            menu: [],
            contextMenu: [
                {
                    label: 'Logout',
                    click: this.logout.bind(this)
                }
            ]
        }
    },
    mounted () {
        let me = this
        
        me.loadMenu()
    },
    methods: {
        loadMenu () {
            let me = this
            
            me.$http.get('backend/index/menu').then(res => res.data).then(({ data }) => {
                me.menu = data
            })
        },
        logout () {
            let me = this
            
            me.$store.state.currentUser = null
            me.$router.push({ name: 'login' })
            
            me.$http.get('backend/user/logout')
        }
    }
}
</script>

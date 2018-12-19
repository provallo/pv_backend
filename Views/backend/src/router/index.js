import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

export default new Router({
    routes: [
        {
            name: 'index',
            path: '/',
            component: require('@/views/Index').default
        },
        {
            name: 'login',
            path: '/login',
            component: require('@/views/Login').default
        },
        {
            name: 'user.index',
            path: '/users',
            component: require('@/views/user').default
        },
        {
            name: 'page.index',
            path: '/pages',
            component: require('@/views/page').default
        }
    ]
})

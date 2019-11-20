import Vue from 'vue'
import App from './App'
import router from './router'
import axios from 'axios'
import store from './store'
import models from './models'
import swal from 'sweetalert2/dist/sweetalert2.js'
import 'sweetalert2/src/sweetalert2.scss'

Vue.config.productionTip = false

import '@yurderi/vue-ui/src/assets/less/all.less'
import VueUI from '@yurderi/vue-ui/src/index.js'
import '@/assets/less/all.less'

Vue.use(VueUI)

Vue.http = Vue.prototype.$http = axios.create({
    baseURL: '/'
})

Vue.models = Vue.prototype.$models = models
Vue.swal   = Vue.prototype.$swal = swal

Vue.component('v-form', require('@/modules/Form.vue').default)
Vue.component('v-message', require('@/modules/Message.vue').default)
Vue.component('v-grid-header', require('@/modules/GridHeader.vue').default)
Vue.component('v-grid', require('@/modules/Grid.vue').default)
Vue.component('v-modal-form', require('@/modules/ModalForm.vue').default)
Vue.component('v-detail', require('@/modules/Detail.vue').default)
Vue.component('v-remote-combo', require('@/modules/forms/RemoteCombo').default)

Vue.component('v-sidebar', require('@/components/Sidebar').default)
Vue.component('v-menu', require('@/components/Menu').default)
Vue.component('v-context-menu', require('@/components/ContextMenu').default)

/* eslint-disable no-new */
let app = new Vue({
    el: '#app',
    router,
    store,
    components: { App },
    template: '<App/>'
})

window.ProVallo = {
    $vm: app,
    $models: models,
    $router: router,
    $store: store
}

import plugins from '@vendor'

plugins.forEach(plugin => {
    plugin()
})

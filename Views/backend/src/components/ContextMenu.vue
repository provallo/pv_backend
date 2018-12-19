<template>
    <div class="context-menu" :style="style" v-show="visible">
        <v-menu :items="items"></v-menu>
    </div>
</template>

<script>
export default {
    name: 'v-context-menu',
    props: {
        items: Array,
        pos: Object,
    },
    data: () => ({
        visible: false,
        hideLock: false
    }),
    computed: {
        style () {
            let me = this
            
            return {
                top: me.pos.top || 'auto',
                left: me.pos.left || 'auto',
                bottom: me.pos.bottom || 'auto',
                right: me.pos.right || 'auto'
            }
        }
    },
    mounted () {
        let me = this
        
        document.addEventListener('click', me.onClick.bind(me))
    },
    beforeDestroy () {
        let me = this
        
        document.removeEventListener('click', me.onClick.bind(me))
    },
    methods: {
        show () {
            let me = this
            
            me.visible = true
            me.hideLock = true
        },
        onClick (e) {
            let me = this
            let target = e.target
            
            if (target && (me.$el.contains(target) || target.isEqualNode(me.$el))) {
                // ok, fine
            } else {
                if (me.hideLock) {
                    me.hideLock = false
                } else {
                    me.hideLock = true
                    me.visible = false
                }
            }
        }
    }
}
</script>
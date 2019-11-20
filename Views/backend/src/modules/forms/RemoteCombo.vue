<template>
    <div class="remote-combo">
        <v-select :data="items" :displayField="displayField" :valueField="valueField" v-model="selectedItem"></v-select>
    </div>
</template>

<script>
export default {
    props: {
        model: {
            type: String,
            required: true
        },
        displayField: {
            type: String,
            required: false,
            defaultValue: 'label'
        },
        valueField: {
            type: String,
            required: false,
            defaultValue: 'id'
        },
        value: {
            required: true
        }
    },
    data: () => ({
        items: [],
        selectedItem: null
    }),
    watch: {
        selectedItem (value) {
            let me = this

            me.$emit('input', value)
        }
    },
    mounted () {
        let me = this
        let model = me.$models[me.model]
        
        if (!model) {
            throw new Error('Used unknown model in RemoteCombo')
        }
        
        model.list().then(items => me.items = items)
        me.selectedItem = me.value
    }
}
</script>

<template>
    <div class="is--config-index view">
        <v-grid ref="grid" :config="gridConfig">
            <div class="grid-item config" slot="item" slot-scope="{ model }"
                 :class="{ active: editingModel && editingModel.id === model.id }">
                <div class="item-meta" @click="edit(model)">
                    <div class="item-label">
                        {{ model.label }}
                    </div>
                </div>
            </div>
        </v-grid>
        <v-detail :disabled="!editingModel" :loading="loadingModel">
            <v-form v-if="editingModel"
                    @submit="submit" :buttons="formButtons"
                    :style="{ maxWidth: '500px' }"
                    ref="form">
                <div class="form-item" v-if="editingModel.id > 0">
                    <label for="id">
                        ID
                    </label>
                    <v-input type="text" id="id" :value="editingModel.id.toString()" readonly></v-input>
                </div>
                
                <div class="form-item" v-for="element in editingModel.elements" :class="'is--' + element.data.type">
                    <!-- text, password, email, number, textarea, checkbox, select (local store), select (remote store) -->
                    
                    <template v-if="['text', 'password', 'email', 'number', 'textarea'].indexOf(element.data.type) > -1">
                        <label :for="'element_' + element.id">
                            {{ element.data.label }}
                        </label>
                        
                        <div class="form-item-field">
                            <v-input :type="element.data.type" v-model="element.value.value" :placeholder="element.data.placeholder" :required="element.data.required"></v-input>
                            <v-info v-if="element.data.description">
                                {{ element.data.description }}
                            </v-info>
                        </div>
                    </template>
                    <template v-else-if="element.data.type === 'checkbox'">
                        <v-checkbox v-model="element.value.value" :label="element.data.label"></v-checkbox>
                        <v-info v-if="element.data.description">
                            {{ element.data.description }}
                        </v-info>
                    </template>
                    <template v-else-if="element.data.type === 'select'">
                        <label :for="'element_' + element.id">
                            {{ element.data.label }}
                        </label>
                        
                        <div class="form-item-field">
                            <template v-if="element.data.store.type === 'local'">
                                <v-select :data="element.data.store.data" v-model="element.value.value"
                                          :displayField="element.data.store.displayField"
                                          :valueField="element.data.store.valueField"></v-select>
                            </template>
                            <template v-else-if="element.data.store.type === 'remote'">
                                <template v-if="!stores[element.id].loading">
                                    <v-select :data="stores[element.id].items" v-model="element.value.value"
                                              :displayField="element.data.store.displayField"
                                              :valueField="element.data.store.valueField"></v-select>
                                </template>
                                <template v-else>
                                    <v-input type="text" value="Loading..." readonly></v-input>
                                </template>
                            </template>
                            <template v-else>
                                Unknown store type
                            </template>
                            
                            <v-info v-if="element.data.description">
                                {{ element.data.description }}
                            </v-info>
                        </div>
                    </template>
                    <template v-else>
                        Unknown element type
                    </template>
                </div>
            </v-form>
        </v-detail>
    </div>
</template>

<script>
export default {
    data() {
        let me = this
        
        return {
            gridConfig: {
                model: me.$models.config,
                buttons: [
                    {
                        name: 'refresh',
                        label: '',
                        icon: 'sync-alt',
                        action: 'load'
                    }
                ]
            },
            formButtons: [
                {
                    label: 'Save',
                    primary: true,
                    name: 'submit'
                }
            ],
            editingModel: null,
            loadingModel: false,
            
            stores: {}
        }
    },
    methods: {
        edit (model) {
            let me = this
            
            me.loadingModel = true
            me.$models.config.get(model.id).then(config => {
                me.loadingModel = false
                me.editingModel = config
                
                // Parse element data
                me.editingModel.elements.forEach(element => {
                    element.data = JSON.parse(element.data)
                    
                    if (element.data.type === 'select') {
                        // Prepare remote stores
                        if (element.data.store.type === 'remote') {
                            let model = element.data.store.model
                            let $model = me.$models[model]
    
                            if ($model) {
                                me.stores[element.id] = {
                                    loading: true,
                                    items: []
                                }
        
                                $model.list().then(items => {
                                    me.stores[element.id].loading = false
                                    me.stores[element.id].items   = items
            
                                    // We need to $forceUpdate since `me.store` is not reactive
                                    me.$forceUpdate()
                                })
                            } else {
                                throw new Error('Store referenced an unknown model: ' + model)
                            }
                        }
                        
                        element.value.value = parseInt(element.value.value)
                    } else if (element.data.type === 'checkbox') {
                        element.value.value = !!element.value.value
                    }
                })
    
                me.$nextTick(() => {
                    me.$refs.form.reset()
                })
            })
        },
        submit ({ setMessage, setLoading, setProgress }) {
            let me = this
            
            setLoading(true)
            me.$models.config.save(me.editingModel).then(({ success, data, messages }) => {
                if (success) {
                    setMessage('success', 'The configuration were saved successfully')
                    setLoading(false)
    
                    me.$refs.grid.load()
                } else {
                    setMessage('error', messages[0])
                    setLoading(false)
                }
            }).catch(error => {
                setMessage('error', error.toString())
                setLoading(false)
            })
        }
    }
}
</script>
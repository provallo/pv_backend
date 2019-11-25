<template>
    <div class="is--user-index view">
        <v-grid ref="grid" :config="gridConfig" @create="create">
            <div class="grid-item group" slot="item" slot-scope="{ model }"
                 :class="{ active: editingModel && editingModel.id === model.id }">
                <div class="item-meta" @click="edit(model)">
                    <div class="item-name">
                        {{ model.name }}
                    </div>
                </div>
                <div class="item-actions">
                    <div class="item-action" @click="remove(model)">
                        <fa icon="trash"></fa>
                    </div>
                </div>
            </div>
        </v-grid>
        <v-detail :disabled="!editingModel">
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
                <div class="form-item">
                    <label for="name">
                        Name
                    </label>
                    <v-input type="text" id="name" v-model="editingModel.name"></v-input>
                </div>
              
                <div class="permission-container" v-if="permissions">
                  <div class="header">
                    Permissions
                  </div>
                  
                    <template v-for="p in permissions">
                      <v-checkbox :name="p.name"
                                  :label="p.label || p.name"
                                  v-model="p.value.value"></v-checkbox>
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
                model: me.$models.group
            },
            formButtons: [
                {
                    label: 'Save',
                    primary: true,
                    name: 'submit'
                }
            ],
            editingModel: null,
            permissions: []
        }
    },
    methods: {
        create () {
            let me = this
            
            me.editingModel = me.$models.group.create()
            me.$nextTick(() => me.$refs.form.reset())
        },
        edit (model) {
            let me = this
            
            me.editingModel = model
            me.$nextTick(() => me.$refs.form.reset())
            
            me.loadPermissions()
        },
        submit ({ setMessage, setLoading, setProgress }) {
            let me = this
            
            me.editingModel.permissions = me.permissions
            
            setLoading(true)
            me.$models.group.save(me.editingModel).then(({ success, data, messages }) => {
                if (success) {
                    setMessage('success', 'The group were saved successfully')
                    setLoading(false)
    
                    me.editingModel.id = data.id
                    
                    delete me.editingModel.permissions
                    me.loadPermissions()
                    
                    me.$refs.grid.load()
                } else {
                    setMessage('error', messages[0])
                    setLoading(false)
                }
            }).catch(error => {
                setMessage('error', error.toString())
                setLoading(false)
            })
        },
        remove (model) {
            let me = this
            
            me.$models.group.remove(model).then((success) => {
                if (success) {
                    me.$refs.grid.load()
    
                    if (me.editingModel && me.editingModel.id === model.id) {
                        me.editingModel = null
                    }
                } else {
                    me.$swal({
                        type: 'error',
                        title: 'Sorry!',
                        text: 'Unfortunately you are not allowed to delete this group.'
                    })
                }
            }).catch(error => {
                console.log(error)
            })
        },
        loadPermissions () {
            let me = this

            me.$models.permission.list({ groupID: me.editingModel.id }).then(data => {
                me.permissions = data
            })
        }
    }
}
</script>

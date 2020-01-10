<template>
    <div class="is--plugin-index view">
        <v-grid ref="grid" :config="gridConfig">
            <div class="grid-item user" slot="item" slot-scope="{ model }"
                 :class="{ active: editingModel && editingModel.id === model.id }">
                <div class="item-meta row" @click="edit(model)">
                    <div class="item-active">
                      <fa icon="check-square" v-if="model.active" />
                    </div>
                    <div class="item-label">
                        {{ model.label }}
                    </div>
                    <div class="item-version">
                      {{ model.version }}
                      <template v-if="model.remoteUpdate || model.localUpdate">
                        <fa icon="long-arrow-alt-right" style="margin:0 5px;" />
                         {{ model.localUpdate || model.remoteUpdate }}
                         <fa icon="cloud" style="margin:0 5px;" v-if="model.remoteUpdate" />
                      </template>
                    </div>
                </div>
            </div>
        </v-grid>
        <v-detail :disabled="!editingModel">
          <div class="plugin-detail" v-if="editingModel">
            <div class="plugin-header">
              {{ editingModel.label }}
            </div>
            <div class="plugin-description">
              {{ editingModel.description }}
            </div>
            <div class="plugin-meta">
              <div class="meta-item">
                <div class="item-label">Installed</div>
                <div class="item-value">{{ editingModel.active ? 'Yes' : 'No' }}</div>
              </div>
              <div class="meta-item">
                <div class="item-label">Technical Name</div>
                <div class="item-value">{{ editingModel.name }}</div>
              </div>
              <div class="meta-item">
                <div class="item-label">Version</div>
                <div class="item-value">{{ editingModel.version }}</div>
              </div>
              <div class="meta-item">
                <div class="item-label">Author</div>
                <div class="item-value">{{ editingModel.author }}</div>
              </div>
              <div class="meta-item">
                <div class="item-label">Authors email</div>
                <div class="item-value">{{ editingModel.email }}</div>
              </div>
              <div class="meta-item">
                <div class="item-label">Website</div>
                <div class="item-value">{{ editingModel.website }}</div>
              </div>
              <div class="meta-item">
                <div class="item-label">Created</div>
                <div class="item-value">{{ editingModel.created }}</div>
              </div>
            </div>
            <div class="plugin-actions">
              <v-button :disabled="editingModel.active"
                        @click="install(editingModel)"
                        :spin="pluginAction.id === 'install'">
                Install
              </v-button>
              <v-button :disabled="!editingModel.active"
                        @click="uninstall(editingModel)"
                        :spin="pluginAction.id === 'uninstall'">
                Uninstall
              </v-button>
              <v-button :disabled="!(editingModel.localUpdate || editingModel.remoteUpdate) || !editingModel.active"
                        @click="update(editingModel)"
                        :spin="pluginAction.id === 'update'">
                Update
                <template v-if="editingModel.localUpdate || editingModel.remoteUpdate">
                  to {{ editingModel.localUpdate || editingModel.remoteUpdate }}
                </template>
              </v-button>
            </div>
            <div class="plugin-action-result"
                 v-if="pluginAction.success || pluginAction.error">
              <v-message type="success" v-if="pluginAction.success">
                {{ pluginAction.success }}
              </v-message>

              <template v-if="pluginAction.error">
                <v-message type="error">
                  {{ pluginAction.error }}
                </v-message>
                <div class="error-details">{{ pluginAction.errorDetails }}</div>
              </template>
            </div>
          </div>
        </v-detail>
    </div>
</template>

<script>
export default {
  data() {
    let me = this

    return {
      gridConfig: {
        model: me.$models.plugin,
        buttons: [
          {
            name: 'refresh',
            label: '',
            icon: 'sync-alt',
            action: 'load'
          }
        ]
      },
      editingModel: null,
      pluginAction: {
        id: null,
        success: null,
        error: null,
        errorDetails: null
      }
    }
  },
  methods: {
    edit(model) {
      let me = this

      me.editingModel = model
    },
    install(model) {
      const me = this

      me.pluginAction.id = 'install'
      me.pluginAction.success = null
      me.pluginAction.error = null
      me.pluginAction.errorDetails = null

      me.$http.post('backend/plugin/install', { name: model.name }).then(response => {
        me.pluginAction.id = null
        me.$refs.grid.load()

        if (response.data.success) {
          me.pluginAction.success = 'The plugin were installed successfully.'
          me.editingModel.active = true
        } else {
          me.pluginAction.error = 'The plugin were not installed. See error details below.'
          me.pluginAction.errorDetails = response
        }
      })
    },
    uninstall(model) {
      const me = this

      if (!confirm('Are you sure to uninstall this plugin?')) {
        return
      }

      me.pluginAction.id = 'uninstall'
      me.pluginAction.success = null
      me.pluginAction.error = null
      me.pluginAction.errorDetails = null

      me.$http.post('backend/plugin/uninstall', { name: model.name }).then(response => {
        me.pluginAction.id = null
        me.$refs.grid.load()

        if (response.data.success) {
          me.pluginAction.success = 'The plugin were uninstalled successfully.'
          me.editingModel.active = false
        } else {
          me.pluginAction.error = 'The plugin were not uninstalled. See error details below.'
          me.pluginAction.errorDetails = response
        }
      })
    },
    update(model) {
      const me = this

      me.pluginAction.id = 'update'
      me.pluginAction.success = null
      me.pluginAction.error = null
      me.pluginAction.errorDetails = null

      me.$http.post('backend/plugin/update', { name: model.name }).then(response => {
        me.pluginAction.id = null
        me.$refs.grid.load()

        if (response.data.success) {
          me.pluginAction.success = 'The plugin were updated successfully.'
          me.editingModel.version = response.data.version

          if (me.editingModel.localUpdate) {
            delete me.editingModel.localUpdate
          } else if (me.editingModel.remoteUpdate) {
            delete me.editingModel.remoteUpdate
          }
        } else {
          me.pluginAction.error = 'The plugin were not updated. See error details below.'
          me.pluginAction.errorDetails = response
        }
      })
    }
  }
}
</script>

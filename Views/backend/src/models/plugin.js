export default {
  fields: [
    { name: 'id', type: 'integer' },
    { name: 'active', type: 'boolean' },
    { name: 'name', type: 'string', filterable: true },
    { name: 'label', type: 'string', filterable: true },
    { name: 'description', type: 'string', filterable: true },
    { name: 'version', type: 'string', filterable: true },
    { name: 'author', type: 'string', filterable: true },
    { name: 'email', type: 'string', filterable: true },
    { name: 'website', type: 'string', filterable: true },
    { name: 'created', type: 'string' },
    { name: 'changed', type: 'string' }
  ],
  proxy: {
    list: 'backend/plugin/list',
    detail: 'backend/plugin/detail',
    save: 'backend/plugin/save',
    remove: 'backend/plugin/remove'
  }
}

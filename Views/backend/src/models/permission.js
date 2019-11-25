export default {
  fields: [
    { name: 'id', type: 'integer' },
    { name: 'name', type: 'string', filterable: true },
    { name: 'defaultValue', type: 'boolean' },
    { name: 'label', type: 'string', filterable: true }
  ],
  proxy: {
    list: 'backend/permission/list',
    detail: 'backend/permission/detail',
    save: 'backend/permission/save',
    remove: 'backend/permission/remove'
  }
}

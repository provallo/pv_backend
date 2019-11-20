export default {
    fields: [
        { name: 'id', type: 'integer' },
        { name: 'name', type: 'string', filterable: true },
        { name: 'created', type: 'string' },
        { name: 'changed', type: 'string' }
    ],
    proxy: {
        list: 'backend/group/list',
        detail: 'backend/group/detail',
        save: 'backend/group/save',
        remove: 'backend/group/remove'
    }
}
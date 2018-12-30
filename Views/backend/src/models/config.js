export default {
    fields: [
        { name: 'id', type: 'integer' },
        { name: 'name', type: 'string', filterable: true },
        { name: 'label', type: 'string', filterable: true },
        { name: 'data', type: 'string' }
    ],
    proxy: {
        list: 'backend/config/list',
        detail: 'backend/config/detail',
        save: 'backend/config/save',
        remove: 'backend/config/remove'
    }
}
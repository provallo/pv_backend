let components = require.context('.', true, /index\.js$/)
let plugins = []

components.keys().forEach(key => {
    if (key === './index.js') {
        return
    }
    
    let plugin = components(key)
    
    plugins.push(plugin)
})

export default plugins
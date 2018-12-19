<template>
    <div class="is--login">
    
        <div class="logo">
            <img src="@/assets/logo.svg" alt="ProVallo Logo">
        </div>
        
        <v-form :buttons="formButtons" @submit="submit">
            
            <label for="username">Username</label>
            <v-input type="text" id="username" v-model="formData.username"></v-input>
            
            <label for="password">Password</label>
            <v-input type="password" id="password" v-model="formData.password"></v-input>
            
        </v-form>
    
    </div>
</template>

<script>
export default {
    data: () => ({
        formButtons: [
            {
                label: 'Login',
                primary: true,
                name: 'submit'
            }
        ],
        formData: {
            username: '',
            password: ''
        }
    }),
    methods: {
        submit ({ setMessage, setLoading, setProgress }) {
            let me = this
            
            setLoading(true)
            
            me.$http.post('backend/user/login', me.formData).then(({ data }) => {
                if (data.success === true) {
                    me.$store.state.currentUser = {
                        username: data.username
                    }
                    
                    me.$router.push('/')
                } else {
                    setMessage('error', data.message)
                    setLoading(false)
                }
            })
        }
    }
}
</script>
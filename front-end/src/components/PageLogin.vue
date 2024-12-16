<template>
    
        <v-sheet class="bg-deep-purple pa-12 " min-height="100vh" rounded elevation="8">
            <v-card class="mx-auto px-6 py-8" max-width="400" outlined>
                <v-form v-model="form" @submit.prevent="onSubmit">
                    <v-text-field v-model="email" :readonly="loading" :rules="emailRules" class="mb-2" label="Email"
                        clearable></v-text-field>
                    <v-text-field v-model="password" :readonly="loading" :rules="passwordRules" label="Password"
                        placeholder="Enter your password" :type="showPassword ? 'text' : 'password'" clearable
                        append-icon="mdi-eye" @click:append="togglePassword"></v-text-field>

                    <v-btn :disabled="!form" :loading="loading" color="success" size="large" type="submit"
                        variant="elevated" block>
                        Sign In
                    </v-btn>
                </v-form>
            </v-card>
        </v-sheet>
  
</template>
<script>
import axios from 'axios';
export default {
    data: () => ({
        showPassword: false,
        form: false,
        email: '',
        password: '',
        loading: false,
        passwordRules: [
            v => !!v || 'Password is required',
            v => v && v.length >= 6 || 'Password must be at least 6 characters'
        ],
        emailRules: [
            v => !!v || 'Email is required',
            v => /.+@.+\..+/.test(v) || 'E-mail must be valid'
        ]
    }),

    methods: {
        togglePassword() {
            this.showPassword = !this.showPassword;
        },
        async onSubmit() {
            if (!this.form) return false;

            this.loading = true;

            try {
                // const response = await fetch('http://localhost:8989/api/auth/login', {
                //     method: 'POST',
                //     headers: { 'Content-Type': 'application/json' },
                //     body: JSON.stringify({
                //         email: this.email,
                //         password: this.password
                //     })
                // });

                const response = await axios.post('http://localhost:8989/api/auth/login', {
                    email: this.email,
                    password: this.password
                });

                console.log(response);


                if (!response.statusText) throw new Error('Invalid login credentials');

                const data = await response.data;
                console.log('Login successful', data);      
                localStorage.setItem('token', data.access_token);
                this.$router.push('/');

                // Redirecionar ou salvar token, etc.
            } catch (error) {
                console.error('Login failed', error);
                alert(error.message);
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
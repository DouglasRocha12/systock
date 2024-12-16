<template>
  <div class="text-center pa-4">
    <v-btn @click="createUsermodal = true">
      Criar Usuario
    </v-btn>

    <v-dialog v-model="createUsermodal" width="auto">
      <v-card class="mx-auto" max-width="500" title="User Registration">
        <v-container>
          <v-text-field v-model="name" color="primary" label="Full name" variant="underlined"></v-text-field>

          <v-text-field v-model="email" :rules="[rules.email]" color="primary" label="Email"
            variant="underlined"></v-text-field>

          <v-text-field v-model="cpf" v-mask="'###.###.###-##'" color="primary" label="CPF" placeholder="Enter your cpf"
            variant="underlined"></v-text-field>

          <v-text-field v-model="password" label="Password" placeholder="Enter your password" clearable
            type="password"></v-text-field>

          <v-checkbox v-model="terms" color="secondary" label="I agree to site terms and conditions"></v-checkbox>
        </v-container>

        <v-divider></v-divider>

        <v-card-actions>
          <v-spacer></v-spacer>

          <v-btn color="success" @click="submitForm">
            Complete Registration
            <v-icon icon="mdi-chevron-right" end></v-icon>
            
          </v-btn>
          <v-progress-circular v-if="loading" indeterminate color="primary"></v-progress-circular>
        </v-card-actions>
      </v-card>

    </v-dialog>
  </div>
</template>
<script>
import { mask } from 'vue-the-mask'
import axios from 'axios';
export default {
  directives: { mask },
  data() {
    return {
      createUsermodal: false,
      name: null,
      email: null,
      cpf: null,
      password: null,
      terms: false,
      rules: {
        email: (value) => {
          if (!value) return 'E-mail is required.';
          if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) return 'E-mail must be valid.';
          return true;
        },
      },
    }
  },
  methods: {
    submitForm() {
      this.loading = true;
      this.$emit('create-loading', true)
      console.log(this.name, this.email, this.cpf, this.password)
      let data = JSON.stringify({
        "name": this.name,
        "email":  this.email,
        "cpf":  this.cpf,
        "password": this.password,
        "password_confirmation": this.password
      });
      let token = localStorage.getItem('token');
      let config = {
        method: 'post',
        maxBodyLength: Infinity,
        url: 'http://localhost:8989/api/auth/register',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${token}`
        },
        data: data
      };

      axios.request(config)
        .then((response) => {
          console.log(JSON.stringify(response.data));
          this.createUsermodal=false;
          this.$emit('create-loading', false)
          this.$emit("user-created")
        })
        .catch((error) => {
          console.log(error);
          this.$emit('create-loading', false)
          this.$emit("user-created")
        }) 
        .finally(() => {
          this.loading = false; // Set loading to false in the finally block
        });

    },
  },
}
</script>
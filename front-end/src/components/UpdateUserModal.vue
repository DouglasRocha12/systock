<template>
  <div class="text-center pa-4">
    <v-dialog v-model="modalVisible" width="500">
      <v-card>
        <v-card-title>Update User</v-card-title>
        <v-card-text v-if="editableUser">
          <v-text-field v-model="editableUser.name" label="Name"></v-text-field>
          <v-text-field v-model="editableUser.email" :rules="[rules.editableUser.email]" label="Email"></v-text-field>
          <v-text-field v-model="editableUser.cpf" v-mask="'###.###.###-##'" label="CPF"></v-text-field>
          <!-- <v-select v-model="editableUser.status" :items="['Active', 'Inactive']" label="Status"></v-select> -->
        </v-card-text>
        <v-card-actions>
          <v-btn color="primary" @click="saveChanges">Save</v-btn>
          <v-btn color="secondary" @click="closeModal">Cancel</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import { mask } from 'vue-the-mask';
import axios from 'axios';
export default {
  directives: { mask },
  props: {
    selectedUser: {
      type: [Object, null],
      required: true
    },
    modelValue: {
      type: Boolean,
      required: true
    }
  },
  data() {
    return {
      editableUser: null,
      rules: {
        editableUser:
        {
          email: (value) => {
            if (!value) return 'E-mail is required.';
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) return 'E-mail must be valid.';
            return true;
          }
        }
      },
    };
  },
  computed: {
    modalVisible: {
      get() {
        return this.modelValue;
      },
      set(value) {
        this.$emit('update:modelValue', value);
      }
    }
  },
  watch: {
    selectedUser: {
      immediate: true,
      handler(newValue) {
        this.editableUser = newValue ? { ...newValue } : null;
      }
    }
  },
  methods: {
    async saveChanges() {
      if (this.editableUser) {
        this.$emit('update-loading', true)

        let data = JSON.stringify({
          "id": this.editableUser.id,
          "name": this.editableUser.name,
          "cpf": this.editableUser.cpf,
          "email": this.editableUser.email,
          "password": this.editableUser.password,
          "password_confirmation": this.editableUser.password
        });
        let token = localStorage.getItem('token');
        let config = {
          method: 'post',
          maxBodyLength: Infinity,
          url: 'http://localhost:8989/api/auth/update',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
          },
          data: data
        };

        axios.request(config)
          .then((response) => {
            console.log(JSON.stringify(response.data));
            this.createUsermodal = false;
            this.$emit('update-loading', false)
            this.$emit('update-user');
          })
          .catch((error) => {
            console.log(error);
            this.$emit('create-loading', false)
            this.$emit("user-created")
          })
          .finally(() => {
            this.$emit('create-loading', false)
            this.$emit("user-created")
          });
    

      }
      this.closeModal();
    },
    closeModal() {
      this.editableUser = null;
      this.modalVisible = false;
    }
  }
};
</script>

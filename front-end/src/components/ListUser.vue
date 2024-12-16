<template>
    <div>
        <v-dialog v-model="loading"  persistent width="auto">
            <v-card>
                <v-card-text class="pa-4 d-flex flex-column align-center">
                    <v-progress-circular indeterminate color="primary"></v-progress-circular>
                    <span class="mt-2">Loading ...</span>
                </v-card-text>
            </v-card>
        </v-dialog>
        <Search @search-term-updated="updateSearchTerm" />
        <v-table fixed-header>
            <thead>
                <tr>
                    <th class="text-center">Name</th>
                    <th class="text-center">Email</th>
                    <th class="text-center">CPF</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in filteredUsers" :key="item.name">
                    <td class="text-center">{{ item.name }}</td>
                    <td class="text-center">{{ item.email }}</td>
                    <td class="text-center">{{ item.cpf }}</td>
                    <td class="text-center">{{ item.status }}</td>
                    <td class="text-center">
                        <v-row justify="center">
                            <v-col>
                                <v-btn color="success" @click="onEditUser(item)">
                                    Edit
                                    <v-icon icon="mdi-account-plus-outline" end></v-icon>
                                </v-btn>
                            </v-col>
                            <v-col>
                                <v-btn :color="item.status === 'Inactive' ? 'red' : 'success'"
                                    @click="toggleStatus(item)">
                                    {{ item.status }}
                                    <v-icon icon="mdi-chevron-right" end></v-icon>
                                </v-btn>
                            </v-col>
                        </v-row>
                    </td>
                </tr>
            </tbody>
        </v-table>
        <CreateUserModal @user-created="fetchUsers"  @create-loading="handleUpdateLoading" />
        <UpdateUserModal v-model="showUpdateUserModal" :selectedUser="selectedUser" @update-user="fetchUsers" @update-loading="handleUpdateLoading"/>
    </div>

</template>

<script>
import UpdateUserModal from './UpdateUserModal.vue';
import CreateUserModal from './CreateUserModal.vue';
import Search from './Search.vue';
import axios from 'axios';
export default {
    components: { UpdateUserModal, CreateUserModal, Search },
    data() {
        return {
            users: [],
            filteredUsers: [],
            searchTerm: '',
            showUpdateUserModal: false,
            selectedUser: null,
            loading: false,
            // editableUser: null 
        };
    },
    watch: {
        searchTerm() {
            this.filterUsers();
        }
    },
    mounted() {
        this.fetchUsers();
    },
    methods: {
        handleUpdateLoading(isLoading) {
             this.loading = isLoading;
        },
        async fetchUsers() {
            this.loading = true;
            this.error = null;
            let token = localStorage.getItem('token');
            try {
                const response = await axios.get('http://localhost:8989/api/auth/users', {
                    headers: {
                        'Authorization': `Bearer ${token}`
                    }
                });
                this.users = response.data;
                this.filterUsers(); // chama a função para filtrar ao iniciar
            } catch (error) {
                this.error = error.message;
                console.error("Error fetching users:", error);
            } finally {
                this.loading = false;
            }
        },
        filterUsers() {
            if (!this.searchTerm) {
                this.filteredUsers = this.users
                return;
            }
            const search = this.searchTerm.toLowerCase();
            this.filteredUsers = this.users.filter(user => {
                return Object.values(user).some(value =>
                    String(value).toLowerCase().includes(search)
                );
            });
        },
        updateSearchTerm(newSearchTerm) { // Method to update searchTerm
            this.searchTerm = newSearchTerm;
        },
        async toggleStatus(user) {
            this.loading = true;
            let token = localStorage.getItem('token');
            let config = {
                method: 'post',
                maxBodyLength: Infinity,
                url: 'http://localhost:8989/api/auth/activate/' + user.id,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
            };

           await axios.request(config)
                .then((response) => {
                    user.status = user.status === 'Active' ? 'Inactive' : 'Active';
                })
                .catch((error) => {
                    console.log(error);
                }).finally(() => {
                    this.loading = false;
                });
  
        },
        onEditUser(user) {
            this.selectedUser = user;
            this.showUpdateUserModal = true; // Abre o modal
        },
        updateUser(updatedUser) {
            // Atualiza o usuário na lista de usuários
            const index = this.users.findIndex(user => user.id === updatedUser.id);
            if (index !== -1) {
                this.users.splice(index, 1, updatedUser);
            }
        }
    },


};
</script>

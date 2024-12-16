<template>
    <v-navigation-drawer v-model="drawer" app>
        <v-list-item title="Systock" subtitle="system"></v-list-item>
        <v-divider></v-divider>
        <v-list-item v-for="item in routers" :key="item.nome" :to="item.rota">
            <v-row align="center">
                <v-col cols="auto"> <v-icon>{{ item.icon }}</v-icon> </v-col>
                <v-col> <v-list-item-title>{{ item.nome }}</v-list-item-title> </v-col>
            </v-row>

        </v-list-item>

    </v-navigation-drawer>
    <v-app-bar :elevation="2">
        <template v-slot:prepend>
            <v-app-bar-nav-icon @click="drawer = !drawer"></v-app-bar-nav-icon>
        </template>
    </v-app-bar>
    
</template>

<script>
export default {
    data: () => ({
        drawer: false, // Initialize to false (closed by default)
        routers: [
            { nome: "Home", icon: "mdi-home-outline", rota: "/" },
            { nome: "List Users", icon: "mdi-format-list-bulleted", rota: "/users/list" },
        ]
    }),
}
</script>
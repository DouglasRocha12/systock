// Search.vue
<template>
  <v-card class="mx-auto" color="surface-light">
    <v-card-text>
      <v-text-field
        v-model="searchText"
        :loading="loading"
        append-inner-icon="mdi-magnify"
        density="compact"
        label="Search templates"
        variant="solo"
        hide-details
        single-line
        @input="onSearchInput"/>
    </v-card-text>
  </v-card>
</template>

<script>
export default {
  data: () => ({
    loaded: false,
    loading: false,
    searchText: '',
  }),
  methods: {
    onSearchInput() {
        this.$emit('search-term-updated', this.searchText); 
    },
  },
};
</script>

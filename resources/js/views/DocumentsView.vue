<template>
  <div>
    <h1 class="text-primary text-2xl font-bold mb-4">Documents Overview</h1>
    <div v-if="loading">
      <p>Loading documents...</p>
    </div>
    <div v-else-if="error">
      <p class="text-error">{{ error }}</p>
    </div>
    <div v-else>
      <DocumentsTable :documents="documents" />
    </div>
  </div>
</template>

<script>
import DocumentsTable from '@/components/DocumentsTable.vue'
import { fetchDocuments } from '@/services/apiService'

export default {
  name: 'DocumentsView',
  components: {
    DocumentsTable
  },
  data() {
    return {
      documents: [],
      loading: true,
      error: null
    }
  },
  async mounted() {
    console.log("Mounted DocumentsView");
    await this.fetchDocuments();
  },
  methods: {
    async fetchDocuments() {
      console.log("Fetching all documents...");
      try {
        const data = await fetchDocuments();
        // console.log("Documents fetched:", data);
        this.documents = data;
      } catch (error) {
        this.error = 'Error fetching documents. Please try again later.';
        console.error('Error fetching documents:', error);
      } finally {
        this.loading = false;
      }
    }
  }
}
</script>


<style scoped>
/* Add your custom styles here */
</style>
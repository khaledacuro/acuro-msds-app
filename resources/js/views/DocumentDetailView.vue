<template>
  <div class="max-w-4xl mx-auto bg-white p-10 shadow-lg rounded mt-20">
    <h1 class="text-3xl font-bold text-primary mb-6">Document Details</h1>

    <div v-if="loading">
      <p>Loading document data...</p>
    </div>

    <div v-else-if="error">
      <p class="text-error">{{ error }}</p>
    </div>

    <div v-else-if="document">
      <p><strong>File Name:</strong> {{ document.file_name }}</p>
      <p><strong>Created At:</strong> {{ document.created_at }}</p>
      <p><strong>Updated At:</strong> {{ document.updated_at }}</p>
      <h2 class="text-2xl font-semibold mt-6">Fields</h2>
      <ul class="mt-4 space-y-2">
        <li
          v-for="(fieldData, index) in document.document_data"
          :key="index"
          class="bg-gray-100 p-4 rounded-lg"
        >
          <strong>{{ fieldData.field.field_name }}:</strong> {{ fieldData.value }}<br />
          <strong>Confidence: </strong>
          <!-- if confidence is equal or greater than 90%, show in green, otherwise show in red -->
          <span :class="fieldData.confidence >= 85 ? 'text-secondary' : 'text-error'"
            >{{ fieldData.confidence }}%</span
          >
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
import { fetchDocumentById } from '@/services/apiService'
export default {
  name: 'DocumentDetailView',
  props: {
    id: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      document: null,
      loading: true,
      error: null
    }
  },
  async mounted() {
    console.log("Mounted DocumentDetailView - Document ID:", this.id);
    await this.fetchDocumentById();
  },
  methods: {
    async fetchDocumentById() {
      console.log("Fetching document by ID:", this.id);
      this.loading = true;
      this.error = null;
      try {
        const data = await fetchDocumentById(this.id);
        console.log("Document fetched:", data);
        this.document = data;
      } catch (error) {
        this.error = 'Error fetching document details. Please try again later.';
        console.error('Error fetching document details:', error);
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

<template>
  <div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold text-primary mb-4 text-center">Upload Your MSDS File</h1>
    <FileUploader />
  </div>
</template>

<script>
import FileUploader from '../components/FileUploader.vue'

export default {
  name: 'UploadView',
  components: {
    FileUploader
  }
}
</script>

<style scoped>
</style>

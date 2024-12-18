<template>
  <div class="max-w-screen-md mx-auto bg-white p-10 shadow-lg rounded-lg mt-20">
    <!-- Title of the form -->
    <h2 class="text-3xl font-bold text-primary mb-6 text-center flex items-center justify-center">
      <i class="fas fa-upload h-8 w-8 mr-3 text-primary"></i>
      Upload MSDS Documents
    </h2>

    <!-- Form for file upload -->
    <form @submit.prevent="uploadFiles" class="space-y-6">
      <!-- Custom Browse Button with Hidden Input -->
      <div
        class="relative flex flex-col items-center justify-center border-2 border-dashed border-gray-300 p-8 rounded-lg text-center">
        <input type="file" multiple accept=".pdf" @change="handleFileUpload"
          class="absolute inset-0 opacity-0 cursor-pointer" ref="fileInput" :disabled="loading" />
        <i class="fas fa-file-pdf text-gray-400 text-4xl mb-4"></i>
        <p class="text-gray-500">Drag and drop PDF files or click to browse</p>
      </div>


      <!-- Display list of selected files with status (uploading, success, or error) -->
      <ul v-if="selectedFiles.length" class="mt-4 text-sm text-primary space-y-2">
        <li v-for="(file, index) in selectedFiles" :key="index" class="flex items-center space-x-2">
          <i class="fas fa-file-pdf text-error"></i>
          <span class="text-primary">{{ file.name }}</span>

          <!-- Remove file button -->
          <button v-if="!loading" @click.prevent="removeFile(index)" class="text-error hover:text-red-600 ml-2">
            <i class="fas fa-trash-alt"></i> Remove
          </button>

          <!-- Show file upload status -->
          <span v-if="file.status === 'uploading'" class="text-gray-500 flex items-center">
            <i class="fas fa-spinner fa-spin mr-1"></i> Uploading...
          </span>
          <span v-if="file.status === 'success'" class="text-secondary flex items-center">
            <i class="fas fa-check-circle mr-1"></i> Uploaded successfully
          </span>
          <span v-if="file.status === 'error'" class="text-error flex items-center">
            <i class="fas fa-times-circle mr-1"></i> Upload failed
          </span>
        </li>
      </ul>

      <!-- Error message displayed if an invalid file type or size is selected -->
      <p v-if="errorMessage" class="text-error mt-4">{{ errorMessage }}</p>

      <!-- Success message displayed after successful uploads -->
      <p v-if="successMessage" class="text-secondary mt-4">{{ successMessage }}</p>

      <!-- Upload and Reset Buttons -->
      <div class="text-start space-x-4 flex justify-start">
        <!-- Reset Button -->
        <button type="button" @click.prevent="resetFiles"
          class="flex items-center justify-center btn py-3 px-6 rounded-lg text-lg bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold transition-all duration-300"
          :disabled="loading">
          <i class="fas fa-sync mr-2"></i>
          Reset
        </button>

        <!-- Upload Button -->
        <button type="submit"
          class="flex items-center justify-center btn py-3 px-6 rounded-lg text-lg bg-secondary hover:bg-primary-hover text-white font-semibold transition-all duration-300"
          :disabled="loading">
          <i :class="loading ? 'fas fa-spinner fa-spin mr-2' : 'fas fa-upload mr-2'"></i>
          {{ loading ? 'Uploading...' : 'Upload' }}
        </button>
      </div>

    </form>

    <!-- List of Uploaded Files -->
    <h3 class="text-xl font-bold text-primary mt-8">Uploaded Files</h3>
    <ul v-if="uploadedFiles.length" class="mt-4 text-sm text-secondary space-y-2">
      <li v-for="(file, index) in uploadedFiles" :key="index" class="flex items-center space-x-2">
        <i class="fas fa-check-circle text-secondary"></i>
        <span>{{ file.name }}</span>
      </li>
    </ul>
  </div>
</template>

<script>
const MAX_FILE_SIZE_MB = 5 // Set maximum file size to 5MB

export default {
  name: 'FileUploader',
  data() {
    return {
      selectedFiles: [], // Array to hold the selected files
      uploadedFiles: [], // Array to hold successfully uploaded files
      errorMessage: '', // Error message for invalid files
      successMessage: '', // Success message after file upload
      loading: false // Loading state to track upload progress
    }
  },
  methods: {
    // Trigger the file input
    triggerFileInput() {
      if (!this.loading) {
        this.$refs.fileInput.click()
      }
    },
    // Handle file selection and validation
    handleFileUpload(event) {
      const files = Array.from(event.target.files) // Get selected files

      const pdfFiles = files.filter((file) => file.type === 'application/pdf')

      if (pdfFiles.length < files.length) {
        this.errorMessage = 'Only PDF files are allowed. Please select only PDF files.'
        return
      }

      const largeFiles = pdfFiles.filter((file) => file.size / 1024 / 1024 > MAX_FILE_SIZE_MB)
      if (largeFiles.length > 0) {
        this.errorMessage = `Each file must be smaller than ${MAX_FILE_SIZE_MB} MB.`
        return
      }

      const duplicateFiles = pdfFiles.filter((file) =>
        this.uploadedFiles.some((uploadedFile) => uploadedFile.name === file.name)
      )
      if (duplicateFiles.length > 0) {
        this.errorMessage = 'Some files were already uploaded and have been ignored.'
        return
      }

      const newFiles = pdfFiles.filter(
        (newFile) => !this.selectedFiles.some((file) => file.name === newFile.name)
      )
      if (newFiles.length < pdfFiles.length) {
        this.errorMessage = 'Some files were already selected and have been ignored.'
      }

      this.errorMessage = ''
      this.selectedFiles = [...this.selectedFiles, ...newFiles]
    },

    async uploadFiles() {
      if (this.selectedFiles.length === 0) {
        this.errorMessage = 'Please select at least one PDF file to upload.'
        return
      }

      this.loading = true
      this.successMessage = ''
      this.errorMessage = ''

      for (let i = 0; i < this.selectedFiles.length; i++) {
        const formData = new FormData()
        formData.append('file_content', this.selectedFiles[i], this.selectedFiles[i].name) // Backend expects file_content
        formData.append('file_name', this.selectedFiles[i].name) // Backend expects file_name

        this.selectedFiles[i].status = 'uploading'

        try {
          // Send request to your backend API
          const response = await fetch('http://localhost:8000/api/documents/triggerLogicApp', {
            method: 'POST',
            body: formData
          })

          if (!response.ok) {
            const result = await response.json()
            throw new Error(result.message || `Failed to upload ${this.selectedFiles[i].name}.`)
          }

          this.selectedFiles[i].status = 'success'
          this.uploadedFiles.push(this.selectedFiles[i]) // Add to uploaded files
        } catch (error) {
          console.error('Error:', error)
          this.selectedFiles[i].status = 'error'
          this.errorMessage += ` Error uploading ${this.selectedFiles[i].name}.`
        }
      }

      this.selectedFiles = this.selectedFiles.filter((file) => file.status !== 'success')

      if (this.selectedFiles.length === 0) {
        this.successMessage = 'All files have been uploaded successfully!'
      } else {
        this.errorMessage = 'Some files failed to upload. Please try again.'
      }

      this.loading = false
    },

    removeFile(index) {
      if (!this.loading) {
        this.selectedFiles = this.selectedFiles.filter((_, i) => i !== index)
      }
    },

    resetFiles() {
      if (!this.loading) {
        this.selectedFiles = []
        this.uploadedFiles = []
        this.errorMessage = ''
        this.successMessage = ''
        this.$refs.fileInput.value = ''
      }
    }
  }
}
</script>

<style scoped>
/* No additional styles required as Tailwind classes handle the necessary styling */
</style>

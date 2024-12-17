<template>
  <div class="max-w-4xl mx-auto text-center mt-20">
    <h1 class="text-4xl font-bold text-primary mb-6">Welcome to the MSDS Processing Application</h1>
    <p class="text-xl text-primary mb-12">Upload your MSDS documents and view the extracted information easily.</p>
    <router-link to="/upload">
      <button class="btn text-lg py-3 px-6">Get Started</button>
    </router-link>
  </div>
</template>

<script>
export default {
  name: 'HomeView'
}
</script>

<style scoped>
/* Add your custom styles here */
</style>

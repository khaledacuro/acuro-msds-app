<template>
  <div class="flex items-center justify-center min-h-screen">
    <div class="text-center p-8">
      <h1 class="text-6xl font-bold text-primary mb-4">404</h1>
      <p class="text-xl text-primary mb-8">Oops! The page you're looking for does not exist.</p>
      <router-link
        to="/"
        class="btn shadow-md hover:bg-primary-hover transition"
      >
        Back to Home
      </router-link>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PageNotFoundView'
}
</script>

<style scoped>
/* Add your custom styles here */
</style>

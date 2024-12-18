<template>
  <footer class="bg-primary text-white py-10">
    <div class="container mx-auto flex flex-col md:flex-row justify-between items-center px-4">
      <div class="mb-6 md:mb-0">
        <p class="font-semibold">Acuro B.V.</p>
        <p>T +31 (0)88-2323500</p>
        <p>
          M
          <a href="mailto:info@acuro.nl" class="text-secondary hover:underline">info@acuro.nl</a>
        </p>
        <p>
          W
          <a href="https://www.acuro.nl" target="_blank" class="text-secondary hover:underline">www.acuro.nl</a>
        </p>
      </div>
      <div class="flex space-x-6 text-sm mt-6 md:mt-0">
        <router-link to="/" class="hover:text-secondary">Home</router-link>
        <router-link to="/upload" class="hover:text-secondary">Upload File</router-link>
        <router-link to="/documents" class="hover:text-secondary">Documents</router-link>
      </div>
      <div class="text-sm mt-6 md:mt-0 text-center md:text-left">
        <div class="flex justify-center md:justify-start mb-2">
          <a href="https://nl.linkedin.com/company/acurobrandveiligheidenbouwregie" target="_blank" class="hover:text-secondary mx-2" aria-label="LinkedIn">
            <i class="fab fa-linkedin-in"></i>
          </a>
         
          <a href="https://www.instagram.com/acurobrandveiligheidbouwregie/" target="_blank" class="hover:text-secondary mx-2" aria-label="Instagram">
            <i class="fab fa-instagram"></i>
          </a>
        </div>
        <p>&copy; {{ currentYear }} Acuro</p>
      </div>
    </div>
  </footer>
</template>

<script>
export default {
  name: 'AcuroFooter',
  computed: {
    currentYear() {
      return new Date().getFullYear();
    }
  }
};
</script>

<template>
  <nav class="bg-white shadow-md">
    <div class="container mx-auto flex justify-between items-center px-4 py-8">
      <router-link to="/">
        <img src="@/assets/acuro-labs-logo.svg" alt="Acuro Logo" class="h-10" />
      </router-link>
      <div class="hidden md:flex text-primary space-x-8">
        <router-link
          v-for="link in links"
          :key="link.name"
          :to="link.path"
          class="hover:text-secondary transition-all"
          :class="{ 'text-secondary font-bold': isActive(link.path) }"
        >
          {{ link.name }}
        </router-link>
      </div>
      <div class="md:hidden">
        <button @click="toggleMenu" class="focus:outline-none">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path
              v-if="!menuOpen"
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M4 6h16M4 12h16m-7 6h7"
            />
            <path
              v-else
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M6 18L18 6M6 6l12 12"
            />
          </svg>
        </button>
      </div>
    </div>
    <div v-if="menuOpen" class="md:hidden bg-primary text-white space-y-4 py-4 px-4 shadow-md">
      <router-link
        v-for="link in links"
        :key="link.name"
        :to="link.path"
        class="block hover:text-secondary transition-all"
        :class="{ 'text-secondary font-bold': isActive(link.path) }"
        @click="toggleMenu"
      >
        {{ link.name }}
      </router-link>
    </div>
  </nav>
</template>

<script>
export default {
  name: 'NavBar',
  data() {
    return {
      menuOpen: false
    };
  },
  computed: {
    links() {
      return [
        { name: 'Home', path: '/' },
        { name: 'Upload File', path: '/upload' },
        { name: 'Documents', path: '/documents' }
      ];
    }
  },
  methods: {
    toggleMenu() {
      this.menuOpen = !this.menuOpen;
    },
    isActive(path) {
      return this.$route.path === path;
    }
  }
};
</script>

import { createApp } from 'vue';
import App from './App.vue'; // Main Vue app component
import router from './router'; // Vue Router setup

const app = createApp(App);

app.use(router); // Use Vue Router
app.mount('#app'); // Mount Vue app to the `#app` div

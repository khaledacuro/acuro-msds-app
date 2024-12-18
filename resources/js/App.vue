<template>

    <head>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    </head>
    <div id="app" class="flex flex-col min-h-screen">
        <NavBar />

        <div class="flex-grow mx-auto py-10 w-11/12">
            <router-view />
        </div>

        <AcuroFooter />
    </div>
</template>

<script>
import NavBar from './components/NavBar.vue'
import AcuroFooter from './components/AcuroFooter.vue'

export default {
    name: 'App',
    components: {
        NavBar,
        AcuroFooter
    }
}
</script>

<style scoped>
/* Add your custom styles here */
</style>
<template>
    <div id="app">
        <router-view></router-view>
    </div>
</template>

<script setup>
// No setup needed for this basic root component
</script>

<style>
/* Global styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    background: linear-gradient(180deg, #4a6ba6 0%, #2b3c5e 100%);
    min-height: 100vh;
}

#app {
    min-height: 100vh;
}
</style> 
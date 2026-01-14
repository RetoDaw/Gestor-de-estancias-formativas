<script setup>
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue-3/dist/bootstrap-vue-3.css';

import "./assets/css/nav.css";
import "./assets/css/main.css";
import { ref, defineAsyncComponent } from 'vue'
const Alumno = defineAsyncComponent(()=>import('./alumno/alumno.vue'));
const Crear = defineAsyncComponent(()=>import('./crear/crear.vue'));
const vistaActiva = ref(null)
function cambiar(vista){
    vistaActiva.value = vistaActiva.value === vista ? null : vista;
}
</script>
<template>
    <body class="container mt-4">
        <nav class="row mb-5 p-3">
            <h1 class="mb-4">Gestion de estancias formativas</h1>
            <button class="col-2" @click="cambiar('alumno')">Info de alumno</button>
            <button class="col-2 ms-3" @click="cambiar('crear')">Crear</button>
        </nav>
        <main class="row" v-if="vistaActiva !== null">
            <Alumno v-if="vistaActiva === 'alumno'"/>
            <Crear v-if="vistaActiva === 'crear'"/>
        </main>
    </body>
</template>
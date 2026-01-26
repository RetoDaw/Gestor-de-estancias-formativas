<script setup>
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue-3/dist/bootstrap-vue-3.css';
import axios from '@/axios'
import "./assets/css/nav.css";
import "./assets/css/main.css";
import { ref, defineAsyncComponent, onMounted } from 'vue'
const Alumno = defineAsyncComponent(()=>import('./alumno/alumno.vue'));
const Crear = defineAsyncComponent(()=>import('./crear/crear.vue'));
const Cuaderno = defineAsyncComponent(()=>import('./cuaderno/cuaderno.vue'));
const Sesion = defineAsyncComponent(()=>import('./logout.vue'));
const vistaActiva = ref(null)
let esAdmin_v = ref(false)

let token = localStorage.getItem('token')

function cambiar(vista){
    vistaActiva.value = vistaActiva.value === vista ? null : vista;
}
    async function esAdmin(){
        try{
            const response = await axios.get('http://localhost:8000/api/esAdmin', {
                headers: {Authorization: `Bearer ${token}`}
            });
           esAdmin_v.value = response.data === 'ADMIN'
        }catch(error){
            console.error("Error validando el admin:", error)
        }
    }
    onMounted(() => {
        esAdmin();
    });
</script>
<template>
    <body class="container mt-4">
        <nav class="row mb-5 p-3">
            <h1 class="mb-4">Gestion de estancias formativas</h1>
            <button class="col-2" @click="cambiar('alumno')">Info de alumno</button>
            <button v-if="esAdmin_v" class="col-2 ms-3" @click="cambiar('crear')">Crear</button>
            <button class="col-2 ms-3" @click="cambiar('cuaderno')">Cuaderno</button>
            <button class="col-2 ms-3" @click="cambiar('cerr_sesion')">Cerrar Sesión</button>
        </nav>
        <main class="row" v-if="vistaActiva !== null">
            <Alumno v-if="vistaActiva === 'alumno'"/>
            <Crear v-if="vistaActiva === 'crear'"/>
            <Cuaderno v-if="vistaActiva === 'cuaderno'"/>
            <Sesion v-if="vistaActiva === 'cerr_sesion'"/>
            
        </main>
    </body>
</template>
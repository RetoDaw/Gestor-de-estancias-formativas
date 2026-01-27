<script setup>
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue-3/dist/bootstrap-vue-3.css';
import axios from '@/axios'
import "./assets/css/nav.css";
import "./assets/css/main.css";
import { ref, defineAsyncComponent, onMounted } from 'vue'
import apiService from '@/services/api.js'

const Alumno = defineAsyncComponent(()=>import('./alumno/alumno.vue'));
const Crear = defineAsyncComponent(()=>import('./crear/crear.vue'));
const Cuaderno = defineAsyncComponent(()=>import('./cuaderno/cuaderno.vue'));
const Sesion = defineAsyncComponent(()=>import('./logout.vue'));

const vistaActiva = ref(null)

const esAdmin = ref(false)
const esAlumno = ref(false)
const esTutorCentro = ref(false)
const esTutorEmpresa = ref(false)

const token = localStorage.getItem('token')

function cambiar(vista){
    vistaActiva.value = vistaActiva.value === vista ? null : vista;
}

async function verificarTipoUsuario() {
    try {
        const responseAdmin = await axios.get('http://localhost:8000/api/esAdmin', {
            headers: {Authorization: `Bearer ${token}`}
        });
        esAdmin.value = responseAdmin.data === true;

        // Si no es admin, verificar otros tipos de usuario
        if (!esAdmin.value) {
            const esAlumnoResponse = await apiService.tipoUsuario('alumno');
            const esTutorCentroResponse = await apiService.tipoUsuario('tutor_centro');
            const esTutorEmpResponse = await apiService.tipoUsuario('tutor_empresa');
            
            esAlumno.value = esAlumnoResponse;
            esTutorCentro.value = esTutorCentroResponse;
            esTutorEmpresa.value = esTutorEmpResponse;
        }
    } catch (error) {
        console.error("Error validando el tipo de usuario:", error)
    }
}

onMounted(() => {
    verificarTipoUsuario();
});
</script>

<template>
    <body class="container mt-4">
        <nav class="row mb-5 p-3">
            <h1 class="mb-4">Gestión de estancias formativas</h1>
            
            <button v-if="esAdmin" class="col-2" @click="cambiar('crear')">Crear</button>
            
            <template v-if="esAlumno">
                <button class="col-2" @click="cambiar('alumno')">Mi información</button>     
                <button class="col-2 ms-3" @click="cambiar('cuaderno')">Cuaderno</button>          
            </template>
            <template v-if="esTutorCentro">
                <button class="col-2" @click="cambiar('alumno')">Info de alumno</button>
                <button class="col-2 ms-3" @click="cambiar('cuaderno')">Cuaderno</button>
            </template>
            
            <button v-if="esTutorEmpresa" class="col-2" @click="cambiar('alumno')">Info de alumno</button>
            
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
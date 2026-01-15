<script setup>
import { reactive, ref, defineAsyncComponent } from 'vue'
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue-3/dist/bootstrap-vue-3.css';
import "../assets/css/alumno.css";
import Tabla from './tabla.vue'



const Calendario = defineAsyncComponent(()=>import('./calendario.vue'));
const Empresa = defineAsyncComponent(()=>import('./empresa.vue'));
const Notas = defineAsyncComponent(()=>import('./notas.vue'));
const Seguimiento = defineAsyncComponent(()=>import('./seguimiento.vue'));

const vistaActiva = ref(null)

function cambiar(vista){
    vistaActiva.value = vistaActiva.value === vista ? null : vista;
}
const alumno = reactive({});
async function getUser(){
    try{
        const response = await fetch('http://localhost:8000/api/buscarUsuario?user_id=5');
        const data = await response.json();
        Object.assign(alumno, data);
        
    }catch(error){
        console.error('Error al buscar el alumno:', error);
    }
}
getUser();

</script>

<template>
    <div class="row">
        <div id="principal" class="row col-4">
            <div id="info" class="col-12">
                <h1>Informacion del alumno {{ alumno.nombre }}</h1>
                <Tabla :alumno="alumno"/>
            </div>
            <div id="opciones" class="row col mt-3">
                <button class="col-5 mb-2" @click="cambiar('calendario')">Ver calendario</button>
                <button class="col-5 ms-2 mb-2" @click="cambiar('empresa')">Ver empresa</button>
                <button class="col-5" @click="cambiar('notas')">Ver notas</button>
                <button class="col-5 ms-2" @click="cambiar('seguimiento')">Ver seguimiento</button>
            </div>
        </div>

        <div id="secundario" class="col-8 row">
            <Calendario v-if="vistaActiva === 'calendario'"/>
            <Empresa v-if="vistaActiva === 'empresa'"/>
            <Notas v-if="vistaActiva === 'notas'"/>
            <Seguimiento v-if="vistaActiva === 'seguimiento'"/>
        </div>
    </div>
</template>

<style scoped>
</style>
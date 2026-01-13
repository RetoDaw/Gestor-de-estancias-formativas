<script setup>
import { reactive, ref, defineAsyncComponent } from 'vue'
import Tabla from './tabla.vue'

const alumno = reactive({
    nombre: 'Aaron',
    apellidos: 'Jimenez Teixeira',
    email: 'aaron@aaron.com',
    telefono: '292929299',
    grado: 'Desarrollo Web'
})

const Calendario = defineAsyncComponent(()=>import('./calendario.vue'));
const calendario = ref(false);
const Empresa = defineAsyncComponent(()=>import('./empresa.vue'));
const empresa = ref(false);
const Notas = defineAsyncComponent(()=>import('./notas.vue'));
const notas = ref(false);
const Seguimiento = defineAsyncComponent(()=>import('./seguimiento.vue'));
const seguimiento = ref(false);

const vistaActiva = ref(null)

function cambiar(vista){
    vistaActiva.value = vistaActiva.value === vista ? null : vista;
}

</script>

<template>
    <div id="principal">
        <div id="info">
            <h1>Informacion del alumno {{ alumno.nombre }}</h1>
            <Tabla :alumno="alumno"/>
        </div>
        <div id="opciones">
            <button @click="cambiar('calendario')">Ver calendario</button>
            <button @click="cambiar('empresa')">Ver empresa</button>
            <button @click="cambiar('notas')">Ver notas</button>
            <button @click="cambiar('seguimiento')">Ver seguimiento</button>
        </div>
    </div>

    <div id="secundario">
        <Calendario v-if="vistaActiva === 'calendario'"/>
        <Empresa v-if="vistaActiva === 'empresa'"/>
        <Notas v-if="vistaActiva === 'notas'"/>
        <Seguimiento v-if="vistaActiva === 'seguimiento'"/>
    </div>

</template>

<style scoped>
</style>
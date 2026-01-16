<script setup>
import { ref, defineAsyncComponent, onMounted } from 'vue'
import api from '@/services/api'
const Entrega = defineAsyncComponent(() => import('./entrega.vue'));
const VerEntrega = defineAsyncComponent(() => import('./verEntregas.vue'));
const Subir = defineAsyncComponent(() => import('./subir.vue'));
import "../assets/css/cuaderno.css";


const entregaActiva = ref(null)
function cambiarVista(vista) {
    entregaActiva.value = entregaActiva.value === vista ? null : vista;
}

const esTutorCentro = ref(false)
const esAlumno = ref(false)

onMounted(async () => {
    esTutorCentro.value = await api.tipoUsuario('tc')
    esAlumno.value = await api.tipoUsuario('al')
})
</script>
<template>
    <h1>Entrega de Cuaderno</h1>
    <div id="opcion">
        <div v-if="esTutorCentro">
            <button class="col-2" @click="cambiarVista('entrega')">Crear entrega</button>
            <button class="col-2" @click="cambiarVista('verEntrega')">Ver entregas</button>
        </div>
        <button v-if="esAlumno" class="col-2" @click="cambiarVista('subir')">Subir cuaderno</button>
    </div>
    <div id="datos">
        <Entrega v-if="entregaActiva === 'entrega'" />
        <VerEntrega v-if="entregaActiva === 'verEntrega'" />
        <Subir v-if="(entregaActiva === 'subir')" />
    </div>
</template>
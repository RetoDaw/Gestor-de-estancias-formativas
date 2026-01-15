<script setup>
import { ref, defineAsyncComponent } from 'vue'
const Entrega = defineAsyncComponent(() => import('./entrega.vue'));
const VerEntrega = defineAsyncComponent(() => import('./verEntregas.vue'));
const Subir = defineAsyncComponent(() => import('./subir.vue'));

const entregaActiva = ref(null)
function cambiarVista(vista) {
    entregaActiva.value = entregaActiva.value === vista ? null : vista;
}
</script>
<template>
    <h1>Entrega de Cuaderno</h1>
    <div id="opcion">
        <button class="col-2" @click="cambiarVista('entrega')">Crear entrega</button>
        <button class="col-2" @click="cambiarVista('verEntrega')">Ver entregas</button>
        <button class="col-2" @click="cambiarVista('subir')">Subir cuaderno</button>
    </div>
    <div id="datos">
        <Entrega v-if="entregaActiva === 'entrega'" />
        <VerEntrega v-if="entregaActiva === 'verEntrega'" />
        <Subir v-if="entregaActiva === 'subir'" />
    </div>
</template>
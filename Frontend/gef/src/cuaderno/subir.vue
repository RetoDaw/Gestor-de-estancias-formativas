<script setup>
import { ref, defineAsyncComponent } from 'vue'
const Entregar = defineAsyncComponent(() => import('./subirEntrega.vue'));
const token = {
    user:{
        id:1,
        grado:{
            id:1,
            nombre:"daw"
        },
        nombre:"Aaron"
    }
}
const entregas = [
    {
        id:1,
        grado:{
            id:1,
            nombre:"daw"
        }
    },
    {
        id:2,
        grado:{
            id:3,
            nombre:"asir"
        }
    }
]
const habilitado = ref(null)
function habilitar(vista) {
    habilitado.value = habilitado.value === vista ? null : vista;
}
</script>
<template>
    <div v-for="entrega in entregas" :key="entrega.id" id="entrega">
        <button v-if="entrega.grado.id === token.user.grado.id" @click="habilitar('entrega')">
            Tienes una entrega pendiente
        </button>
    </div>
    <div id="subirEntrega">
        <Entregar v-if="habilitado === 'entrega'"/>
    </div>
</template>
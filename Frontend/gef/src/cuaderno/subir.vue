<script setup>
import { ref, onMounted, defineAsyncComponent } from 'vue'
import api from '@/services/api'

const Entregar = defineAsyncComponent(() => import('./subirEntrega.vue'))

const entregas = ref([])
const usuario = ref(null)
const habilitado = ref(null)
const entregaSeleccionada = ref(null)
const loading = ref(true)

onMounted(async () => {
    try {
        // Obtener usuario actual
        const userResponse = await api.getUser()
        usuario.value = userResponse.data

        // Obtener entregas pendientes para este alumno
        const entregasResponse = await api.getEntregas()
        entregas.value = entregasResponse.data
    } catch (err) {
        console.error('Error al cargar datos:', err)
    } finally {
        loading.value = false
    }
})

function habilitar(entrega) {
    if (habilitado.value === entrega.id_entrega) {
        habilitado.value = null
        entregaSeleccionada.value = null
    } else {
        habilitado.value = entrega.id_entrega
        entregaSeleccionada.value = entrega
    }
}

function cerrarFormulario() {
    habilitado.value = null
    entregaSeleccionada.value = null
    // Recargar entregas después de subir
    cargarEntregas()
}

async function cargarEntregas() {
    try {
        const response = await api.getEntregas()
        entregas.value = response.data
    } catch (err) {
        console.error('Error al cargar entregas:', err)
    }
}
</script>

<template>
    <div v-if="loading">
        <p>Cargando...</p>
    </div>
    
    <div v-else>
        <div v-if="entregas.length === 0">
            <p>No tienes entregas pendientes</p>
        </div>

        <div v-else>
            <div v-for="entrega in entregas" :key="entrega.id_entrega" class="entrega-item">
                <button @click="habilitar(entrega)" class="btn-entrega">
                    Tienes una entrega pendiente para {{ entrega.grado.nombre }}
                </button>
            </div>
        </div>

        <div v-if="habilitado" id="subirEntrega">
            <Entregar 
                :entrega="entregaSeleccionada" 
                @cerrar="cerrarFormulario"
            />
        </div>
    </div>
</template>

<style scoped>
.entrega-item {
    margin: 1rem 0;
}

.btn-entrega {
    padding: 1rem;
    background-color: #ffc107;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 1rem;
    width: 100%;
    text-align: left;
}

.btn-entrega:hover {
    background-color: #ffb300;
}

#subirEntrega {
    margin-top: 2rem;
    padding: 1rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    background-color: #f9f9f9;
}
</style>
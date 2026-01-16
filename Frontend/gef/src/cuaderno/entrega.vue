<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'
import "../assets/css/entrega.css";


const grados = ref([])
const gradoSeleccionado = ref('')
const loading = ref(false)
const mensaje = ref('')
const error = ref('')

onMounted(async () => {
    try {
        const response = await api.getGrados()
        grados.value = response.data
    } catch (err) {
        error.value = 'Error al cargar los grados'
        console.error(err)
    }
})

async function crearEntrega(e) {
    e.preventDefault()
    
    if (!gradoSeleccionado.value) {
        error.value = 'Debes seleccionar un grado'
        return
    }

    loading.value = true
    error.value = ''
    mensaje.value = ''

    try {
        await api.crearEntrega(gradoSeleccionado.value)
        mensaje.value = 'Entrega creada exitosamente'
        gradoSeleccionado.value = ''
    } catch (err) {
        error.value = err.response?.data?.message || 'Error al crear la entrega'
        console.error(err)
    } finally {
        loading.value = false
    }
}
</script>

<template>
    <form @submit="crearEntrega">
        <p>¿Para qué grado quieres hacer la entrega?</p>
        
        <select v-model="gradoSeleccionado" name="grado" id="grado" required>
            <option value="" disabled>Selecciona un grado</option>
            <option v-for="grado in grados" :key="grado.id_grado" :value="grado.id_grado">
                {{ grado.nombre }}
            </option>
        </select>

        <button type="submit" :disabled="loading">
            {{ loading ? 'Creando...' : 'Crear entrega' }}
        </button>

        <p v-if="mensaje" class="mensaje-exito">{{ mensaje }}</p>
        <p v-if="error" class="mensaje-error">{{ error }}</p>
    </form>
</template>

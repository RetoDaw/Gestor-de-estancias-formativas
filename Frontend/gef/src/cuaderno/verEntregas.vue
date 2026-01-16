<script setup>
import { ref, onMounted, computed } from 'vue'
import api from '@/services/api'
import "../assets/css/verEntregas.css";


const cuadernos = ref([])
const grados = ref([])
const gradoSeleccionado = ref('')
const loading = ref(true)
const error = ref('')

onMounted(async () => {
    try {
        // Cargar grados
        const gradosResponse = await api.getGrados()
        grados.value = gradosResponse.data

        // Cargar todos los cuadernos
        await cargarCuadernos()
    } catch (err) {
        error.value = 'Error al cargar los datos'
        console.error(err)
    } finally {
        loading.value = false
    }
})

async function cargarCuadernos() {
    try {
        loading.value = true
        const idGrado = gradoSeleccionado.value || null
        const response = await api.getCuadernos(idGrado)
        cuadernos.value = response.data
    } catch (err) {
        error.value = 'Error al cargar los cuadernos'
        console.error(err)
    } finally {
        loading.value = false
    }
}

// Filtrar cuadernos cuando cambia el grado seleccionado
async function cambiarFiltro() {
    await cargarCuadernos()
}

const cuadernosFiltrados = computed(() => {
    if (!gradoSeleccionado.value) {
        return cuadernos.value
    }
    return cuadernos.value.filter(c => c.grado.id === parseInt(gradoSeleccionado.value))
})

function formatearFecha(fecha) {
    const date = new Date(fecha)
    return date.toLocaleDateString('es-ES')
}
</script>

<template>
    <div>
        <form>
            <label for="grado">Filtrar por grado:</label>
            <select v-model="gradoSeleccionado" id="grado" @change="cambiarFiltro">
                <option value="">Todos los grados</option>
                <option v-for="grado in grados" :key="grado.id_grado" :value="grado.id_grado">
                    {{ grado.nombre }}
                </option>
            </select>
        </form>

        <div v-if="loading" class="loading">
            <p>Cargando cuadernos...</p>
        </div>

        <div v-else-if="error" class="error">
            <p>{{ error }}</p>
        </div>

        <div v-else-if="cuadernosFiltrados.length === 0" class="sin-datos">
            <p>No hay cuadernos entregados</p>
        </div>

        <table v-else>
            <thead>
                <tr>
                    <th>Alumno</th>
                    <th>Grado</th>
                    <th>Cuaderno</th>
                    <th>Fecha de entrega</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="cuaderno in cuadernosFiltrados" :key="cuaderno.id">
                    <td>{{ cuaderno.alumno.nombre }} {{ cuaderno.alumno.apellidos }}</td>
                    <td>{{ cuaderno.grado.nombre }}</td>
                    <td>
                        <a :href="cuaderno.archivo_pdf" target="_blank" class="link-cuaderno">
                            Ver Cuaderno
                        </a>
                    </td>
                    <td>{{ formatearFecha(cuaderno.fecha_entrega) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

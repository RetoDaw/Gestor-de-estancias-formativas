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

const cuadernoNotaActivo = ref(null);
const notaCuaderno = ref(null);

function mostrarInputNota(idCuaderno) {
    cuadernoNotaActivo.value = idCuaderno
    notaCuaderno.value = null
}

function cancelarNota() {
    cuadernoNotaActivo.value = null
    notaCuaderno.value = null
}

async function guardarNota(idCuaderno) {
    if (notaCuaderno.value === null) {
        alert('Tiene que haber valor para guardar la nota')
        return
    }

    if (notaCuaderno.value < 0) {
        alert('La nota tiene que ser de al menos 0')
        return
    }

    if (notaCuaderno.value > 10) {
        alert('La nota no puede ser mayor a 10')
        return
    }
    try {
        await api.guardarNotaCuaderno(idCuaderno, notaCuaderno.value)
        cancelarNota()
    } catch (e) {
        console.error(e)
        alert('Error al guardar la nota')
    }
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
                    <th>Nota de cuaderno</th>
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
                    <td>
                        <button v-if="cuadernoNotaActivo !== cuaderno.id"
                        @click="mostrarInputNota(cuaderno.id)">
                            Poner nota
                        </button>

                        <div v-else>
                            <input type="number" min="0" max="10" required="Debes poner una nota"
                            v-model.number="notaCuaderno" placeholder="Nota cuaderno"/>

                            <button @click="guardarNota(cuaderno.id)">Guardar</button>
                            <button @click="cancelarNota">Cancelar</button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

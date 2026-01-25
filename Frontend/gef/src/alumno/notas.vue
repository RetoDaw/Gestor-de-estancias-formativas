<script setup>
import { ref, onMounted, computed } from 'vue'
import api from '@/services/api'

const props = defineProps({
    idAlumno: {
        type: Number,
        required: true
    }
});

const asignaturas = ref([])
const tecnicas = ref([])
const transversales = ref([])
const tieneEstancia = ref(false)
const editando = ref(null) // { id_asignatura: X, nota: Y }
const notaTemporal = ref('')
const guardando = ref(false)
const esTutorCentro = ref(false)
const mensaje = ref({ tipo: '', texto: '' })

function mostrarDato(dato) {
    if (dato == null || dato === undefined) {
        return 'No hay nota'
    }
    return dato;
}

async function verificarTipoUsuario() {
    try {
        const response = await api.tipoUsuario('tutor_centro')
        esTutorCentro.value = response
    } catch (err) {
        console.error('Error al verificar tipo de usuario:', err)
        esTutorCentro.value = false
    }
}

async function getNotas() {
    try {
        const response = await api.getNotas(props.idAlumno)
        console.log('Respuesta de API:', response.data)
        asignaturas.value = response.data.asignaturas
        tecnicas.value = response.data.tecnicas
        transversales.value = response.data.transversales 
        tieneEstancia.value = response.data.tiene_estancia
    } catch (err) {
        console.error('Error al cargar notas:', err)
        asignaturas.value = []
        tecnicas.value = []
        transversales.value = []
    }
}

function getNotaTransversalPromedio() {
    if (transversales.value.length === 0) return null
    
    const notasValidas = transversales.value
        .filter(t => t.nota !== null && t.nota !== undefined)
        .map(t => parseFloat(t.nota))
    
    if (notasValidas.length === 0) return null
    
    const promedio = notasValidas.reduce((sum, n) => sum + n, 0) / notasValidas.length
    return promedio.toFixed(2)
}

function getNotaTecnicaAsignatura(nombreAsignatura) {
    const resultado = tecnicas.value.find(t => t.asignatura_nombre === nombreAsignatura)
    return resultado?.nota ?? null
}

function iniciarEdicion(asignatura) {
    if (!esTutorCentro.value) {
        mostrarMensaje('error', 'Solo los tutores de centro pueden modificar notas')
        return
    }
    
    editando.value = {
        id_asignatura: asignatura.id_asignatura,
        nota: asignatura.nota
    }
    notaTemporal.value = asignatura.nota ?? ''
}

function cancelarEdicion() {
    editando.value = null
    notaTemporal.value = ''
    mensaje.value = { tipo: '', texto: '' }
}

async function guardarNota(asignatura) {
    // Validar formato
    const nota = parseFloat(notaTemporal.value)
    
    if (isNaN(nota)) {
        mostrarMensaje('error', 'Debe introducir un número válido')
        return
    }
    
    if (nota < 0 || nota > 10) {
        mostrarMensaje('error', 'La nota debe estar entre 0 y 10')
        return
    }
    
    guardando.value = true
    
    try {
        const response = await api.actualizarNotaCentro(
            props.idAlumno,
            asignatura.id_asignatura,
            nota
        )
        
        // Actualizar la nota en el array local
        const index = asignaturas.value.findIndex(
            a => a.id_asignatura === asignatura.id_asignatura
        )
        if (index !== -1) {
            asignaturas.value[index].nota = nota
        }
        
        mostrarMensaje('exito', 'Nota actualizada correctamente')
        editando.value = null
        notaTemporal.value = ''
        
    } catch (err) {
        console.error('Error al guardar nota:', err)
        const errorMsg = err.response?.data?.message || 'Error al guardar la nota'
        mostrarMensaje('error', errorMsg)
    } finally {
        guardando.value = false
    }
}

function mostrarMensaje(tipo, texto) {
    mensaje.value = { tipo, texto }
    setTimeout(() => {
        mensaje.value = { tipo: '', texto: '' }
    }, 4000)
}

onMounted(async () => {
    await verificarTipoUsuario()
    await getNotas()
})

async function calcularNota() {
    if (!props.idAlumno) return

    for (const asignatura of asignaturas.value) {
        if (asignatura.nota !== null) {
            try {
                const response = await api.getNotaFinalAlumno(
                    props.idAlumno,
                    asignatura.id_asignatura
                )
                asignatura.nota = response.data.nota_final
            } catch (err) {
                asignatura.nota = ''
            }
        }
    }
}
</script>

<template>
    <div class="notas-container">
        <div class="header">
            <h1>Notas del Alumno</h1>
        </div>

        <!-- Mensaje de feedback -->
        <transition name="fade">
            <div v-if="mensaje.texto" :class="['mensaje', `mensaje-${mensaje.tipo}`]">
                {{ mensaje.texto }}
            </div>
        </transition>
        
        <div v-if="!tieneEstancia" class="alerta alerta-warning">
            El alumno no tiene una estancia asignada. Las notas de empresa no están disponibles.
        </div>

        <div class="tabla-wrapper">
            <table class="tabla-notas">
                <thead>
                    <tr>
                        <th rowspan="2" class="th-asignatura">Asignatura</th>
                        <th colspan="4" class="th-grupo">Evaluaciones</th>
                    </tr>
                    <tr>
                        <th>Nota Centro</th>
                        <th>Com. Técnicas</th>
                        <th>Com. Trans.</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="asignatura in asignaturas" :key="asignatura.id_asignatura" 
                        :class="{'fila-editando': editando?.id_asignatura === asignatura.id_asignatura}">
                        
                        <td class="td-asignatura">{{ asignatura.nombre }}</td>
                        
                        <!-- Nota Centro (editable) -->
                        <td class="td-nota">
                            <div v-if="editando?.id_asignatura === asignatura.id_asignatura" class="input-grupo">
                                <input 
                                    v-model="notaTemporal"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    max="10"
                                    class="input-nota"
                                    @keyup.enter="guardarNota(asignatura)"
                                    @keyup.escape="cancelarEdicion"
                                    autofocus
                                >
                                <div class="botones-inline">
                                    <button 
                                        @click="guardarNota(asignatura)"
                                        :disabled="guardando"
                                        class="btn-guardar"
                                        title="Guardar (Enter)"
                                    >
                                    </button>
                                    <button 
                                        @click="cancelarEdicion"
                                        :disabled="guardando"
                                        class="btn-cancelar"
                                        title="Cancelar (Esc)"
                                    >
                                    </button>
                                </div>
                            </div>
                            <span v-else class="nota-display">
                                {{ mostrarDato(asignatura.nota) }}
                            </span>
                        </td>
                        
                        <td class="td-nota">{{ mostrarDato(getNotaTecnicaAsignatura(asignatura.nombre)) }}</td>
                        <td class="td-nota">{{ mostrarDato(getNotaTransversalPromedio() * 2.5) }}</td>
                        
                        <!-- Botón de acción -->
                        <td class="td-accion">
                            <button 
                                v-if="editando?.id_asignatura !== asignatura.id_asignatura"
                                @click="iniciarEdicion(asignatura)"
                                :disabled="!esTutorCentro"
                                class="btn-editar"
                                :title="esTutorCentro ? 'Editar nota' : 'Solo tutores de centro pueden editar'"
                            >
                                Editar
                            </button>
                            <span v-else class="texto-editando">Editando...</span>
                        </td>
                    </tr>

                    <tr v-if="asignaturas.length === 0">
                        <td colspan="5" class="td-vacio">No hay asignaturas para mostrar</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="acciones-footer">
            <button @click="calcularNota" class="btn-calcular">
                Calcular Nota Final
            </button>
        </div>
    </div>
</template>
<style src="../assets/css/verNotas.css">

</style>

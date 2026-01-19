<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import "../assets/css/calendario.css"
import apiService from '@/services/api.js'

const props = defineProps({
    alumnoId: {
        type: Number,
        default: null
    }
})

// Estado
const cargando = ref(true)
const error = ref(null)
const tieneHorario = ref(false)
const modoEdicion = ref(false)
const esTutorCentro = ref(false)

// Datos del horario
const horarioData = reactive({
    id_estancia: null,
    fecIni: '',
    fecFin: '',
    horario: {
        lunes: { horaIni: [], horaFin: [] },
        martes: { horaIni: [], horaFin: [] },
        miercoles: { horaIni: [], horaFin: [] },
        jueves: { horaIni: [], horaFin: [] },
        viernes: { horaIni: [], horaFin: [] }
    }
})

// Formulario para crear/editar
const formulario = reactive({
    fecha_inicio: '',
    fecha_fin: '',
    horario: [
        { dia: 'Lunes', franjas: [{ hora_inicial: 8, hora_final: 15 }] },
        { dia: 'Martes', franjas: [{ hora_inicial: 8, hora_final: 15 }] },
        { dia: 'Miercoles', franjas: [{ hora_inicial: 8, hora_final: 15 }] },
        { dia: 'Jueves', franjas: [{ hora_inicial: 8, hora_final: 15 }] },
        { dia: 'Viernes', franjas: [{ hora_inicial: 8, hora_final: 15 }] }
    ]
})

const diasSemana = ['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes']

// Computed
const puedeCrearEditar = computed(() => esTutorCentro.value)
const mostrarFormulario = computed(() => modoEdicion.value || (!tieneHorario.value && esTutorCentro.value))

// Métodos
async function verificarTipoUsuario() {
    try {
        esTutorCentro.value = await apiService.tipoUsuario('tutor_centro')
    } catch (err) {
        console.error('Error al verificar tipo de usuario:', err)
    }
}

async function cargarHorario() {
    try {
        cargando.value = true
        error.value = null
        
        const response = await apiService.getHorarioAlumno(props.alumnoId)
        
        if (response.data.success) {
            tieneHorario.value = true
            Object.assign(horarioData, response.data.data)
        }
    } catch (err) {
        if (err.response?.status === 404) {
            tieneHorario.value = false
        } else {
            error.value = 'Error al cargar el horario'
            console.error('Error al cargar horario:', err)
        }
    } finally {
        cargando.value = false
    }
}

function ponerHorario(dia) {
    if (!dia || !dia.horaIni || !dia.horaFin) return 'Sin horario'
    
    let horas = []
    for (let i = 0; i < dia.horaIni.length; i++) {
        horas.push(`${dia.horaIni[i]}:00 - ${dia.horaFin[i]}:00`)
    }
    return horas.join(', ')
}

function iniciarEdicion() {
    // Cargar datos actuales en el formulario
    formulario.fecha_inicio = convertirFechaParaInput(horarioData.fecIni)
    formulario.fecha_fin = convertirFechaParaInput(horarioData.fecFin)
    
    // Convertir horario al formato del formulario
    formulario.horario = diasSemana.map(dia => {
        const diaKey = dia.toLowerCase()
        const horarioDia = horarioData.horario[diaKey]
        
        if (horarioDia && horarioDia.horaIni && horarioDia.horaIni.length > 0) {
            const franjas = []
            for (let i = 0; i < horarioDia.horaIni.length; i++) {
                franjas.push({
                    hora_inicial: horarioDia.horaIni[i],
                    hora_final: horarioDia.horaFin[i]
                })
            }
            return { dia, franjas }
        }
        
        return { dia, franjas: [{ hora_inicial: 8, hora_final: 15 }] }
    })
    
    modoEdicion.value = true
}

function cancelarEdicion() {
    modoEdicion.value = false
    resetFormulario()
}

function resetFormulario() {
    formulario.fecha_inicio = ''
    formulario.fecha_fin = ''
    formulario.horario = diasSemana.map(dia => ({
        dia,
        franjas: [{ hora_inicial: 8, hora_final: 15 }]
    }))
}

function agregarFranja(indexDia) {
    formulario.horario[indexDia].franjas.push({
        hora_inicial: 8,
        hora_final: 15
    })
}

function eliminarFranja(indexDia, indexFranja) {
    if (formulario.horario[indexDia].franjas.length > 1) {
        formulario.horario[indexDia].franjas.splice(indexFranja, 1)
    }
}

function convertirFechaParaInput(fechaStr) {
    // Convierte "01/03/2026" a "2026-03-01"
    const partes = fechaStr.split('/')
    return `${partes[2]}-${partes[1]}-${partes[0]}`
}

async function guardarHorario() {
    try {
        cargando.value = true
        error.value = null
        
        const data = {
            fecha_inicio: formulario.fecha_inicio,
            fecha_fin: formulario.fecha_fin,
            horario: formulario.horario
        }
        
        let response
        if (tieneHorario.value) {
            // Actualizar
            data.id_alumno = props.alumnoId
            response = await apiService.actualizarHorario(horarioData.id_estancia, data)
        } else {
            // Crear
            data.id_alumno = props.alumnoId
            response = await apiService.crearHorario(data)
        }
        
        if (response.data.success) {
            tieneHorario.value = true
            Object.assign(horarioData, response.data.data)
            modoEdicion.value = false
            resetFormulario()
        }
    } catch (err) {
        error.value = err.response?.data?.message || 'Error al guardar el horario'
        console.error('Error al guardar horario:', err)
    } finally {
        cargando.value = false
    }
}

onMounted(async () => {
    await verificarTipoUsuario()
    await cargarHorario()
})
</script>

<template>
    <div class="horario-container">
        <h1>Horario de Prácticas</h1>
        
        <!-- Mensaje de error -->
        <div v-if="error" class="alert alert-danger" role="alert">
            {{ error }}
        </div>
        
        <!-- Cargando -->
        <div v-if="cargando" class="text-center">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
        </div>
        
        <!-- Sin horario asignado -->
        <div v-else-if="!tieneHorario && !mostrarFormulario" class="alert alert-info">
            <p class="mb-0">Este alumno aún no tiene un horario asignado.</p>
            <p v-if="!esTutorCentro" class="mb-0 mt-2">
                <small>Contacta con tu tutor de centro para que configure el horario.</small>
            </p>
        </div>
        
        <!-- Mostrar horario existente -->
        <div v-else-if="tieneHorario && !modoEdicion" id="calendario">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3>Horario Actual</h3>
                <button 
                    v-if="puedeCrearEditar" 
                    class="btn btn-primary btn-sm"
                    @click="iniciarEdicion"
                >
                    Editar Horario
                </button>
            </div>
            
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <td><b>Fecha de Inicio:</b></td>
                        <td>{{ horarioData.fecIni }}</td>
                    </tr>
                    <tr>
                        <td><b>Fecha de Fin:</b></td>
                        <td>{{ horarioData.fecFin }}</td>
                    </tr>
                    <tr v-for="(dia, nombre) in horarioData.horario" :key="nombre">
                        <td><b>{{ nombre.charAt(0).toUpperCase() + nombre.slice(1) }}:</b></td>
                        <td>{{ ponerHorario(dia) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Formulario crear/editar -->
        <div v-else-if="mostrarFormulario" class="formulario-horario">
            <h3>{{ tieneHorario ? 'Editar' : 'Crear' }} Horario</h3>
            
            <form @submit.prevent="guardarHorario">
                <!-- Fechas -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="fecha_inicio" class="form-label">Fecha de Inicio:</label>
                        <input 
                            type="date" 
                            id="fecha_inicio"
                            class="form-control" 
                            v-model="formulario.fecha_inicio"
                            required
                        >
                    </div>
                    <div class="col-md-6">
                        <label for="fecha_fin" class="form-label">Fecha de Fin:</label>
                        <input 
                            type="date" 
                            id="fecha_fin"
                            class="form-control" 
                            v-model="formulario.fecha_fin"
                            required
                        >
                    </div>
                </div>
                
                <!-- Horarios por día -->
                <div class="horarios-semanales">
                    <h4>Horarios Semanales</h4>
                    
                    <div 
                        v-for="(diaData, index) in formulario.horario" 
                        :key="diaData.dia"
                        class="dia-horario mb-3 p-3 border rounded"
                    >
                        <h5>{{ diaData.dia }}</h5>
                        
                        <div 
                            v-for="(franja, indexFranja) in diaData.franjas"
                            :key="indexFranja"
                            class="franja-horaria mb-2 d-flex align-items-center gap-2"
                        >
                            <div class="flex-grow-1">
                                <label class="form-label small mb-1">Hora Inicio:</label>
                                <input 
                                    type="number" 
                                    class="form-control form-control-sm"
                                    v-model.number="franja.hora_inicial"
                                    min="0"
                                    max="23"
                                    required
                                >
                            </div>
                            
                            <div class="flex-grow-1">
                                <label class="form-label small mb-1">Hora Fin:</label>
                                <input 
                                    type="number" 
                                    class="form-control form-control-sm"
                                    v-model.number="franja.hora_final"
                                    min="0"
                                    max="23"
                                    required
                                >
                            </div>
                            
                            <button 
                                type="button"
                                class="btn btn-danger btn-sm mt-4"
                                @click="eliminarFranja(index, indexFranja)"
                                :disabled="diaData.franjas.length === 1"
                            >
                                ✕
                            </button>
                        </div>
                        
                        <button 
                            type="button"
                            class="btn btn-secondary btn-sm mt-2"
                            @click="agregarFranja(index)"
                        >
                            + Añadir Franja
                        </button>
                    </div>
                </div>
                
                <!-- Botones -->
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-success" :disabled="cargando">
                        {{ cargando ? 'Guardando...' : 'Guardar Horario' }}
                    </button>
                    <button 
                        type="button" 
                        class="btn btn-secondary"
                        @click="cancelarEdicion"
                        v-if="tieneHorario"
                    >
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>
.horario-container {
    padding: 20px;
}

.formulario-horario {
    background-color: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
}

.dia-horario {
    background-color: white;
}

.franja-horaria {
    background-color: #f8f9fa;
    padding: 10px;
    border-radius: 4px;
}

#calendario table {
    width: 100%;
}

#calendario td {
    padding: 10px;
}

.spinner-border {
    width: 3rem;
    height: 3rem;
}
</style>
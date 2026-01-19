<script setup>
import { reactive, ref, defineAsyncComponent, onMounted } from 'vue'
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue-3/dist/bootstrap-vue-3.css';
import "../assets/css/alumno.css";
import Tabla from './tabla.vue'
import apiService from '@/services/api.js'

const Calendario = defineAsyncComponent(()=>import('./calendario.vue'));
const Empresa = defineAsyncComponent(()=>import('./empresa.vue'));
const Notas = defineAsyncComponent(()=>import('./notas.vue'));
const Seguimiento = defineAsyncComponent(()=>import('./seguimiento.vue'));

const vistaActiva = ref(null)
const alumno = reactive({});

// Estado para tutores
const esTutor = ref(false);
const listaGrados = ref([]);
const listaAlumnos = ref([]);
const gradoSeleccionado = ref(null);
const alumnoSeleccionado = ref(null);

// Estado general
const cargando = ref(true);
const cargandoAlumnos = ref(false);
const error = ref(null);

function cambiar(vista){
    vistaActiva.value = vistaActiva.value === vista ? null : vista;
}

async function verificarTipoUsuario() {
    try {
        const esAlumno = await apiService.tipoUsuario('al');
        const esTutorCentro = await apiService.tipoUsuario('tc');
        
        esTutor.value = esTutorCentro || !esAlumno;
        
        if (esTutor.value) {
            await cargarListaGrados();
        } else {
            await cargarDatosAlumno();
        }
    } catch (err) {
        console.error('Error al verificar tipo de usuario:', err);
        error.value = 'Error al verificar el tipo de usuario';
    } finally {
        cargando.value = false;
    }
}

async function cargarListaGrados() {
    try {
        const response = await apiService.listarGradosConAlumnos();
        listaGrados.value = response.data;
        
        // Si hay grados, seleccionar el primero automáticamente
        if (listaGrados.value.length > 0) {
            gradoSeleccionado.value = listaGrados.value[0].id_grado;
            await onGradoChange();
        }
    } catch (err) {
        console.error('Error al cargar lista de grados:', err);
        error.value = 'Error al cargar la lista de grados';
    }
}

async function onGradoChange() {
    if (!gradoSeleccionado.value) {
        listaAlumnos.value = [];
        alumnoSeleccionado.value = null;
        // Limpiar datos del alumno
        Object.keys(alumno).forEach(key => delete alumno[key]);
        return;
    }
    
    try {
        cargandoAlumnos.value = true;
        const response = await apiService.listarAlumnosPorGrado(gradoSeleccionado.value);
        listaAlumnos.value = response.data;
        
        // Seleccionar el primer alumno automáticamente
        if (listaAlumnos.value.length > 0) {
            alumnoSeleccionado.value = listaAlumnos.value[0].id_usuario;
            await cargarDatosAlumno(alumnoSeleccionado.value);
        } else {
            alumnoSeleccionado.value = null;
            // Limpiar datos del alumno
            Object.keys(alumno).forEach(key => delete alumno[key]);
        }
        
        // Resetear vista activa
        vistaActiva.value = null;
    } catch (err) {
        console.error('Error al cargar alumnos del grado:', err);
        error.value = 'Error al cargar los alumnos del grado seleccionado';
    } finally {
        cargandoAlumnos.value = false;
    }
}

async function cargarDatosAlumno(userId = null) {
    try {
        cargando.value = true;
        let response;
        
        if (userId) {
            // Cargar alumno específico (para tutores)
            response = await apiService.getAlumno(userId);
        } else {
            // Cargar datos del usuario autenticado
            response = await apiService.getAuthUser();
        }
        
        // Limpiar el objeto alumno
        Object.keys(alumno).forEach(key => delete alumno[key]);
        
        // Asignar nuevos datos
        Object.assign(alumno, response.data);
    } catch (err) {
        console.error('Error al cargar datos del alumno:', err);
        error.value = 'Error al cargar los datos del alumno';
    } finally {
        cargando.value = false;
    }
}

async function onAlumnoChange() {
    if (alumnoSeleccionado.value) {
        await cargarDatosAlumno(alumnoSeleccionado.value);
        // Resetear la vista activa al cambiar de alumno
        vistaActiva.value = null;
    }
}

onMounted(() => {
    verificarTipoUsuario();
});
</script>

<template>
    <div class="row">
        <!-- Mensaje de error -->
        <div v-if="error" class="alert alert-danger" role="alert">
            {{ error }}
        </div>

        <!-- Mensaje de carga inicial -->
        <div v-if="cargando && !esTutor" class="text-center">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p>Cargando información...</p>
        </div>

        <!-- Contenido principal -->
        <template v-else>
            <div id="principal" class="row col-4">
                <!-- Selectores para tutores (Grado → Alumno) -->
                <div v-if="esTutor" class="col-12 mb-3">
                    <!-- Selector de Grado -->
                    <div class="mb-3">
                        <label for="selectorGrado" class="form-label">
                            <strong>1. Seleccionar Grado:</strong>
                        </label>
                        <select 
                            id="selectorGrado" 
                            class="form-select" 
                            v-model="gradoSeleccionado"
                            @change="onGradoChange"
                        >
                            <option :value="null">-- Seleccione un grado --</option>
                            <option 
                                v-for="grado in listaGrados" 
                                :key="grado.id_grado" 
                                :value="grado.id_grado"
                            >
                                {{ grado.nombre_completo }}
                            </option>
                        </select>
                    </div>

                    <!-- Selector de Alumno -->
                    <div v-if="gradoSeleccionado" class="mb-3">
                        <label for="selectorAlumno" class="form-label">
                            <strong>2. Seleccionar Alumno:</strong>
                        </label>
                        
                        <!-- Spinner mientras carga alumnos -->
                        <div v-if="cargandoAlumnos" class="text-center py-2">
                            <div class="spinner-border spinner-border-sm" role="status">
                                <span class="visually-hidden">Cargando alumnos...</span>
                            </div>
                        </div>
                        
                        <!-- Selector de alumnos -->
                        <select 
                            v-else
                            id="selectorAlumno" 
                            class="form-select" 
                            v-model="alumnoSeleccionado"
                            @change="onAlumnoChange"
                            :disabled="listaAlumnos.length === 0"
                        >
                            <option v-if="listaAlumnos.length === 0" :value="null">
                                -- No hay alumnos en este grado --
                            </option>
                            <option 
                                v-for="alum in listaAlumnos" 
                                :key="alum.id_usuario" 
                                :value="alum.id_usuario"
                            >
                                {{ alum.nombre_completo }}
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Información del alumno -->
                <div v-if="alumno.nombre" id="info" class="col-12">
                    <h1>Información del alumno {{ alumno.nombre }}</h1>
                    <Tabla :alumno="alumno"/>
                </div>

                <!-- Mensaje si no hay alumno seleccionado (solo tutores) -->
                <div v-else-if="esTutor" class="col-12 text-center text-muted">
                    <p>Seleccione un grado y un alumno para ver la información</p>
                </div>

                <!-- Opciones (solo si hay alumno cargado) -->
                <div v-if="alumno.nombre" id="opciones" class="row col mt-3">
                    <button class="col-5 mb-2" @click="cambiar('calendario')">Ver calendario</button>
                    <button class="col-5 ms-2 mb-2" @click="cambiar('empresa')">Ver empresa</button>
                    <button class="col-5" @click="cambiar('notas')">Ver notas</button>
                    <button class="col-5 ms-2" @click="cambiar('seguimiento')">Ver seguimiento</button>
                </div>
            </div>

            <!-- Vistas secundarias -->
            <div id="secundario" class="col-8 row">
                <Calendario v-if="vistaActiva === 'calendario'"/>
                <Empresa v-if="vistaActiva === 'empresa'"/>
                <Notas v-if="vistaActiva === 'notas'"/>
                <Seguimiento v-if="vistaActiva === 'seguimiento'"/>
            </div>
        </template>
    </div>
</template>

<style scoped>
.spinner-border {
    width: 3rem;
    height: 3rem;
}

.form-label {
    margin-bottom: 0.5rem;
    color: #333;
}

.form-select {
    cursor: pointer;
}

.form-select:disabled {
    cursor: not-allowed;
    background-color: #e9ecef;
}
</style>
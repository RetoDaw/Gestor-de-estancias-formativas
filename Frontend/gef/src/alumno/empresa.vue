<script setup>
import { ref, onMounted, defineProps } from 'vue';
import "../assets/css/empresa.css";
import apiService from '@/services/api.js';

const props = defineProps({
    alumnoId: {
        type: Number,
        required: true
    }
});

const tieneEmpresa = ref(false);
const empresa = ref(null);
const tutor = ref(null);
const idEstancia = ref(null);
const empresasDisponibles = ref([]);
const cargando = ref(true);
const error = ref(null);

// Modal asignar empresa
const mostrarModal = ref(false);
const empresaSeleccionada = ref(null);
const asignando = ref(false);
const formulario = ref({
    fecha_inicio: '',
    fecha_fin: '',
    horas_totales: '',
    dias_totales: ''
});

// Modal asignar tutor de empresa
const mostrarModalTutor = ref(false);
const tutoresDisponibles = ref([]);
const tutorSeleccionado = ref(null);
const asignandoTutor = ref(false);
const cargandoTutores = ref(false);

const formatearClave = (clave) => {
    const mapeo = {
        'id_empresa': 'ID',
        'cif': 'CIF',
        'nombre': 'Nombre',
        'poblacion': 'Población',
        'telefono': 'Teléfono',
        'email': 'Email',
        'apellidos': 'Apellidos'
    };
    return mapeo[clave] || clave;
};

onMounted(async () => {
    await cargarInformacionEmpresa();
});

async function cargarInformacionEmpresa() {
    try {
        cargando.value = true;
        // verifica si el alumno tiene empresa
        const response = await apiService.getEmpresaAlumno(props.alumnoId);
        
        tieneEmpresa.value = response.data.tieneEmpresa;
        empresa.value = response.data.empresa;
        tutor.value = response.data.tutor;
        idEstancia.value = response.data.id_estancia;
        
        if (!tieneEmpresa.value) {
            const empresasResponse = await apiService.getEmpresas();
            empresasDisponibles.value = empresasResponse.data;
        }
    } catch (err) {
        console.error('Error al cargar información de empresa:', err);
        error.value = 'Error al cargar la información. Intenta de nuevo.';
    } finally {
        cargando.value = false;
    }
}

function abrirModal(emp) {
    empresaSeleccionada.value = emp;
    mostrarModal.value = true;
    
    const hoy = new Date().toISOString().split('T')[0];
    const dentroDeUnAno = new Date();
    dentroDeUnAno.setFullYear(dentroDeUnAno.getFullYear() + 1);
    
    formulario.value = {
        fecha_inicio: hoy,
        fecha_fin: dentroDeUnAno.toISOString().split('T')[0],
        horas_totales: 400,
        dias_totales: 50
    };
}

function cerrarModal() {
    mostrarModal.value = false;
    empresaSeleccionada.value = null;
}

async function asignarEmpresa() {
    try {
        asignando.value = true;
        error.value = null;
        
        await apiService.asignarEmpresa({
            id_alumno: props.alumnoId,
            id_empresa: empresaSeleccionada.value.id_empresa,
            fecha_inicio: formulario.value.fecha_inicio,
            fecha_fin: formulario.value.fecha_fin,
            horas_totales: parseInt(formulario.value.horas_totales),
            dias_totales: parseInt(formulario.value.dias_totales)
        });
        
        cerrarModal();
        await cargarInformacionEmpresa();
        
    } catch (err) {
        console.error('Error al asignar empresa:', err);
        error.value = err.response?.data?.message || 'Error al asignar la empresa';
    } finally {
        asignando.value = false;
    }
}

async function abrirModalTutor() {
    try {
        cargandoTutores.value = true;
        mostrarModalTutor.value = true;
        tutorSeleccionado.value = null;
        error.value = null;
        
        const response = await apiService.getTutoresEmpresa();
        tutoresDisponibles.value = response.data;
        
    } catch (err) {
        console.error('Error al cargar tutores:', err);
        error.value = 'Error al cargar la lista de tutores de empresa';
    } finally {
        cargandoTutores.value = false;
    }
}

function cerrarModalTutor() {
    mostrarModalTutor.value = false;
    tutorSeleccionado.value = null;
}

async function asignarTutorEmpresa() {
    if (!tutorSeleccionado.value) {
        error.value = 'Debe seleccionar un tutor';
        return;
    }

    try {
        asignandoTutor.value = true;
        error.value = null;
        
        await apiService.asignarTutorEmpresa({
            id_estancia: idEstancia.value,
            id_tutor_empresa: tutorSeleccionado.value
        });
        
        cerrarModalTutor();
        await cargarInformacionEmpresa();
        
    } catch (err) {
        console.error('Error al asignar tutor:', err);
        error.value = err.response?.data?.message || 'Error al asignar el tutor de empresa';
    } finally {
        asignandoTutor.value = false;
    }
}
</script>

<template>
    <div class="empresa-container">
        <!-- Error -->
        <div v-if="error" class="alert alert-danger">
            {{ error }}
        </div>

        <!-- Cargando -->
        <div v-if="cargando" class="text-center py-4">
            <div class="spinner-border"></div>
            <p class="mt-2">Cargando información...</p>
        </div>

        <!-- Tiene empresa asignada -->
        <div v-else-if="tieneEmpresa" class="row">
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3>Empresa Asignada</h3>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <tbody>
                                <tr v-for="(valor, clave) in empresa" :key="clave">
                                    <th>{{ formatearClave(clave) }}</th>
                                    <td>{{ valor || 'No especificado' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div v-if="tutor" class="card">
                    <div class="card-header">
                        <h3>Tutor de Empresa</h3>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <tbody>
                                <tr v-for="(valor, clave) in tutor" :key="clave">
                                    <th>{{ formatearClave(clave) }}</th>
                                    <td>{{ valor || 'No especificado' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div v-else class="card">
                    <div class="card-header">
                        <h3>Tutor de Empresa</h3>
                    </div>
                    <div class="card-body text-center">
                        <p class="text-muted mb-3">Esta empresa no tiene tutor asignado</p>
                        <button class="btn btn-primary" @click="abrirModalTutor">
                            Asignar Tutor de Empresa
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- No tiene empresa -->
        <div v-else class="no-empresa">
            <div class="alert alert-info">
                <strong>Este alumno no tiene empresa asignada</strong>
            </div>

            <div v-if="empresasDisponibles.length > 0" class="card">
                <div class="card-header">
                    <h3>Empresas Disponibles</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">
                        Selecciona una empresa para asignar al alumno
                    </p>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>CIF</th>
                                <th>Población</th>
                                <th>Teléfono</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="emp in empresasDisponibles" :key="emp.id_empresa">
                                <td>{{ emp.nombre }}</td>
                                <td>{{ emp.cif }}</td>
                                <td>{{ emp.poblacion }}</td>
                                <td>{{ emp.telefono }}</td>
                                <td>
                                    <button class="btn btn-primary btn-sm" @click="abrirModal(emp)">Asignar</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div v-else class="alert alert-warning">
                No hay empresas registradas
            </div>
        </div>

        <!-- Modal Asignar Empresa -->
        <div v-if="mostrarModal" class="modal-overlay" @click="cerrarModal">
            <div class="modal-content" @click.stop>
                <div class="modal-header">
                    <h3>Asignar Empresa: {{ empresaSeleccionada?.nombre }}</h3>
                    <button class="btn-close" @click="cerrarModal">&times;</button>
                </div>
                
                <div class="modal-body">
                    <form @submit.prevent="asignarEmpresa">
                        <div class="mb-3">
                            <label class="form-label">Fecha Inicio</label>
                            <input type="date" class="form-control" v-model="formulario.fecha_inicio" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Fecha Fin</label>
                            <input type="date" class="form-control" v-model="formulario.fecha_fin" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Horas Totales</label>
                            <input type="number" class="form-control" v-model="formulario.horas_totales" min="1"required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Días Totales</label>
                            <input type="number" class="form-control" v-model="formulario.dias_totales" min="1" required>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="cerrarModal":disabled="asignando">Cancelar</button>
                            <button type="submit" class="btn btn-primary":disabled="asignando">
                                <span>Asignar Empresa</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Asignar Tutor de Empresa -->
        <div v-if="mostrarModalTutor" class="modal-overlay" @click="cerrarModalTutor">
            <div class="modal-content" @click.stop>
                <div class="modal-header">
                    <h3>Asignar Tutor de Empresa</h3>
                    <button class="btn-close" @click="cerrarModalTutor">&times;</button>
                </div>
                
                <div class="modal-body">
                    <div v-if="cargandoTutores" class="text-center py-4">
                        <div class="spinner-border"></div>
                        <p class="mt-2">Cargando tutores...</p>
                    </div>

                    <div v-else-if="tutoresDisponibles.length === 0" class="alert alert-warning">
                        No hay tutores de empresa registrados en el sistema
                    </div>

                    <form v-else @submit.prevent="asignarTutorEmpresa">
                        <div class="mb-3">
                            <label class="form-label">Seleccionar Tutor de Empresa</label>
                            <select class="form-select" v-model="tutorSeleccionado" required>
                                <option :value="null">Seleccione un tutor...</option>
                                <option 
                                    v-for="tut in tutoresDisponibles" 
                                    :key="tut.id_usuario" 
                                    :value="tut.id_usuario"
                                >
                                    {{ tut.nombre }} {{ tut.apellidos }} - {{ tut.email }}
                                </option>
                            </select>
                        </div>

                        <div v-if="tutorSeleccionado" class="alert alert-info">
                            <strong>Tutor seleccionado:</strong>
                            <div class="mt-2">
                                {{ tutoresDisponibles.find(t => t.id_usuario === tutorSeleccionado)?.nombre }}
                                {{ tutoresDisponibles.find(t => t.id_usuario === tutorSeleccionado)?.apellidos }}
                            </div>
                            <div class="text-muted small">
                                {{ tutoresDisponibles.find(t => t.id_usuario === tutorSeleccionado)?.email }}
                            </div>
                        </div>
                        
                        <div class="modal-footer">
                            <button 
                                type="button" 
                                class="btn btn-secondary" 
                                @click="cerrarModalTutor"
                                :disabled="asignandoTutor"
                            >
                                Cancelar
                            </button>
                            <button 
                                type="submit" 
                                class="btn btn-primary"
                                :disabled="asignandoTutor || !tutorSeleccionado"
                            >
                                <span v-if="asignandoTutor">Asignando...</span>
                                <span v-else>Asignar Tutor</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
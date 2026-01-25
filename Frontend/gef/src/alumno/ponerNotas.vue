<script setup>
import { defineProps, ref, onMounted, computed } from 'vue';
import api from '@/services/api';

const props = defineProps({
    idAlumno: {
        type: Number,
        required: true
    }
});

// Estados
const notasTransversales = ref({
    comunicacion: 1,
    trabajoEnEquipo: 1,
    adaptabilidad: 1,
    responsabilidad: 1
});

const competenciasDisponibles = ref([]);
const competenciasSeleccionadas = ref([]);
const competenciaSeleccionada = ref(null);

const idEstancia = ref(null);
const idGrado = ref(null);
const cargando = ref(true);
const error = ref(null);

// Cargar datos iniciales
const cargarDatosIniciales = async () => {
    try {
        cargando.value = true;
        
        // Obtener estancia del alumno
        const responseEstancia = await api.getEstanciaAlumno(props.idAlumno);
        idEstancia.value = responseEstancia.data.id_estancia;
        
        // Obtener alumno para saber su grado
        const responseAlumno = await api.getAlumno(props.idAlumno);
        idGrado.value = responseAlumno.data.id_grado;
        
        // Obtener competencias técnicas del grado
        const responseCompetencias = await api.getCompetenciasTecnicas(idGrado.value);
        competenciasDisponibles.value = responseCompetencias.data;
        
    } catch (err) {
        console.error('Error al cargar datos:', err);
        error.value = 'No se pudieron cargar los datos necesarios';
    } finally {
        cargando.value = false;
    }
};

// Añadir competencia a la tabla
const añadirCompetencia = () => {
    if (!competenciaSeleccionada.value) {
        alert('Selecciona una competencia');
        return;
    }
    
    // Verificar si ya está añadida
    const yaExiste = competenciasSeleccionadas.value.some(
        c => c.id_competencia === competenciaSeleccionada.value.id_competencia
    );
    
    if (yaExiste) {
        alert('Esta competencia ya está añadida');
        return;
    }
    
    // Añadir con nota por defecto 1
    competenciasSeleccionadas.value.push({
        id_competencia: competenciaSeleccionada.value.id_competencia,
        descripcion: competenciaSeleccionada.value.descripcion,
        nota: 1
    });
    
    // Resetear selección
    competenciaSeleccionada.value = null;
};

// Eliminar competencia de la tabla
const eliminarCompetencia = (idCompetencia) => {
    competenciasSeleccionadas.value = competenciasSeleccionadas.value.filter(
        c => c.id_competencia !== idCompetencia
    );
};

// Guardar todas las notas
const guardarNotas = async () => {
    if (!idEstancia.value) {
        alert('No se encontró una estancia para este alumno');
        return;
    }

    try {
        // 1. Guardar notas transversales
        const payloadTransversales = {
            id_estancia: parseInt(idEstancia.value),
            notas: [
                { id_competencia_trans: 1, nota: parseFloat(notasTransversales.value.comunicacion) },
                { id_competencia_trans: 2, nota: parseFloat(notasTransversales.value.trabajoEnEquipo) },
                { id_competencia_trans: 3, nota: parseFloat(notasTransversales.value.adaptabilidad) },
                { id_competencia_trans: 4, nota: parseFloat(notasTransversales.value.responsabilidad) }
            ]
        };
        
        await api.ponerNotasTrans(props.idAlumno, payloadTransversales);
        
        // 2. Guardar notas técnicas (si hay)
        if (competenciasSeleccionadas.value.length > 0) {
            const payloadTecnicas = {
                id_estancia: parseInt(idEstancia.value),
                notas: competenciasSeleccionadas.value.map(c => ({
                    id_competencia: c.id_competencia,
                    nota: parseFloat(c.nota)
                }))
            };
            
            await api.ponerNotasTecnicas(props.idAlumno, payloadTecnicas);
        }
        
        alert('Todas las notas guardadas exitosamente');
        
    } catch (err) {
        console.error('Error completo:', err);
        alert('Error al guardar las notas: ' + (err.response?.data?.message || err.message));
    }
};

onMounted(() => {
    cargarDatosIniciales();
});
</script>

<template>
    <div v-if="cargando" class="text-center">
        <div class="spinner-border" role="status">
            <span class="visually-hidden">Cargando...</span>
        </div>
    </div>

    <div v-else-if="error" class="alert alert-danger">
        {{ error }}
    </div>

    <div v-else>
        <h2>Poner Notas de Empresa</h2>

        <!-- SECCIÓN 1: COMPETENCIAS TÉCNICAS -->
        <div class="seccion-notas">
            <h3>Competencias Técnicas</h3>
            
            <!-- Selector para añadir competencias -->
            <div class="selector-competencias">
                <select v-model="competenciaSeleccionada" class="form-select">
                    <option :value="null">Selecciona una competencia técnica</option>
                    <option 
                        v-for="comp in competenciasDisponibles" 
                        :key="comp.id_competencia"
                        :value="comp"
                    >
                        {{ comp.descripcion }}
                    </option>
                </select>
                <button @click="añadirCompetencia" class="btn btn-primary">Añadir</button>
            </div>

            <!-- Tabla de competencias añadidas -->
            <div v-if="competenciasSeleccionadas.length > 0" class="tabla-competencias">
                <table>
                    <thead>
                        <tr>
                            <th>Competencia Técnica</th>
                            <th>Nota</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="comp in competenciasSeleccionadas" :key="comp.id_competencia">
                            <td>{{ comp.descripcion }}</td>
                            <td>
                                <select v-model="comp.nota" class="form-select">
                                    <option v-for="n in 4" :key="n" :value="n">{{ n }}</option>
                                </select>
                            </td>
                            <td>
                                <button 
                                    @click="eliminarCompetencia(comp.id_competencia)"
                                    class="btn btn-danger btn-sm"
                                >
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-else class="alert alert-info">
                No hay competencias técnicas añadidas. Selecciona una competencia y haz clic en "Añadir".
            </div>
        </div>

        <!-- SECCIÓN 2: COMPETENCIAS TRANSVERSALES -->
        <div class="seccion-notas">
            <h3>Competencias Transversales</h3>
            
            <table>
                <thead>
                    <tr>
                        <th>Competencia Transversal</th>
                        <th>Nota</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Comunicación efectiva</td>
                        <td>
                            <select v-model="notasTransversales.comunicacion" class="form-select">
                                <option v-for="n in 4" :key="n" :value="n">{{ n }}</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td>Trabajo en equipo y colaboración</td>
                        <td>
                            <select v-model="notasTransversales.trabajoEnEquipo" class="form-select">
                                <option v-for="n in 4" :key="n" :value="n">{{ n }}</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td>Adaptabilidad, autonomía y gestión del tiempo</td>
                        <td>
                            <select v-model="notasTransversales.adaptabilidad" class="form-select">
                                <option v-for="n in 4" :key="n" :value="n">{{ n }}</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td>Responsabilidad ética y compromiso profesional</td>
                        <td>
                            <select v-model="notasTransversales.responsabilidad" class="form-select">
                                <option v-for="n in 4" :key="n" :value="n">{{ n }}</option>
                            </select>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- BOTÓN PRINCIPAL -->
        <div class="acciones-principales">
            <button @click="guardarNotas" class="btn btn-success btn-lg">
                Guardar todas las notas
            </button>
        </div>
    </div>
</template>

<style scoped src="../assets/css/ponerNotas.css"></style>


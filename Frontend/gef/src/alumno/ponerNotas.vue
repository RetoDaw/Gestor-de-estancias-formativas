<script setup>
import { defineProps, ref, onMounted } from 'vue';
import api from '@/services/api';

const props = defineProps({
    idAlumno: {
        type: Number,
        required: true
    }
});

const notas = ref({
    comunicacion: 1,
    trabajoEnEquipo: 1,
    adaptabilidad: 1,
    responsabilidad: 1
});

const idEstancia = ref(null);
const cargando = ref(true);
const error = ref(null);

const cargarEstancia = async () => {
    try {
        cargando.value = true;
        const response = await api.getEstanciaAlumno(props.idAlumno);
        idEstancia.value = response.data.id_estancia;
    } catch (err) {
        console.error('Error al cargar la estancia:', err);
        error.value = 'No se pudo cargar la estancia del alumno';
    } finally {
        cargando.value = false;
    }
};

const guardarNotas = async () => {
    if (!idEstancia.value) {
        alert('No se encontró una estancia para este alumno');
        return;
    }

    const payload = {
        id_estancia: parseInt(idEstancia.value), 
        notas: [
            {
                id_competencia_trans: 1, 
                nota: parseFloat(notas.value.comunicacion)
            },
            {
                id_competencia_trans: 2,
                nota: parseFloat(notas.value.trabajoEnEquipo)
            },
            {
                id_competencia_trans: 3,
                nota: parseFloat(notas.value.adaptabilidad)
            },
            {
                id_competencia_trans: 4,
                nota: parseFloat(notas.value.responsabilidad)
            }
        ]
    };


    try {
        const response = await api.ponerNotasTrans(props.idAlumno, payload);
        alert('Notas guardadas exitosamente');
    } catch (err) {
        console.error('Error completo:', err);
        alert('Estas notas ya han sido puestas');
    }
};
onMounted(() => {
    cargarEstancia();
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
        <h2>Poner Notas</h2>

        <table>
            <thead>
                <tr>
                    <th>Competencias Transversales</th>
                    <th>Nota</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Comunicación efectiva</td>
                    <td>
                        <select v-model="notas.comunicacion">
                            <option v-for="n in 4" :key="n" :value="n">{{ n }}</option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td>Trabajo en equipo y colaboración</td>
                    <td>
                        <select v-model="notas.trabajoEnEquipo">
                            <option v-for="n in 4" :key="n" :value="n">{{ n }}</option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td>Adaptabilidad, autonomía y gestión del tiempo</td>
                    <td>
                        <select v-model="notas.adaptabilidad">
                            <option v-for="n in 4" :key="n" :value="n">{{ n }}</option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td>Responsabilidad ética y compromiso profesional</td>
                    <td>
                        <select v-model="notas.responsabilidad">
                            <option v-for="n in 4" :key="n" :value="n">{{ n }}</option>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>

        <button @click="guardarNotas">Guardar notas</button>
    </div>
</template>

<style scoped src="../assets/css/notasTrans.css"></style>
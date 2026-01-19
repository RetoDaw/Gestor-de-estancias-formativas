<script setup>
import { ref, onMounted } from 'vue';
import "../assets/css/empresa.css";
import axios from '@/axios';

const empresa = ref(null);
const tutorEmpresa = ref(null);
const listaDatos = ref([]);
const cargando = ref(true);

onMounted(async () => {
    try {
        const response = await axios.get('http://localhost:8000/api/empresas', {
            headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
        });
        listaDatos.value = Array.isArray(response.data) ? response.data : [];
    } catch (error) {
        console.error('Error al cargar empresas:', error);
    } finally {
        cargando.value = false;
    }
});

function seleccionarEmpresa(evento) {
    const nombreSeleccionado = evento.target.value;
    const encontrado = listaDatos.value.find(item => item?.datosEmpresa?.nombre === nombreSeleccionado);
    
    if (encontrado) {
        empresa.value = encontrado.datosEmpresa;
        tutorEmpresa.value = encontrado.datosTutor;
    }
}
</script>

<template>
    <div class="main-wrapper">
        <div v-if="cargando">Cargando...</div>

        <div v-else-if="!empresa" class="col-12">
            <p>No hay ninguna empresa seleccionada.</p>
            
            <select @change="seleccionarEmpresa">
                <option :value="null" selected disabled>-- Selecciona una empresa --</option>
                <template v-for="(item, index) in listaDatos">
                    <option 
                        v-if="item && item.datosEmpresa" 
                        :key="item.datosEmpresa.id || index" 
                        :value="item.datosEmpresa.nombre"
                    >
                        {{ item.datosEmpresa.nombre }}
                    </option>
                </template>
            </select>
        </div>

        <div v-else class="row">
            <div class="col-6">
                <h1>Empresa</h1>
                <table>
                    <tbody>
                        <tr v-for="(valor, clave, index) in empresa" :key="'emp-' + index">
                            <td>
                                <b>{{ clave }}: </b>
                                <span>{{ valor || 'No hay registros' }}</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <button @click="empresa = null">Cambiar empresa</button>
            </div>

            <div v-if="tutorEmpresa" class="col-6">
                <h1>Tutor</h1>
                <table>
                    <tbody>
                        <tr v-for="(valor, clave, index) in tutorEmpresa" :key="'tut-' + index">
                            <td>
                                <b>{{ clave }}: </b>
                                <span>{{ valor || 'No hay registros' }}</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
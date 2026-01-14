<script setup>
import { ref } from 'vue';
import "../assets/css/empresa.css";

const empresa = ref(null);
const tutorEmpresa = ref(null);

const listaDatos = [
    {
        datosEmpresa: { 
            cif: '20202000', 
            nombre: 'Bilbomatica', 
            poblacion: 'Vitoria-Gasteiz', 
            telefono: '945929292', 
            email: 'svarela@bilbomatica.es' 
        },
        datosTutor: { 
            nombre: 'Santiago', 
            apellidos: 'Varela Varela', 
            email: 'svarela@bilbomatica.es', 
            telefono: '' 
        }
    },
    {
        datosEmpresa: { 
            cif: 'B9999999', 
            nombre: 'Ibermática', 
            poblacion: 'Donostia', 
            telefono: '943000000', 
            email: 'info@ibermatica.com' 
        },
        datosTutor: { 
            nombre: 'Amaia', 
            apellidos: 'Pérez', 
            email: 'amaia@ibermatica.com', 
            telefono: '600111222' 
        }
    }
];

function seleccionarEmpresa(evento){
    
    const encontrado = listaDatos.find(item => item.datosEmpresa.nombre === evento.target.value);
    
    if (encontrado) {
        empresa.value = encontrado.datosEmpresa;
        tutorEmpresa.value = encontrado.datosTutor;
    }
}
</script>

<template>
    <div v-if="!empresa" class="col-12">
        <p>No hay ninguna empresa para ese alumno.</p>
        <label>¿Desea añadir una empresa? </label>
        
        <select @change="seleccionarEmpresa">
            <option selected disabled>-- Selecciona una empresa --</option>
            <option v-for="empresa in listaDatos" :key="empresa.datosEmpresa.cif">
                {{ empresa.datosEmpresa.nombre }}
            </option>
        </select>
    </div>

    <template v-else>
        <div id="empresa" class="col-6">
            <h1>Empresa</h1>
            <table>
                <tbody>
                    <tr v-for="(dato, nombre) in empresa" :key="nombre">
                        <td><b class="nombre">{{ nombre }}: </b> 
                            <span v-if="dato">{{ dato }}</span>
                            <span v-else class="noDato">No hay registros</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div id="tutor" class="col-6">
            <h1>Tutor de la empresa</h1>
            <table>
                <tbody>
                    <tr v-for="(dato, nombre) in tutorEmpresa" :key="nombre">
                        <td><b class="nombre">{{ nombre }}: </b> 
                            <span v-if="dato">{{ dato }}</span>
                            <span v-else class="noDato">No hay registros</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </template>
</template>
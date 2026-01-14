<script setup>
import "../assets/css/empresa.css";
const empresa = {
    cif: '20202000',
    nombre: 'Bilbomatica',
    poblacion: 'Vitoria-Gasteiz',
    telefono: '945929292',
    email: 'svarela@bilbomatica.es'
}
const tutorEmpresa = {
    nombre: 'Santiago',
    apellidos: 'Varela Varela',
    email: 'svarela@bilbomatica.es',
    telefono: ''
}
</script>
<template>
    <div id="empresa" class="col-6">
        <h1>Empresa</h1>
        <table>
            <tbody>
                <tr v-for="(dato,nombre) in empresa" :key="nombre">
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
                <tr v-for="(dato,nombre) in tutorEmpresa" :key="nombre">
                    <td><b class="nombre">{{ nombre }}: </b> 
                        <span v-if="dato">{{ dato }}</span>
                        <span v-else class="noDato">No hay registros</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
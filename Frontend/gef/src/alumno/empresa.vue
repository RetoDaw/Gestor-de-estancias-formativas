<script setup>
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
function mostrarDato(dato){
    if(dato == ''){
        return 'No hay registros';
    }
    return dato;
}
</script>
<template>
    <h1>Empresa</h1>
    <table>
        <tbody>
            <tr v-for="(dato,nombre) in empresa" :key="nombre">
                <td><b>{{ nombre }}:</b> {{ mostrarDato(dato) }}</td>
            </tr>
        </tbody>
    </table>
    <h1>Tutor de la empresa</h1>
    <table>
        <tbody>
            <tr v-for="(dato,nombre) in tutorEmpresa" :key="nombre">
                <td><b>{{ nombre }}:</b> {{ mostrarDato(dato) }}</td>
            </tr>
        </tbody>
    </table>
</template>
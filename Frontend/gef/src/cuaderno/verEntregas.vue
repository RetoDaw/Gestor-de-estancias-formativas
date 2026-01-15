<script setup>
const entregas = [
    {
        id:1,
        grado:{id:1,nombre:"daw"},
        cuaderno:{id:1,archivo_pdf:"/cuadernosPrueba/04_linux_4_setencias_control.pdf"},
        fechaEntrega:'10/01/2026',
        estancia:{
            id:1,
            alumno:{
                nombre:"Aaron"
            }
        }
    },
    {
        id:2,
        grado:{id:3,nombre:"asir"},
        cuaderno:{id:2,archivo_pdf:"/cuadernosPrueba/EGIBIDE___Ciclos___Parciales_20251201_092849.pdf"},
        fechaEntrega:'14/01/2026',
        estancia:{
            id:2,
            alumno:{
                nombre:"Unax"
            }
        }
    }
]
const grados = [
    {
        id: 1,
        nombre: "daw"
    },
    {
        id: 2,
        nombre: "dam"
    },
    {
        id: 3,
        nombre: "asir"
    }
]
let grado = '';

</script>
<template>
    <form>
        <select v-model="grado" id="grado">
            <option v-for="grado in grados" :key="grado.id" :value="grado.nombre">
                {{ grado.nombre }}
            </option>
        </select>
    </form>
    <table>
        <tr v-for="entrega in entregas" :key="entrega.id">
            <td>Alumno: {{ entrega.estancia.alumno.nombre }}</td>
            <td>Grado: {{ entrega.grado.nombre }}</td>
            <td>Cuaderno: <a :href="entrega.cuaderno.archivo_pdf" target="_blank">Ver Cuaderno</a></td>
            <td>Fecha de subida: {{ entrega.fechaEntrega }}</td>
        </tr>
    </table>
</template>
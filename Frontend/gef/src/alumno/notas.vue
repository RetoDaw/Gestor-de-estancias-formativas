<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'

const props = defineProps({
    idAlumno: {
        type: Number,
        required: true
    }
});

const notas = ref([])

function mostrarDato(dato) {
    if (dato == null) {
        return 'No hay nota asignada'
    }
    return dato;
}

async function getNotas() {
    try {
        const response = await api.getNotas(props.idAlumno)
        notas.value = response.data.asignaturas
    } catch (err) {
        notas.value = []
    }
}

async function calcularNota() {
    if (!props.idAlumno) return

    for (const asignatura of notas.value) {
        try {
            const response = await api.getNotaFinalAlumno(
                props.idAlumno,
                asignatura.id_asignatura
            )

            asignatura.nota = response.data.nota_final
        } catch (err) {
            asignatura.nota = ''
        }
    }
}
onMounted(() => {
    getNotas()
})

</script>
<template>
    <h1>Notas</h1>
    <table>
        <tbody>
            <tr>
                <th colspan="2">Centro</th>
                <th colspan="3">Empresa</th>
            </tr>
            <tr>
                <th>Nombre</th>
                <th>Nota</th>
                <th>Com. tec</th>
                <th>Com. trans</th>
                <th>Cuaderno</th>
            </tr>
            <tr v-for="asignatura in notas" :key="asignatura.nombre">
                <td>{{ asignatura.nombre }}</td>
                <td>{{ mostrarDato(asignatura.nota) }}</td>
            </tr>
        </tbody>
    </table>
    <button @click="calcularNota()">Calcular nota</button>
</template>
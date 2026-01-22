<script setup>
import { ref, onMounted, computed } from 'vue'
import api from '@/services/api'

const props = defineProps({
    idAlumno: {
        type: Number,
        required: true
    }
});

const asignaturas = ref([])
const tecnicas = ref([])
const transversales = ref([])
const tieneEstancia = ref(false)

function mostrarDato(dato) {
    if (dato == null || dato === undefined) {
        return 'No hay nota'
    }
    return dato;
}

async function getNotas() {
    try {
        const response = await api.getNotas(props.idAlumno)
        console.log('Respuesta de API :', response.data)
        asignaturas.value = response.data.asignaturas
        tecnicas.value = response.data.tecnicas
        transversales.value = response.data.transversales 
        tieneEstancia.value = response.data.tiene_estancia
    } catch (err) {
        console.error('Error al cargar notas:', err)
        asignaturas.value = []
        tecnicas.value = []
        transversales.value = []
    }
}function getNotaTransversalPromedio() {
    if (transversales.value.length === 0) return null
    
    const notasValidas = transversales.value
        .filter(t => t.nota !== null && t.nota !== undefined)
        .map(t => parseFloat(t.nota))
    
    if (notasValidas.length === 0) return null
    
    const promedio = notasValidas.reduce((sum, n) => sum + n, 0) / notasValidas.length
    return promedio.toFixed(2)
}
function getNotaTecnicaAsignatura(nombreAsignatura) {
    // Filtramos las técnicas de la asignatura
    const resultado = tecnicas.value.find(t => t.asignatura_nombre === nombreAsignatura)
    
    // Devolvemos la nota si existe, null si no
    return resultado?.nota ?? null
}

onMounted(() => {
    getNotas()
})
function cambiarnota() {

}

async function calcularNota() {
    if (!props.idAlumno) return

    for (const asignatura of asignaturas.value) {
        if(asignatura.nota !== null){
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
}
</script>

<template>
    <div>
        <h1>Notas</h1>
        
        <div v-if="!tieneEstancia" class="alert alert-warning mb-3">
            El alumno no tiene una estancia asignada. Las notas de empresa no están disponibles.
        </div>

        <table>
            <thead  style="border: 1px solid black;">
                <tr>
                    <th rowspan="2">Asignatura</th>


                </tr>
                <tr>
                    <th>Nota Centro</th>
                    <th>Com. Técnicas</th>
                    <th>Com. Trans.</th>
                    <th>Cambiar Notas</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="asignatura in asignaturas" :key="asignatura.id_asignatura">
                    <td >{{ asignatura.nombre }}</td>
                    <td>{{ mostrarDato(asignatura.nota) }}</td>
                    <td>{{ mostrarDato(getNotaTecnicaAsignatura(asignatura.nombre)) }}</td>
                    <td>{{ mostrarDato(getNotaTransversalPromedio() * 2.5) }}</td>
                    <td><button @click="cambiarnota">Cambiar Nota</button></td>
                </tr>

                
                <tr v-if="asignaturas.length === 0">
                    <td colspan="4">No hay asignaturas para mostrar</td>
                </tr>
            </tbody>
        </table>
        <button @click="calcularNota">Calcular Nota Final</button>
        

        
    </div>
</template>
<style>
  
</style>
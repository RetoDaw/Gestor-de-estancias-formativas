<script setup>
import { computed } from 'vue';

const props = defineProps({
    alumno: Object
});

// Lista SOLO los atributos que SÍ quieres mostrar
const atributosMostrar = ['nombre', 'apellidos', 'email', 'telefono', 'id_grado'];

const alumnoFiltrado = computed(() => {
    if (!props.alumno) return {};
    
    const filtrado = {};
    atributosMostrar.forEach(attr => {
        if (props.alumno[attr] !== undefined) {
            filtrado[attr] = props.alumno[attr];
        }
    });
    return filtrado;
});
</script>

<template>
    <table>
        <tr v-for="(valor, attr) in alumnoFiltrado" :key="attr">
            <td><b class="nombre">{{ attr }}:</b> {{ valor }}</td>
        </tr>
    </table>
</template>

<style scoped>
.campo-nombre {
    width: 40%;
    background-color: #f8f9fa;
    font-weight: bold;
}

.campo-valor {
    width: 60%;
}

.table {
    margin-bottom: 0;
}
</style>
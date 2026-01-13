<script setup>
    const props = defineProps({
        alumno: Object
    })
</script>
<template>
    <table>
        <tr v-for="(valor,attr) in props.alumno" :key="attr">
            <td>{{ attr }}: {{ valor }}</td>
        </tr>
    </table>
</template>
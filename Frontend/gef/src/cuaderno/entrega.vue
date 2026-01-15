<script setup>
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
</script>
<template>
    <form>
        <p>¿Para que grado quieres hacer la entrega?</p>
        <select name="grado" id="grado">
            <option v-for="grado in grados" :key="grado.id" :value="grado.id">
                {{ grado.nombre }}
            </option>
        </select>
        <button type="submit">Crear entrega</button>
    </form>
</template>
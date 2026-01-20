<template>
  <div>
    <h2>Crear Resultado de Aprendizaje</h2>

    <form @submit.prevent="$emit('guardar')">
      <label>Descripción del Resultado de Aprendizaje:</label>
      <textarea v-model="ra.descripcion" required></textarea>

      <label>Grado:</label>
      <select v-model="ra.grado" required>
        <option disabled value="">Selecciona un grado</option>
        <option v-for="grado in grados":key="grado.id_grado":value="grado.id_grado">
          {{ grado.nombre }}
        </option>
      </select>

      <label>Asignaturas a las que pertenece:</label>
      <select v-model="ra.asignaturas" multiple required>
        <option
          v-for="asignatura in asignaturas" :key="asignatura.id_asignatura" :value="asignatura.id_asignatura">
          {{ asignatura.nombre }}
        </option>
      </select>

      <button type="submit">Guardar Resultado de Aprendizaje</button>
    </form>
  </div>
</template>


<script>
export default {
  props: {
    ra: Object,
    grados: Array,
    asignaturas: Array
  }
}
</script>

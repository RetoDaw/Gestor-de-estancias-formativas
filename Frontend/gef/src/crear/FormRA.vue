<template>
  <div>

    <form @submit.prevent="$emit('guardar')">
      <label>Descripción:</label>
      <textarea v-model="ra.descripcion" required></textarea>

      <label>Grado:</label>
        <select v-model="ra.grado" required>
        <option disabled value="">Selecciona un grado</option>
        <option v-for="grado in grados" :key="grado.id_grado" :value="grado.id_grado">
          {{ grado.nombre }}
        </option>
      </select>

      <label>Asignatura a las que pertenece:</label>
      <select v-model="ra.asignatura"  required>
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
  import "../assets/css/crear.css";
  import axios from '@/axios';
export default {
  props: {
    ra: Object,
    grados: Array,
    asignatura: Number,
    token: String,
    asignaturas: Array
  },
  watch: {
    'ra.grado'(nuevoGrado){
      if (!nuevoGrado) return;
      this.mostrarAsignaturas();}
    },
  
  methods: {
    async mostrarAsignaturas() {

        if (!this.ra.grado) return;
        try {
          const response = await axios.get(`http://localhost:8000/api/asignaturas/${this.ra.grado}`, { headers: { Authorization: `Bearer ${this.token}` } });
          this.$emit('actualizar-asignaturas', response.data);
      } catch (error) { console.error(error); }
    }
  }
};
</script>

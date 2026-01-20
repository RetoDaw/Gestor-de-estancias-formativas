<template>
  <div>
    <title>Crear</title>
    <select v-model="tipo">
      <option value="" disabled selected>¿Qué quieres Agregar?</option>
      <option value="empresa">Empresa</option>
      <option value="usuario">Usuario</option>
      <option value="competencia">Competencia</option>
      <option value="RA">Resultado de Aprendizaje</option>
    </select>

    <EmpresaForm v-if="tipo==='empresa'" :empresa="empresa" @guardar="guardarEmpresa" />
    <UsuarioForm v-else-if="tipo==='usuario'" :usuario="usuario" :grados="grados" @guardar="guardarUsuario" />
    <RAForm v-else-if="tipo==='RA'" :ra="ra" :grados="grados" :asignaturas="asignaturas" :token="token"@guardar="guardarRA" @actualizar-asignaturas="actualizarAsignaturas" />
    <FormCompetencia v-else-if="tipo==='competencia'" :competencia="competencia" :grados="grados" @guardar="guardarCompetencia" /> 
  </div>
</template>

<script>
import axios from '@/axios';
import EmpresaForm from './FormEmpresa.vue';
import UsuarioForm from './FormUsuario.vue';
import RAForm from './FormRA.vue';
import FormCompetencia from './FormCompetencia.vue';

export default {
  components: { EmpresaForm, UsuarioForm, RAForm, FormCompetencia },
  
  data() {
    return {
      tipo: '',
      token: localStorage.getItem('token'),
      empresa: { nombre_empresa: '', cif: '', poblacion: '', email: '', telefono: '' },
      usuario: { nombre: '', apellidos: '', email: '', password: '', tipo_usuario: '', id_grado: '', telefono: '' },
      ra: { descripcion: '', grado: '', asignatura: '' },
      grados: [],
      asignaturas: [],
      competencia: { descripcion: '', id_grado: '' }
    };
  },
  
  mounted() {
    this.cargarGrados();
  },
  
  methods: {
    async cargarGrados() {
      try {
        const response = await axios.get('http://localhost:8000/api/grados', { 
          headers: { Authorization: `Bearer ${this.token}` } 
        });
        this.grados = response.data;
      } catch (error) { 
        console.error(error); 
      }
    },

    actualizarAsignaturas(asignaturas) {
      this.asignaturas = asignaturas;
      this.ra.asignatura = ''; 
    },

    async guardarRA() {
      try {
   

        const payload = { 
          descripcion: this.ra.descripcion,
          id_grado: this.ra.grado,
          id_asignatura: this.ra.asignatura || null
        };


        await axios.post('http://localhost:8000/api/guardarRA', payload, { 
          headers: { Authorization: `Bearer ${this.token}` } 
        });
        
        alert('RA guardado con éxito');
        this.ra = { descripcion: '', grado: '', asignatura: '' };
        this.asignaturas = [];
      } catch (error) { 
        console.error('Error:', error);
        alert('Error al guardar el RA');
      }
    },

    async guardarEmpresa() {
      try {
        await axios.post('http://localhost:8000/api/guardarEmpresa', this.empresa, { 
          headers: { Authorization: `Bearer ${this.token}` } 
        });
        alert('Empresa guardada con éxito');
        this.empresa = { nombre_empresa: '', cif: '', poblacion: '', email: '', telefono: '' };
      } catch (error) {
        console.error('Error completo:', error);
      }
    },
    
    async guardarCompetencia() {
      try {
        await axios.post('http://localhost:8000/api/guardarCompetencia', this.competencia, { 
          headers: { Authorization: `Bearer ${this.token}` } 
        });
        alert('Competencia guardada con éxito');
        this.competencia = { descripcion: '', id_grado: '' };
      } catch (error) { 
        console.error(error); 
      }
    },
    
    async guardarUsuario() {
      try {
        const payload = { ...this.usuario };
        if (payload.tipo_usuario !== 'alumno') { 
          delete payload.id_grado;
          delete payload.curso;
          await axios.post('http://localhost:8000/api/guardarUsuario', payload, { 
            headers: { Authorization: `Bearer ${this.token}` } 
          });
          alert('Usuario guardado con éxito');
        } else {
          await axios.post('http://localhost:8000/api/guardarAlumno', payload, { 
            headers: { Authorization: `Bearer ${this.token}` } 
          });
          alert('Alumno guardado con éxito');
        }
        this.usuario = { nombre: '', apellidos: '', email: '', password: '', tipo_usuario: '', id_grado: '', telefono: '' };
      } catch (error) {
        console.error(error);
      }
    }
  }
};
</script>
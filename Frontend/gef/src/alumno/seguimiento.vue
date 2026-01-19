<template>
  <h1>Seguimiento</h1>

  <button class="btn-nuevo" @click="crearSeguimiento">Nuevo seguimiento</button>

  <div id="seguimiento">
    <table border="0">
      <thead>
        <tr>
          <th>Día</th>
          <th>Hora</th>
          <th>Acción</th>
          <th>Emisor</th>
          <th>Receptor</th>
          <th>Medio</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(seguimiento, index) in seguimientos" :key="seguimiento.id_seguimiento || index">
          <td>
            <span v-if="!seguimiento.editando">{{ mostrarDato(seguimiento.dia) }}</span>
            <input v-else type="date" v-model="seguimiento.dia" />
          </td>

          <td>
            <span v-if="!seguimiento.editando">{{ mostrarDato(seguimiento.hora) }}</span>
            <input v-else type="time" v-model="seguimiento.hora" />
          </td>

          <td>
            <span v-if="!seguimiento.editando">{{ mostrarDato(seguimiento.accion) }}</span>
            <input v-else type="text" v-model="seguimiento.accion" />
          </td>

          <td>
            <span v-if="!seguimiento.editando">{{ obtenerNombreUsuario(seguimiento.id_emisor) }}</span>
            <select v-else v-model="seguimiento.id_emisor">
              <option value="" disabled>Selecciona emisor</option>
              <option v-for="usuario in usuarios" :key="usuario.id" :value="usuario.id">
                {{ usuario.nombre }} ({{ usuario.tipo_usuario }})
              </option>
            </select>
          </td>

          <td>
            <span v-if="!seguimiento.editando">{{ obtenerNombreUsuario(seguimiento.id_receptor) }}</span>
            <select v-else v-model="seguimiento.id_receptor">
              <option value="" disabled>Selecciona receptor</option>
              <option v-for="usuario in usuarios" :key="usuario.id" :value="usuario.id">
                {{ usuario.nombre }} ({{ usuario.tipo_usuario }})
              </option>
            </select>
          </td>

          <td>
            <span v-if="!seguimiento.editando">{{ seguimiento.medio }}</span>
            <select v-else v-model="seguimiento.medio">
              <option value="EMAIL">Email</option>
              <option value="TELEFONO">Teléfono</option>
              <option value="EN_PERSONA">En persona</option>
              <option value="VIDEOLLAMADA">Videollamada</option>
              <option value="OTRO">Otro</option>
            </select>
          </td>

          <td>
            <button class="btn-editar" v-if="!seguimiento.editando" @click="editar(seguimiento)">Editar</button>
            <button class="btn-guardar" v-else @click="guardar(seguimiento)">Guardar</button>
            <button class="btn-eliminar" @click="eliminar(index)">Eliminar</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import axios from '@/axios';

export default {
  data() {
    return {
      token: localStorage.getItem('token'),
      seguimientos: [],
      usuarios: [], 
      id_estancia: null,
    };
  },
  mounted() {
    this.obtenerIdEstancia();
    this.cargarUsuarios();
    this.cargarSeguimientos();
  },
  methods: {
    mostrarDato(dato) {
      return dato && dato !== '' ? dato : 'No hay registros';
    },

    obtenerIdEstancia() {
       const user = JSON.parse(localStorage.getItem('user'));
       if (user && user.alumno) {
         this.id_estancia = user.alumno.id_estancia_actual;
       }
    },

    async cargarUsuarios() {
      try {
        const response = await axios.get('/usuarios');
        this.usuarios = response.data;
      } catch (error) {
        console.error('Error cargando usuarios:', error);
      }
    },

    async cargarSeguimientos() {
      try {
        const response = await axios.get(`/seguimientos?id_estancia=${this.id_estancia}`);
        this.seguimientos = response.data.map(s => ({ ...s, editando: false }));
      } catch (error) {
        console.error('Error cargando seguimientos:', error);
      }
    },

    obtenerNombreUsuario(id_usuario) {
      const usuario = this.usuarios.find(u => u.id === id_usuario);
      return usuario ? `${usuario.nombre} (${usuario.tipo_usuario})` : 'Desconocido';
    },

    editar(seguimiento) {
      seguimiento.editando = true;
    },

    async guardar(seguimiento) {
      try {
        // Validar que tenga todos los campos requeridos
        if (!seguimiento.dia || !seguimiento.hora || !seguimiento.accion || 
            !seguimiento.id_emisor || !seguimiento.id_receptor || !seguimiento.medio) {
          alert('Por favor completa todos los campos');
          return;
        }

        const datos = {
          id_estancia: this.id_estancia,
          dia: seguimiento.dia,
          hora: seguimiento.hora,
          accion: seguimiento.accion,
          id_emisor: seguimiento.id_emisor,
          id_receptor: seguimiento.id_receptor,
          medio: seguimiento.medio,
        };

        if (seguimiento.id_seguimiento) {
          // Actualizar seguimiento existente
          await axios.put(`/seguimientos/${seguimiento.id_seguimiento}`, datos);
          alert('Seguimiento actualizado con éxito');
        } else {
          // Crear nuevo seguimiento
          const response = await axios.post('/seguimientos', datos);
          seguimiento.id_seguimiento = response.data.id_seguimiento;
          alert('Seguimiento creado con éxito');
        }
        
        seguimiento.editando = false;
      } catch (error) {
        console.error('Error guardando seguimiento:', error);
        if (error.response && error.response.data.errors) {
          const errores = Object.entries(error.response.data.errors)
            .map(([campo, mensajes]) => `${campo}: ${mensajes.join(', ')}`)
            .join('\n');
          alert('Errores de validación:\n' + errores);
        }
      }
    },

    async eliminar(index) {
      if (!confirm('¿Estás seguro de eliminar este seguimiento?')) return;

      const seguimiento = this.seguimientos[index];
      try {
        if (seguimiento.id_seguimiento) {
          await axios.delete(`/seguimientos/${seguimiento.id_seguimiento}`);
        }
        this.seguimientos.splice(index, 1);
        alert('Seguimiento eliminado con éxito');
      } catch (error) {
        console.error('Error eliminando seguimiento:', error);
      }
    },

    crearSeguimiento() {
      if (!this.id_estancia) {
        alert('No se puede crear seguimiento sin una estancia asociada');
        return;
      }

      this.seguimientos.push({
        id_estancia: this.id_estancia,
        dia: new Date().toISOString().split('T')[0], // Fecha actual
        hora: new Date().toTimeString().split(' ')[0].substring(0, 5), // Hora actual
        accion: '',
        id_emisor: '',
        id_receptor: '',
        medio: 'EMAIL',
        editando: true,
      });
    },
  },
};
</script>
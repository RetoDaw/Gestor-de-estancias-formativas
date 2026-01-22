<script>
import apiService from '@/services/api.js';
import "../assets/css/seguimiento.css";

export default {
  props: {
    idEstancia: {
      type: Number,
      required: false,
      default: null
    }
  },
  data() {
    return {
      seguimientos: [],
      usuarios: [],
      cargando: false,
      error: null,
      puedeCrear: false, // Si el usuario puede crear/editar seguimientos
    };
  },
  async mounted() {
    await this.verificarPermisos();
    if (this.idEstancia) {
      await this.cargarUsuarios();
      await this.cargarSeguimientos();
    }
  },
  watch: {
    idEstancia(newVal) {
      if (newVal) {
        this.cargarSeguimientos();
      }
    }
  },
  methods: {
    async verificarPermisos() {
      try {
        const esTutorCentro = await apiService.tipoUsuario('tutor_centro');
        // También deberíamos verificar si es tutor de empresa, 
        // pero no tenemos ese método en api.js todavía
        this.puedeCrear = esTutorCentro;
      } catch (error) {
        console.error('Error verificando permisos:', error);
        this.puedeCrear = false;
      }
    },

    mostrarDato(dato) {
      return dato && dato !== '' ? dato : 'No hay registros';
    },

    formatearFecha(fecha) {
      if (!fecha) return 'No hay registros';
      try {
        const date = new Date(fecha);
        return date.toLocaleDateString('es-ES');
      } catch {
        return fecha;
      }
    },

    formatearHora(hora) {
      if (!hora) return 'No hay registros';
      // Si viene en formato HH:MM:SS, extraer solo HH:MM
      if (typeof hora === 'string' && hora.includes(':')) {
        return hora.substring(0, 5);
      }
      return hora;
    },

    formatearMedio(medio) {
      const medios = {
        'EMAIL': 'Email',
        'TELEFONO': 'Teléfono',
        'EN_PERSONA': 'En persona',
        'VIDEOLLAMADA': 'Videollamada',
        'OTRO': 'Otro'
      };
      return medios[medio] || medio;
    },

    async cargarUsuarios() {
      try {
        const response = await apiService.getUsuarios();
        this.usuarios = response.data;
      } catch (error) {
        console.error('Error cargando usuarios:', error);
        this.error = 'Error al cargar la lista de usuarios';
      }
    },

    async cargarSeguimientos() {
      if (!this.idEstancia) return;

      try {
        this.cargando = true;
        this.error = null;
        const response = await apiService.getSeguimientos(this.idEstancia);
        this.seguimientos = response.data.map(s => ({ ...s, editando: false }));
      } catch (error) {
        console.error('Error cargando seguimientos:', error);
        if (error.response?.status === 403) {
          this.error = 'No tienes permiso para ver estos seguimientos';
        } else {
          this.error = 'Error al cargar los seguimientos';
        }
      } finally {
        this.cargando = false;
      }
    },

    obtenerNombreUsuario(id_usuario) {
      const usuario = this.usuarios.find(u => u.id === id_usuario);
      return usuario ? `${usuario.nombre} (${usuario.tipo_usuario})` : 'Desconocido';
    },

    editar(seguimiento) {
      if (!this.puedeCrear) {
        alert('No tienes permisos para editar seguimientos');
        return;
      }
      seguimiento.editando = true;
    },

    async guardar(seguimiento) {
      if (!this.puedeCrear) {
        alert('No tienes permisos para guardar seguimientos');
        return;
      }

      try {
        // Validar que tenga todos los campos requeridos
        if (!seguimiento.dia || !seguimiento.hora || !seguimiento.accion || 
            !seguimiento.id_emisor || !seguimiento.id_receptor || !seguimiento.medio) {
          alert('Por favor completa todos los campos');
          return;
        }

        // Formatear hora si es necesario
        let horaFormateada = seguimiento.hora;
        if (typeof horaFormateada === 'string' && horaFormateada.length === 5) {
          horaFormateada = horaFormateada + ':00';
        }

        const datos = {
          id_estancia: this.idEstancia,
          dia: seguimiento.dia,
          hora: horaFormateada,
          accion: seguimiento.accion,
          id_emisor: seguimiento.id_emisor,
          id_receptor: seguimiento.id_receptor,
          medio: seguimiento.medio,
        };

        if (seguimiento.id_seguimiento) {
          // Actualizar seguimiento existente
          await apiService.actualizarSeguimiento(seguimiento.id_seguimiento, datos);
          alert('Seguimiento actualizado con éxito');
        } else {
          // Crear nuevo seguimiento
          const response = await apiService.crearSeguimiento(datos);
          seguimiento.id_seguimiento = response.data.id_seguimiento;
          alert('Seguimiento creado con éxito');
        }
        
        seguimiento.editando = false;
        // Recargar la lista para asegurar consistencia
        await this.cargarSeguimientos();
      } catch (error) {
        console.error('Error guardando seguimiento:', error);
        if (error.response?.status === 403) {
          alert('No tienes permiso para realizar esta acción');
        } else if (error.response?.data?.errors) {
          const errores = Object.entries(error.response.data.errors)
            .map(([campo, mensajes]) => `${campo}: ${mensajes.join(', ')}`)
            .join('\n');
          alert('Errores de validación:\n' + errores);
        } else {
          alert('Error al guardar el seguimiento');
        }
      }
    },

    async eliminar(index, seguimiento) {
      if (!this.puedeCrear) {
        alert('No tienes permisos para eliminar seguimientos');
        return;
      }

      if (!confirm('¿Estás seguro de eliminar este seguimiento?')) return;

      try {
        if (seguimiento.id_seguimiento) {
          await apiService.eliminarSeguimiento(seguimiento.id_seguimiento);
        }
        this.seguimientos.splice(index, 1);
        alert('Seguimiento eliminado con éxito');
      } catch (error) {
        console.error('Error eliminando seguimiento:', error);
        if (error.response?.status === 403) {
          alert('No tienes permiso para eliminar este seguimiento');
        } else {
          alert('Error al eliminar el seguimiento');
        }
      }
    },

    crearSeguimiento() {
      if (!this.puedeCrear) {
        alert('No tienes permisos para crear seguimientos');
        return;
      }

      if (!this.idEstancia) {
        alert('No se puede crear seguimiento sin una estancia asociada');
        return;
      }

      this.seguimientos.push({
        id_estancia: this.idEstancia,
        dia: new Date().toISOString().split('T')[0], // Fecha actual
        hora: new Date().toTimeString().split(' ')[0].substring(0, 5), // Hora actual HH:MM
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
<template>
  <div class="seguimiento-container">
    <h1>Seguimiento</h1>

    <!-- Mensaje si no hay estancia -->
    <div v-if="!idEstancia" class="alert alert-warning">
      No hay una estancia asignada para este alumno.
    </div>

    <template v-else>
      <!-- Solo tutores pueden crear seguimientos -->
      <button v-if="puedeCrear" class="btn-nuevo" @click="crearSeguimiento">
        Nuevo seguimiento
      </button>

      <div v-if="cargando" class="text-center my-4">
        <div class="spinner-border" role="status">
          <span class="visually-hidden">Cargando seguimientos...</span>
        </div>
      </div>

      <div v-else-if="error" class="alert alert-danger">
        {{ error }}
      </div>

      <div v-else id="seguimiento">
        <div v-if="seguimientos.length === 0" class="alert alert-info">
          No hay seguimientos registrados para esta estancia.
        </div>

        <table v-else border="0">
          <thead>
            <tr>
              <th>Día</th>
              <th>Hora</th>
              <th>Acción</th>
              <th>Emisor</th>
              <th>Receptor</th>
              <th>Medio</th>
              <th v-if="puedeCrear">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(seguimiento, index) in seguimientos" :key="seguimiento.id_seguimiento || index">
              <td>
                <span v-if="!seguimiento.editando">{{ formatearFecha(seguimiento.dia) }}</span>
                <input v-else type="date" v-model="seguimiento.dia" />
              </td>

              <td>
                <span v-if="!seguimiento.editando">{{ formatearHora(seguimiento.hora) }}</span>
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
                <span v-if="!seguimiento.editando">{{ formatearMedio(seguimiento.medio) }}</span>
                <select v-else v-model="seguimiento.medio">
                  <option value="EMAIL">Email</option>
                  <option value="TELEFONO">Teléfono</option>
                  <option value="EN_PERSONA">En persona</option>
                  <option value="VIDEOLLAMADA">Videollamada</option>
                  <option value="OTRO">Otro</option>
                </select>
              </td>

              <td v-if="puedeCrear">
                <button class="btn-editar" v-if="!seguimiento.editando" @click="editar(seguimiento)">
                  Editar
                </button>
                <button class="btn-guardar" v-else @click="guardar(seguimiento)">
                  Guardar
                </button>
                <button class="btn-eliminar" @click="eliminar(index, seguimiento)">
                  Eliminar
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </template>
  </div>
</template>
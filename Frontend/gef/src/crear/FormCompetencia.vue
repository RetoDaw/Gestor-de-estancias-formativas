<template>

    <form @submit.prevent="$emit('guardar')">
        <div>
            <label for="descripcion">Descripción:</label>
            <textarea v-model="competencia.descripcion" required></textarea>
        </div>
        <div>
            <label for="grado">Grado al que pertenece:</label>
            <select v-model="competencia.id_grado" required>
                <option disabled value="">Selecciona un grado</option>
                <option v-for="grado in grados" :key="grado.id_grado" :value="grado.id_grado">
                    {{ grado.nombre }}
                </option>
            </select>        
        </div>
        <label>Resultados De Aprendizaje Asociados:</label>
        <div v-if="resultadoAprendizaje.length > 0">
            <div v-for="ra in resultadoAprendizaje" :key="ra.id_resultado" class="checkbox-item">
                <input type="checkbox" :id="'ra-' + ra.id_resultado" v-model="competencia.resultado_aprendizaje" :value="ra.id_resultado">
                <label :for="'ra-' + ra.id_resultado">
                    {{ ra.descripcion }}
                    <span v-if="ra.asignatura">({{ ra.asignatura.nombre }})</span>
                </label>
            </div>
        </div>
        <p v-else-if="competencia.id_grado">No hay resultados de aprendizaje disponibles para este grado.</p>
        <p v-else>Selecciona un grado para ver los resultados de aprendizaje</p>
        <button type="submit" >Guardar Competencia</button>
    </form>
</template>
<script>
import "../assets/css/crear.css";
import axios from '@/axios';
    export default {
        props: {
            competencia: Object,
            grados: Array,
            token: String
        },
        data() {
            return {
                resultadoAprendizaje: []
            };
        },
        watch:{
            'competencia.id_grado'(nuevoGrado) {
                if(!nuevoGrado) return;
                this.cargarRA();
            }
        },
        methods: {
            async cargarRA(){
                if(!this.competencia.id_grado) return;
                try{
                    const response = await axios.get(`http://localhost:8000/api/resultados-aprendizaje/${this.competencia.id_grado}`,
                        {headers: {Authorization: `Bearer ${this.token}`}
                    });
                    this.resultadoAprendizaje = response.data;
                    this.competencia.resultado_aprendizaje = [];
                }catch(error){
                    console.error('Error al cargar los resultados de aprendizaje', error)
                    alert('Error al cargar los resultados de aprendizaje');

                }
            }
        }
    }
</script>

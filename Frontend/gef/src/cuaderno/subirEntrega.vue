<script setup>
import { ref, defineProps, defineEmits } from 'vue'
import api from '@/services/api'

const props = defineProps({
    entrega: {
        type: Object,
        required: true
    }
})

const emit = defineEmits(['cerrar'])

const archivo = ref(null)
const loading = ref(false)
const mensaje = ref('')
const error = ref('')

function handleFileChange(e) {
    const file = e.target.files[0]
    
    if (file) {
        // Validar que sea PDF
        if (file.type !== 'application/pdf') {
            error.value = 'Solo se permiten archivos PDF'
            archivo.value = null
            e.target.value = ''
            return
        }
        
        // Validar tamaño (máximo 10MB)
        const maxSize = 10 * 1024 * 1024 // 10MB en bytes
        if (file.size > maxSize) {
            error.value = 'El archivo no puede superar los 10MB'
            archivo.value = null
            e.target.value = ''
            return
        }
        
        archivo.value = file
        error.value = ''
    }
}

async function subirCuaderno(e) {
    e.preventDefault()
    
    if (!archivo.value) {
        error.value = 'Debes seleccionar un archivo'
        return
    }

    loading.value = true
    error.value = ''
    mensaje.value = ''

    try {
        await api.subirCuaderno(props.entrega.id_entrega, archivo.value)
        mensaje.value = 'Cuaderno subido exitosamente'
        
        // Esperar 2 segundos y cerrar el formulario
        setTimeout(() => {
            emit('cerrar')
        }, 2000)
    } catch (err) {
        error.value = err.response?.data?.message || 'Error al subir el cuaderno'
        console.error(err)
    } finally {
        loading.value = false
    }
}

function cancelar() {
    emit('cerrar')
}
</script>

<template>
    <form @submit="subirCuaderno">
        <h3>Subir cuaderno para {{ entrega.grado.nombre }}</h3>
        
        <div class="file-input">
            <label for="archivo">Sube el cuaderno (PDF):</label>
            <input 
                type="file" 
                id="archivo"
                accept=".pdf"
                @change="handleFileChange"
                :disabled="loading"
                required
            />
        </div>

        <div v-if="archivo" class="file-info">
            <p>Archivo seleccionado: {{ archivo.name }}</p>
            <p>Tamaño: {{ (archivo.size / 1024 / 1024).toFixed(2) }} MB</p>
        </div>

        <div class="botones">
            <button type="submit" :disabled="loading || !archivo">
                {{ loading ? 'Subiendo...' : 'Subir' }}
            </button>
            <button type="button" @click="cancelar" :disabled="loading">
                Cancelar
            </button>
        </div>

        <p v-if="mensaje" class="mensaje-exito">{{ mensaje }}</p>
        <p v-if="error" class="mensaje-error">{{ error }}</p>
    </form>
</template>

<style scoped>
form {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

h3 {
    margin: 0 0 1rem 0;
}

.file-input {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

input[type="file"] {
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.file-info {
    padding: 0.5rem;
    background-color: #e9ecef;
    border-radius: 4px;
    font-size: 0.9rem;
}

.file-info p {
    margin: 0.25rem 0;
}

.botones {
    display: flex;
    gap: 1rem;
}

button {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 1rem;
}

button[type="submit"] {
    background-color: #28a745;
    color: white;
}

button[type="button"] {
    background-color: #6c757d;
    color: white;
}

button:disabled {
    background-color: #ccc;
    cursor: not-allowed;
}

.mensaje-exito {
    color: green;
    font-weight: bold;
}

.mensaje-error {
    color: red;
    font-weight: bold;
}
</style>
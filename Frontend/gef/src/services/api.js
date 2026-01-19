// services/api.js
import axios from 'axios';

const api = axios.create({
    baseURL: 'http://localhost:8000/api',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    }
});

// Interceptor para agregar el token a todas las peticiones
api.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem('token');
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

// Interceptor para manejar errores de autenticación
api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            window.location.href = '/login';
        }
        return Promise.reject(error);
    }
);

export default {
    // Grados
    getGrados() {
        return api.get('/grados');
    },

    // Entregas
    crearEntrega(idGrado) {
        return api.post('/entregas', { id_grado: idGrado });
    },

    getEntregas() {
        return api.get('/entregas');
    },

    // Cuadernos
    getCuadernos(idGrado = null) {
        const params = idGrado ? { id_grado: idGrado } : {};
        return api.get('/cuadernos', { params });
    },

    subirCuaderno(idEntrega, archivo) {
        const formData = new FormData();
        formData.append('id_entrega', idEntrega);
        formData.append('archivo', archivo);

        return api.post('/cuadernos', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
    },

    // Usuario
    getUser() {
        return api.get('/user');
    },

    async tipoUsuario(tipo){
        let response;
        switch(tipo){
            case "tc":
                response = await api.get('/esTutorCentro');
                break;
            case "al":
                response = await api.get('/esAlumno');
                break;
        }
        return response?.data ?? false;
    },

    // alumnos
    // Obtener datos del usuario autenticado con grado
    getAuthUser() {
        return api.get('/usuario/me');
    },

    // Listar grados que tienen alumnos (solo para tutores)
    listarGradosConAlumnos() {
        return api.get('/grados-con-alumnos');
    },

    // Listar alumnos por grado (solo para tutores)
    listarAlumnosPorGrado(idGrado) {
        return api.get('/alumnos-por-grado', { params: { id_grado: idGrado } });
    },

    // Obtener datos de un alumno específico
    getAlumno(userId) {
        return api.get('/alumno', { params: { user_id: userId } });
    }
};
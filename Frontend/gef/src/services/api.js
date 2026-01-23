// services/api.js
import axios from '@/axios';

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
    // EMPRESAS

    getEmpresas() {
        return api.get('/empresas');
    },

    getEmpresaAlumno(userId) {
        return api.get('/empresa_alumno', { params: { user_id: userId } });
    },

    asignarEmpresa(datos) {
        return api.post('/asignar-empresa', datos);
    },

    // Tutor de empresa
    getTutoresEmpresa() {
        return api.get('/tutores-empresa');
    },

    asignarTutorEmpresa(datos) {
        return api.post('/asignar-tutor-empresa', datos);
    },

    // GRADOS

    getGrados() {
        return api.get('/grados');
    },

    listarGradosConAlumnos() {
        return api.get('/grados-con-alumnos');
    },

    listarAlumnosPorGrado(idGrado) {
        return api.get('/alumnos-por-grado', { params: { id_grado: idGrado } });
    },


    // USUARIOS

    getUser() {
        return api.get('/user');
    },

    async tipoUsuario(tipo){
        let response;
        switch(tipo){
            case "tutor_centro":
                response = await api.get('/esTutorCentro');
                break;
            case "alumno":
                response = await api.get('/esAlumno');
                break;
        }
        return response?.data ?? false;
    },

    getAuthUser() {
        return api.get('/usuario/me');
    },

    getAlumno(userId) {
        return api.get('/alumno', { params: { user_id: userId } });
    },

    // Listar usuarios para selectores (emisor/receptor)
    getUsuarios() {
        return api.get('/usuarios');
    },


    // HORARIO/CALENDARIO

    getHorarioAlumno(userId = null) {
        const params = userId ? { user_id: userId } : {};
        return api.get('/horario-alumno', { params });
    },

    crearHorario(data) {
        return api.post('/horario', data);
    },

    actualizarHorario(idEstancia, data) {
        return api.put(`/horario/${idEstancia}`, data);
    },


    // NOTAS

    getNotaFinalAlumno(idAlumno, idAsignatura) {
        return api.get(`/alumnos/${idAlumno}/asignaturas/${idAsignatura}/nota-final`);
    },

    getNotas(idAlumno) {
        return api.get(`/alumnos/${idAlumno}/notas`);
    },

    ponerNotasTrans(idAlumno, notas) {
        console.log('Datos enviados a API:', notas);
        return api.post(`/alumnos/${idAlumno}/notasTrans`, notas);
    },


    // ESTANCIAS

    getEstanciaAlumno(idAlumno) {
        return api.get(`/alumnos/${idAlumno}/estancia`);
    },

    // ENTREGAS Y CUADERNOS

    crearEntrega(idGrado) {
        return api.post('/entregas', { id_grado: idGrado });
    },

    getEntregas() {
        return api.get('/entregas');
    },

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

    // SEGUIMIENTOS
    
    getSeguimientos(idEstancia) {
        return api.get('/seguimientos', { params: { id_estancia: idEstancia } });
    },

    crearSeguimiento(datos) {
        return api.post('/seguimientos', datos);
    },

    actualizarSeguimiento(idSeguimiento, datos) {
        return api.put(`/seguimientos/${idSeguimiento}`, datos);
    },

    eliminarSeguimiento(idSeguimiento) {
        return api.delete(`/seguimientos/${idSeguimiento}`);
    },

    // COMPETENCIAS TÉCNICAS

    getCompetenciasTecnicas(idGrado) {
        return api.get(`/competencias-tecnicas/${idGrado}`);
    },

    ponerNotasTecnicas(idAlumno, notas) {
        console.log('Datos de notas técnicas enviados a API:', notas);
        return api.post(`/alumnos/${idAlumno}/notasTecnicas`, notas);
    },
};
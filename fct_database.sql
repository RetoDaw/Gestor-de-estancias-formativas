-- ============================================
-- BASE DE DATOS: SISTEMA DE GESTIÓN FCT
-- SISTEMA DE NOTAS: Competencia → RA → Asignatura
-- ============================================

/*
FLUJO DE NOTAS (EMPRESA → ASIGNATURAS):

1. Tutor EMPRESA evalúa COMPETENCIAS TÉCNICAS (ej: "Programación" → 4/4)
2. Esa nota se PROPAGA a todos los RESULTADOS_APRENDIZAJE relacionados
3. Cada ASIGNATURA calcula su nota técnica = MEDIA de sus RAs evaluados
*/

-- Eliminar tablas si existen
DROP TABLE IF EXISTS nota_cuaderno;
DROP TABLE IF EXISTS cuaderno_practicas;
DROP TABLE IF EXISTS nota_competencia_transversal;
DROP TABLE IF EXISTS nota_resultado_aprendizaje;
DROP TABLE IF EXISTS nota_asignatura_centro;
DROP TABLE IF EXISTS seguimiento;
DROP TABLE IF EXISTS horario;
DROP TABLE IF EXISTS estancia;
DROP TABLE IF EXISTS empresa;
DROP TABLE IF EXISTS competencia_tecnica_resultado;
DROP TABLE IF EXISTS competencia_tecnica;
DROP TABLE IF EXISTS competencia_transversal;
DROP TABLE IF EXISTS resultado_aprendizaje;
DROP TABLE IF EXISTS asignatura;
DROP TABLE IF EXISTS grado;
DROP TABLE IF EXISTS alumno;
DROP TABLE IF EXISTS usuario;

-- ============================================
-- 1. USUARIOS (Tabla base simplificada)
-- ============================================
CREATE TABLE users (
    id_usuario INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(150) NOT NULL,
    telefono VARCHAR(20),
    tipo_usuario ENUM('ALUMNO', 'TUTOR_CENTRO', 'TUTOR_EMPRESA') NOT NULL
);

-- ============================================
-- 2. GRADOS (antes CURSOS)
-- ============================================
CREATE TABLE grado (
    id_grado INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    familia VARCHAR(100) NOT NULL,
    codigo VARCHAR(20) UNIQUE NOT NULL
);

CREATE TABLE alumno (
    id_alumno INT PRIMARY KEY,
    id_grado INT NOT NULL,
    FOREIGN KEY (id_alumno) REFERENCES usuario(id_usuario) ON DELETE CASCADE
);

-- ============================================
-- 3. ESTRUCTURA ACADÉMICA (GRADO → ASIGNATURA → RA)
-- ============================================
CREATE TABLE asignatura (
    id_asignatura INT PRIMARY KEY AUTO_INCREMENT,
    id_grado INT NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    FOREIGN KEY (id_grado) REFERENCES grado(id_grado) ON DELETE CASCADE
);

CREATE TABLE resultado_aprendizaje (
    id_resultado INT PRIMARY KEY AUTO_INCREMENT,
    id_asignatura INT NOT NULL,
    descripcion TEXT NOT NULL,
    FOREIGN KEY (id_asignatura) REFERENCES asignatura(id_asignatura) ON DELETE CASCADE
);

-- ============================================
-- 4. COMPETENCIAS TÉCNICAS
-- ============================================
CREATE TABLE competencia_tecnica (
    id_competencia INT PRIMARY KEY AUTO_INCREMENT,
    id_grado INT NOT NULL,
    descripcion TEXT NOT NULL,
    FOREIGN KEY (id_grado) REFERENCES grado(id_grado) ON DELETE CASCADE
);

-- RELACIÓN N:N entre Competencias y Resultados de Aprendizaje
CREATE TABLE competencia_tecnica_resultado (
    id_competencia INT NOT NULL,
    id_resultado INT NOT NULL,
    PRIMARY KEY (id_competencia, id_resultado),
    FOREIGN KEY (id_competencia) REFERENCES competencia_tecnica(id_competencia) ON DELETE CASCADE,
    FOREIGN KEY (id_resultado) REFERENCES resultado_aprendizaje(id_resultado) ON DELETE CASCADE
);

-- ============================================
-- 5. EMPRESAS Y ESTANCIAS
-- ============================================
CREATE TABLE empresa (
    id_empresa INT PRIMARY KEY AUTO_INCREMENT,
    cif VARCHAR(20) UNIQUE NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    poblacion VARCHAR(100) NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL
);

CREATE TABLE estancia (
    id_estancia INT PRIMARY KEY AUTO_INCREMENT,
    id_alumno INT NOT NULL,
    id_empresa INT NOT NULL,
    id_tutor_empresa INT,
    id_tutor_centro INT,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    horas_totales INT NOT NULL,
    dias_totales INT NOT NULL,
    FOREIGN KEY (id_alumno) REFERENCES alumno(id_alumno) ON DELETE CASCADE,
    FOREIGN KEY (id_empresa) REFERENCES empresa(id_empresa),
    FOREIGN KEY (id_tutor_empresa) REFERENCES usuario(id_usuario),
    FOREIGN KEY (id_tutor_centro) REFERENCES usuario(id_usuario)
);

CREATE TABLE horario_semanal (
    id_horario_semanal INT PRIMARY KEY AUTO_INCREMENT,
    id_estancia INT NOT NULL,
    dia_semana VARCHAR(10),
    CHECK (dia_semana IN ('Lunes','Martes','Miercoles','Jueves','Viernes')),
    FOREIGN KEY (id_estancia) REFERENCES estancia(id_estancia) ON DELETE CASCADE
);

CREATE TABLE horario (
    id_horario INT PRIMARY KEY AUTO_INCREMENT,
    id_horario_semanal INT NOT NULL,
    hora_inicial INT NOT NULL,
    hora_final INT NOT NULL,
    FOREIGN KEY (id_horario_semanal) REFERENCES horario_semanal(id_horario_semanal) ON DELETE CASCADE
);

-- ============================================
-- 6. SEGUIMIENTO (rediseñado)
-- ============================================
CREATE TABLE seguimiento (
    id_seguimiento INT PRIMARY KEY AUTO_INCREMENT,
    id_estancia INT NOT NULL,
    dia DATE NOT NULL,
    hora TIME NOT NULL,
    accion TEXT NOT NULL,
    id_receptor INT NOT NULL,
    id_emisor INT NOT NULL,
    medio ENUM('EMAIL', 'EN_PERSONA', 'TELEFONO', 'VIDEOLLAMADA', 'OTRO') NOT NULL,
    FOREIGN KEY (id_estancia) REFERENCES estancia(id_estancia) ON DELETE CASCADE,
    FOREIGN KEY (id_receptor) REFERENCES usuario(id_usuario),
    FOREIGN KEY (id_emisor) REFERENCES usuario(id_usuario)
);

-- ============================================
-- 7. SISTEMA DE NOTAS
-- ============================================

-- 7.1 NOTAS DEL CENTRO (80% - evaluación académica normal)
CREATE TABLE nota_asignatura_centro (
    id_nota INT PRIMARY KEY AUTO_INCREMENT,
    id_alumno INT NOT NULL,
    id_asignatura INT NOT NULL,
    nota DECIMAL(4,2),
    FOREIGN KEY (id_alumno) REFERENCES alumno(id_alumno) ON DELETE CASCADE,
    FOREIGN KEY (id_asignatura) REFERENCES asignatura(id_asignatura) ON DELETE CASCADE,
    UNIQUE KEY unique_alumno_asig (id_alumno, id_asignatura)
);

-- 7.2 NOTAS DE EMPRESA EN RESULTADOS DE APRENDIZAJE
CREATE TABLE nota_resultado_aprendizaje (
    id_nota_ra INT PRIMARY KEY AUTO_INCREMENT,
    id_estancia INT NOT NULL,
    id_competencia INT NOT NULL,
    id_resultado INT NOT NULL,
    nota DECIMAL(4,2) NOT NULL,
    FOREIGN KEY (id_estancia) REFERENCES estancia(id_estancia) ON DELETE CASCADE,
    FOREIGN KEY (id_competencia, id_resultado) REFERENCES competencia_tecnica_resultado(id_competencia, id_resultado) ON DELETE CASCADE,
    UNIQUE KEY unique_estancia_ra_competencia (id_estancia, id_competencia, id_resultado)
);

-- 7.3 COMPETENCIAS TRANSVERSALES (4 fijas)
CREATE TABLE competencia_transversal (
    id_competencia_trans INT PRIMARY KEY AUTO_INCREMENT,
    descripcion TEXT NOT NULL
);

CREATE TABLE nota_competencia_transversal (
    id_nota_trans INT PRIMARY KEY AUTO_INCREMENT,
    id_estancia INT NOT NULL,
    id_competencia_trans INT NOT NULL,
    nota DECIMAL(4,2),
    FOREIGN KEY (id_estancia) REFERENCES estancia(id_estancia) ON DELETE CASCADE,
    FOREIGN KEY (id_competencia_trans) REFERENCES competencia_transversal(id_competencia_trans) ON DELETE CASCADE,
    UNIQUE KEY unique_estancia_trans (id_estancia, id_competencia_trans)
);

-- 7.4 CUADERNO DE PRÁCTICAS (rediseñado)
CREATE TABLE entrega (
    id_entrega INT PRIMARY KEY AUTO_INCREMENT,
    id_tutor INT NOT NULL,
    id_grado INT NOT NULL,
    FOREIGN KEY (id_tutor) REFERENCES usuario(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_grado) REFERENCES grado(id_grado) ON DELETE CASCADE
);

CREATE TABLE cuaderno_practicas (
    id_cuaderno INT PRIMARY KEY AUTO_INCREMENT,
    id_estancia INT NOT NULL,
    id_entrega INT NOT NULL,
    fecha_entrega DATE NOT NULL,
    archivo_pdf VARCHAR(255) NOT NULL, -- Ruta al archivo PDF
    FOREIGN KEY (id_estancia) REFERENCES estancia(id_estancia) ON DELETE CASCADE,
    FOREIGN KEY (id_entrega) REFERENCES entrega(id_entrega) ON DELETE CASCADE
);

CREATE TABLE nota_cuaderno (
    id_nota_cuaderno INT PRIMARY KEY AUTO_INCREMENT,
    id_cuaderno INT NOT NULL,
    id_tutor INT NOT NULL,
    nota DECIMAL(4,2),
    fecha_evaluacion DATE,
    comentarios TEXT,
    FOREIGN KEY (id_cuaderno) REFERENCES cuaderno_practicas(id_cuaderno) ON DELETE CASCADE,
    FOREIGN KEY (id_tutor) REFERENCES usuario(id_usuario) ON DELETE CASCADE,
    UNIQUE KEY unique_cuaderno (id_cuaderno)
);

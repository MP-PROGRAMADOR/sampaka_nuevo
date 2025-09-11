-- ===============================
--  SISTEMA HOSPITALARIO DISTRITAL
-- ===============================

drop database if EXISTS hospital_sampaka;
CREATE DATABASE IF NOT EXISTS hospital_sampaka;
USE hospital_sampaka;


-- ===============================
--  TABLA HOSPITALES
-- ===============================

CREATE TABLE hospitales (
    id_hospital INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150),
    distrito VARCHAR(100),
    categoria VARCHAR(50),
    direccion VARCHAR(200),
    telefono VARCHAR(20),
    logo VARCHAR(255)

);


INSERT INTO `hospitales` (`id_hospital`, `nombre`, `distrito`, `categoria`, `direccion`, `telefono`, `logo`) VALUES
(1, 'Hospital de Sampaka', 'Malabo', '1', 'Malabo, Bioko Norte', '555897654', 'jsjsjs');




-- ===============================
--  TABLA USUARIOS Y PERSONAL
-- ===============================

CREATE TABLE personal (
    id_personal INT AUTO_INCREMENT PRIMARY KEY,
    id_hospital INT,
    nombre VARCHAR(100),
    apellido VARCHAR(100),
    especialidad VARCHAR(100),
    cargo VARCHAR(100),
    telefono VARCHAR(20),
    correo VARCHAR(150),
    direccion varchar(100),
    nivel_estudios varchar(150),
    nacionalidad varchar(150),
    codigo varchar(100),
    FOREIGN KEY (id_hospital) REFERENCES hospitales(id_hospital)
);

-- Modificar la tabla de personal para añadir la columna 'codigo'
ALTER TABLE personal
ADD COLUMN codigo VARCHAR(50) UNIQUE NOT NULL;


INSERT INTO `personal` (`id_personal`, `id_hospital`, `nombre`, `apellido`, `especialidad`, `cargo`, `telefono`, `correo`, `codigo`) VALUES
(1, 1, 'Salvador', 'Mete Bijeri', 'Medico', 'medico', '222780932', 'salvadormete2@gmail.com', '');



CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    id_personal INT,
    username VARCHAR(50) UNIQUE,
    password VARCHAR(255), -- encriptado
    rol ENUM('Administrador','General','Urgencias','Farmaceutico','Laboratorio','Finanzas'),
    estado ENUM('Activo','Inactivo') DEFAULT 'Activo',
    FOREIGN KEY (id_personal) REFERENCES personal(id_personal)
);

INSERT INTO `usuarios` (`id_usuario`, `id_personal`, `username`, `password`, `rol`, `estado`) VALUES
(1, 1, 'admin', '$2y$10$L3JaPO64XA5DhDGyuWwUc.T91ZSmPRuCa4PWXgEGU9gltoVfFMbrC', 'Administrador', 'Activo');


-- ===============================
--  TABLA PACIENTES
-- ===============================

CREATE TABLE pacientes (
    id_paciente INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) UNIQUE,
    nombre VARCHAR(100),
    apellido VARCHAR(100),
    sexo ENUM('M','F'),
    fecha_nacimiento DATE,
    correo VARCHAR(150),
    direccion VARCHAR(200),
    telefono VARCHAR(20),
    nacionalidad VARCHAR(100),
    ocupacion VARCHAR(100),
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    id_usuario INT,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

INSERT INTO `pacientes` (`id_paciente`, `codigo`, `nombre`, `apellido`, `sexo`, `fecha_nacimiento`, `correo`, `direccion`, `telefono`, `nacionalidad`, `ocupacion`, `fecha_registro`, `id_usuario`) VALUES
(1, 'SAMEcdea', 'salvador', 'mete', 'M', '1995-01-03', 'salvadormete2@gmail.com', '', '+240222478702', 'ecuatoguineano', 'estudiante', '2025-08-30 16:30:42', 1);


-- ===============================
--  TABLA CONSULTAS
-- ===============================

CREATE TABLE consultas (
    id_consulta INT AUTO_INCREMENT PRIMARY KEY,
    id_paciente INT,
    id_hospital INT,
    id_medico INT,
    fecha_consulta DATETIME DEFAULT CURRENT_TIMESTAMP,
    tipo_consulta ENUM(
        'General', 
        'Urgencias', 
        'Gastroenterología', 
        'Ginecología', 
        'Pediatría', 
        'Cardiología', 
        'Dermatología', 
        'Neurología', 
        'Traumatología', 
        'Psiquiatría', 
        'Oncología', 
        'Oftalmología', 
        'Otorrinolaringología', 
        'Endocrinología', 
        'Neumología', 
        'Reumatología'
    ) NOT NULL,
    diagnostico TEXT,
    temperatura DECIMAL(5,2),
    presion_arterial VARCHAR(20),
    tension_arterial VARCHAR(20),
    saturacion_oxigeno DECIMAL(5,2),
    pulso INT,
    peso DECIMAL(5,2),
    talla DECIMAL(5,2),
    motivo TEXT,
    IMC TEXT,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    pagado BOOLEAN DEFAULT FALSE,
    precio DECIMAL(10,2),
    id_usuario INT,
    FOREIGN KEY (id_paciente) REFERENCES pacientes(id_paciente),
    FOREIGN KEY (id_hospital) REFERENCES hospitales(id_hospital),
    FOREIGN KEY (id_medico) REFERENCES personal(id_personal),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);


-- ===============================
--  TABLA DETALLE CONSULTA
-- ===============================

CREATE TABLE detalle_consulta (
    id_detalle INT AUTO_INCREMENT PRIMARY KEY,
    id_consulta INT,
    orina VARCHAR(100),
    defeca VARCHAR(100),
    horas_sueno INT,
    antecedentes_familiares TEXT,
    antecedentes_conyuge TEXT,
    alergias TEXT,
    operaciones TEXT,
    transfuciones Text,
    id_usuario INT,
    FOREIGN KEY (id_consulta) REFERENCES consultas(id_consulta),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

-- ===============================
--  TABLA SALAS
-- ===============================

CREATE TABLE salas (
    id_sala INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100), -- ej: Urgencias, Pediatría, Maternidad
    id_usuario INT,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

-- ===============================
--  TABLA HOSPITALIZACIONES
-- ===============================

CREATE TABLE hospitalizaciones (
    id_hospitalizacion INT AUTO_INCREMENT PRIMARY KEY,
    id_paciente INT,
    id_hospital INT,
    id_sala INT,
    numero_cama VARCHAR(10),
    fecha_ingreso DATE,
    fecha_alta DATE NULL,
    causa TEXT,
    estado_alta ENUM('Curado','Mejorado','Fallecido'),
    id_usuario INT,
    FOREIGN KEY (id_paciente) REFERENCES pacientes(id_paciente),
    FOREIGN KEY (id_hospital) REFERENCES hospitales(id_hospital),
    FOREIGN KEY (id_sala) REFERENCES salas(id_sala),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

-- ===============================
--  TABLA PRUEBAS MÉDICAS
-- ===============================

CREATE TABLE pruebas_medicas (
    id_prueba INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150),
    precio DECIMAL(10,2),
    id_usuario INT,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

-- ===============================
--  TABLA ANALÍTICAS
-- ===============================

CREATE TABLE analiticas (
    id_analitica INT AUTO_INCREMENT PRIMARY KEY,
    id_consulta INT,
    id_paciente INT,
    codigo_paciente VARCHAR(50),
    resultado TEXT,
    estado ENUM('Pendiente','Entregado') DEFAULT 'Pendiente',
    id_prueba INT,
    comentario TEXT,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    pagado BOOLEAN DEFAULT FALSE,
    valores_referencia TEXT,
    archivo VARCHAR(255),
    id_usuario INT,
    FOREIGN KEY (id_consulta) REFERENCES consultas(id_consulta),
    FOREIGN KEY (id_paciente) REFERENCES pacientes(id_paciente),
    FOREIGN KEY (id_prueba) REFERENCES pruebas_medicas(id_prueba),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

-- ===============================
--  TABLA PAGOS
-- ===============================

CREATE TABLE pagos (
    id_pago INT AUTO_INCREMENT PRIMARY KEY,
    cantidad DECIMAL(10,2),
    id_analitica INT,
    id_prueba INT,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    id_usuario INT,
    FOREIGN KEY (id_analitica) REFERENCES analiticas(id_analitica),
    FOREIGN KEY (id_prueba) REFERENCES pruebas_medicas(id_prueba),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

-- ===============================
--  TABLA HORARIOS DEL PERSONAL
-- ===============================

CREATE TABLE horarios (
    id_horario INT AUTO_INCREMENT PRIMARY KEY,
    id_personal INT,
    mes INT,
    anio INT,
    turno ENUM('Manana','Tarde','Noche'),
    dias_asignados VARCHAR(100),
    id_usuario INT,
    FOREIGN KEY (id_personal) REFERENCES personal(id_personal),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

-- ===============================
--  TABLA VACUNACIÓN
-- ===============================

CREATE TABLE vacunaciones (
    id_vacunacion INT AUTO_INCREMENT PRIMARY KEY,
    id_paciente INT,
    id_hospital INT,
    tipo_vacuna VARCHAR(150),
    fecha_aplicacion DATE,
    dosis VARCHAR(50),
    id_usuario INT,
    FOREIGN KEY (id_paciente) REFERENCES pacientes(id_paciente),
    FOREIGN KEY (id_hospital) REFERENCES hospitales(id_hospital),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

-- ===============================
--  TABLA DEFUNCIONES
-- ===============================

CREATE TABLE defunciones (
    id_defuncion INT AUTO_INCREMENT PRIMARY KEY,
    id_paciente INT,
    id_hospital INT,
    fecha_defuncion DATE,
    causa_muerte TEXT,
    lugar ENUM('Hospital','Domicilio','Traslado'),
    id_usuario INT,
    FOREIGN KEY (id_paciente) REFERENCES pacientes(id_paciente),
    FOREIGN KEY (id_hospital) REFERENCES hospitales(id_hospital),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

-- ===============================
--  TABLA ADMINISTRACIÓN Y FINANZAS
-- ===============================

CREATE TABLE gastos (
    id_gasto INT AUTO_INCREMENT PRIMARY KEY,
    id_hospital INT,
    concepto VARCHAR(200),
    monto DECIMAL(10,2),
    fecha_gasto DATE,
    id_usuario INT,
    FOREIGN KEY (id_hospital) REFERENCES hospitales(id_hospital),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

CREATE TABLE ingresos (
    id_ingreso INT AUTO_INCREMENT PRIMARY KEY,
    id_hospital INT,
    concepto VARCHAR(200),
    monto DECIMAL(10,2),
    fecha_ingreso DATE,
    id_usuario INT,
    FOREIGN KEY (id_hospital) REFERENCES hospitales(id_hospital),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

-- ===============================
--  TABLA LOGS
-- ===============================

CREATE TABLE logs (
    id_log INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NULL,
    accion VARCHAR(100),
    descripcion TEXT,
    fecha_hora DATETIME DEFAULT CURRENT_TIMESTAMP,
    ip_origen text,
    dispositivo Text,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

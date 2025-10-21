-- Tabla de roles
CREATE TABLE rol (
  id_rol INT AUTO_INCREMENT PRIMARY KEY,
  nombre_rol VARCHAR(50) NOT NULL
);

-- Tabla de departamentos
CREATE TABLE dpto (
  id_dpto INT AUTO_INCREMENT PRIMARY KEY,
  nombre_dpto VARCHAR(100) NOT NULL
);

-- Tabla de usuarios
CREATE TABLE usuario (
  id_usuario INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  correo VARCHAR(100) UNIQUE NOT NULL,
  contraseña VARCHAR(255) NOT NULL,
  pfp VARCHAR(255),
  puesto VARCHAR(100),
  id_rol INT NOT NULL,
  id_dpto INT NOT NULL,
  FOREIGN KEY (id_rol) REFERENCES rol(id_rol),
  FOREIGN KEY (id_dpto) REFERENCES dpto(id_dpto)
);

-- Tabla de tickets
CREATE TABLE ticket (
  id_ticket INT AUTO_INCREMENT PRIMARY KEY,
  fecha_creacion DATETIME NOT NULL,
  fecha_finalizacion DATETIME,
  descripcion TEXT NOT NULL,
  status VARCHAR(50) NOT NULL,
  id_usuario INT NOT NULL,       -- quien crea el ticket
  id_auxiliar INT,               -- quien atiende el ticket
  FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario),
  FOREIGN KEY (id_auxiliar) REFERENCES usuario(id_usuario)
);

-- Tabla de historial de tickets
CREATE TABLE historialTicket (
  id_historial INT AUTO_INCREMENT PRIMARY KEY,
  id_ticket INT NOT NULL,
  id_usuario INT NOT NULL,       -- quien realiza la acción
  fecha_evento DATETIME NOT NULL,
  accion VARCHAR(100) NOT NULL,
  status_nuevo VARCHAR(50),
  comentario TEXT,
  FOREIGN KEY (id_ticket) REFERENCES ticket(id_ticket),
  FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario)
);
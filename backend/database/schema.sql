-- ============================================================
-- Ready for the Test? — Esquema de base de datos (MySQL / XAMPP)
-- Grupo 15 — RA1
-- Compartida entre los dos módulos fusionados: calculadora y laboratorio
-- ============================================================

CREATE DATABASE IF NOT EXISTS ready_for_the_test
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE ready_for_the_test;

-- ---------- Usuarios (docentes y estudiantes) ----------
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(120) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('estudiante', 'docente') NOT NULL DEFAULT 'estudiante',
  points INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- MÓDULO: CALCULADORA
-- ============================================================

CREATE TABLE calculator_operations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  expression VARCHAR(255) NOT NULL,
  result VARCHAR(60) NOT NULL,
  operation_type ENUM('basica', 'cientifica') NOT NULL DEFAULT 'cientifica',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_calc_op_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ============================================================
-- MÓDULO: LABORATORIO
-- ============================================================

-- Muestras que el usuario registra (estación de densidad / masa y volumen)
CREATE TABLE lab_samples (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  name VARCHAR(80) NOT NULL,
  mass DECIMAL(10, 3) NOT NULL,
  volume DECIMAL(10, 3) NOT NULL,
  state ENUM('solido', 'liquido', 'gas') NOT NULL DEFAULT 'liquido',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_sample_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Catálogo de sustancias conocidas (estación de identificación)
CREATE TABLE known_substances (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL,
  description TEXT NOT NULL,
  state ENUM('solido', 'liquido', 'gas') NOT NULL,
  transparency ENUM('transparente', 'opaco') NOT NULL,
  conductivity ENUM('conduce', 'noconduce') NOT NULL,
  density DECIMAL(10, 4) NOT NULL,
  detail TEXT NOT NULL
);

-- Reactivos disponibles en la mesa de mezclas
CREATE TABLE reagents (
  id VARCHAR(30) PRIMARY KEY,
  name VARCHAR(80) NOT NULL,
  color_hex CHAR(7) NOT NULL,
  icon VARCHAR(10) NOT NULL,
  description TEXT NOT NULL
);

-- Recetas de reacciones entre 2 reactivos
CREATE TABLE mixture_reactions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  reagent_a_id VARCHAR(30) NOT NULL,
  reagent_b_id VARCHAR(30) NOT NULL,
  result_name VARCHAR(100) NOT NULL,
  description TEXT NOT NULL,
  effect ENUM('burbujea', 'espuma', 'precipita', 'colorea', 'ninguno') NOT NULL DEFAULT 'ninguno',
  result_color_hex CHAR(7) NOT NULL,
  CONSTRAINT fk_reaction_reagent_a FOREIGN KEY (reagent_a_id) REFERENCES reagents(id),
  CONSTRAINT fk_reaction_reagent_b FOREIGN KEY (reagent_b_id) REFERENCES reagents(id),
  UNIQUE KEY unique_pair (reagent_a_id, reagent_b_id)
);

-- Reacciones que cada usuario ya descubrió (para el recetario y los puntos extra)
CREATE TABLE discovered_reactions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  reaction_id INT NOT NULL,
  discovered_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_discovered_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_discovered_reaction FOREIGN KEY (reaction_id) REFERENCES mixture_reactions(id) ON DELETE CASCADE,
  UNIQUE KEY unique_user_reaction (user_id, reaction_id)
);

-- Historial general de experimentos (todas las estaciones del laboratorio)
CREATE TABLE lab_experiments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  sample_id INT NULL,
  experiment_type ENUM('densidad', 'temperatura', 'identificacion', 'masa_volumen', 'mezcla') NOT NULL,
  result_summary VARCHAR(255) NOT NULL,
  points_awarded INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_experiment_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_experiment_sample FOREIGN KEY (sample_id) REFERENCES lab_samples(id) ON DELETE SET NULL
);

-- ============================================================
-- MÓDULO: SALAS DE EXAMEN (Kahoot)
-- ============================================================

-- Salas creadas por docentes; los estudiantes se unen con el código
CREATE TABLE IF NOT EXISTS quiz_rooms (
  id INT AUTO_INCREMENT PRIMARY KEY,
  creator_id INT NOT NULL,
  title VARCHAR(120) NOT NULL,
  code VARCHAR(8) NOT NULL UNIQUE,
  status ENUM('abierta', 'cerrada') NOT NULL DEFAULT 'abierta',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_room_creator FOREIGN KEY (creator_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Preguntas de cada sala (correct_option indica la posición 1-4 de la opción)
CREATE TABLE IF NOT EXISTS quiz_questions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  room_id INT NOT NULL,
  question_text VARCHAR(255) NOT NULL,
  correct_option SMALLINT NOT NULL,
  points_value INT NOT NULL DEFAULT 10,
  CONSTRAINT fk_question_room FOREIGN KEY (room_id) REFERENCES quiz_rooms(id) ON DELETE CASCADE
);

-- Opciones de cada pregunta (4 por pregunta, posición 1-4)
CREATE TABLE IF NOT EXISTS quiz_question_options (
  id INT AUTO_INCREMENT PRIMARY KEY,
  question_id INT NOT NULL,
  option_position SMALLINT NOT NULL,
  option_text VARCHAR(120) NOT NULL,
  CONSTRAINT fk_option_question FOREIGN KEY (question_id) REFERENCES quiz_questions(id) ON DELETE CASCADE,
  UNIQUE KEY unique_position (question_id, option_position)
);

-- Un intento por estudiante y por sala (integridad de la evaluación)
CREATE TABLE IF NOT EXISTS quiz_attempts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  room_id INT NOT NULL,
  user_id INT NOT NULL,
  score INT NOT NULL DEFAULT 0,
  total_questions INT NOT NULL,
  finished_at TIMESTAMP NULL DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_attempt_room FOREIGN KEY (room_id) REFERENCES quiz_rooms(id) ON DELETE CASCADE,
  CONSTRAINT fk_attempt_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  UNIQUE KEY unique_room_user (room_id, user_id)
);

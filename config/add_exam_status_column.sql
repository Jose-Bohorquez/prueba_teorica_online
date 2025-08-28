-- Agregar columna para rastrear el estado del examen del usuario
ALTER TABLE users ADD COLUMN exam_status ENUM('allowed', 'annulled') DEFAULT 'allowed';

-- Agregar columna para rastrear la fecha de anulación
ALTER TABLE users ADD COLUMN exam_annulled_at TIMESTAMP NULL DEFAULT NULL;

-- Agregar comentarios para documentar las columnas
ALTER TABLE users MODIFY COLUMN exam_status ENUM('allowed', 'annulled') DEFAULT 'allowed' COMMENT 'Estado del examen: allowed=puede hacer examen, annulled=examen anulado';
ALTER TABLE users MODIFY COLUMN exam_annulled_at TIMESTAMP NULL DEFAULT NULL COMMENT 'Fecha y hora cuando se anuló el examen';
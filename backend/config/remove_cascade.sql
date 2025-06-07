/*
*    File        : backend/config/remove_cascade.sql
*    Project     : CRUD PHP
*    Author      : Tecnologías Informáticas B - Facultad de Ingeniería - UNMdP
*    License     : http://www.gnu.org/licenses/gpl.txt  GNU GPL 3.0
*    Date        : Mayo 2025
*    Status      : Prototype
*    Iteration   : 3.0 ( prototype )
*    Description : Script para deshabilitar ON DELETE CASCADE en la tabla students_subjects
*/

USE students_db_3;

-- Deshabilitar las constraints de foreign key temporalmente
SET FOREIGN_KEY_CHECKS = 0;

-- Eliminar las foreign keys existentes con CASCADE
ALTER TABLE students_subjects 
DROP FOREIGN KEY students_subjects_ibfk_1;

ALTER TABLE students_subjects 
DROP FOREIGN KEY students_subjects_ibfk_2;

-- Recrear las foreign keys sin CASCADE (RESTRICT por defecto)
ALTER TABLE students_subjects 
ADD CONSTRAINT fk_students_subjects_student_id 
FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE RESTRICT;

ALTER TABLE students_subjects 
ADD CONSTRAINT fk_students_subjects_subject_id 
FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE RESTRICT;

-- Reactivar las constraints de foreign key
SET FOREIGN_KEY_CHECKS = 1;

-- Mostrar la estructura actualizada de la tabla
DESCRIBE students_subjects;

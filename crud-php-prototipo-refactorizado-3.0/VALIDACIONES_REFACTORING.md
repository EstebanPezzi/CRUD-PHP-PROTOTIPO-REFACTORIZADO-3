# Refactorización - Validaciones Implementadas

## Fecha: Junio 2025
## Versión: 3.0

### 1. Validación de Materias Duplicadas

**Ubicación:** `backend/models/subjects.php` y `backend/controllers/subjectsController.php`

**Funcionalidad:** 
- Verifica que no existan dos materias con el mismo nombre
- Se aplica en CREATE y UPDATE
- Función: `subjectExistsByName($conn, $name, $excludeId = null)`

**Mensaje de error:** "Ya existe una materia con ese nombre"

### 2. Validación de Relaciones Estudiante-Materia Duplicadas

**Ubicación:** `backend/models/studentsSubjects.php` y `backend/controllers/studentsSubjectsController.php`

**Funcionalidad:**
- Verifica que no exista la misma relación estudiante-materia
- Se aplica en CREATE y UPDATE de asignaciones
- Función: `relationExists($conn, $student_id, $subject_id, $excludeId = null)`

**Mensaje de error:** "Esta relación estudiante-materia ya existe"

### 3. Deshabilitación de ON DELETE CASCADE

**Ubicación:** `backend/config/remove_cascade.sql`

**Funcionalidad:**
- Script SQL para remover las foreign keys con CASCADE
- Recrear las foreign keys con RESTRICT
- Esto requiere que las validaciones de negocio manejen las eliminaciones

**Validaciones agregadas:**
- En `studentsController.php`: Verificar si el estudiante tiene materias asignadas antes de eliminar
- En `subjectsController.php`: Verificar si la materia tiene estudiantes asignados antes de eliminar

**Mensajes de error:** 
- "No se puede eliminar el estudiante porque tiene materias asignadas"
- "No se puede eliminar la materia porque tiene estudiantes asignados"

### 4. Validaciones Adicionales Implementadas

#### A. Validación de Datos de Estudiantes
**Función:** `validateStudentData($fullname, $email, $age)`
- Nombre completo: obligatorio, mínimo 2 caracteres
- Email: obligatorio, formato válido, único
- Edad: numérica, entre 16 y 100 años

#### B. Validación de Datos de Materias
**Función:** `validateSubjectData($name)`
- Nombre: obligatorio, mínimo 3 caracteres, máximo 100 caracteres

#### C. Validación de Relaciones Estudiante-Materia
**Función:** `validateStudentSubjectData($student_id, $subject_id, $approved)`
- student_id: numérico y mayor a 0
- subject_id: numérico y mayor a 0
- approved: debe ser 0, 1, true o false

#### D. Validación de Existencia de Entidades
**Funciones:**
- `studentExists($conn, $student_id)`: Verifica que el estudiante existe
- `subjectExists($conn, $subject_id)`: Verifica que la materia existe

### 5. Instrucciones de Implementación

1. **Ejecutar el script SQL:**
   ```sql
   source backend/config/remove_cascade.sql
   ```

2. **Verificar validaciones en frontend:**
   - Las validaciones del backend son independientes del frontend
   - Se recomienda agregar validaciones JavaScript correspondientes

3. **Mensajes de error unificados:**
   - Todos los errores devuelven JSON con estructura: `{"error": "mensaje"}`
   - Código HTTP 400 para errores de validación
   - Código HTTP 500 para errores del servidor

### 6. Mejoras Adicionales Sugeridas

1. **Logging:** Implementar logs de errores y operaciones críticas
2. **Sanitización:** Validar y sanitizar todas las entradas del usuario
3. **Rate Limiting:** Limitar cantidad de peticiones por IP
4. **Validación de autorización:** Implementar roles y permisos de usuario
5. **Backup automático:** Antes de operaciones DELETE críticas
6. **Transacciones:** Envolver operaciones complejas en transacciones
7. **Paginación:** Para consultas que devuelven muchos registros
8. **Caché:** Implementar caché para consultas frecuentes

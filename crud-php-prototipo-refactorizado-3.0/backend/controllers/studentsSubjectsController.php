<?php
/**
*    File        : backend/controllers/studentsSubjectsController.php
*    Project     : CRUD PHP
*    Author      : Tecnologías Informáticas B - Facultad de Ingeniería - UNMdP
*    License     : http://www.gnu.org/licenses/gpl.txt  GNU GPL 3.0
*    Date        : Mayo 2025
*    Status      : Prototype
*    Iteration   : 3.0 ( prototype )
*/

require_once("./models/studentsSubjects.php");

function handleGet($conn) 
{
    $studentsSubjects = getAllSubjectsStudents($conn);
    echo json_encode($studentsSubjects);
}

function handlePost($conn) 
{
    $input = json_decode(file_get_contents("php://input"), true);
    
    // Validar datos básicos
    $errors = validateStudentSubjectData($input['student_id'], $input['subject_id'], $input['approved']);
    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode(["error" => implode(", ", $errors)]);
        return;
    }
    
    // Validar que el estudiante existe
    if (!studentExists($conn, $input['student_id'])) {
        http_response_code(400);
        echo json_encode(["error" => "El estudiante especificado no existe"]);
        return;
    }
    
    // Validar que la materia existe
    if (!subjectExists($conn, $input['subject_id'])) {
        http_response_code(400);
        echo json_encode(["error" => "La materia especificada no existe"]);
        return;
    }
    
    // Validar si ya existe esta relación estudiante-materia
    if (relationExists($conn, $input['student_id'], $input['subject_id'])) {
        http_response_code(400);
        echo json_encode(["error" => "Esta relación estudiante-materia ya existe"]);
        return;
    }
    
    $result = assignSubjectToStudent($conn, $input['student_id'], $input['subject_id'], $input['approved']);
    if ($result['inserted'] > 0) 
    {
        echo json_encode(["message" => "Asignación realizada"]);
    } 
    else 
    {
        http_response_code(500);
        echo json_encode(["error" => "Error al asignar"]);
    }
}

function handlePut($conn) 
{
    $input = json_decode(file_get_contents("php://input"), true);

    if (!isset($input['id'], $input['student_id'], $input['subject_id'], $input['approved'])) 
    {
        http_response_code(400);
        echo json_encode(["error" => "Datos incompletos"]);
        return;
    }

    // Validar datos básicos
    $errors = validateStudentSubjectData($input['student_id'], $input['subject_id'], $input['approved']);
    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode(["error" => implode(", ", $errors)]);
        return;
    }
    
    // Validar que el estudiante existe
    if (!studentExists($conn, $input['student_id'])) {
        http_response_code(400);
        echo json_encode(["error" => "El estudiante especificado no existe"]);
        return;
    }
    
    // Validar que la materia existe
    if (!subjectExists($conn, $input['subject_id'])) {
        http_response_code(400);
        echo json_encode(["error" => "La materia especificada no existe"]);
        return;
    }

    // Validar si ya existe esta relación estudiante-materia (excluyendo la actual)
    if (relationExists($conn, $input['student_id'], $input['subject_id'], $input['id'])) {
        http_response_code(400);
        echo json_encode(["error" => "Esta relación estudiante-materia ya existe"]);
        return;
    }

    $result = updateStudentSubject($conn, $input['id'], $input['student_id'], $input['subject_id'], $input['approved']);
    if ($result['updated'] > 0) 
    {
        echo json_encode(["message" => "Actualización correcta"]);
    } 
    else 
    {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo actualizar"]);
    }
}

function handleDelete($conn) 
{
    $input = json_decode(file_get_contents("php://input"), true);

    $result = removeStudentSubject($conn, $input['id']);
    if ($result['deleted'] > 0) 
    {
        echo json_encode(["message" => "Relación eliminada"]);
    } 
    else 
    {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo eliminar"]);
    }
}
?>

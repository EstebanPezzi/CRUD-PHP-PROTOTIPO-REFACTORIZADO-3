<?php
/**
*    File        : backend/controllers/studentsController.php
*    Project     : CRUD PHP
*    Author      : Tecnologías Informáticas B - Facultad de Ingeniería - UNMdP
*    License     : http://www.gnu.org/licenses/gpl.txt  GNU GPL 3.0
*    Date        : Mayo 2025
*    Status      : Prototype
*    Iteration   : 3.0 ( prototype )
*/

require_once("./models/students.php");

function handleGet($conn) 
{
    $input = json_decode(file_get_contents("php://input"), true);
    
    if (isset($input['id'])) 
    {
        $student = getStudentById($conn, $input['id']);
        echo json_encode($student);
    } 
    else
    {
        $students = getAllStudents($conn);
        echo json_encode($students);
    }
}

function handlePost($conn) 
{
    $input = json_decode(file_get_contents("php://input"), true);

    // Validar datos del estudiante
    $errors = validateStudentData($input['fullname'], $input['email'], $input['age']);
    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode(["error" => implode(", ", $errors)]);
        return;
    }

    // Validar si el email ya existe
    if (emailExists($conn, $input['email'])) {
        http_response_code(400);
        echo json_encode(["error" => "Ya existe un estudiante con ese email"]);
        return;
    }

    $result = createStudent($conn, $input['fullname'], $input['email'], $input['age']);
    if ($result['inserted'] > 0) 
    {
        echo json_encode(["message" => "Estudiante agregado correctamente"]);
    } 
    else 
    {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo agregar"]);
    }
}

function handlePut($conn) 
{
    $input = json_decode(file_get_contents("php://input"), true);

    // Validar datos del estudiante
    $errors = validateStudentData($input['fullname'], $input['email'], $input['age']);
    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode(["error" => implode(", ", $errors)]);
        return;
    }

    // Validar si el email ya existe (excluyendo el estudiante actual)
    if (emailExists($conn, $input['email'], $input['id'])) {
        http_response_code(400);
        echo json_encode(["error" => "Ya existe un estudiante con ese email"]);
        return;
    }

    $result = updateStudent($conn, $input['id'], $input['fullname'], $input['email'], $input['age']);
    if ($result['updated'] > 0) 
    {
        echo json_encode(["message" => "Actualizado correctamente"]);
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

    // Validar si el estudiante tiene relaciones en students_subjects
    if (studentHasRelations($conn, $input['id'])) {
        http_response_code(400);
        echo json_encode(["error" => "No se puede eliminar el estudiante porque tiene materias asignadas"]);
        return;
    }

    $result = deleteStudent($conn, $input['id']);
    if ($result['deleted'] > 0) 
    {
        echo json_encode(["message" => "Eliminado correctamente"]);
    } 
    else 
    {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo eliminar"]);
    }
}
?>
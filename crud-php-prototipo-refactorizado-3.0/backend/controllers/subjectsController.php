<?php
/**
*    File        : backend/controllers/subjectsController.php
*    Project     : CRUD PHP
*    Author      : Tecnologías Informáticas B - Facultad de Ingeniería - UNMdP
*    License     : http://www.gnu.org/licenses/gpl.txt  GNU GPL 3.0
*    Date        : Mayo 2025
*    Status      : Prototype
*    Iteration   : 3.0 ( prototype )
*/

require_once("./models/subjects.php");

function handleGet($conn) 
{
    $input = json_decode(file_get_contents("php://input"), true);

    if (isset($input['id'])) 
    {
        $subject = getSubjectById($conn, $input['id']);
        echo json_encode($subject);
    } 
    else 
    {
        $subjects = getAllSubjects($conn);
        echo json_encode($subjects);
    }
}

function handlePost($conn) 
{
    $input = json_decode(file_get_contents("php://input"), true);

    // Validar datos de la materia
    $errors = validateSubjectData($input['name']);
    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode(["error" => implode(", ", $errors)]);
        return;
    }

    // Validar si ya existe una materia con el mismo nombre
    if (subjectExistsByName($conn, $input['name'])) {
        http_response_code(400);
        echo json_encode(["error" => "Ya existe una materia con ese nombre"]);
        return;
    }

    $result = createSubject($conn, $input['name']);
    if ($result['inserted'] > 0) 
    {
        echo json_encode(["message" => "Materia creada correctamente"]);
    } 
    else 
    {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo crear"]);
    }
}

function handlePut($conn) 
{
    $input = json_decode(file_get_contents("php://input"), true);

    // Validar datos de la materia
    $errors = validateSubjectData($input['name']);
    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode(["error" => implode(", ", $errors)]);
        return;
    }

    // Validar si ya existe una materia con el mismo nombre (excluyendo la actual)
    if (subjectExistsByName($conn, $input['name'], $input['id'])) {
        http_response_code(400);
        echo json_encode(["error" => "Ya existe una materia con ese nombre"]);
        return;
    }

    $result = updateSubject($conn, $input['id'], $input['name']);
    if ($result['updated'] > 0) 
    {
        echo json_encode(["message" => "Materia actualizada correctamente"]);
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
    
    // Validar si la materia tiene relaciones en students_subjects
    if (subjectHasRelations($conn, $input['id'])) {
        http_response_code(400);
        echo json_encode(["error" => "No se puede eliminar la materia porque tiene estudiantes asignados"]);
        return;
    }
    
    $result = deleteSubject($conn, $input['id']);
    if ($result['deleted'] > 0) 
    {
        echo json_encode(["message" => "Materia eliminada correctamente"]);
    } 
    else 
    {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo eliminar"]);
    }
}
?>
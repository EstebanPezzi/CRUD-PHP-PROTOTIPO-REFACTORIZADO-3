<?php
/**
*    File        : backend/models/students.php
*    Project     : CRUD PHP
*    Author      : Tecnologías Informáticas B - Facultad de Ingeniería - UNMdP
*    License     : http://www.gnu.org/licenses/gpl.txt  GNU GPL 3.0
*    Date        : Mayo 2025
*    Status      : Prototype
*    Iteration   : 3.0 ( prototype )
*/

function getAllStudents($conn) 
{
    $sql = "SELECT * FROM students";

    //MYSQLI_ASSOC devuelve un array ya listo para convertir en JSON:
    return $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
}

function getStudentById($conn, $id) 
{
    $stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    //fetch_assoc() devuelve un array asociativo ya listo para convertir en JSON de una fila:
    return $result->fetch_assoc(); 
}

function studentHasRelations($conn, $id) 
{
    $sql = "SELECT COUNT(*) as count FROM students_subjects WHERE student_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return $row['count'] > 0;
}

function emailExists($conn, $email, $excludeId = null) 
{
    $sql = "SELECT COUNT(*) as count FROM students WHERE email = ?";
    $params = [$email];
    $types = "s";
    
    if ($excludeId !== null) {
        $sql .= " AND id != ?";
        $params[] = $excludeId;
        $types .= "i";
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return $row['count'] > 0;
}

function validateStudentData($fullname, $email, $age) 
{
    $errors = [];
    
    // Validar nombre completo
    if (empty(trim($fullname))) {
        $errors[] = "El nombre completo es obligatorio";
    } elseif (strlen($fullname) < 2) {
        $errors[] = "El nombre completo debe tener al menos 2 caracteres";
    }
    
    // Validar email
    if (empty(trim($email))) {
        $errors[] = "El email es obligatorio";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "El email no tiene un formato válido";
    }
    
    // Validar edad
    if (!is_numeric($age) || $age < 0 || $age > 100) {
        $errors[] = "La edad debe ser un número entre 16 y 100 años";
    }
    
    return $errors;
}

function createStudent($conn, $fullname, $email, $age) 
{
    $sql = "INSERT INTO students (fullname, email, age) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $fullname, $email, $age);
    $stmt->execute();

    //Se retorna un arreglo con la cantidad e filas insertadas 
    //y id insertado para validar en el controlador:
    return 
    [
        'inserted' => $stmt->affected_rows,        
        'id' => $conn->insert_id
    ];
}

function updateStudent($conn, $id, $fullname, $email, $age) 
{
    $sql = "UPDATE students SET fullname = ?, email = ?, age = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $fullname, $email, $age, $id);
    $stmt->execute();

    //Se retorna fila afectadas para validar en controlador:
    return ['updated' => $stmt->affected_rows];
}

function deleteStudent($conn, $id) 
{
    $sql = "DELETE FROM students WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    //Se retorna fila afectadas para validar en controlador
    return ['deleted' => $stmt->affected_rows];
}
?>
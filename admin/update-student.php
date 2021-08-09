<?php

require_once "../php/functions.php";
require_once "../php/repository.php";
if (isAdmin()) {
    header('Content-type: application/json');

    $student_id = trim(filter_input(INPUT_POST, "student-id", FILTER_SANITIZE_NUMBER_INT), " \t\n");
    $student_name = trim(filter_input(INPUT_POST, "student-name", FILTER_SANITIZE_STRING), " \t\n");
    $group_id = trim(filter_input(INPUT_POST, "group-id", FILTER_SANITIZE_STRING), " \t\n");
    
    $status = "error";
    $msg = "Values can't be empty";
    if (!empty($student_id) && !empty($student_name) && !empty($group_id)) {
        if (updateStudent($student_id, $student_name, $group_id)) {
            $status = "ok";
            $msg = "Student has been updated";
        } else {
            $msg = "Error updating student";
        }
    }

    $response = new \stdClass();
    $response->status = $status;
    $response->msg = $msg;

    echo json_encode($response);
} else {
    logOut();
    header("HTTP/1.0 401 Unauthenticated");
    include '../401.php';
    exit();
}
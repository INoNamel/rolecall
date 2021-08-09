<?php

require_once "../php/functions.php";
require_once "../php/repository.php";
if (isAdmin()) {
    header('Content-type: application/json');

    $student_name = trim(filter_input(INPUT_POST, "student-name", FILTER_SANITIZE_STRING), " \t\n");
    $student_email = trim(filter_input(INPUT_POST, "student-email", FILTER_SANITIZE_STRING), " \t\n");
    $student_pwd = trim(filter_input(INPUT_POST, "student-pwd", FILTER_SANITIZE_STRING), " \t\n");
    $group_id = trim(filter_input(INPUT_POST, "group-id", FILTER_SANITIZE_NUMBER_INT), " \t\n");
    
    $status = "error";
    $msg = "Values can't be empty";
    
    if (!empty($student_email) && !empty($student_name) && !empty($student_pwd) && !empty($group_id) && addNewStudent($student_email, $student_name, $student_pwd, $group_id)) {
        $status = "ok";
        $msg = "New lecture added";
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
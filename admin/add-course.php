<?php

require_once "../php/functions.php";
require_once "../php/repository.php";
if (isAdmin()) {
    header('Content-type: application/json');

    $course_name = trim(filter_input(INPUT_POST, "course-name", FILTER_SANITIZE_STRING), " \t\n");
    
    $status = "error";
    $msg = "Values can't be empty";
    
    if (!empty($course_name) && addNewCourse($course_name)) {
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
<?php

require_once "../php/functions.php";
require_once "../php/repository.php";
if (isAdmin()) {
    header('Content-type: application/json');

    $course_id = trim(filter_input(INPUT_POST, "course-id", FILTER_SANITIZE_NUMBER_INT), " \t\n");
    $course_name = trim(filter_input(INPUT_POST, "course-name", FILTER_SANITIZE_STRING), " \t\n");
    $course_teacher = trim(filter_input(INPUT_POST, "course-teacher", FILTER_SANITIZE_STRING), " \t\n");
    
    $status = "error";
    $msg = "Values can't be empty";
    if (!empty($course_id) && !empty($course_name)) {
        if (updateCourse($course_id, $course_name, empty($course_teacher) ? null : $course_teacher)) {
            $status = "ok";
            $msg = "Course has been updated";
        } else {
            $msg = "Error updating course";
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
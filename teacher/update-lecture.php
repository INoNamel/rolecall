<?php

require_once "../php/functions.php";
require_once "../php/repository.php";
if (isTeacher()) {
    header('Content-type: application/json');

    $lecture_id = trim(filter_input(INPUT_POST, "lecture-id", FILTER_SANITIZE_NUMBER_INT), " \t\n");
    $lecture_state = trim(filter_input(INPUT_POST, "lecture-state", FILTER_SANITIZE_NUMBER_INT), " \t\n");
    $lecture_name = trim(filter_input(INPUT_POST, "lecture-name", FILTER_SANITIZE_STRING), " \t\n");


    $status = "error";
    $msg = "Values can't be empty";
    if (!empty($lecture_id) && !empty($lecture_state) && !empty($lecture_name)) {
        if (updateLecture($lecture_id, $lecture_state, $lecture_name)) {
            $status = "ok";
            $msg = "Lecture has been updated";
        } else {
            $msg = "Error updating lecture";
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
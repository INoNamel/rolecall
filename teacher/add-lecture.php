<?php

require_once "../php/functions.php";
require_once "../php/repository.php";
if (isTeacher()) {
    header('Content-type: application/json');

    $lecture_name = trim(filter_input(INPUT_POST, "lecture-name", FILTER_SANITIZE_STRING), " \t\n");
    $course_id = trim(filter_input(INPUT_POST, "course-id", FILTER_SANITIZE_STRING), " \t\n");

    //$ip = $_SERVER['REMOTE_ADDR'];
    $ip = "194.255.34.95";

    $ip_from = "https://freegeoip.app/json/$ip";
    if (!empty($ip_from)) {
        $visitor_from = json_decode(file_get_contents($ip_from));

        $lat = $visitor_from->latitude;
        $long = $visitor_from->longitude;
    } else {
        $msg = "Unable to determine location";
    }
    
    if (!empty($lecture_name) && !empty($ip_from) && addNewLecture($lecture_name, $course_id, getAccountID(), $lat, $long)) {
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
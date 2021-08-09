<?php

require_once "php/functions.php";
require_once "php/repository.php";

if (isStudent()) {
    header('Content-type: application/json');

    $status = "error";
    $msg = "--";
    $lecture_id = filter_input(INPUT_POST, "lecture-id", FILTER_SANITIZE_STRING);

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

    if (!empty($ip_from) && !empty($lecture_id)) {
        $lecture = getLectureByID($lecture_id);
        if ($lecture != null) {
            if ($lecture->getState() == "In progress" && abs($lecture->getLocation_latitude() - $lat) < 1 && abs($lecture->getLocation_longitude() - $long) < 1) {
                if (enlistMe(getAccountID(), $lecture_id)) {
                    $status = "ok";
                    $msg = "Successfully attending";
                } else {
                    $msg = "Failed to attend";
                }
            } else {
                $msg = "Your current position is too far away from the lecture";
            }
        } else {
            $msg = "Lecture unavailable";
        }
    }

    $response = new \stdClass();
    $response->status = $status;
    $response->msg = $msg;
    echo json_encode($response);
} else {
    logOut();
    header("HTTP/1.0 401 Unauthenticated");
    include '401.php';
    exit();
}



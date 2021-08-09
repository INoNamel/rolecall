<?php

class Lecture {
    private $lecture_id = NULL;
    private $lecture_name = NULL;
    private $course_name = NULL;
    private $starts_at = NULL;
    private $ends_at = NULL;
    private $location_latitude = NULL;
    private $location_longitude = NULL;
    private $state = NULL;
    private $teacher = NULL;
    
    function __construct($lecture_id, $lecture_name, $course_name, $starts_at, $ends_at, $location_latitude, $location_longitude, $state, $teacher) {
        $this->lecture_id = $lecture_id;
        $this->lecture_name = $lecture_name;
        $this->course_name = $course_name;
        $this->starts_at = $starts_at;
        $this->ends_at = $ends_at;
        $this->location_latitude = $location_latitude;
        $this->location_longitude = $location_longitude;
        $this->state = $state;
        $this->teacher = $teacher;
    }

    function getLecture_id() {
        return $this->lecture_id;
    }

    function getLecture_name() {
        return $this->lecture_name;
    }

    function getCourse_name() {
        return $this->course_name;
    }

    function getStarts_at() {
        return $this->starts_at;
    }

    function getEnds_at() {
        return $this->ends_at;
    }

    function getLocation_latitude() {
        return $this->location_latitude;
    }

    function getLocation_longitude() {
        return $this->location_longitude;
    }

    function getState() {
        return $this->state;
    }

    function getTeacher() {
        return $this->teacher;
    }

    function setLecture_id($lecture_id): void {
        $this->lecture_id = $lecture_id;
    }

    function setLecture_name($lecture_name): void {
        $this->lecture_name = $lecture_name;
    }

    function setCourse_name($course_name): void {
        $this->course_name = $course_name;
    }

    function setStarts_at($starts_at): void {
        $this->starts_at = $starts_at;
    }

    function setEnds_at($ends_at): void {
        $this->ends_at = $ends_at;
    }

    function setLocation_latitude($location_latitude): void {
        $this->location_latitude = $location_latitude;
    }

    function setLocation_longitude($location_longitude): void {
        $this->location_longitude = $location_longitude;
    }

    function setState($state): void {
        $this->state = $state;
    }

    function setTeacher($teacher): void {
        $this->teacher = $teacher;
    }


}
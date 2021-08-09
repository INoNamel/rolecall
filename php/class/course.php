<?php

class Course {

    private $course_id = NULL;
    private $course_name = NULL;
    private $teacher = NULL;
    private $teacher_name = NULL;

    function __construct($course_id, $course_name, $teacher, $teacher_name) {
        $this->course_id = $course_id;
        $this->course_name = $course_name;
        $this->teacher = $teacher;
        $this->teacher_name = $teacher_name;
    }

    function getCourse_id() {
        return $this->course_id;
    }

    function getCourse_name() {
        return $this->course_name;
    }

    function getTeacher() {
        return $this->teacher;
    }

    function getTeacher_name() {
        return $this->teacher_name;
    }

    function setCourse_id($course_id): void {
        $this->course_id = $course_id;
    }

    function setCourse_name($course_name): void {
        $this->course_name = $course_name;
    }

    function setTeacher($teacher): void {
        $this->teacher = $teacher;
    }

    function setTeacher_name($teacher_name): void {
        $this->teacher_name = $teacher_name;
    }

}

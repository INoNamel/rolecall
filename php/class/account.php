<?php

class Account {
    
    private $account_id = NULL;
    private $email = NULL;
    private $name = NULL;
    private $pwd = NULL;
    private $role = NULL;
    private $registration_date = NULL;
    private $course_group = NULL;
    
    function __construct($account_id, $email, $name, $pwd, $registration_date, $role, $course_group) {
        $this->account_id = $account_id;
        $this->email = $email;
        $this->name = $name;
        $this->pwd = $pwd;
        $this->role = $role;
        $this->registration_date = $registration_date;
        $this->course_group = $course_group;
    }

    function getAccount_id() {
        return $this->account_id;
    }

    function getEmail() {
        return $this->email;
    }

    function getName() {
        return $this->name;
    }

    function getPwd() {
        return $this->pwd;
    }

    function getRole() {
        return $this->role;
    }

    function getRegistration_date() {
        return $this->registration_date;
    }

    function getCourse_group() {
        return $this->course_group;
    }

    function setAccount_id($account_id): void {
        $this->account_id = $account_id;
    }

    function setEmail($email): void {
        $this->email = $email;
    }

    function setName($name): void {
        $this->name = $name;
    }

    function setPwd($pwd): void {
        $this->pwd = $pwd;
    }

    function setRole($role): void {
        $this->role = $role;
    }

    function setRegistration_date($registration_date): void {
        $this->registration_date = $registration_date;
    }

    function setCourse_group($course_group): void {
        $this->course_group = $course_group;
    }


}


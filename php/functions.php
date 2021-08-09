<?php

session_start();

function logOut() {
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }
}
function getAccountInfo() {
    return $_SESSION["user_info"];
}

function getAccountID() {
    return $_SESSION["user_info"]["account_id"];
}

function isLoggedIn() {
    if (isset($_SESSION['login_success']) && $_SESSION['login_success']) {
        return true;
    } else {
        return false;
    }
}

function isAdmin() {
    if (isLoggedIn() && $_SESSION["user_info"]["role"] === "Admin") {
        return true;
    } else {
        return false;
    }
}

function isTeacher() {
    if (isLoggedIn() && $_SESSION["user_info"]["role"] === "Teacher") {
        return true;
    } else {
        return false;
    }
}

function isStudent() {
    if (isLoggedIn() && $_SESSION["user_info"]["role"] === "Student") {
        return true;
    } else {
        return false;
    }
}

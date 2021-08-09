<?php

header('Content-type: application/json');
require_once "php/functions.php";
require_once "php/repository.php";

$login_email = filter_input(INPUT_POST, 'login-email', FILTER_SANITIZE_EMAIL);
$login_pwd = $_POST['login-pwd'];

$response = new \stdClass();
$status = "error";
$msg = "Authorize yourself";

if (!empty($login_email) && !empty($login_pwd)) {
    $msg = "Authorization failed";
    
    $login = selectLogin($login_email);
    if ($login != null) {
        //if (password_verify($login_pwd, $login->getPwd())) {
        
        if ($login_pwd == $login->getPwd()) {
            $_SESSION['login_success'] = true;
            $user_info = array(
                'account_id' => $login->getAccount_id(),
                'email' => $login->getEmail(),
                'name' => $login->getName(),
                'role' => $login->getRole(),
                'course_group' => $login->getCourse_group(),
                'registration_date' => $login->getRegistration_date());
            $_SESSION['user_info'] = $user_info;

            $status = "ok";
            $msg = "Successfully logged in";
        }
    }
}

$response->status = $status;
$response->msg = $msg;
echo json_encode($response);



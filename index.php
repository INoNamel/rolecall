<?php
require_once 'php/functions.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Role Call</title>
        <base href="http://localhost/rolecall/index.php">
        <?php
        include_once 'include/meta.php';
        ?>
    </head>
    <body>
        <div id="msg-box"></div>
        <?php
            if(isLoggedIn()) {
                
        ?>
        <a href="profile/courses">courses</a>
        <br><br>
        <a href="logout">logout</a>
        <?php
            } else {
        ?>
        <form class="log-in-form" action="log-in.php" method="post">
            <p><label for="login-email">Account</label></p>
            <input type="email" placeholder="Your E-mail" name="login-email" required="required">
            <p><label for="login-pwd">Password</label></p>
            <input type="password" placeholder="************" name="login-pwd" autocomplete="on" required="required">
            <br><br>
            <div class="flex">
            <input type="submit" class="btn-login" name="log-in" value="Sign In" />
            </div>
        </form>
        <?php
            }
        ?>
        <script>
        $(document).ready(function () {
            $('.log-in-form').submit(function (event) {
                event.preventDefault();
                $.when($.ajax({
                    type: 'POST',
                    url: 'log-in.php',
                    data: $(this).serialize()
                }).done(function (data) {
                    if(data.status === "ok") {
                        location.href = "profile/courses";
                    }
                    $("#msg-box").html(data.msg).toggle(1).delay(3300).fadeOut();
                }).fail(function () {
                    $("#msg-box").html("Error handling proccess").toggle(1).delay(1300).fadeOut();
                }));
            });
        });
        </script>
    </body>
</html>

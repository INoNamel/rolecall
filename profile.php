<?php
require_once 'php/functions.php';
require_once 'php/repository.php';
if (!isLoggedIn()) {
    logOut();
    header("HTTP/1.0 401 Unauthenticated");
    include '401.php';
    exit();
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Profile - Role Call</title>
        <base href="http://localhost/rolecall/profile.php">
        <?php
        include_once 'include/meta.php';

        $section_ = trim(filter_input(INPUT_GET, "section", FILTER_SANITIZE_STRING), " \t\n\r");
        $course_id = trim(filter_input(INPUT_GET, "course", FILTER_SANITIZE_STRING), " \t\n\r");
        $section = empty($section_) ? "courses" : $section_;
        ?>
        <?php
        if ((isAdmin() || isTeacher()) && $section == "stats") {

            $attendance_for = "Total Attendance";
            if (isAdmin()) {
                $attendance = getTotalAttendance();
            } else {
                $courses = getAllCourses(getAccountID());
                $attendance = getTotalAttendanceForCourse(getAccountID(), $courses[0]->getCourse_id());
                $attendance_for = "Attendance for course: " . $courses[0]->getCourse_name();
            }

            $total_filled_lectures = $attendance[0]["total_lectures"] * $attendance[0]["total_students"];
            $attended_finished = $attendance[0]["attended_finished"] / $total_filled_lectures * 100;
            $attended_ongoing = $attendance[0]["attended_ongoing"] / $total_filled_lectures * 100;
            $ongoing = ($attendance[0]["total_students"] * $attendance[0]["ongoing_lectures"] - $attendance[0]["attended_ongoing"]) / $total_filled_lectures * 100;
            $not_present_finished = (100 - ($attended_finished + $attended_ongoing + $ongoing));

            $dataPoints = array(
                array("label" => "Attended finished lectures", "symbol" => "Attended finished", "y" => $attended_finished),
                array("label" => "Attended ongoing lectures", "symbol" => "Attended ongoing", "y" => $attended_ongoing),
                array("label" => "Not present at finished lectures", "symbol" => "Not present at finished", "y" => $not_present_finished),
                array("label" => "Ongoing lectures awaiting attendance", "symbol" => "Ongoing lectures", "y" => $ongoing),
                    )
            ?>
            <script src="scripts/canvas.min.js"></script>
            <script>
                window.onload = function () {
                    var chart = new CanvasJS.Chart("chartContainer", {
                        theme: "light2",
                        animationEnabled: true,
                        title: {
                            text: "<?php echo $attendance_for; ?>"
                        },
                        data: [{
                                type: "doughnut",
                                indexLabel: "{symbol} - {y}",
                                yValueFormatString: "#,##0.0\"%\"",
                                showInLegend: true,
                                legendText: "{label} : {y}",
                                dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
                            }]
                    });
                    chart.render();
                }
            </script>
            <?php
        }
        ?>
    </head>
    <body>
        <div id="msg-box"></div>
        <div>
            <?php
            if (isAdmin()) {
                $links = array("courses", "teachers", "students", "stats");
                foreach ($links as $link) {
                    echo "<a href='profile/" . $link . "'>[ " . $link . " ]</a>";
                }

                $course_groups = getAllCourseGroups();
            } else if (isTeacher()) {
                $links = array("courses", "stats");
                foreach ($links as $link) {
                    echo "<a href='profile/" . $link . "'>[ " . $link . " ]</a>";
                }
            }

            echo "<br><br>#" . getAccountID() . "<br>";
            echo getAccountInfo()['role'] . ":" . getAccountInfo()['name'];
            if (isStudent()) {
                echo " (" . getAccountInfo()['course_group'] . ") ";
            }
            ?>
        </div>
        <?php
        switch ($section) {
            case ("courses"):
                if (isTeacher()) {
                    $courses = getAllCourses(getAccountID());
                } else if (isAdmin()) {
                    $courses = getAllCourses(null);
                    $teachers = getAllTeachers();
                    echo "<br><button id='add-course'>New course</button>";
                    echo "<br><br><div class='add-course-container'>";
                    echo "<form action='admin/add-course.php' class='add-course-form' method='post'>";
                    echo "<label for='course-name'>Course name: <input type='text' name='course-name' placeholder='course name' required/></label>";
                    echo "<input type='submit' name='add-course' value='Add'>";
                    echo "</form>";
                    echo "</div>";
                } else {
                    $courses = getAllCourses(null);
                }

                if ($courses != null) {
                    echo "<table>";
                    echo "<tr><th>course id</th><th>course name</th><th>teacher</th></tr>";

                    foreach ($courses as $course) {
                        echo "<tr>";
                        echo "<td><a href='profile/course/" . $course->getCourse_id() . "/lectures' >" . $course->getCourse_id() . "</a></td>";
                        if (isAdmin()) {
                            echo "<form action='admin/update-course.php' class='update-course-form' method='post'>";
                            echo "<td>";
                            echo "<input type='text' name='course-name' value='" . $course->getCourse_name() . "' required />";
                            echo "<input type='hidden' name='course-id' value='" . $course->getCourse_id() . "' required />";
                            echo "</td>";
                            echo "<td>";
                            if ($teachers != null) {
                                echo "<select name='course-teacher' >";
                                echo "<option disabled selected>select teacher</option>";
                                foreach ($teachers as $teacher) {
                                    if ($teacher['account_id'] == $course->getTeacher()) {
                                        $selected = "selected";
                                    } else {
                                        $selected = "";
                                    }
                                    echo "<option value='" . $teacher['account_id'] . "' $selected>" . $teacher['name'] . "</option>";
                                }
                                echo "</select>";
                            }
                            echo "</td>";
                            echo "<td>";
                            echo "<input type='submit' name='update-course' value='Update'>";
                            echo "</td>";
                            echo "</form>";
                        } else {
                            echo "<td>";
                            echo $course->getCourse_name();
                            echo "</td>";
                            echo "<td>";
                            echo $course->getTeacher_name();
                            echo "</td>";
                        }
                        echo "</tr>";
                    }
                    echo "</table>";
                }
                break;
            case ("lectures"):
                ?> 
                <div>
                    <br><a href="profile/courses">Back to courses</a><br><br>
                </div>
                <?php
                if (!empty($course_id) && getCourse($course_id) != null) {
                    if (isTeacher()) {
                        echo "<button id='add-lecture'>New lecture</button>";
                        echo "<div class='add-lecture-container'>";
                        echo "<form action='teacher/add-lecture.php' class='add-lecture-form' method='post'>";
                        echo "<label for='lecture-name'>Lecture name: <input type='text' name='lecture-name' placeholder='lecture name' required/></label>";
                        echo "<input type='hidden' name='course-id' value='$course_id' required />";
                        echo "<input type='submit' name='add-lecture' value='Add'>";
                        echo "</form>";
                        echo "</div>";
                    }

                    $lectures = getLecturesFromCourse($course_id);
                    if ($lectures != null) {
                        $attendance = getAttendanceFor(getAccountID());
                        echo "<table>";
                        echo "<tr><th>state</th><th>course name</th><th>lecture name</th><th>initiated at</th><th>ended at</th><th>teacher</th><th>attendance</th></tr>";
                        foreach ($lectures as $lecture) {
                            echo "<tr>";
                            if (isTeacher()) {
                                echo "<form action='teacher/update-lecture.php' class='update-lecture-form' method='post'>";
                                $lecture_states = getAllLectureStates();
                                echo "<td>";
                                if ($lecture_states != null) {
                                    echo "<select name='lecture-state' required>";
                                    foreach ($lecture_states as $lecture_state) {
                                        if ($lecture_state['state'] == $lecture->getState()) {
                                            $selected = "selected";
                                        } else {
                                            $selected = "";
                                        }
                                        echo "<option value='" . $lecture_state['id'] . "' $selected>" . $lecture_state['state'] . "</option>";
                                    }
                                    echo "</select>";
                                } else {
                                    echo "No lecture states found";
                                }
                                echo "</td>";
                            } else if (isStudent()) {
                                echo "<td>" . $lecture->getState() . "</td>";
                            }

                            echo "<td>" . $lecture->getCourse_name() . "</td>";
                            echo "<td>";
                            if (isTeacher()) {
                                echo "<input type='text' name='lecture-name' placeholder='lecture name' value='" . $lecture->getLecture_name() . "' required />";
                            } else {
                                echo $lecture->getLecture_name();
                            }
                            echo "</td>";
                            echo "<td>" . $lecture->getStarts_at() . "</td>";
                            echo "<td>" . $lecture->getEnds_at() . "</td>";
                            echo "<td>" . $lecture->getTeacher() . "</td>";
                            echo "<td class='attendance-td'>";
                            if (isTeacher()) {
                                echo "<input type='hidden' name='lecture-id' value='" . $lecture->getLecture_id() . "' required />";
                                echo "<input type='submit' name='update-lecture' value='Update' />";
                                echo "</form>";
                            } else {
                                if ($attendance != null && in_array($lecture->getLecture_id(), $attendance)) {
                                    echo "<span style='color:lightgreen;'>Attended</span>";
                                } else {
                                    if ($lecture->getState() == "In progress") {
                                        echo "<button name='submit-attendance' class='submit-attendance' data-lecture-id='" . $lecture->getLecture_id() . "'>I am here</button>";
                                    } else {
                                        echo "<span style='color:red;'>Not Attended</span>";
                                    }
                                }
                            }
                            echo "</td>";
                            echo "</tr>";
                        }
                        echo "</table>";
                    } else {
                        echo "<h2>No lectures found</h2>";
                    }
                } else {
                    echo "<h2>Course not found</h2>";
                }
                break;

            case ("teachers") :
                if (isAdmin()) {
                    $teachers = getAllTeachers();
                    if ($teachers != null) {
                        echo "<table>";
                        echo "<tr><th>#</th><th>Teacher name</th></tr>";
                        foreach ($teachers as $teacher) {
                            echo "<tr>";
                            echo "<td>";
                            echo $teacher['account_id'];
                            echo "</td>";
                            echo "<td>";
                            echo $teacher['name'];
                            echo "</td>";
                            echo "</tr>";
                        }
                        echo "</table>";
                    }
                    break;
                }
            case ("students") :
                if (isAdmin()) {
                    echo "<br><form action='admin/add-student.php' class='add-student-form' method='post'>";
                    echo "<label for='student-email'>Student email: <input type='email' name='student-email' placeholder='student email' required/></label>";
                    echo "<label for='student-name'>Student name: <input type='text' name='student-name' placeholder='student name' required/></label>";
                    echo "<label for='student-pwd'>Password: <input type='password' name='student-pwd' placeholder='student password' required/></label>";
                    echo "<select name='group-id' required>";
                    if ($course_groups != null) {
                        foreach ($course_groups as $course_group) {
                            echo "<option value='" . $course_group['id'] . "'>" . $course_group['name'] . "</option>";
                        }
                    }
                    echo "</select>";
                    echo "<input type='submit' name='add-lecture' value='Add'>";
                    echo "</form>";
                    $students = getAllStudents();
                    if ($students != null) {
                        echo "<table>";
                        echo "<tr><th>#</th><th>Student name</th><th>Course group</th></tr>";
                        foreach ($students as $student) {
                            echo "<form action='admin/update-student.php' class='update-student-form' method='post'>";
                            echo "<tr>";
                            echo "<td>";
                            echo $student['account_id'];
                            echo "<input type='hidden' name='student-id' value='" . $student['account_id'] . "' required />";
                            echo "</td>";
                            echo "<td>";
                            echo "<input type='text' name='student-name' placeholder='student name' value='" . $student['student_name'] . "' required />";
                            echo "</td>";
                            echo "<td>";
                            if ($course_groups != null) {
                                echo "<select name='group-id' required>";
                                foreach ($course_groups as $course_group) {
                                    if ($course_group['id'] == $student['course_group_id']) {
                                        $selected = "selected";
                                    } else {
                                        $selected = "";
                                    }
                                    echo "<option value='" . $course_group['id'] . "' $selected>" . $course_group['name'] . "</option>";
                                }
                                echo "</select>";
                            }
                            echo "</td>";
                            echo "<td>";
                            echo "<input type='submit' name='update-student' value='Update' />";
                            echo "</td>";
                            echo "</tr>";
                            echo "</form>";
                        }
                        echo "</table>";
                    }
                }
                break;
            case ("stats"):
                ?>
                <div id="chartContainer" style="height: 370px; width: 100%;"></div>
                <?php
                break;
        }
        ?>
        <div><a href="logout">logout</a></div>
        <script>
            $(document).ready(function () {
                $('.submit-attendance').click(function (event) {
                    event.preventDefault();
                    let send = {'lecture-id': $(this).data('lecture-id')};
                    let this_td = $(this).closest("td");
                    $.when($.ajax({
                        type: 'POST',
                        url: 'enlist-me.php',
                        data: send
                    }).done(function (data) {
                        if (data.status !== "error") {
                            this_td.html("<span style='color:lightgreen;'>Attended</span>");
                        }
                        $("#msg-box").html(data.msg).toggle(1).delay(3300).fadeOut();
                    }).fail(function () {
                        $("#msg-box").html("Error handling process").toggle(1).delay(1300).fadeOut();
                    }));
                });

                $('.update-course-form').submit(function (event) {
                    event.preventDefault();
                    $.when($.ajax({
                        type: 'POST',
                        url: 'admin/update-course.php',
                        data: $(this).serialize()
                    }).done(function (data) {
                        if (data.status !== "error") {
                            localStorage.fadeInSuccessMessage = data.msg;
                            location.reload();
                        }
                        $("#msg-box").html(data.msg).toggle(1).delay(3300).fadeOut();
                    }).fail(function () {
                        $("#msg-box").html("Error handling process").toggle(1).delay(1300).fadeOut();
                    }));
                });

                $('.update-lecture-form').submit(function (event) {
                    event.preventDefault();
                    $.when($.ajax({
                        type: 'POST',
                        url: 'teacher/update-lecture.php',
                        data: $(this).serialize()
                    }).done(function (data) {
                        if (data.status !== "error") {
                            localStorage.fadeInSuccessMessage = data.msg;
                            location.reload();
                        }
                        $("#msg-box").html(data.msg).toggle(1).delay(3300).fadeOut();
                    }).fail(function () {
                        $("#msg-box").html("Error handling process").toggle(1).delay(1300).fadeOut();
                    }));
                });

                $('.update-student-form').submit(function (event) {
                    event.preventDefault();
                    $.when($.ajax({
                        type: 'POST',
                        url: 'admin/update-student.php',
                        data: $(this).serialize()
                    }).done(function (data) {
                        if (data.status !== "error") {
                            localStorage.fadeInSuccessMessage = data.msg;
                            location.reload();
                        }
                        $("#msg-box").html(data.msg).toggle(1).delay(3300).fadeOut();
                    }).fail(function () {
                        $("#msg-box").html("Error handling process").toggle(1).delay(1300).fadeOut();
                    }));
                });

                $('#add-lecture').click(function () {
                    $('.add-lecture-container').toggle();
                });

                $('#add-course').click(function () {
                    $('.add-course-container').toggle();
                });

                $('.add-lecture-form').submit(function (event) {
                    event.preventDefault();
                    $.when($.ajax({
                        type: 'POST',
                        url: 'teacher/add-lecture.php',
                        data: $(this).serialize()
                    }).done(function (data) {
                        if (data.status !== "error") {
                            localStorage.fadeInSuccessMessage = data.msg;
                            //location.reload();
                        }
                        $("#msg-box").html(data.msg).toggle(1).delay(3300).fadeOut();
                    }).fail(function () {
                        $("#msg-box").html("Error handling process").toggle(1).delay(1300).fadeOut();
                    }));
                });

                $('.add-course-form').submit(function (event) {
                    event.preventDefault();
                    $.when($.ajax({
                        type: 'POST',
                        url: 'admin/add-course.php',
                        data: $(this).serialize()
                    }).done(function (data) {
                        if (data.status !== "error") {
                            localStorage.fadeInSuccessMessage = data.msg;
                            location.reload();
                        }
                        $("#msg-box").html(data.msg).toggle(1).delay(3300).fadeOut();
                    }).fail(function () {
                        $("#msg-box").html("Error handling process").toggle(1).delay(1300).fadeOut();
                    }));
                });

                $('.add-student-form').submit(function (event) {
                    event.preventDefault();
                    $.when($.ajax({
                        type: 'POST',
                        url: 'admin/add-student.php',
                        data: $(this).serialize()
                    }).done(function (data) {
                        if (data.status !== "error") {
                            localStorage.fadeInSuccessMessage = data.msg;
                            location.reload();
                        }
                        $("#msg-box").html(data.msg).toggle(1).delay(3300).fadeOut();
                    }).fail(function () {
                        $("#msg-box").html("Error handling process").toggle(1).delay(1300).fadeOut();
                    }));
                });

                if ("fadeInSuccessMessage" in localStorage) {
                    $("#msg-box").html(localStorage.fadeInSuccessMessage).toggle(1).delay(1300).fadeOut();
                    delete localStorage.fadeInSuccessMessage;
                }
            });
        </script>
    </body>
</html>
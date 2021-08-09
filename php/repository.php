<?php

require_once 'db-config.php';
require_once 'db-connect.php';

function selectLogin($login) {
    require_once 'class/account.php';
    $mysql = new Dbconnect();
    $mysql->connect();

    $query = "SELECT account_id, email, accounts.name as account_name, pwd, account_roles.role as account_role, registration_date, course_groups.name as group_name FROM accounts "
            . "INNER JOIN account_roles ON account_roles.id = accounts.role_ref "
            . "LEFT JOIN course_groups ON course_groups.id = accounts.course_group_ref "
            . "WHERE email = '$login' ";

    $result = $mysql->executeQuery($query);
    $mysql->close();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $account_id = $row['account_id'];
            $email = $row['email'];
            $name = $row['account_name'];
            $pwd = $row['pwd'];
            $registration_date = $row['registration_date'];
            $role = $row['account_role'];
            $course_group = $row['group_name'];
        }
        return new Account($account_id, $email, $name, $pwd, $registration_date, $role, $course_group);
    } else {
        return null;
    }
}

function getCourse($course_id) {
    require_once 'class/course.php';
    $mysql = new Dbconnect();
    $mysql->connect();

    $query = "SELECT courses.id as course_id, course_name, teacher_ref, accounts.name as teacher_name FROM courses INNER JOIN accounts ON courses.teacher_ref = accounts.account_id WHERE courses.id = $course_id ";

    $result = $mysql->executeQuery($query);
    $mysql->close();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $course_id = $row['course_id'];
            $course_name = $row['course_name'];
            $teacher_ref = $row['teacher_ref'];
            $teacher_name = $row['teacher_name'];
        }
        return new Course($course_id, $course_name, $teacher_ref, $teacher_name);
    } else {
        return null;
    }
}

function getAllCourses($teacher_id) {
    require_once 'class/course.php';
    $mysql = new Dbconnect();
    $mysql->connect();

    if ($teacher_id != null) {
        $query = "SELECT courses.id, course_name, teacher_ref, accounts.name as teacher_name FROM courses INNER JOIN accounts ON courses.teacher_ref = accounts.account_id WHERE teacher_ref = $teacher_id ";
    } else {
        $query = "SELECT courses.id, course_name, teacher_ref, accounts.name as teacher_name FROM courses INNER JOIN accounts ON courses.teacher_ref = accounts.account_id ";
    }

    $result = $mysql->executeQuery($query);
    $mysql->close();
    if ($result->num_rows > 0) {
        $courses = array();
        while ($row = $result->fetch_assoc()) {
            $courses[] = new Course($row['id'], $row['course_name'], $row['teacher_ref'], $row['teacher_name']);
        }
        return $courses;
    } else {
        return null;
    }
}

function getLecturesFromCourse($course_id) {
    require_once 'class/lecture.php';
    $mysql = new Dbconnect();
    $mysql->connect();

    $query = "SELECT lectures.id as lecture_id, lectures.name as lecture_name, courses.course_name, starts_at, ends_at, location_latitude, location_longitude, lecture_states.state, accounts.name as teacher FROM lectures "
            . "INNER JOIN accounts on accounts.account_id = lectures.teacher_ref "
            . "INNER JOIN courses ON courses.id = lectures.course_ref "
            . "INNER JOIN lecture_states ON lecture_states.id = lectures.state_ref "
            . "WHERE course_ref = $course_id ORDER BY starts_at DESC";
    $result = $mysql->executeQuery($query);
    $mysql->close();
    if ($result->num_rows > 0) {
        $lectures = array();
        while ($row = $result->fetch_assoc()) {
            $lectures[] = new Lecture($row['lecture_id'], $row['lecture_name'], $row['course_name'], $row['starts_at'], $row['ends_at'], $row['location_latitude'], $row['location_longitude'], $row['state'], $row['teacher']);
        }
        return $lectures;
    } else {
        return null;
    }
}

function getLectureByID($lecture_id) {
    require_once 'class/lecture.php';
    $mysql = new Dbconnect();
    $mysql->connect();

    $query = "SELECT lectures.id as lecture_id, lectures.name as lecture_name, courses.course_name, starts_at, ends_at, location_latitude, location_longitude, lecture_states.state, accounts.name as teacher FROM lectures "
            . "INNER JOIN accounts on accounts.account_id = lectures.teacher_ref "
            . "INNER JOIN courses ON courses.id = lectures.course_ref "
            . "INNER JOIN lecture_states ON lecture_states.id = lectures.state_ref "
            . "WHERE lectures.id = $lecture_id ";
    $result = $mysql->executeQuery($query);
    $mysql->close();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $lecture_id = $row['lecture_id'];
            $lecture_name = $row['lecture_name'];
            $course_name = $row['course_name'];
            $lecture_starts = $row['starts_at'];
            $lecture_ends = $row['ends_at'];
            $lecture_lat = $row['location_latitude'];
            $lecture_long = $row['location_longitude'];
            $lecture_state = $row['state'];
            $teacher = $row['teacher'];
        }
        return new Lecture($lecture_id, $lecture_name, $course_name, $lecture_starts, $lecture_ends, $lecture_lat, $lecture_long, $lecture_state, $teacher);
    } else {
        return null;
    }
}

function enlistMe($student_id, $lecture_id) {
    $mysql = new Dbconnect();
    $mysql->connect();

    $query = "INSERT INTO attendance(lecture_ref, student_ref) VALUES('$lecture_id', '$student_id') ";
    if ($mysql->executeQuery($query) === false) {
        $mysql->close();
        return false;
    }

    $mysql->close();
    return true;
}

function getTotalAttendance() {
    $mysql = new Dbconnect();
    $mysql->connect();

    $query = "SELECT (SELECT COUNT(id) FROM lectures) AS total_lectures, "
            . "(SELECT COUNT(DISTINCT lecture_ref, student_ref) FROM attendance INNER JOIN lectures ON lectures.id = attendance.lecture_ref WHERE state_ref = 2) AS attended_finished, "
            . "(SELECT COUNT(DISTINCT lecture_ref, student_ref) FROM attendance INNER JOIN lectures ON lectures.id = attendance.lecture_ref WHERE state_ref = 1) AS attended_ongoing, "
            . "(SELECT COUNT(id) FROM lectures WHERE state_ref = 1) AS ongoing_lectures, "
            . "(SELECT COUNT(account_id) FROM accounts WHERE role_ref = 3) AS total_students";
    $result = $mysql->executeQuery($query);
    $mysql->close();
    if ($result->num_rows > 0) {
        $attendance = array();
        while ($row = $result->fetch_assoc()) {
            $attendance[] = $row;
        }
        return $attendance;
    } else {
        return null;
    }
}

function getTotalAttendanceForCourse($teacher_id, $course_id) {
    $mysql = new Dbconnect();
    $mysql->connect();

    $query = "SELECT (SELECT COUNT(id) FROM lectures WHERE teacher_ref = '$teacher_id' AND course_ref = '$course_id') AS total_lectures,"
            . "(SELECT COUNT(DISTINCT lecture_ref, student_ref) FROM attendance INNER JOIN lectures ON lectures.id = attendance.lecture_ref WHERE state_ref = 2 AND teacher_ref = '$teacher_id' AND course_ref = '$course_id') AS attended_finished, "
            . "(SELECT COUNT(DISTINCT lecture_ref, student_ref) FROM attendance INNER JOIN lectures ON lectures.id = attendance.lecture_ref WHERE state_ref = 1 AND teacher_ref = '$teacher_id' AND course_ref = '$course_id') AS attended_ongoing, "
            . "(SELECT COUNT(id) FROM lectures WHERE state_ref = 1 AND course_ref = '$course_id' AND teacher_ref = '$teacher_id') AS ongoing_lectures, "
            . "(SELECT COUNT(account_id) FROM accounts INNER JOIN course_groups ON accounts.course_group_ref = course_groups.id INNER JOIN course_groups_rel ON course_groups_rel.course_group_ref = course_groups.id INNER JOIN courses ON courses.id = course_groups_rel.course_ref WHERE role_ref = 3 AND courses.id = '$course_id') AS total_students";
    $result = $mysql->executeQuery($query);
    $mysql->close();
    if ($result->num_rows > 0) {
        $attendance = array();
        while ($row = $result->fetch_assoc()) {
            $attendance[] = $row;
        }
        return $attendance;
    } else {
        return null;
    }
}

function getAttendanceFor($student_id) {
    $mysql = new Dbconnect();
    $mysql->connect();

    $query = "SELECT lecture_ref, student_ref FROM attendance WHERE student_ref = $student_id";
    $result = $mysql->executeQuery($query);
    $mysql->close();
    if ($result->num_rows > 0) {
        $attendance = array();
        while ($row = $result->fetch_assoc()) {
            $attendance[] = $row["lecture_ref"];
        }
        return $attendance;
    } else {
        return null;
    }
}

function getAllLectureStates() {
    $mysql = new Dbconnect();
    $mysql->connect();

    $query = "SELECT id, state FROM lecture_states";
    $result = $mysql->executeQuery($query);
    $mysql->close();
    if ($result->num_rows > 0) {
        $statuses = array();
        while ($row = $result->fetch_assoc()) {
            $statuses[] = $row;
        }
        return $statuses;
    } else {
        return null;
    }
}

function updateLecture($lecture_id, $lecture_state, $lecture_name) {
    $mysql = new Dbconnect();
    $mysql->connect();

    if ($lecture_state == 2) {
        $ends_at = ", ends_at = NOW() ";
    } else {
        $ends_at = NULL;
    }

    $query = "UPDATE lectures SET name = '$lecture_name', state_ref = $lecture_state $ends_at WHERE id = $lecture_id";
    if ($mysql->executeQuery($query) === false) {
        $mysql->close();
        return false;
    }

    $mysql->close();
    return true;
}

function addNewLecture($lecture_name, $course_id, $account_id, $lat, $long) {
    $mysql = new Dbconnect();
    $mysql->connect();

    $query = "INSERT INTO lectures(name, course_ref, location_latitude, location_longitude, teacher_ref) VALUES('$lecture_name', '$course_id', '$lat', '$long', '$account_id') ";
    if ($mysql->executeQuery($query) === false) {
        $mysql->close();
        return false;
    }

    $mysql->close();
    return true;
}

function getAllTeachers() {
    $mysql = new Dbconnect();
    $mysql->connect();

    $query = "SELECT account_id, name FROM accounts WHERE role_ref = 2";
    $result = $mysql->executeQuery($query);
    $mysql->close();
    if ($result->num_rows > 0) {
        $teachers = array();
        while ($row = $result->fetch_assoc()) {
            $teachers[] = $row;
        }
        return $teachers;
    } else {
        return null;
    }
}

function getAllCourseGroups() {
    $mysql = new Dbconnect();
    $mysql->connect();

    $query = "SELECT * FROM course_groups ";
    $result = $mysql->executeQuery($query);
    $mysql->close();
    if ($result->num_rows > 0) {
        $course_groups = array();
        while ($row = $result->fetch_assoc()) {
            $course_groups[] = $row;
        }
        return $course_groups;
    } else {
        return null;
    }
}

function getAllStudents() {
    $mysql = new Dbconnect();
    $mysql->connect();

    $query = "SELECT account_id, accounts.name as student_name, course_groups.id as course_group_id, course_groups.name as group_name FROM accounts INNER JOIN course_groups ON accounts.course_group_ref = course_groups.id WHERE role_ref = 3 ORDER BY account_id";
    $result = $mysql->executeQuery($query);
    $mysql->close();
    if ($result->num_rows > 0) {
        $students = array();
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }
        return $students;
    } else {
        return null;
    }
}

function updateStudent($student_id, $student_name, $group_id) {
    $mysql = new Dbconnect();
    $mysql->connect();

    $query = "UPDATE accounts SET name = '$student_name', course_group_ref = $group_id WHERE account_id = $student_id";
    if ($mysql->executeQuery($query) === false) {
        $mysql->close();
        return false;
    }

    $mysql->close();
    return true;
}

function updateCourse($course_id, $course_name, $course_teacher) {
    $mysql = new Dbconnect();
    $mysql->connect();

    if ($course_teacher != NULL) {
        $teacher_ref = "teacher_ref = $course_teacher,";
    } else {
        $teacher_ref = NULL;
    }

    $query = "UPDATE courses SET $teacher_ref course_name = '$course_name' WHERE id = $course_id";
    if ($mysql->executeQuery($query) === false) {
        $mysql->close();
        return false;
    }

    $mysql->close();
    return true;
}

function addNewCourse($course_name) {
    $mysql = new Dbconnect();
    $mysql->connect();

    $query = "INSERT INTO courses(course_name) VALUES('$course_name') ";
    if ($mysql->executeQuery($query) === false) {
        $mysql->close();
        return false;
    }

    $mysql->close();
    return true;
}

function addNewStudent($student_email, $student_name, $student_pwd, $group_id) {
    $mysql = new Dbconnect();
    $mysql->connect();

    $query = "INSERT INTO accounts(email, name, pwd, course_group_ref) VALUES('$student_email', '$student_name', '$student_pwd', $group_id) ";
    if ($mysql->executeQuery($query) === false) {
        $mysql->close();
        return false;
    }

    $mysql->close();
    return true;
}

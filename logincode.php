<?php
session_start();
include('./config/dbconfig.php');

if (isset($_POST['login_btn'])) {
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $selected_role = mysqli_real_escape_string($con, $_POST['role']);

    if ($selected_role === "") {
        $_SESSION['message'] = "Please select a role before logging in.";
        header("Location: login.php");
        exit();
    }

    $login_query = "SELECT * FROM user WHERE u_email='$email' AND u_password='$password' AND u_role='$selected_role' LIMIT 1";
    $login_query_run = mysqli_query($con, $login_query);

    if (mysqli_num_rows($login_query_run) > 0) {
        $data = mysqli_fetch_assoc($login_query_run);

        $user_id = $data['u_id'];
        $user_name = $data['u_firstname'] . ' ' . $data['u_lastname'];
        $user_email = $data['u_email'];
        $role_as = $data['u_role'];

        $_SESSION['auth'] = true;
        $_SESSION['auth_role'] = $role_as;
        $_SESSION['auth_user'] = [
            'user_id' => $user_id,
            'user_name' => $user_name,
            'user_email' => $user_email,
        ];
        $_SESSION['email'] = $email;

        if ($role_as == '1') {
            $_SESSION['message'] = "Welcome, Admin";
            header("Location: ./admin/index.php");
        } elseif ($role_as == '2') {
            $_SESSION['message'] = "Welcome, Vaccinator";
            header("Location: ./vaccinator/index.php");
        } elseif ($role_as == '0') {
            $_SESSION['message'] = "You are Logged In";
            header("Location: view_user_information.php");
        }
        exit();
    } else {
        $_SESSION['message'] = "Invalid login credentials or role.";
        header("Location: login.php");
        exit();
    }
} else {
    $_SESSION['message'] = "Access denied.";
    header("Location: login.php");
    exit();
}

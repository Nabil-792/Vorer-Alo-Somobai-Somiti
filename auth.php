<?php
include 'config.php';

$email = mysqli_real_escape_string($conn, $_POST['email']);
$password = md5($_POST['password']);

$query = "SELECT * FROM members WHERE email='$email' AND password='$password'";
$result = mysqli_query($conn, $query);

if(mysqli_num_rows($result) == 1) {
    $user = mysqli_fetch_assoc($result);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];
    header("Location: dashboard.php");
} else {
    header("Location: login.php?error=1");
}
?>
<?php
include('../config/dbconfig.php');


require 'includes/PHPMailer.php';
require 'includes/SMTP.php';
require 'includes/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if(isset($_POST['updatebtn']))
{
    // $status = $_POST['edit_status'];
    $serial =$_POST['edit_id'];
    $email =$_POST['email'];

    $result = mysqli_query($con, "SELECT u_id FROM user WHERE u_email='$email'");
    $retrive = mysqli_fetch_array($result);
    $vaccinatorid = $retrive['u_id'];

    $query = "UPDATE registers_for SET status ='2' WHERE reg_serial ='$serial' ";
    $query_run = mysqli_query($con, $query);

    if($query_run)
    {
        $query = "INSERT INTO pushed(appointmentid, vaccinatorid) VALUES ('$serial', '$vaccinatorid')";
        $query_run = mysqli_query($con, $query);

        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
            $mail->SMTPAuth = true;
            $mail->Username = "safevaxit@gmail.com";//Set gmail password
            $mail->Password = "dchl eosu jila vjwy";
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587; // TCP port to connect to

            // Recipients
            $mail->setFrom('safevaxit@gmail.com', 'XYZ Health Care');
            $mail->addAddress($email); // Add a recipient

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Appointment Approved';
            $mail->Body = 'Your appointment has been approved.';

            $mail->send();
            $_SESSION['message'] = 'Your Data is Updated and email sent.';
        } catch (Exception $e) {
            $_SESSION['message'] = "Your Data is Updated but email not sent: {$mail->ErrorInfo}";
        }
        header('Location: view-request.php'); 
        exit(0);
    }
    else
    {
        $_SESSION['message'] = "Your Data is NOT Updated";
        header('Location: view-request.php'); 
        exit(0);
    }
}
else
{
    $_SESSION['message'] = "Your Data is NOT Updated";
    header('Location: view-request.php'); 
    exit(0);
}

?>
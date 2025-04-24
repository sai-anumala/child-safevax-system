<?php
// Start session
session_start();

// Include required PHPMailer files
require 'includes/PHPMailer.php';
require 'includes/SMTP.php';
require 'includes/Exception.php';

// Define namespaces
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Include database configuration
include('./config/dbconfig.php');

// Check if the form is submitted
if (isset($_POST['send_reminder_mail'])) {
    // Sanitize input
    $serial = mysqli_real_escape_string($con, $_POST['edit_serial']);

    // Prepare and execute the query to fetch data from the pushed table
    $stmt_pushed = $con->prepare("SELECT * FROM pushed WHERE pushed_serial = ?");
    $stmt_pushed->bind_param("s", $serial);
    $stmt_pushed->execute();
    $query_run = $stmt_pushed->get_result();
    $retrive = $query_run->fetch_assoc();

    // Check if the record was found
    if ($retrive) {
        $p_id = $retrive['patientid'];
        $v_id = $retrive['vaccineid'];
        $dose = $retrive['doseno'] + 1;
        $up_push = $retrive['dateofpushed'];

        // Calculate the next dose date based on vaccine ID
        switch ($v_id) {
            case 2:
            case 3:
            case 4:
                $up_push = date('Y-m-d', strtotime($up_push . ' + 28 days'));
                break;
            case 5:
                $up_push = date('Y-m-d', strtotime($up_push . ' + 56 days'));
                break;
            case 6:
                $up_push = date('Y-m-d', strtotime($up_push . ' + 180 days'));
                break;
            case 7:
                switch ($retrive['dose_no']) {
                    case 1:
                        $up_push = date('Y-m-d', strtotime($up_push . ' + 28 days'));
                        break;
                    case 2:
                    case 3:
                    case 4:
                        $up_push = date('Y-m-d', strtotime($up_push . ' + 365 days'));
                        break;
                }
                break;
            case 8:
                $up_push = date('Y-m-d', strtotime($up_push . ' + 60 days'));
                break;
            default:
                $up_push = date('Y-m-d', strtotime($up_push . ' + 28 days')); // Default to 28 days if vaccine ID is unknown
                break;
        }

        // Prepare and execute the query to fetch patient data
        $stmt_patient = $con->prepare("SELECT u_firstname, u_lastname, u_email FROM user WHERE u_id = ?");
        $stmt_patient->bind_param("i", $p_id);
        $stmt_patient->execute();
        $person_run = $stmt_patient->get_result();

        // Prepare and execute the query to fetch vaccine data
        $stmt_vaccine = $con->prepare("SELECT v_name FROM vaccine WHERE v_id = ?");
        $stmt_vaccine->bind_param("i", $v_id);
        $stmt_vaccine->execute();
        $vaccine_run = $stmt_vaccine->get_result();
        $v_name = $vaccine_run->fetch_assoc();
        $vname = $v_name['v_name'];

        // Check if the patient record was found
        if ($person_run->num_rows > 0) {
            while ($row = $person_run->fetch_assoc()) {
                $name = $row['u_firstname'];
                $lname = $row['u_lastname'];
                $full_name = $name . " " . $lname;
                $email = $row['u_email'];

                // Create instance of PHPMailer
                $mail = new PHPMailer(true);

                try {
                    // Server settings
                    $mail->isSMTP();
                    $mail->Host = "smtp.gmail.com";
                    $mail->SMTPAuth = true;
                    $mail->Username = "safevaxit@gmail.com"; // Use environment variables or a secure config file for this
                    $mail->Password = "dchl eosu jila vjwy"; // Use environment variables or a secure config file for this
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    // Recipients
                    $mail->setFrom('safevaxit@gmail.com', 'XYZ Health Care');
                    $mail->addAddress($email, $full_name);

                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = "Remainder from XYZ Health Care: Get your next dose of $vname on time";
                    $mail->Body    = "<p>Dear $full_name,<br>
                                      This is a reminder email in the case that your upcoming dose of $vname vaccine i.e. dose number $dose is needed to be taken on $up_push. Get your dose on time and stay protected. Don't forget to make an appointment for this upcoming dose.<br><br>
                                      Regards<br>
                                      XYZ Health Care<br></p>";

                    $mail->send();
                    $_SESSION['status'] = "Mail sent.";
                    $_SESSION['status_code'] = "success";
                } catch (Exception $e) {
                    $_SESSION['status'] = "Mail couldn't be sent. Error: {$mail->ErrorInfo}";
                    $_SESSION['status_code'] = "error";
                }
            }
        } else {
            $_SESSION['status'] = "Patient not found.";
            $_SESSION['status_code'] = "error";
        }
    } else {
        $_SESSION['status'] = "Pushed record not found.";
        $_SESSION['status_code'] = "error";
    }
} else {
    $_SESSION['status'] = "No data submitted.";
    $_SESSION['status_code'] = "error";
}

// Redirect to the send_reminder.php page
header("Location: ../admin/send_reminder.php");
exit(0);
?>

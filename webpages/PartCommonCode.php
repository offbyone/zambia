<?php
    require_once('error_functions.php');
    require_once('CommonCode.php');
    require_once('ParticipantHeader.php');
    require_once('ParticipantFooter.php');
    $_SESSION['role'] = "Participant";
    $badgeid=$_SESSION['badgeid'];
    if (!(may_I("Participant"))) {
        $message="You are not authorized to access this page.";
        require ('login.php');
        exit();
        };
?>

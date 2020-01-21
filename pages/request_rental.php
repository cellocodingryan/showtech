<?php
$edit_mode = false;
$pagenum = 3;
if (isset($_GET['edit_mode']) and $_GET['edit_mode'] == 1) {
    $edit_mode = true;
    $pagenum = 2;
}
$rentalorshow = "Rental";
require_once 'pages/request_either.php';
?>

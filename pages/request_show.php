<?php
$edit_mode = false;
$pagenum = 5;
if (isset($_GET['edit_mode']) and $_GET['edit_mode'] == 1) {
    $edit_mode = true;
    $pagenum = 4;
}
$rentalorshow = "Show";
require_once 'pages/request_either.php';
?>

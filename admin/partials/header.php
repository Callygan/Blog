<?php
require '../partials/header.php';

// verifica starea de conectare
if (!isset($_SESSION['user-id'])) {
    header('location: ' . ROOT_URL . 'signin.php');
    die();
}

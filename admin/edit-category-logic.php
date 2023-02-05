<?php
require 'config/database.php';

if (isset($_POST['submit'])) {
    $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
    $title = filter_var($_POST['title'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $description = filter_var($_POST['description'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // validați intrarea
    if (!$title || !$description) {
        $_SESSION['edit-category'] = "Intrare de formular nevalidă pe pagina de editare a categoriei";
    } else {
        $query = "UPDATE categories SET title='$title', description='$description' WHERE id=$id LIMIT 1";
        $result = mysqli_query($connection, $query);

        if (mysqli_errno($connection)) {
            $_SESSION['edit-category'] = "Nu s-a putut actualiza categoria";
        } else {
            $_SESSION['edit-category-success'] = "Categoria $title a fost actualizat[ cu succes";
        }
    }
}

header('location: ' . ROOT_URL . 'admin/manage-categories.php');
die();

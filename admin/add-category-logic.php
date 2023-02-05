<?php
require 'config/database.php';

if (isset($_POST['submit'])) {
    // obțineți date din formular
    $title = filter_var($_POST['title'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $description = filter_var($_POST['description'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if (!$title) {
        $_SESSION['add-category'] = "Introduce-ți titlul";
    } elseif (!$description) {
        $_SESSION['add-category'] = "Introduce-ți o descriere";
    }

    // redirecționați înapoi pentru a adăuga pagina de categorie cu date din formular dacă a existat o intrare nevalidă
    if (isset($_SESSION['add-category'])) {
        $_SESSION['add-category-data'] = $_POST;
        header('location: ' . ROOT_URL . 'admin/add-category.php');
        die();
    } else {
        // inserați categoria în baza de date
        $query = "INSERT INTO categories (title, description) VALUES ('$title', '$description')";
        $result = mysqli_query($connection, $query);
        if (mysqli_errno($connection)) {
            $_SESSION['add-category'] = "Couldn't add category";
            header('location: ' . ROOT_URL . 'admin/add-category.php');
            die();
        } else {
            $_SESSION['add-category-success'] = "Categoria $title a fost adăugată cu succes";
            header('location: ' . ROOT_URL . 'admin/manage-categories.php');
            die();
        }
    }
}

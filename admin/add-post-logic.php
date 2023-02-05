<?php
require 'config/database.php';

if (isset($_POST['submit'])) {
    $author_id = $_SESSION['user-id'];
    $title = filter_var($_POST['title'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $body = filter_var($_POST['body'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $category_id = filter_var($_POST['category'], FILTER_SANITIZE_NUMBER_INT);
    $is_featured = filter_var($_POST['is_featured'], FILTER_SANITIZE_NUMBER_INT);
    $thumbnail = $_FILES['thumbnail'];

    // setare is_featured la 0 dacă este nebifat
    $is_featured = $is_featured == 1 ?: 0;

    // validarea datelor din formular
    if (!$title) {
        $_SESSION['add-post'] = "Adaugare titlu pentru postare";
    } elseif (!$category_id) {
        $_SESSION['add-post'] = "Alege categoria pentru postare";
    } elseif (!$body) {
        $_SESSION['add-post'] = "Adăugare subiect postare";
    } elseif (!$thumbnail['name']) {
        $_SESSION['add-post'] = "Alege miniatura pentru postare";
    } else {
        // LUCRU PE poza
        // redenumire imagine
        $time = time(); // face fiecare nume de imagine unic
        $thumbnail_name = $time . $thumbnail['name'];
        $thumbnail_tmp_name = $thumbnail['tmp_name'];
        $thumbnail_destination_path = '../images/' . $thumbnail_name;

        // asigurați-vă că fișierul este o imagine
        $allowed_files = ['png', 'jpg', 'jpeg'];
        $extension = explode('.', $thumbnail_name);
        $extension = end($extension);
        if (in_array($extension, $allowed_files)) {
            // asigurați-vă că imaginea nu este prea mare. (2mb+)
            if ($thumbnail['size'] < 2000000) {
                // incarcare poza
                move_uploaded_file($thumbnail_tmp_name, $thumbnail_destination_path);
            } else {
                $_SESSION['add-post'] = "Dimensiunea miniaturii este prea mare. Ar trebui să fie mai puțin de 2MB";
            }
        } else {
            $_SESSION['add-post'] = "Miniatura ar trebui să fie png, jpg sau jpeg";
        }
    }

    // redirecționați înapoi (cu datele formularului) la pagina de adăugare a postării dacă există vreo problemă
    if (isset($_SESSION['add-post'])) {
        $_SESSION['add-post-data'] = $_POST;
        header('location: ' . ROOT_URL . 'admin/add-post.php');
        die();
    } else {
        // setați is_featured din toate psots la 0 dacă is_featured pentru această postare este 1
        if ($is_featured == 1) {
            $zero_all_is_featured_query = "UPDATE posts SET is_featured=0";
            $zero_all_is_featured_result = mysqli_query($connection, $zero_all_is_featured_query);
        }

        // introduceți mesajul în baza de date
        $query = "INSERT INTO posts (title, body, thumbnail, category_id, author_id, is_featured) VALUES ('$title', '$body', '$thumbnail_name', $category_id, $author_id, $is_featured)";
        $result = mysqli_query($connection, $query);

        if (!mysqli_errno($connection)) {
            $_SESSION['add-post-success'] = "Postara nouă a fost adăugată cu succes";
            header('location: ' . ROOT_URL . 'admin/');
            die();
        }
    }
}

header('location: ' . ROOT_URL . 'admin/add-post.php');
die();

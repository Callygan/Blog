<?php
require 'config/database.php';

if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

    // preluați utilizatorul din baza de date
    $query = "SELECT * FROM users WHERE id=$id";
    $result = mysqli_query($connection, $query);
    $user = mysqli_fetch_assoc($result);

    // asigurați-vă că am primit înapoi un singur utilizator
    if (mysqli_num_rows($result) == 1) {
        $avatar_name = $user['avatar'];
        $avatar_path = '../images/' . $avatar_name;
        // ștergeți imaginea dacă este disponibilă
        if ($avatar_path) {
            unlink($avatar_path);
        }
    }

    // preluați toate miniaturile postărilor utilizatorului și ștergeți-le
    $thumbnails_query = "SELECT thumbnail FROM posts WHERE author_id=$id";
    $thumbnails_result = mysqli_query($connection, $thumbnails_query);
    if (mysqli_num_rows($thumbnails_result) > 0) {
        while ($thumbnail = mysqli_fetch_assoc($thumbnails_result)) {
            $thumbnail_path = '../images/' . $thumbnail['thumbnail'];
            // șterge miniatura din folderul imagini există
            if ($thumbnail_path) {
                unlink($thumbnail_path);
            }
        }
    }




    // ștergeți utilizatorul din baza de date
    $delete_user_query = "DELETE FROM users WHERE id=$id";
    $delete_user_result = mysqli_query($connection, $delete_user_query);
    if (mysqli_errno($connection)) {
        $_SESSION['delete-user'] = "Nu s-a putut șterge '{$user['firstname']} '{$user['lastname']}'";
    } else {
        $_SESSION['delete-user-success'] = "{$user['firstname']} {$user['lastname']} a fost șters cu succes";
    }
}

header('location: ' . ROOT_URL . 'admin/manage-users.php');
die();

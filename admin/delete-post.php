<?php
require 'config/database.php';

if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

    // preluați postarea din baza de date pentru a șterge miniaturile din folderul de imagini
    $query = "SELECT * FROM posts WHERE id=$id";
    $result = mysqli_query($connection, $query);

    // asigurați-vă că a fost preluată doar o înregistrare/postare
    if (mysqli_num_rows($result) == 1) {
        $post = mysqli_fetch_assoc($result);
        $thumbnail_name = $post['thumbnail'];
        $thumbnail_path = '../images/' . $thumbnail_name;

        if ($thumbnail_path) {
            unlink($thumbnail_path);

            // sterge postarea din baza de date
            $delete_post_query = "DELETE FROM posts WHERE id=$id LIMIT 1";
            $delete_post_result = mysqli_query($connection, $delete_post_query);

            if (!mysqli_errno($connection)) {
                $_SESSION['delete-post-success'] = "Postarea a fost ștearsă cu succes.";
            }
        }
    }
}

header('location: ' . ROOT_URL . 'admin/');
die();

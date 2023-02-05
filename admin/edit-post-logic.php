<?php
require 'config/database.php';

// asigurați-vă că ați făcut clic pe butonul Editați postarea
if (isset($_POST['submit'])) {
    $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
    $previous_thumbnail_name = filter_var($_POST['previous_thumbnail_name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $title = filter_var($_POST['title'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $body = filter_var($_POST['body'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $category_id = filter_var($_POST['category'], FILTER_SANITIZE_NUMBER_INT);
    $is_featured = filter_var($_POST['is_featured'], FILTER_SANITIZE_NUMBER_INT);
    $thumbnail = $_FILES['thumbnail'];

    // set is_featured to 0 if dacă era nebifată
    $is_featured = $is_featured == 1 ?: 0;

    // verificați și validați valorile de intrare
    if (!$title) {
        $_SESSION['edit-post'] = "Postarea nu a putut fi actualizată. Date de formular nevalide pe pagina de editare a postării.";
    } elseif (!$category_id) {
        $_SESSION['edit-post'] = "Postarea nu a putut fi actualizată. Date de formular nevalide pe pagina de editare a postării.";
    } elseif (!$body) {
        $_SESSION['edit-post'] = "Postarea nu a putut fi actualizată. Date de formular nevalide pe pagina de editare a postării.";
    } else {
        // ștergeți miniatura existentă dacă este disponibilă o miniatură nouă
        if ($thumbnail['name']) {
            $previous_thumbnail_path = '../images/' . $previous_thumbnail_name;
            if ($previous_thumbnail_path) {
                unlink($previous_thumbnail_path);
            }

            // LUCRU PE MINIATURA NOUA
            // redenumire imagine
            $time = time(); // face încărcarea fiecărei imagini unică folosind marcajul de timp actual
            $thumbnail_name = $time . $thumbnail['name'];
            $thumbnail_tmp_name = $thumbnail['tmp_name'];
            $thumbnail_destination_path = '../images/' . $thumbnail_name;

            // asigurați-vă că fișierul este o imagine
            $allowed_files = ['png', 'jpg', 'jpeg'];
            $extension = explode('.', $thumbnail_name);
            $extension = end($extension);
            if (in_array($extension, $allowed_files)) {
                // asigurați-vă că avatarul nu este prea mare (2 MB+)
                if ($thumbnail['size'] < 2000000) {
                    // incarcare avatar
                    move_uploaded_file($thumbnail_tmp_name, $thumbnail_destination_path);
                } else {
                    $_SESSION['edit-post'] = "Postarea nu a putut fi actualizată. Dimensiunea miniaturii este prea mare. Ar trebui să fie mai puțin de 2MB";
                }
            } else {
                $_SESSION['edit-post'] = "Postarea nu a putut fi actualizată. Miniatura ar trebui să fie png, jpg sau jpeg";
            }
        }
    }


    if ($_SESSION['edit-post']) {
        // redirecționați către gestionarea paginii de formular dacă formularul nu era valid
        header('location: ' . ROOT_URL . 'admin/');
        die();
    } else {
        // seteaza is_featured tuturor postărilor către 0 daca is_featured pentru aceasta postare este 1
        if ($is_featured == 1) {
            $zero_all_is_featured_query = "UPDATE posts SET is_featured=0";
            $zero_all_is_featured_result = mysqli_query($connection, $zero_all_is_featured_query);
        }

        // setați numele miniaturii dacă a fost încărcat unul nou, altfel păstrați numele vechii miniaturi
        $thumbnail_to_insert = $thumbnail_name ?? $previous_thumbnail_name;

        $query = "UPDATE posts SET title='$title', body='$body', thumbnail='$thumbnail_to_insert', category_id=$category_id, is_featured=$is_featured WHERE id=$id LIMIT 1";
        $result = mysqli_query($connection, $query);
    }


    if (!mysqli_errno($connection)) {
        $_SESSION['edit-post-success'] = "Postarea a fost actualizată cu succes";
    }
}

header('location: ' . ROOT_URL . 'admin/');
die();

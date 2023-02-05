<?php
require 'config/database.php';

if (isset($_POST['submit'])) {
    // obțineți date din formular
    $username_email = filter_var($_POST['username_email'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password = filter_var($_POST['password'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if (!$username_email) {
        $_SESSION['signin'] = "Este necesar un nume de utilizator sau e-mail";
    } elseif (!$password) {
        $_SESSION['signin'] = "Parola obligatorie";
    } else {
        // preluați utilizatorul din baza de date
        $fetch_user_query = "SELECT * FROM users WHERE username='$username_email' OR email='$username_email'";
        $fetch_user_result = mysqli_query($connection, $fetch_user_query);

        if (mysqli_num_rows($fetch_user_result) == 1) {
            // convertiți înregistrarea în matrice
            $user_record = mysqli_fetch_assoc($fetch_user_result);
            $db_password = $user_record['password'];
            // comparați parola formularului cu parola bazei de date
            if (password_verify($password, $db_password)) {
                // set session for access control
                $_SESSION['user-id'] = $user_record['id'];
                // setați sesiunea dacă utilizatorul este administrator
                if ($user_record['is_admin'] == 1) {
                    $_SESSION['user_is_admin'] = true;
                }
                // conectați utilizatorul
                header('location: ' . ROOT_URL . 'admin/');
            } else {
                $_SESSION['signin'] = "Vă rugăm să verificați datele introduse";
            }
        } else {
            $_SESSION['signin'] = "Utilizator nu a fost găsit";
        }
    }

    // dacă există o problemă, redirecționați înapoi la pagina de conectare cu datele de conectare
    if (isset($_SESSION['signin'])) {
        $_SESSION['signin-data'] = $_POST;
        header('location: ' . ROOT_URL . 'signin.php');
        die();
    }
} else {
    header('location: ' . ROOT_URL . 'signin.php');
    die();
}

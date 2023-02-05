<?php
require 'config/database.php';

// obțineți datele formularului dacă s-a făcut clic pe butonul de trimitere
if (isset($_POST['submit'])) {
    $firstname = filter_var($_POST['firstname'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $lastname = filter_var($_POST['lastname'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $username = filter_var($_POST['username'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $createpassword = filter_var($_POST['createpassword'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $confirmpassword = filter_var($_POST['confirmpassword'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $is_admin = filter_var($_POST['userrole'], FILTER_SANITIZE_NUMBER_INT);
    $avatar = $_FILES['avatar'];

    // validarea valorilor de intrare
    if (!$firstname) {
        $_SESSION['add-user'] = "Vă rugăm să introduceți Numele";
    } elseif (!$lastname) {
        $_SESSION['add-user'] = "Vă rugăm să introduceți Prenume";
    } elseif (!$username) {
        $_SESSION['add-user'] = "Vă rugăm să introduceți Loginul";
    } elseif (!$email) {
        $_SESSION['add-user'] = "Vă rugăm să introduceți un e-mail valid";
    } elseif (strlen($createpassword) < 8 || strlen($confirmpassword) < 8) {
        $_SESSION['add-user'] = "Parola trebuie să aibă 8+ caracteres";
    } elseif (!$avatar['name']) {
        $_SESSION['add-user'] = "Vă rugăm să adăugați un avatar";
    } else {
        // verificați dacă parolele nu se potrivesc
        if ($createpassword !== $confirmpassword) {
            $_SESSION['signup'] = "Parolele nu coincid";
        } else {
            // hash pass
            $hashed_password = password_hash($createpassword, PASSWORD_DEFAULT);

            // verificați dacă numele de utilizator sau adresa de e-mail există deja în baza de date
            $user_check_query = "SELECT * FROM users WHERE username='$username' OR email='$email'";
            $user_check_result = mysqli_query($connection, $user_check_query);
            if (mysqli_num_rows($user_check_result) > 0) {
                $_SESSION['add-user'] = "Nume de utilizator sau e-mail există deja";
            } else {
                // LUCRU CU AVATARUL
                // redenumire avatar
                $time = time(); // face fiecare nume de imagine unic utilizând marcajul de timp actual
                $avatar_name = $time . $avatar['name'];
                $avatar_tmp_name = $avatar['tmp_name'];
                $avatar_destination_path = '../images/' . $avatar_name;

                // asigurați-vă că fișierul este o imagine
                $allowed_files = ['png', 'jpg', 'jpeg'];
                $extention = explode('.', $avatar_name);
                $extention = end($extention);
                if (in_array($extention, $allowed_files)) {
                    // asigurați-vă că imaginea nu este prea mare (1mb+)
                    if ($avatar['size'] < 1000000) {
                        // incarcare avatar
                        move_uploaded_file($avatar_tmp_name, $avatar_destination_path);
                    } else {
                        $_SESSION['add-user'] = "Dimensiunea fișierului este prea mare. Ar trebui să fie mai mică de 1 MB";
                    }
                } else {
                    $_SESSION['add-user'] = "Fișierul trebuie să fie png, jpg sau jpeg";
                }
            }
        }
    }

    // redirecționați înapoi la pagina de adăugare a utilizatorului dacă a existat vreo problemă
    if (isset($_SESSION['add-user'])) {
        // transmiteți datele formularului înapoi la pagina de înregistrare
        $_SESSION['add-user-data'] = $_POST;
        header('location: ' . ROOT_URL . '/admin/add-user.php');
        die();
    } else {
        // inserați un utilizator nou în tabelul utilizatori
        $insert_user_query = "INSERT INTO users SET firstname='$firstname', lastname='$lastname', username='$username', email='$email', password='$hashed_password', avatar='$avatar_name', is_admin=$is_admin";
        $insert_user_result = mysqli_query($connection, $insert_user_query);

        if (!mysqli_errno($connection)) {
            // redirecționează către pagina de conectare cu mesaj de succes
            $_SESSION['add-user-success'] = "Utilizatorul $firstname $lastname a fost adăugat cu succes.";
            header('location: ' . ROOT_URL . 'admin/manage-users.php');
            die();
        }
    }
} else {
    // dacă butonul nu a fost apăsat, reveniți la pagina de înscriere
    header('location: ' . ROOT_URL . 'admin/add-user.php');
    die();
}

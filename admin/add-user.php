<?php
include 'partials/header.php';

// recuperați datele din formular dacă a apărut o eroare
$firstname = $_SESSION['add-user-data']['firstname'] ?? null;
$lastname = $_SESSION['add-user-data']['lastname'] ?? null;
$username = $_SESSION['add-user-data']['username'] ?? null;
$email = $_SESSION['add-user-data']['email'] ?? null;
$createpassword = $_SESSION['add-user-data']['createpassword'] ?? null;
$confirmpassword = $_SESSION['add-user-data']['confirmpassword'] ?? null;

// ștergeți datele sesiunii
unset($_SESSION['add-user-data']);
?>



<section class="form__section">
    <div class="container form__section-container">
        <h2>Add User</h2>
        <?php if (isset($_SESSION['add-user'])) : ?>
            <div class="alert__message error">
                <p>
                    <?= $_SESSION['add-user'];
                    unset($_SESSION['add-user']);
                    ?>
                </p>
            </div>

        <?php endif ?>
        <form action="<?= ROOT_URL ?>admin/add-user-logic.php" enctype="multipart/form-data" method="POST">
            <input type="text" name="firstname" value="<?= $firstname ?>" placeholder="Nume">
            <input type="text" name="lastname" value="<?= $lastname ?>" placeholder="Prenume">
            <input type="text" name="username" value="<?= $username ?>" placeholder="Login">
            <input type="email" name="email" value="<?= $email ?>" placeholder="Email">
            <input type="password" name="createpassword" value="<?= $createpassword ?>" placeholder="Parola">
            <input type="password" name="confirmpassword" value="<?= $confirmpassword ?>" placeholder="Confirmare parola">
            <select name="userrole">
                <option value="0">Autor</option>
                <option value="1">Administrator</option>
            </select>
            <div class="form__control">
                <label for="avatar">Poza de Avatar</label>
                <input type="file" name="avatar" id="avatar">
            </div>
            <button type="submit" name="submit" class="btn">Adăugare utilizator</button>
        </form>
    </div>
</section>



<?php
include '../partials/footer.php';
?>
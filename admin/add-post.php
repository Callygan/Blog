<?php
include 'partials/header.php';

// preluați categorii din baza de date
$query = "SELECT * FROM categories";
$categories = mysqli_query($connection, $query);

// obțineți înapoi datele formularului dacă formularul nu era valid
$title = $_SESSION['add-post-data']['title'] ?? null;
$body = $_SESSION['add-post-data']['body'] ?? null;

// stergere datele de formular din sesiune
unset($_SESSION['add-post-data']);
?>



<section class="form__section">
    <div class="container form__section-container">
        <h2>Adăugare Postare</h2>
        <?php if (isset($_SESSION['add-post'])) : ?>
            <div class="alert__message error">
                <p>
                    <?= $_SESSION['add-post'];
                    unset($_SESSION['add-post']);
                    ?>
                </p>
            </div>
        <?php endif ?>
        <form action="<?= ROOT_URL ?>admin/add-post-logic.php" enctype="multipart/form-data" method="POST">
            <input type="text" name="title" value="<?= $title ?>" placeholder="Titlu">
            <select name="category">
                <?php while ($category = mysqli_fetch_assoc($categories)) : ?>
                    <option value="<?= $category['id'] ?>"><?= $category['title'] ?></option>
                <?php endwhile ?>
            </select>
            <textarea rows="10" name="body" placeholder="Conținut"><?= $body ?></textarea>
            <?php if (isset($_SESSION['user_is_admin'])) : ?>
                <div class="form__control inline">
                    <input type="checkbox" name="is_featured" value="1" id="is_featured" checked>
                    <label for="is_featured">Recomandată</label>
                </div>
            <?php endif ?>
            <div class="form__control">
                <label for="thumbnail">Adăugare Miniatura</label>
                <input type="file" name="thumbnail" id="thumbnail">
            </div>
            <button type="submit" name="submit" class="btn">Adăugare Postare</button>
        </form>
    </div>
</section>


<?php
include '../partials/footer.php';
?>
<?php include 'header.php';

// C H E C K - I F - C O N N E C T E D
if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
    header('Location: signin.php');
    exit;
}

// CHECK IF THERE IS SOMETHING IN THE INGREDIENT LIST
if (!isset($_SESSION['ingredientList']) || empty($_SESSION['ingredientList'])) {
    header('Location: selectingredient.php');
    exit;
}
?>

<?php

// GET INFORMATIONS FROM DATA BASE
$nationality = $db->query('SELECT * FROM nationality');
$region = $db->query('SELECT * FROM region');
$city = $db->query('SELECT * FROM city');

?>

<h1>Ajoutez une recette</h1>

<form action="addrecipe_post.php" method="post" enctype="multipart/form-data">

    <div class="form-group">
        <label for="recipe_title" class="form-label">Nom de la recette<span class="required-field">*<span> :</label>
        <input type="text" class="form-control" name="recipe_title" id="recipe_title" required>
    </div>

    <div class="form-group">
        <label for="recipe_time" class="form-label">Temps de la recette approximatif<span class="required-field">*<span> :</label>
        <input type="time" id="recipe_time" name="recipe_time" min="00:10" required>
    </div>

    <div class="form-group">
        <label for="recipe_level" class="form-label">Difficulté de la recette (1 = Facile, 2 = Moyen, 3 = Difficile) :</label>
        <input type="range" class="form-range" min="1" max="3" step="1" value="1" id="recipe_level" name=recipe_level>
    </div>

    <div class="form-group">
        <label for="recipe_price" class="form-label">Prix de la recette (1 = Bon marché, 2 = Budget moyen, 3 = Onéreux) :</label>
        <input type="range" class="form-range" min="1" max="3" step="1" value="1" id="recipe_price" name=recipe_price>
    </div>

    <div class="form-check">
        <input type="checkbox" class="form-check-input" id="recipe_is_restricted" name="recipe_is_restricted" value="1">
        <label class="form-check-label" for="recipe_is_restricted">Alcool dans la recette ?</label>
    </div>

    <div class="form-group">
        <label for="recipe_desc" class="form-label">Description de votre recette<span class="required-field">*<span> :</label>
        <textarea class="form-control" id="recipe_desc" name="recipe_desc" rows="5" required></textarea>
    </div>

    <div class="form-group">
        <label for='fk_nationality' class="form-label">Nationalitée du plat :</label>

        <select class="form-control" id="fk_nationality" name="fk_nationality">
            <option value='null'>- Selectionnez une nationalitée -</option>
            <?php
            while ($result = $nationality->fetch()) { ?>
                <option value="<?= $result['id_nationality']; ?>"><?= $result['nationality_name']?></option>
            <?php } ?>
        </select>
    </div>

    <div class="form-group">
        <label for='fk_region' class="form-label">Région du plat :</label>

        <select class="form-control" id="fk_region" name="fk_region">
            <option value='null'>- Selectionnez une région -</option>
            <?php
            while ($result = $region->fetch()) { ?>
                <option value="<?= $result['id_region']; ?>"><?= $result['region_name']?></option>
            <?php } ?>
        </select>
    </div>

    <div class="form-group">
        <label for='fk_city' class="form-label">Ville du plat :</label>

        <select class="form-control" id="fk_city" name="fk_city">
            <option value='null'>- Selectionnez une ville -</option>
            <?php
            while ($result = $city->fetch()) { ?>
                <option value="<?= $result['id_city']; ?>"><?= $result['city_name']?></option>
            <?php } ?>
        </select>
    </div>


    <div class="form-group">
        <label for="recipe_picture" class="form-label">Upload de l'image de la recette :</label>
        <input type="file" name="recipe_picture" id="recipe_picture">
    </div>

    <button type="submit" class="btn btn-primary">Ajouter</button>

</form>

<p class="margin-top">
    <span class="required-field">* : Champs obligatoires</span>
</p>

<?php include 'footer.php' ?>
<?php include 'header.php' ?>

<?php

// REDIRECTION TO SIGNIN IF NOT CONNECTED

if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {

    header('Location: signin.php');
    exit;
}
?>

<?php

// SQL QUERY FOR SEARCH INGREDIENTS IN DATABASE

if (isset($_POST['search']) && !empty($_POST['search'])) {
    try {

        $curSearch = $_POST['search'];

        $req = $db->prepare("SELECT * FROM ingredient WHERE ingredient_name LIKE ?");
        $req->execute(array(
            '%' . $curSearch . '%'
        ));

    } catch (Exception $th) {
        die($th->getMessage());
    }
}
?>

<?php

// VERIFY IF THE INGREDIENT IS NOT ALREADY IN DATABASE

if(isset($_POST['addingredientname']) && !empty($_POST['addingredientname']) && isset($_POST['addnutritionalvalue'])) {

    if(empty($_POST['addnutritionalvalue'])) {
        $_POST['addnutritionalvalue'] = null;
    }

    try {

    $req = $db->query('SELECT * FROM ingredient');

    while($result = $req->fetch()) {

        if($result['ingredient_name'] == $_POST['addingredientname']) {
            $errorMsg ="Cet ingrédient existe déja";
        }

    }
        if(empty($errorMsg)) {

            // IF NOT, ADD INGREDIENT INTO DATABASE
            $req = $db->prepare("INSERT INTO ingredient (ingredient_name, ingredient_nutritional_value) VALUES(?,?)");

            $req->execute(array(
                $_POST['addingredientname'],
                $_POST['addnutritionalvalue']
            ));

            $successMsg = "Vous avez bien ajouté un ingrédient !";
        }

    } catch (Exception $e) {
        
        die($e->getMessage());
    }

}
?>

<?php

// CREATE ingredientList ARRAY

if (!isset($_SESSION['ingredientList'])) {

    $_SESSION['ingredientList'] = [];
}

?>

<?php

// ADD INGREDIENT IN SESSION ARRAY

if (isset($_GET['id']) && isset($_GET['name'])) {
    $_SESSION['ingredientList'][$_GET['id']] = array('name' => $_GET['name']);
}

?>

<?php

// RESET

if (isset($_GET['reset']))
    $_SESSION['ingredientList'] = [];
?>

<?php

// DELETE INGREDIENT FROM SESSION

    if(isset($_GET['delete'])) {
        foreach ($_SESSION['ingredientList'] as $key => $array) {
            if($array['name'] == $_GET['name']) {
                unset($_SESSION['ingredientList']['name']);
            }
        }
    }
?>

<h1>Ajoutez vos ingrédients</h1>

<form action="" method="post">

    <div class="form-group">
        <label for="search">Recherche :</label>
        <input type="text" class="form-control" name="search" id="search" placeholder="Exemple : Sucre">
    </div>

    <button type="submit" class="btn btn-primary">Valider</button>
    
</form>

<?php if (isset($_POST['search']) && !empty($_POST['search'])) { ?>

    <div class='ingredientList'>
        <label for='ingredientList' class="margin-top">Liste correspondante :</label>
        <br />
        <?php
        while ($result = $req->fetch()) { ?>
            <a href="selectingredient.php?id=<?= $result['id_ingredient']; ?>&name=<?= $result['ingredient_name']; ?>">
                <?= $result['ingredient_name'] . '<br/>'; ?>
            </a>
        <?php } ?>
        <a href="selectingredient.php?addingredient">Ajouter ingrédient</a>
    </div>
    </form>

<?php }; ?>

<?php if (isset($_GET['addingredient'])) { ?>

    <form action="" method="post">

    <div class="form-group">
        <label for="addingredientname">Nom de l'ingredient :</label>
        <input type="text" class="form-control" name="addingredientname" id="addingredientname">
    </div>

    <div class="form-group">
        <label for="addnutritionalvalue">Valeur nutrionnelle :</label>
        <input type="text" class="form-control" name="addnutritionalvalue" id="addnutritionalvalue" placeholder="Peut être laissé vide">
    </div>

    <button type="submit" class="btn btn-primary">Ajouter</button>
    
</form>

<?php } ?>

<div style='margin:20px 0'>

    <?php if(count($_SESSION['ingredientList']) > 0) { ?>
        <label for="curIngredients">Vos ingrédients :</label>
    <?php } ?>
    <ul class="list-group">
        <?php
        foreach ($_SESSION['ingredientList'] as $key => $array) {
            echo "<a href='selectingredient.php?delete&name={$array['name']}' class='list-group-item'>{$array['name']} (Cliquez pour supprimer) </a>";
        }
        ?>
    </ul>
</div>

<?php if(count($_SESSION['ingredientList']) > 0) { ?>
    <a type="reset" href="selectingredient.php?reset">Vider la liste</a>
    <br>
    <a type="submit" href="addrecipe.php" class="btn btn-primary">Création de la recette</a>
<?php } ?>

<?php include 'footer.php'; ?>
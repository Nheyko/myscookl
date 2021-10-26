<?php include 'header.php' ?>

<?php

$isVisible = true;

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

// RESET

if (isset($_GET['reset']))
    $_SESSION['tagList'] = [];
?>

<?php

if (isset($_GET['save'])) {
    try {
        $db->beginTransaction();

        // REQUETES SQL => Ajout de la recette

        // Title
        if (isset($_SESSION['recipe']['recipe_title']) && !empty($_SESSION['recipe']['recipe_title'])) {
            $recipe_title = htmlspecialchars($_SESSION['recipe']['recipe_title']);
        } else {
            $errorMsg = "Merci de remplir tout les champs";
        }

        // Time
        if (isset($_SESSION['recipe']['recipe_time']) && !empty($_SESSION['recipe']['recipe_time'])) {
            $recipe_time = $_SESSION['recipe']['recipe_time'];
        } else {
            $errorMsg = "Merci de remplir tout les champs";
        }

        // Difficulty
        if (isset($_SESSION['recipe']['recipe_level']) && !empty($_SESSION['recipe']['recipe_level'])) {
            $recipe_level = $_SESSION['recipe']['recipe_level'];
        } else {
            $errorMsg = "Merci de remplir tout les champs";
        }

        // Price
        if (isset($_SESSION['recipe']['recipe_price']) && !empty($_SESSION['recipe']['recipe_price'])) {
            $recipe_price = $_SESSION['recipe']['recipe_price'];
        } else {
            $errorMsg = "Merci de remplir tout les champs";
        }

        // Recipe_is_restricted
        if (isset($_SESSION['recipe']['recipe_is_restricted'])) {
            $recipe_is_restricted = $_SESSION['recipe']['recipe_is_restricted'];
        } else {
            $recipe_is_restricted = 0;
        }

        // Nationality
        if (isset($_SESSION['recipe']['fk_nationality']) && !empty($_SESSION['recipe']['fk_nationality'])) {
            $fk_nationality = $_SESSION['recipe']['fk_nationality'] === 'null' ? null : $_SESSION['recipe']['fk_nationality'];
        } else {
            $errorMsg = "Merci de remplir tout les champs";
        }

        // Region
        if (isset($_SESSION['recipe']['fk_region']) && !empty($_SESSION['recipe']['fk_region'])) {
            $fk_region = $_SESSION['recipe']['fk_region'] === 'null' ? null : $_SESSION['recipe']['fk_region'];
        } else {
            $errorMsg = "Merci de remplir tout les champs";
        }

        // City
        if (isset($_SESSION['recipe']['fk_city']) && !empty($_SESSION['recipe']['fk_city'])) {
            $fk_city = $_SESSION['recipe']['fk_city'] === 'null' ? null : $_SESSION['recipe']['fk_city'];
        } else {
            $errorMsg = "Merci de remplir tout les champs";
        }

        // Description
        if (isset($_SESSION['recipe']['recipe_desc']) && !empty($_SESSION['recipe']['recipe_desc'])) {
            $recipe_desc = htmlspecialchars($_SESSION['recipe']['recipe_desc']);
        } else {
            $errorMsg = "Merci de remplir tout les champs";
        }

        $req = $db->prepare('INSERT INTO recipe (recipe_title, recipe_time, recipe_level, recipe_picture, recipe_price, recipe_is_restricted, recipe_desc, fk_user, fk_nationality, fk_region, fk_city) VALUES(?,?,?,?,?,?,?,?,?,?,?)');

        $req->execute(array(
            $recipe_title,
            $recipe_time,
            $recipe_level,
            $_SESSION['FileUpload'],
            $recipe_price,
            $recipe_is_restricted,
            $recipe_desc,
            $_SESSION['user']['id_user'],
            $fk_nationality,
            $fk_region,
            $fk_city
        ));

        $idlastrecipe = $db->lastInsertId();

        // REQUETES SQL => Ajout ingrédients avec boucle

        $add_ingredient = $db->prepare('INSERT INTO recipe_ingredient VALUES (?,?,?)');
        foreach($_SESSION['ingredientList'] as $key => $value) {
            $add_ingredient->execute(array(
                NULL,
                $idlastrecipe,
                $key
            ));
        }
        // REQUETES SQL => Ajout tags avec boucle

        if(isset($_SESSION['tagList']) && !empty($_SESSION['tagList'])) {

            $add_tag = $db->prepare('INSERT INTO recipe_tag VALUES(?,?,?)');
            $add_ingredient = $db->prepare('INSERT INTO recipe_ingredient VALUES (?,?,?)');
            foreach($_SESSION['tagList'] as $key => $value) {
                $add_tag->execute(array(
                    NULL,
                    $idlastrecipe,
                    $key
                ));
            }
        }

        $db->commit();
        
        $successMsg = 'Votre recette a été ajouté !';
        $isVisible = false;

    } catch (Exception $e) {
        $db->rollBack();
        $errorMsg = 'Erreur lors de la sauvegarde de votre recette';
        var_dump($e->getMessage());
    }
}

?>

<?php

if (
    !isset($_SESSION['recipe'])    ||
    !isset($_SESSION['FileUpload']) ||
    empty($_SESSION['recipe'])      ||
    empty($_SESSION['FileUpload'])
) {

    header('Location: addrecipe.php');
    exit;
}

// // Title
// if(isset($_POST['recipe_title']) && !empty($_POST['recipe_title'])) {
//     $recipe_title = htmlspecialchars($_POST['recipe_title']);

// } else {
//     $errorMsg = "Merci de remplir tout les champs";
// }

// // Time
// if(isset($_POST['recipe_time']) && !empty($_POST['recipe_time'])) {
//     $recipe_time = $_POST['recipe_time'];

// } else {
//     $errorMsg = "Merci de remplir tout les champs";
// }

// // Difficulty
// if(isset($_POST['recipe_level']) && !empty($_POST['recipe_level'])) {
//     $recipe_level = $_POST['recipe_level'];

// } else {
//     $errorMsg = "Merci de remplir tout les champs";
// }

// // Price
// if(isset($_POST['recipe_price']) && !empty($_POST['recipe_price'])) {
//     $recipe_price = $_POST['recipe_price'];

// } else {
//     $errorMsg = "Merci de remplir tout les champs";
// }

// // Recipe_is_restricted
// if(isset($_POST['recipe_is_restricted'])) {
//     $recipe_is_restricted = $_POST['recipe_is_restricted'];
// } else {
//     $recipe_is_restricted = 0;
// }

// // Nationality
// if(isset($_POST['fk_nationality']) && !empty($_POST['fk_nationality'])) {
//     $fk_nationality = $_POST['fk_nationality'] === 'null' ? null : $_POST['fk_nationality'];
// } else {
//     $errorMsg = "Merci de remplir tout les champs";
// }

// // Region
// if(isset($_POST['fk_region']) && !empty($_POST['fk_region'])) {
//     $fk_region = $_POST['fk_region'] === 'null' ? null : $_POST['fk_region'];
// } else {
//     $errorMsg = "Merci de remplir tout les champs";
// }

// // City
// if(isset($_POST['fk_city']) && !empty($_POST['fk_city'])) {
//     $fk_city = $_POST['fk_city'] === 'null' ? null : $_POST['fk_city'];
// } else {
//     $errorMsg = "Merci de remplir tout les champs";
// }

// if(isset($_POST['recipe'])) {
//     $recipe = $_POST['recipe'];
// } else {
//     $recipe = "assets/image_recipe/default.jpeg";
// }

// // Description
// if(isset($_POST['recipe_desc']) && !empty($_POST['recipe_desc'])) {
//     $recipe_desc = htmlspecialchars($_POST['recipe_desc']);

// } else {
//     $errorMsg = "Merci de remplir tout les champs";
// }

// $req = $db->prepare('INSERT INTO recipe (recipe_title, recipe_time, recipe_level, recipe, recipe_price, recipe_is_restricted, recipe_desc, fk_user, fk_nationality, fk_region, fk_city) VALUES(?,?,?,?,?,?,?,?,?,?,?)');

// $req->execute(array(
//     $recipe_title,
//     $recipe_time,
//     $recipe_level,
//     $recipe,
//     $recipe_price,
//     $recipe_is_restricted,
//     $recipe_desc,
//     $_SESSION['user']['id_user'],
//     $fk_nationality,
//     $fk_region,
//     $fk_city
// ));

// $idRecipe = $db->lastInsertId(); 
?>

<?php

// SQL QUERY FOR SEARCH INGREDIENTS IN DATABASE

if (isset($_POST['searchTag']) && !empty($_POST['searchTag'])) {
    try {

        $curSearch = $_POST['searchTag'];

        $req = $db->prepare("SELECT * FROM tag WHERE tag_name LIKE ?");
        $req->execute(array(
            '%' . $curSearch . '%'
        ));
    } catch (Exception $e) {
        die($e->getMessage());
    }
}
?>

<?php

// ADD TAG IN SESSION ARRAY

if (isset($_GET['id']) && isset($_GET['name'])) {

    if (!isset($_SESSION['tagList'])) {

        $_SESSION['tagList'] = [];
    }

    foreach ($_SESSION['tagList'] as $ing) {
        foreach ($ing as $key => $value) {
            if ($key == $_GET['id']) {
                $errorMsg = 'Ce tag est déjà dans votre liste';
                break;
            }
        }
    }

    if (empty($errorMsg)) {
        //array_push($_SESSION['tagList'], array($_GET['id'] => $_GET['name']));
        $_SESSION['tagList'][$_GET['id']] = array('name' => $_GET['name']);
    }
}

?>

<?php if($isVisible = true) { ?>



<h1>Ajoutez vos tags</h1>

<form action="" method="post">

    <div class="form-group">
        <label for="searchTag">Recherche :</label>
        <input type="text" class="form-control" name="searchTag" id="searchTag" placeholder="Exemple : Sucré">
    </div>

    <button type="submit" class="btn btn-primary">Valider</button>

</form>

<?php if (isset($_POST['searchTag']) && !empty($_POST['searchTag'])) { ?>

    <div>
        <label for='tagList' class="margin-top">Liste correspondante :</label>
        <br />
        <?php
        while ($result = $req->fetch()) { ?>
            <a href="selecttag.php?id=<?= $result['id_tag']; ?>&name=<?= $result['tag_name']; ?>">
                <?= $result['tag_name'] . '<br/>'; ?>
            </a>
        <?php } ?>
        <a href="selecttag.php?addtag">Ajouter Tag</a>
    </div>
    </form>

<?php };

?>
</form>

<div style='margin:20px 0'>

    <?php if (isset($_SESSION['tagList']) && count($_SESSION['tagList']) > 0) { ?>
        <label for="curIngredients">Vos tags :</label>
    
    <ul class="list-group">
        <?php
        foreach ($_SESSION['tagList'] as $key => $array) {
            echo "<a href='selecttag.php?delete&name={$array['name']}' class='list-group-item'>{$array['name']} (Cliquez pour supprimer) </a>";
        }
    } ?>
    </ul>
</div>

<?php if (isset($_SESSION['tagList']) && count($_SESSION['tagList']) > 0) { ?>
    <a type="reset" href="selecttag.php?reset">Vider la liste</a>
    <br>
<?php } ?>

    <a type="submit" href="selecttag.php?save" class="btn btn-primary">Finalisation</a>

<?php } ?>

<?php include 'footer.php' ?>
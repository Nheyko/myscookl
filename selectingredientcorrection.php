<?php
include 'header.php';

// C H E C K - I F - C O N N E C T E D
if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
    header('Location: signin.php');
    exit;
}

// C L E A N - I N G R E D I E N T - S E S S I O N
if (isset($_GET['clean']) && isset($_SESSION['ingredientSelected'])) {

    $_SESSION['ingredientSelected'] = [];
}

// S E A R C H - I N G R E D I E N T
if (isset($_POST['search'])) {

    try {

        $request = $db->prepare('SELECT * FROM ingredient 
                                         WHERE ingredient_name LIKE ?');

        $request->execute([
            '%' . $_POST['search'] . '%'
        ]);

        $ingredientSearch = $request->fetchAll();

        if (empty($ingredientSearch)) {
            $errorMsg = 'Aucun ingredient trouvé.';
        }

    } catch (Exception $e) {
        var_dump($e->getMessage());
    }
}

// S E L E C T  -  I N G R E D I E N T
if (isset($_GET['id']) && isset($_GET['name']) && !empty($_GET['id']) && !empty($_GET['name'])) {

    if (!isset($_SESSION['ingredientSelected'])) {
        $_SESSION['ingredientSelected'] = [];
    }

    foreach ($_SESSION['ingredientSelected'] as $ing) {
        foreach ($ing as $key => $value) {
            if ($key == $_GET['id']) {
                $errorMsg = 'Cet ingrédient est déjà dans votre liste';
                break;
            }
        }
    }

    if (empty($errorMsg)) {

        array_push($_SESSION['ingredientSelected'], array($_GET['id'] => $_GET['name']));
        $successMsg = 'Ingédient ajouté !';
    }
}

// C R E A T E  -  I N G R E D I E N T
if (isset($_POST['addname']) && !empty($_POST['addname'])) {

    $cal = empty($_POST['addcal']) ? null : $_POST['addcal'];

    try {

        $request = $db->prepare('INSERT INTO ingredient VALUES (?, ?, ?)');
        $request->execute([
            NULL,
            $_POST['addname'],
            $cal
        ]);

        $id = $db->lastInsertId();

        array_push($_SESSION['ingredientSelected'],
            array($id => $_POST['addname']));

        $successMsg = 'Ingédient ajouté !';

    } catch (Exception $e) {
        var_dump($e->getMessage());
    }
}


include 'alertbox.php';
?>

<main>
    <h1>Ajoutez vos ingrédients</h1>
    <!-- S E A R C H  -  I N G R E D I E N T-->
    <form method="post" action="selectingredient.php">
        <div class="form-group">
            <label for="search">Recherche</label>
            <input type="text" name="search" class="form-control" id="search"
                   placeholder="Exemple : Sucre">
        </div>
        <button type="submit" class="btn btn-primary">Valider</button>
    </form>

    <!-- S E A R C H  -  R E S U L T-->
    <?php if (isset($ingredientSearch) && !empty($ingredientSearch)) { ?>
        <ul class="list-group">
            <li class='list-group-item active'>Vos resultats de recherche</li>
            <?php foreach ($ingredientSearch as $ingredient) {
                echo "<li class='list-group-item'>{$ingredient['ingredient_name']}
                        <a href='selectingredient.php?id={$ingredient['ingredient_id']}&name={$ingredient['ingredient_name']}' class='badge badge-primary badge-pill white'>Ajouter</a>
                      </li>";
            } ?>
        </ul>
    <?php } ?>

    <!-- C R E A T E  -  I N G R E D I E N T-->
    <?php if (isset($ingredientSearch)) { ?>
    <p>Vous ne trouvez pas votre ingrédient ? ajoutez le :</p>
    <form method="post" action="selectingredient.php">
        <div class="form-group">
            <label for="addname">Nom de l'ingrédient</label>
            <input type="text" name="addname" class="form-control" id="addname" value="<?= $_POST['search']; ?>">
            <label for="addcal">Calories</label>
            <input type="number" step="0.1" name="addcal" class="form-control" id="addcal">
        </div>
        <button type="submit" class="btn btn-primary">Ajouter</button>
    </form>
    <?php } ?>

    <!-- M Y  -  I N G R E D I E N T  -  L I S T -->
    <?php if (isset($_SESSION['ingredientSelected']) && !empty($_SESSION['ingredientSelected'])) { ?>
        <ul class="list-group">
            <li class='list-group-item list-group-item-dark'>Votre séléction</li>
            <?php foreach ($_SESSION['ingredientSelected'] as $ingredient) {
                foreach ($ingredient as $value) {
                    echo "<li class='list-group-item list-group-item-success'>$value</li>";
                }
            } ?>
        </ul>
        <a href="?clean=true">Vider la liste</a>
    <?php }

    // B U T T O N - N E X T - S T E P
    if (isset($_SESSION['ingredientSelected']) && !empty($_SESSION['ingredientSelected'])) { ?>
        <div class="mt-3 mb-5 float-right">
            <a href="addrecipe.php" class="btn btn-primary">Passer à l'étape suivante</a>
        </div>
    <?php } ?>
</main>

<?php
include 'footer.php';
?>

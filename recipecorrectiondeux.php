<?php
include 'header.php';


// G E T - I N G R E D I E N T  &
// G E T - T A G
try {
    $ingredient = getDataDb($db, 'ingredient', ' ORDER BY ingredient_name ASC');
    $tag        = getDataDb($db, 'tag', ' ORDER BY tag_name ASC');

} catch (Exception $e) {
    var_dump($e->getMessage());
}

// G E N E R A T E  -  S Q L  -  R E Q U E S T
$sql        = 'SELECT recipe.*, left(recipe.recipe_desc, 50) AS r_desc, u.user_name FROM recipe
                LEFT JOIN user AS u on recipe.id_user  = u.user_id';
$where      = [' WHERE recipe_is_validate = ?'];
$groupBy    = ' GROUP BY recipe.recipe_id';
$orderBy    = '';
$execute    = [1];


if (isset($_GET['search'])) {

    if (!empty($_GET['search'])) {

        array_push($where, ' AND recipe.recipe_title LIKE ?');
        array_push($execute, '%' . $_GET['search'] . '%');
    }

    if ($_GET['ingredient'] != 'null') {
        $sql .= ' LEFT JOIN recipe_ingredient AS ri ON recipe.recipe_id = ri.id_recipe';
        array_push($where, ' AND ri.id_ingredient = ?');
        array_push($execute, $_GET['ingredient']);

    }

    if ($_GET['tag'] != 'null') {
        $sql .= ' LEFT JOIN recipe_tag AS rt ON recipe.recipe_id = rt.id_recipe';
        array_push($where, ' AND rt.id_tag = ?');
        array_push($execute, $_GET['tag']);
    }

    array_push($where, ' AND recipe.recipe_level <= ?');
    array_push($execute, $_GET['level']);

    array_push($where, ' AND recipe.recipe_level_price <= ?');
    array_push($execute, $_GET['price']);

    if (isset($_GET['last'])) {

        $orderBy = ' ORDER BY recipe.recipe_date DESC';
    }
}

foreach ($where as $w) {

    $sql .= $w;
}

$sql = $sql . $groupBy . $orderBy;

try {

    $request = $db->prepare($sql);
    $request->execute($execute);
    $recipe = $request->fetchAll();

    if (empty($recipe)) {
        $errorMsg = 'Aucune recette trouvée.';
    } else {

        $s = count($recipe) > 1 ? 's' : '';
        $successMsg = count($recipe) . ' recette' . $s . ' trouvée' . $s;
    }

} catch (Exception $e) {
    var_dump($e->getMessage());
}

?>

<main>
    <h1>Recettes</h1>

    <form method="get" action="recipe.php" class="search">
        <div class="form-group medium mr-2">
            <label for="search">Recherche</label>
            <input type="text" name="search" class="form-control" id="search" value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>"
                   placeholder="ex : Tarte aux pommes">
        </div>

        <div class="form-group medium mr-2">
            <label for="ingredient">Ingredient</label>
            <select class="form-control" name="ingredient" id="ingredient">
                <option value="null">-- Séléctionnez --</option>
                <?php

                foreach ($ingredient as $ing) {

                    $selected = '';
                    if (isset($_GET['ingredient']) AND $_GET['ingredient'] == $ing['ingredient_id']) {
                        $selected = 'selected';
                    }

                    echo "<option $selected value='{$ing['ingredient_id']}'>{$ing['ingredient_name']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group medium mr-2">
            <label for="tag">Tag</label>
            <select class="form-control" name="tag" id="tag">
                <option value="null">-- Séléctionnez --</option>
                <?php

                foreach ($tag as $t) {

                    $selected = '';
                    if (isset($_GET['tag']) AND $_GET['tag'] == $t['tag_id']) {
                        $selected = 'selected';
                    }

                    echo "<option $selected value='{$t['tag_id']}'>{$t['tag_name']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group mr-2">
            <label for="level" class="form-label">Niveau de difficulté</label>
            <div>
                <input name="level" type="range" class="form-range" value="<?= isset($_GET['level']) ? $_GET['level'] : '3' ?>" min="1" max="3" step="1" id="level">
            </div>
        </div>
        <div class="form-group mr-2">
            <label for="price" class="form-label">Estimation du prix</label>
            <div>
                <input name="price" type="range" class="form-range" value="<?= isset($_GET['price']) ? $_GET['price'] : '3' ?>" min="1" max="3" step="1" id="price">
            </div>
        </div>

        <div class="form-check mb-3 mr-2">
            <input <?php echo isset($_GET['last']) ? 'checked' : '' ?> name="last" class="form-check-input" type="checkbox" value="" id="last">
            <label class="form-check-label" for="last">
                Récent uniquement
            </label>
        </div>

        <button type="submit" class="btn btn-primary">Valider</button>
    </form>

    <?php include 'alertbox.php'; ?>

    <div class="recipe_list mt-5">
        <?php foreach ($recipe as $r) { ?>
            <div class="card mr-2 mt-2" style="width: 18rem;">
                <div class="bg_rec">
                    <img class="card-img-top" src="<?= $r['recipe_picture'] ?>" alt="Card image cap">
                </div>
                <div class="card-body">
                    <h5 class="card-title"><?= $r['recipe_title'] ?></h5>
                    <p class="card-text"><?= $r['r_desc'] ?>...</p>
                    <div class="picto">
                        <?php for ($i = 0; $i < 3; $i++) {
                            $opacity = $r['recipe_level_price'] > $i ? '' : 'empty';
                            echo "<img class='$opacity mr-1' src='public/assets/picture/euro.png'>";
                        } ?>
                    </div>
                    <div class="picto mt-2">
                        <?php for ($i = 0; $i < 3; $i++) {
                            $opacity = $r['recipe_level'] > $i ? '' : 'empty';
                            echo "<img class='$opacity mr-1' src='public/assets/picture/muscle (1).png'>";
                        } ?>
                    </div>
                    <a href="recipedetail.php?id=<?= $r['recipe_id'] ?>" class="btn btn-primary mt-4">Voir la recette
                        de <?= $r['user_name'] ?></a>
                </div>
            </div>
        <?php } ?>
    </div>
</main>
<?php
include 'footer.php';
?>

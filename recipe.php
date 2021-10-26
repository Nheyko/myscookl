<?php include 'header.php';

$tag = $db->query('SELECT * from tag');
$ingredient = $db->query('SELECT * from ingredient');

$sql = 'SELECT re.*, us.user_name
FROM recipe                 AS re
LEFT JOIN user              AS us ON re.fk_user = us.id_user
LEFT JOIN recipe_ingredient AS ri ON ri.fk_recipe = re.id_recipe
LEFT JOIN recipe_tag        AS rt ON rt.fk_recipe = re.id_recipe
WHERE recipe_is_validate = ?
';

$searchArray = array(1);

if(isset($_GET['searchName'])) {

    if(!empty($_GET['searchName'])) {
        $sql .= " AND recipe_title LIKE ?";
        array_push($searchArray, '%' . $_GET['searchName'] . '%');
    } else {
        $_GET['searchName'] = '';
    }
    if(!empty($_GET['searchIngredient']) && $_GET['searchIngredient'] != 'null') {
        $sql .= " AND fk_ingredient = ?";
        array_push($searchArray, $_GET['searchIngredient']);
    }
    if(!empty($_GET['searchTag']) && $_GET['searchTag'] != 'null') {
        $sql .= " AND fk_tag = ?";
        array_push($searchArray, $_GET['searchTag']);
    }
    if(!empty($_GET['searchDifficulty'])) {
        $sql .= " AND recipe_level <= ?";
        array_push($searchArray, $_GET['searchDifficulty']);
    }
    if(!empty($_GET['searchPrice'])) {
        $sql .= " AND recipe_price <= ?";
        array_push($searchArray, $_GET['searchPrice']);
    }
}

$sql .= ' GROUP BY ' . 'id_recipe';

?>

<?php

$req = $db->prepare($sql);
$req->execute($searchArray);

?>

<div>
    <p>Champs de recherche :</p>
    <form action="" method="GET">
        <label for="searchName">Nom de recette</label>
        <input type="text" id="searchName" name="searchName" value="<?= isset($_GET['searchName']) ? $_GET['searchName'] : '' ?>">

        <label for="searchIngredient">Ingrédients</label>
        <select name="searchIngredient" id="searchIngredient">
            <option value="null">- Selectionnez un ingrédient -</option>
            <?php 
            
            $selected = '';
            if (isset($_GET['searchIngredient']) AND $_GET['searchIngredient'] == $t['id_ingredient']) {
                $selected = 'selected';
            }
            
            while($result = $ingredient->fetch()) { ?>
                <option $selected value="<?= $result['id_ingredient'];?>"><?= $result['ingredient_name'];?></option>
            <?php } ?>
        </select>

        <label for="searchTag">Tags</label>
        <select name="searchTag" id="searchTag">
        <option value="null">- Selectionnez un tag -</option>
        <?php 
        
        $selected = '';
        if (isset($_GET['searchTag']) AND $_GET['searchTag'] == $t['id_tag']) {
            $selected = 'selected';
        }
        
        while($result = $tag->fetch()) { ?>
        <option $selected value="<?= $result['id_tag'];?>"><?= $result['tag_name'];?></option>
        <?php } ?>
        </select>

        <br>

        <label for="searchDifficulty">Difficulté</label>
        <input type="range" min="1" max="3" step="1" id="searchDifficulty" name="searchDifficulty" value="<?= isset($_GET['searchDifficulty']) ? $_GET['searchDifficulty'] : 2 ?>">

        <label for="searchPrice">Prix</label>
        <input type="range" min="1" max="3" step="1" id="searchPrice" name="searchPrice" value="<?= isset($_GET['searchPrice']) ? $_GET['searchPrice'] : 2 ?>">

        <input type="submit" value="Valider">

    </form>
</div>

<?php

while ($result = $req->fetch()) { ?>

    <div class="card" style="width: 18rem;">
        <img class="card-img-top" src="<?= $result['recipe_picture']; ?>" alt="Card image cap">
        <div class="card-body">
            <h5 class="card-title"><?= $result['recipe_title']; ?></h5>
            <p class="card-text"><?= $result['recipe_desc']; ?> (posted by <?= $result['user_name']; ?>)</p>

            <p class="card-text">Difficulty :
                <?php

                for ($i = 0; $i < 3; $i++) {

                    $opcaity = $result['recipe_level'] > $i ? '' : 'empty';
                    echo "<img class='$opcaity' src='./public/assets/image/Difficulty.png' width='20px'>";
                }

                ?>
            </p>

            <p class="card-text">Price :
                <?php

                for ($i = 0; $i < 3; $i++) {

                    $opcaity = $result['recipe_price'] > $i ? '' : 'empty';
                    echo "<img class='$opcaity' src='./public/assets/image/Price.png' width='20px'>";
                }
                ?>
            </p>

            <a href="recipedetail.php?id=<?= $result['id_recipe'] ?>" class="btn btn-primary">More details</a>
        </div>
    </div>

<?php } ?>

<?php include 'footer.php' ?>
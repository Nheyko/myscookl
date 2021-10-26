<?php
include 'header.php';

try {

    $request = $db->prepare('SELECT recipe.*,
                                            left(recipe.recipe_desc, 50) AS r_desc,
                                            u.user_name,
                                            a.*
                                     FROM recipe
                                     LEFT JOIN user   AS u on recipe.id_user  = u.user_id
                                     LEFT JOIN avatar AS a on u.id_avatar     = a.avatar_id
                                     WHERE recipe_is_validate = ?');

    $request->execute([
        1
    ]);


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


include 'alertbox.php';
?>

<main>
    <h1>Recettes</h1>

    <div class="recipe_list">
        <?php foreach ($recipe as $r) { ?>
            <div class="card" style="width: 18rem;">
                <div class="bg_rec">
                    <img class="card-img-top" src="<?= $r['recipe_picture'] ?>" alt="Card image cap">
                </div>
                <div class="card-body">
                    <h5 class="card-title"><?= $r['recipe_title'] ?></h5>
                    <p class="card-text"><?= $r['r_desc'] ?>...</p>
                    <div class="picto">
                        <?php for ($i = 0; $i < 3; $i++) {
                            $opcaity = $r['recipe_level_price'] > $i ? '' : 'empty';
                            echo "<img class='$opcaity mr-1' src='public/assets/picture/euro.png' alt='Card image cap'>";
                        }?>
                    </div>
                    <div class="picto mt-2">
                        <?php for ($i = 0; $i < 3; $i++) {
                            $opcaity = $r['recipe_level'] > $i ? '' : 'empty';
                            echo "<img class='$opcaity mr-1' src='public/assets/picture/muscle (1).png'>";
                        }?>
                    </div>
                    <a href="recipedetail.php?id=<?= $r['recipe_id'] ?>" class="btn btn-primary mt-4">Voir la recette de <?= $r['user_name'] ?></a>
                </div>
            </div>
        <?php } ?>
    </div>
</main>
<?php
include 'footer.php';
?>

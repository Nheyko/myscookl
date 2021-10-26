<?php include('header.php'); ?>

<?php

// C H E C K - I F - C O N N E C T E D
if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
    header('Location: signin.php');
    exit;
}

?>

<?php

$tag = $db->query('SELECT * from tag');
$ingredient = $db->query('SELECT * from ingredient');

$sql = 'SELECT re.*, us.user_name
FROM recipe                 AS re
LEFT JOIN user              AS us ON re.fk_user = us.id_user
LEFT JOIN recipe_ingredient AS ri ON ri.fk_recipe = re.id_recipe
LEFT JOIN recipe_tag        AS rt ON rt.fk_recipe = re.id_recipe
WHERE us.id_user = ?
';

$sql .= ' GROUP BY ' . 'id_recipe';

$req = $db->prepare($sql);
$req->execute(array(
    $_SESSION['user']['id_user']
));

$results = $req->fetchAll();

?>

<?php

    // DELETE RECIPE

    if(isset($_GET['delete'])) {

        try {
        $req = $db->query("CALL proc_delete_recipe({$_GET['id']})");

        $successMsg = "Message supprimé";
         
        } catch(Exception $e) {
            var_dump($e->getMessage());
        }

    }

?>


<?php foreach ($results as $key => $result) { ?>

    <?php if($result['fk_user'] == $_SESSION['user']['id_user']) { ?>

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

                <a href="recipedetail.php?id=<?= $result['id_recipe']; ?>" class="btn btn-primary">More details</a>
                <a href="myrecipe.php?id=<?= $result['id_recipe']; ?>&delete" class="btn btn-primary">Delete</a>
            </div>
        </div>

        <?php } ?>

<?php } ?>


<?php include('footer.php'); ?>
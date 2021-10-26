<?php include('header.php');

$recipe = $db->prepare("SELECT *, user_name, (SELECT avg(rank_level) FROM rank WHERE rank.fk_recipe = ?) AS rank_avg, r.* FROM recipe
LEFT JOIN rank              AS r    ON recipe.id_recipe = r.fk_recipe AND r.fk_user = ?
LEFT JOIN user              AS u    ON recipe.fk_user   = u.id_user
WHERE id_recipe = ?");
$recipe->execute(array(
    $_GET['id'],
    $_SESSION['user']['id_user'],
    $_GET['id']
));

$result = $recipe->fetch();


$req = $db->prepare("SELECT i.ingredient_name from recipe_ingredient as ri
LEFT JOIN ingredient AS i ON i.id_ingredient = ri.fk_ingredient
WHERE ri.fk_recipe = ?");
$req->execute(array(
    $_GET['id']
));


$ingredient = $req->fetchAll();


$req = $db->prepare("SELECT t.tag_name from recipe_tag as rt
LEFT JOIN tag AS t ON t.id_tag = rt.fk_tag
WHERE rt.fk_recipe = ?");
$req->execute(array(
    $_GET['id']
));

$tag = $req->fetchAll();

$req = $db->prepare("SELECT format_date(recipe_date) FROM recipe WHERE id_recipe = ?;");
$req->execute(array(
    $_GET['id']
));

$date = $req->fetch();

$req = $db->prepare("SELECT format_time(recipe_time) FROM recipe WHERE id_recipe = ?;");
$req->execute(array(
    $_GET['id']
));

$time = $req->fetch();

if (isset($_POST['rank'])) {
    try {

        $req = $db->prepare('INSERT INTO rank VALUES(?,?,?)');
        $req->execute(array(
            $_SESSION['user']['id_user'],
            $_GET['id'],
            $_POST['rank']
        ));
    } catch (Exception $e) {
        var_dump($e);
    }
}

if(isset($_POST['comment'])) {
    try {
        
        $req = $db->prepare('INSERT INTO remark VALUES(?,?,?,?,?)');
        $req->execute(array(
            NULL,
            $_POST['comment'],
            date('Y-m-d H:i:s'),
            $_GET['id'],
            $_SESSION['user']['id_user']
        ));

    } catch (Exception $e) {
        die($e->getMessage());
    }
}

try {

    $req = $db->prepare('SELECT *, user.user_name FROM remark
    LEFT JOIN user ON fk_user = user.id_user WHERE fk_user = ?');

    $req->execute(array(
        $_SESSION['user']['id_user']
    ));

    $comments = $req->fetchAll();

} catch(Exception $e) {
    die($e->getMessage());
}

?>

<div class="card" style="width: 30rem;">
    <img class="card-img-top" src="<?= $result['recipe_picture']; ?>" alt="Card image cap">
    <div class="card-body">
        <h5 class="card-title"><?= $result['recipe_title']; ?></h5>
        <p class="card-text"><?= $result['recipe_desc']; ?> (posté par <?= $result['user_name']; ?>)</p>

        <p class="card-text">
            <img src="./public/assets/image/sablier.jpg" alt="Image d'un sablier" width="20px">
            <?php
            echo $time[0];
            ?>
        </p>

        <p class="card-text">Note moyenne :
            <?php

            if ($result['fk_recipe'] == $_GET['id']) {
                for ($i = 0; $i < 5; $i++) {

                    $opcaity = $result['rank_avg'] > $i ? '' : 'empty';
                    echo "<img class='$opcaity' src='./public/assets/image/Star.png' width='20px'>";
                }
            }
            ?>
        </p>

        <p class="card-text">Difficulté :
            <?php

            for ($i = 0; $i < 3; $i++) {

                $opcaity = $result['recipe_level'] > $i ? '' : 'empty';
                echo "<img class='$opcaity' src='./public/assets/image/Difficulty.png' width='20px'>";
            }

            ?>
        </p>


        <p class="card-text">Prix :
            <?php

            for ($i = 0; $i < 3; $i++) {

                $opcaity = $result['recipe_price'] > $i ? '' : 'empty';
                echo "<img class='$opcaity' src='./public/assets/image/Price.png' width='20px'>";
            }
            ?>
        </p>

        <p class="card-text">Ingrédients :
            <br>
            <?php
            foreach ($ingredient as $key => $value) {
                echo $value['ingredient_name'] . '<br>';
            }
            ?>
        </p>

        <p class="card-text">Tags :
            <br>
            <?php
            foreach ($tag as $key => $value) {
                echo $value['tag_name'] . '<br>';
            }
            ?>
        </p>

        <p class="card-text">Date :
            <br>
            <?= $date[0]; ?>
        </p>

        <p class="card-text"><label for="rank" class="form-label">Votre note :</label>
            <br>
            <?php if (!$result['rank_level']) { ?>
        <form action="recipedetail.php?id=<?= $_GET['id'] ?>" method="POST">
            <input type="range" class="form-range" id="rank" name="rank" step="1" min="1" max="5">
            <input type="submit" value="Noter la recette">
        </form>
    <?php } else {
                for ($i = 0; $i < 5; $i++) {

                    $opcaity = $result['rank_level'] > $i ? '' : 'empty';
                    echo "<img class='$opcaity' src='./public/assets/image/Star.png' width='20px'>";
                }
            } ?>
    </p>
    </div>

</div>

<div>
    <p>Commentaires :</p>

    <?php foreach($comments as $key => $comment) { ?>
        <p>Commentaire posté par <?= $comment['user_name']?></p>
        <p>Commentaire :
            <?= $comment['remark_msg']?>
        </p>
        <p>Le <?=$comment['remark_date']?></p>

    <?php } ?>

    

    <form method="POST" action="recipedetail.php?id=<?= $_GET['id'] ?>">

        <label for="comment">Votre texte :</label>
        <textarea name="comment" id="comment" cols="30" rows="10"></textarea>

        <input type="submit" value="Poster">
    </form>

</div>
<?php include('footer.php') ?>
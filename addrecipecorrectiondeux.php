<?php
include 'header.php';

if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
    header('Location: signin.php');
    exit;
}


if (!isset($_SESSION['ingredientSelected']) || empty($_SESSION['ingredientSelected'])) {
    header('Location: selectingredient.php');
    exit;
}

if (isset($_POST['title'])) {

    if (empty($_POST['title'])          ||
        empty($_POST['level'])          ||
        empty($_POST['price'])          ||
        empty($_POST['time'])           ||
        empty($_POST['desc'])           ||
        empty($_POST['nationality'])    ||
        empty($_POST['region'])         ||
        empty($_POST['city'])) {

        $errorMsg = 'Merci de compléter les champs obligatoires';

    } else {

        try {

            $now = date('Y-m-d H:i:s');

             // U P L O A D //

            $targetDir      = "public/assets/picture_recipe/";
            $imageFileType  = strtolower(pathinfo($_FILES['picture']['name'], PATHINFO_EXTENSION));
            $targetFile     = $targetDir . $_SESSION['user']['user_id'] . ' - ' . $now . '.' . $imageFileType;

            $uploadOk = true;

            $check = getimagesize($_FILES['picture']['tmp_name']);

            if ($check === false) {
                $uploadOk = false;
                $errorMsg = 'Votre fichier n\'est pas de type image.';
            }

            if (file_exists($targetFile)) {
                $uploadOk = false;
                $errorMsg = 'Votre fichier existe déjà.';
            }

            if ($_FILES['picture']['size'] > 500000) {
                $uploadOk = false;
                $errorMsg = 'Votre fichier dépasse la limite des 500ko.';
            }

            if ($imageFileType != "jpg"  &&
                $imageFileType != "jpeg" &&
                $imageFileType != "png") {

                $uploadOk = false;
                $errorMsg = 'Votre fichier doit être au format jpg, jpeg ou png.';

            }

            if ($uploadOk === true) {

                if (move_uploaded_file($_FILES['picture']['tmp_name'], $targetFile)) {

                    $_SESSION['recipe'] = $_POST;
                    header ('Location: selecttag.php');
                    exit();

                } else {

                    $errorMsg = 'Echec de l\'upload, merci de réessayer dans un moment.';
                }
            }

        } catch (Exception $e) {

            var_dump($e->getMessage());
        }
    }
}




try {

    $nationality    = getDataDb($db, 'nationality');
    $region         = getDataDb($db, 'region');
    $city           = getDataDb($db, 'city');

} catch (Exception $e) {

    var_dump($e->getMessage());
}


include 'alertbox.php';
?>

<main>
    <h1></h1>

    <main>
        <h1>Créez votre recette !</h1>

        <form method="post" action="addrecipe.php" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Titre</label>
                <input type="text" name="title" class="form-control" id="title"
                       placeholder="Titre de votre recette">
            </div>
            <div class="form-group">
                <label for="picture">Example file input</label>
                <input name="picture" type="file" class="form-control-file" id="picture">
            </div>
            <div class="form-group">
                <label for="level" class="form-label">Niveau de difficulté</label>
                <div>
                    <input name="level" type="range" class="form-range" min="1" max="3" step="1" id="level">
                </div>
            </div>
            <div class="form-group">
                <label for="price" class="form-label">Estimation du prix</label>
                <div>
                    <input name="price" type="range" class="form-range" min="1" max="3" step="1" id="price">
                </div>
            </div>
            <div class="form-group">
                <label for="time">Temps de réalisation</label>
                <input type="time" name="time" class="form-control" id="time">
            </div>
            <div class="form-check mb-3">
                <input name="restrict" class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                <label class="form-check-label" for="flexCheckDefault">
                    Recette à base d'alcool
                </label>
            </div>
            <div class="form-group">
                <label for="desc">Détails de la recette</label>
                <textarea name="desc" class="form-control" id="desc" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label for="nationality">Nationalité</label>
                <select class="form-control" name="nationality" id="nationality">
                    <option value='null'>-- SELECTIONNEZ -- </option>
                    <?php

                    foreach ($nationality as $nas) {

                        echo "<option value='{$nas['nationality_id']}'>{$nas['nationality_name']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="nationality">Région</label>
                <select class="form-control" name="region" id="region">
                    <option value='null'>-- SELECTIONNEZ -- </option>
                    <?php

                    foreach ($region as $re) {

                        echo "<option value='{$re['region_id']}'>{$re['region_name']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="city">Ville</label>
                <select class="form-control" name="city" id="city">
                    <option value='null'>-- SELECTIONNEZ -- </option>
                    <?php

                    foreach ($city as $ci) {

                        echo "<option value='{$ci['city_id']}'>{$ci['city_name']}</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Valider</button>
        </form>
    </main>
</main>


<?php
include 'footer.php';
?>

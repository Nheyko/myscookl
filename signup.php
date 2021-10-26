<?php include "header.php";

$avatar         = [];
$nationality    = [];
$region         = [];
$city           = [];

if (isset($_POST['email'])) {
    if (
        empty($_POST['email'])              ||
        empty($_POST['password'])           ||
        empty($_POST['passwordconfirm'])    ||
        empty($_POST['civ'])                ||
        empty($_POST['pseudo'])             ||
        empty($_POST['birth'])              ||
        empty($_POST['nationality'])
    ) {
        $errorMsg = 'Merci de compléter tous les champs obligatoires !';

    } else if ($_POST['password'] !== $_POST['passwordconfirm']) {

        $errorMsg = 'Merci de vérifier vos mots de passe';

    } else {

        $dbCiv      = $_POST['civ']     === 'h'     ? 0     : 1;
        $dbCity     = $_POST['city']    === 'null'  ? null  : $_POST['city'];
        $dbRegion   = $_POST['region']  === 'null'  ? null  : $_POST['region'];
        $dbPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);

        try {
            $request = $db->prepare('INSERT INTO user (user_civ, user_email, user_password, user_name, user_birth, fk_avatar, fk_nationality, fk_region, fk_city) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');

            $request->execute(
                [
                    $dbCiv,
                    $_POST['email'],
                    $dbPassword,
                    $_POST['pseudo'],
                    $_POST['birth'],
                    $_POST['avatar'],
                    $_POST['nationality'],
                    $dbRegion,
                    $dbCity
                ]
            );

            // Changer * par les noms des champs
            $request = $db->prepare('SELECT * FROM user AS us
            LEFT JOIN avatar AS av ON us.fk_avatar = av.id_avatar
            LEFT JOIN nationality AS na ON us.fk_nationality = na.id_nationality
            LEFT JOIN region AS re ON us.fk_region = re.id_region
            LEFT JOIN city AS ci ON us.fk_city = ci.id_city
            WHERE us.user_name = ?');

            $request->execute([
                $_POST['pseudo']
            ]);

            $_SESSION['user'] = $request->fetch();
            var_dump($_SESSION);

            $successMsg = 'Bienvenue ' . $_POST['pseudo'];

        } catch (Exception $e) {

            $errorMsg = 'Impossible de créer votre compte pour le moment';
            var_dump($e->getMessage());
        }
    }
}

try {

    $avatar = getData($db, 'avatar');
    $nationality = getData($db, 'nationality');
    $region = getData($db, 'region');
    $city = getData($db, 'city');

    //var_dump($nationality, $region, $city, $avatar);

} catch (Exception $th) {

    var_dump($th->getMessage());
}

function getData($db, $table)
{
    $request = $db->prepare("SELECT * FROM $table");
    $request->execute([]);
    return $request->fetchAll();
}

include 'alertbox.php';

?>

<?php if(empty($successMsg)) { ?>
<h1>Création de votre compte</h1>
<?php } ?>

<?php if(empty($successMsg)) {?>

<form method="post" action="">
    <div class="form-group">
        <label for="email">Adresse email</label>
        <input type="email" class="form-control" name="email" id="email" aria-describedby="emailHelp" placeholder="Adresse email">
    </div>

    <div class="form-group">
        <label for="password">Mot de passe</label>
        <input type="password" class="form-control" name="password" id="password" placeholder="Mot de passe">
    </div>

    <div class="form-group">
        <label for="passwordconfirm">Confirmer le mot de passe</label>
        <input type="password" class="form-control" name="passwordconfirm" id="passwordconfirm" placeholder="Confirmer le mot de passe">
    </div>

    <div class="form-group">
        <label for="pseudo">Pseudo</label>
        <input type="text" class="form-control" name="pseudo" id="pseudo" placeholder="Pseudo">
    </div>

    <div class="bloc_avatar">

        <?php
        foreach ($avatar as $key => $av) {
            $checked = '';
            if ($key == 0) {
                $checked = 'checked';
            }
            echo "
            <div class='form-check'>
            <input class='form-check-input' type='radio' name='avatar' id='{$av['id_avatar']}' value='{$av['id_avatar']}' $checked>
            <label for='{$av['id_avatar']}'>
                <img class='avatar' src='public/assets/avatar/{$av['avatar_file_name']}' alt=''>
            </label>
            </div>
            ";
        }
        ?>

    </div>

    <div class="form-group">
        <label for="birth">Date de naissance</label>
        <input type="date" class="form-control" name="birth" id="birth">
    </div>

    <div class="form-check">
        <input class="form-check-input" type="radio" name="civ" id="civf" value="f" checked>
        <label class="form-check-label" for="civf">
            Mme
        </label>
    </div>

    <div class="form-check">
        <input class="form-check-input" type="radio" name="civ" id="civh" value="h">
        <label class="form-check-label" for="civh">
            Mr
        </label>
    </div>

    <div class='form-group'>
        <label for='nationality'>Nationalité</label>
        <select class='form-control' name='nationality' id='nationality'>";
            <option value='null' selected>- Selectionner une valeur -</option>
            <?php
            foreach ($nationality as $nat) {
                echo "<option value='{$nat['id_nationality']}'>{$nat['nationality_name']}</option>";
            }
            ?>
        </select>
    </div>

    <div class='form-group'>
        <label for='region'>Région</label>
        <select class='form-control' name='region' id='region'>";
            <option value='null' selected>- Selectionner une valeur -</option>
            <?php
            foreach ($region as $reg) {
                echo "<option value='{$reg['id_region']}'>{$reg['region_name']}</option>";
            }
            ?>
        </select>
    </div>

    <div class='form-group'>
        <label for='city'>Ville</label>
        <select class='form-control' name='city' id='city'>";
            <option value='null' selected>- Selectionner une valeur -</option>
            <?php
            foreach ($city as $cit) {
                echo "<option value='{$cit['id_city']}'>{$cit['city_name']}</option>";
            }
            ?>
        </select>
    </div>

    <button type="submit" class="btn btn-primary">S'inscrire</button>
</form>

<?php } ?>

<?php include "footer.php"; ?>
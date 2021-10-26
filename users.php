<?php include('header.php'); ?>

<!-- refaire une procédure stocké pour supprimer en plus les commentaires postés sur d'autres recette etc... -->

<?php

// C H E C K - I F - C O N N E C T E D
if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
    header('Location: signin.php');
    exit;
}

?>

<?php

    // RECUPERATION DONNEES

    $tag = $db->query('SELECT * from tag');
    $ingredient = $db->query('SELECT * from ingredient');

    $sql = 'SELECT *, avatar_file_name FROM user
    LEFT JOIN avatar AS av ON user.fk_avatar = av.id_avatar
    LEFT JOIN nationality AS na ON user.fk_nationality = na.id_nationality
    LEFT JOIN region AS re ON user.fk_region = re.id_region
    LEFT JOIN city AS ci ON user.fk_city = ci.id_city
    WHERE user_name = ?';

    $req = $db->prepare($sql);
    $req->execute(array(
        $_GET['username']
    ));

    $result = $req->fetch();

?>

<?php

    // DELETE USER

    if(isset($_GET['delete'])) {

        try {

        $req = $db->query("CALL proc_delete_user({$_GET['id']})");

        $successMsg = "Utilisateur supprimé";
         
        } catch(Exception $e) {
            var_dump($e->getMessage());
        }

    }

?>

<!-- VIEW -->

<p>
    ID : <?= $result['id_user']?> <br>
    Civilité : <?= $result['user_civ'] == "0" ? "Homme" : "Femme"; ?> <br>
    Email :  <?= $result['user_email']?> <br>
    Login : <?= $result['user_name']?> <br>
    Date de naissance : <?= $result['user_birth']?> <br>
    Date de création du compte : <?= $result['user_date_create']?> <br>
    Avatar : <?= $result['avatar_file_name']?> <br>
    Nationalitée : <?= $result['nationality_name']?> <br>
    Région : <?= $result['region_name'] == NULL ? "Non connue" : $result['region_name'];?> <br>
    Ville : <?= $result['city_name'] == NULL ? "Non connue" : $result['city_name'];?> <br>

    <a href="users.php?username=<?=$result['user_name']?>&id=<?= $result['id_user']; ?>&delete" class="btn btn-primary">Delete User</a>
</p>



<?php include('footer.php') ?>
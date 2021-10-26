<?php include 'header.php'; ?>

<?php

function connect($password, $hash, $user) {
    $verifiedPassword = password_verify($password, $hash);

    if($verifiedPassword === false) {

        //$errorMsg = 'Mauvais mot de passe !';
        $errorMsg = 'Veuillez remplir tous les champs !';

        echo "<p>Redirection automatique dans 3 secondes.</p>";
        header("refresh: 3, URL=signin.php");
    }
    else {

        $_SESSION['user'] = $user;
        $_SESSION['user']['user_password'] = null;

        $successMsg = 'Vous êtes connecté !';

        echo "<p>Redirection automatique dans 3 secondes.</p>";
        header("refresh: 3, URL=index.php");
    }
}

?>

<?php

    if(isset($_POST['pseudo']) && !empty($_POST['pseudo']) && isset($_POST['password']) && !empty($_POST['password'])) {

        $username = htmlspecialchars($_POST['pseudo']);
        $password = htmlspecialchars($_POST['password']);

        try {

            $login = $db->prepare('SELECT * FROM user WHERE user_name = ?');
            $login->execute(array(
                $username
            ));
    
            $user = $login->fetch();
        } catch (Exception $e) {
            die($e->getMessage());
        }

        if(!$user) {

            try {

                $login = $db->prepare('SELECT * FROM admin WHERE admin_name = ?');
                $login->execute(array(
                    $username
                ));
        
                $user = $login->fetch();

                connect($password, $user['admin_password'], $user);

            } catch (Exception $e) {
                die($e->getMessage());
            }

            if(empty($user)) {
                //$errorMsg = 'Pseudo non existant !';
                $errorMsg = 'Veuillez remplir tous les champs !';
        
                echo "<p>Redirection automatique dans 3 secondes.</p>";
                header("refresh: 3, URL=signin.php");
            }

        } else {
            connect($password, $user['user_password'], $user);
        }

    } else {
        $errorMsg = 'Champ inexistant ou non rempli !';
    }
?>

<?php include 'footer.php'; ?>
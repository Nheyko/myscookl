<?php include "header.php";

// C H E C K - I F - C O N N E C T E D
if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
    //header('Location: signin.php');
    //exit;
}

// CHECK IF THERE IS SOMETHING IN THE INGREDIENT LIST
if (!isset($_SESSION['ingredientList']) || empty($_SESSION['ingredientList'])) {
    header('Location: selectingredient.php');
    exit;
}

try {

    $now = date('Y-m-d H-i-s');
    // Picture
    $targetDir      = "public/assets/image_recipe/";
    $imageFileType  = strtolower(pathinfo($_FILES['recipe_picture']['name'], PATHINFO_EXTENSION));
    $targetFile = $targetDir . $_SESSION['user']['id_user'] . ' - ' . $now . '.' . $imageFileType;

    $uploadOK = true;

    $check = getimagesize($_FILES['recipe_picture']['tmp_name']);

    if ($check === false) {
        $uploadOK = false;
        $errorMsg = 'Votre fichier n\'est pas de type image';
    }

    if (file_exists($targetFile)) {
        $uploadOK = false;
        $errorMsg = 'Votre fichier existe déjà.';
    }

    if ($_FILES['recipe_picture']['size'] > 500000) {
        $uploadOK = false;
        $errorMsg = 'Votre fichier dépasse la limite des 500ko';
    }

    if ($imageFileType != "jpg" && $imageFileType != "jpeg" && $imageFileType != "png") {

        $uploadOK = false;
        $errorMsg = "Votre fichier doit être au format jpg, jpeg ou png.";
    }

    if ($uploadOK === true) {

        if (move_uploaded_file($_FILES['recipe_picture']['tmp_name'], $targetFile)) {

            $_SESSION['recipe'] = $_POST;
            $_SESSION['FileUpload'] = $targetFile;
            header('Location: selecttag.php');
            exit;
        } else {
            $errorMsg = 'Echec de l\'envoi, merci de réessayer dans un moment.';
        }
    }
} catch (Exception $e) {
    die($e->getMessage());
}

?>

<?php include "footer.php";

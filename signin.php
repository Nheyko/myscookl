<?php include 'header.php'; ?>

<h1>Connexion</h1>

    <form action="signinpost.php" method="post">

        <div class="form-group">
            <label for="pseudo">Pseudo</label>
            <input type="text" class="form-control" name="pseudo" id="pseudo" placeholder="Pseudo">
        </div>

        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" class="form-control" name="password" id="password" placeholder="Mot de passe">
        </div>
        
        <button type="submit" class="btn btn-primary">Se connecter</button>
    </form>

<?php include 'footer.php' ?>
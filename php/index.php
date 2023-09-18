<?php
$configPath = 'config.ini';


$config = parse_ini_file($configPath);


$dbHost = $config['DB_HOST'];
$dbName = $config['DB_NAME'];
$dbUser = $config['DB_USER'];
$dbPass = $config['DB_PASS'];

try {
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}



function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

function insertUser($nom, $prenom, $email, $mdp) {
    global $pdo;

    $hashedPassword = hashPassword($mdp);

    $stmt = $pdo->prepare("INSERT INTO utilisateur (nom, prenom, email, mdp) VALUES (:nom, :prenom, :email, :mdp)");
    $stmt->bindParam(':nom', $nom);
    $stmt->bindParam(':prenom', $prenom);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':mdp', $hashedPassword);

    return $stmt->execute();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prenom = $_POST['prenom'];
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password_repeat = $_POST['password_repeat'];

    if ($password === $password_repeat) {
        if (insertUser($nom, $prenom, $email, $password)) {
            header("Location: login.php");
            exit();
        } else {
            header("Location: login.php?error=insert");
            exit();
        }
    } else {
        header("Location: login.php?error=password");
        exit();
    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire d'Inscription</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">Mon Site</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">            
            <li class="nav-item">
                <a class="nav-link" href="login.php">Se connecter</a>
            </li>
        </ul>
    </div>
</nav>


<div class="container mt-5">
    <h2>Formulaire d'Inscription</h2>
    <form action="index.php" method="POST">
        <div class="form-group">
            <label for="prenom">Prénom</label>
            <input type="text" class="form-control" id="prenom" name="prenom" required>
        </div>

        <div class="form-group">
            <label for="nom">Nom</label>
            <input type="text" class="form-control" id="nom" name="nom" required>
        </div>

        <div class="form-group">
            <label for="email">Adresse e-mail</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>

        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>

        <div class="form-group">
            <label for="password_repeat">Répéter le mot de passe</label>
            <input type="password" class="form-control" id="password_repeat" name="password_repeat" required oninput="checkPasswordMatch()">
        </div>

        <button type="submit" class="btn btn-primary">S'inscrire</button>
    </form>
</div>

<script>
function checkPasswordMatch() {
    var password = document.getElementById("password").value;
    var passwordRepeat = document.getElementById("password_repeat").value;

    if (password !== passwordRepeat) {
        document.getElementById("password_repeat").setCustomValidity("Les mots de passe ne correspondent pas.");
    } else {
        document.getElementById("password_repeat").setCustomValidity("");
    }
}
</script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>

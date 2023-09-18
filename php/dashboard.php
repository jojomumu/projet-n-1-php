<?php
session_start();

if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

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

$query = "SELECT * FROM contact WHERE Id_utilisateur = :id_utilisateur";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':id_utilisateur', $_SESSION['user_id']);
$stmt->execute();
$contact = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];

    $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM contact WHERE email = :email");
    $stmt_check->bindParam(':email', $email);
    $stmt_check->execute();
    $email_exists = $stmt_check->fetchColumn();

    if ($email_exists) {
        $error_message = "This email is already registered.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO contact (nom, prenom, email, Id_utilisateur) VALUES (:nom, :prenom, :email, :id_utilisateur)");
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':id_utilisateur', $_SESSION['user_id']);
        $stmt->execute();

header("Location: dashboard.php");
exit();

    }
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Gestion des Contacts</title>
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="dashboard.php">Mon Tableau de Bord</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">            
            <li class="nav-item">
                <a class="nav-link" href="?logout=true">Se déconnecter</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container mt-5">
    <h2>Tableau de Bord - Gestion des Contacts</h2>
    <p>Bienvenue dans votre tableau de bord de gestion des contacts. Vous pouvez ajouter des contacts ici.</p>
    <div class="row">
        <div class="col-md-6">
            <form action="#" method="POST">
                <div class="form-group">
                    <label for="nom">Nom</label>
                    <input type="text" class="form-control" id="nom" name="nom" required>
                </div>
                <div class="form-group">
                    <label for="prenom">Prénom</label>
                    <input type="text" class="form-control" id="prenom" name="prenom" required>
                </div>
                <div class="form-group">
                    <label for="email">Adresse e-mail</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>

                <?php if (isset($error_message)): ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>


                <button type="submit" class="btn btn-primary">Ajouter Contact</button>
            </form>
        </div>
        <div class="col-md-6">
            <h3>Liste des Contacts</h3>
            <ul class="list-group">
                <?php foreach ($contact as $contact) { ?>
                    <li class="list-group-item">
                        <?php echo $contact['prenom']; ?> <?php echo $contact['nom']; ?> - <?php echo $contact['email']; ?>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


</body>
</html>

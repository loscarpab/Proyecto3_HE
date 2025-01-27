<?php
require_once dirname(__FILE__) . '/private/conf.php';
session_start();

// Si el usuario ya está autenticado, redirigir a la página de jugadores
if (isset($_SESSION['userId'])) {
    header("Location: list_players.php");
    exit();
}

if (isset($_POST['username']) && isset($_POST['password'])) {
    // Validación de datos de entrada
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Validar que el nombre de usuario y la contraseña no estén vacíos
    if (empty($username) || empty($password)) {
        die("Username and password are required.");
    }

    // Hash de la contraseña para almacenarla de forma segura
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);

    // Preparar la consulta SQL usando consultas preparadas
    $stmt = $db->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");

    // Vincular los parámetros
    $stmt->bindValue(':username', $username, SQLITE3_TEXT);
    $stmt->bindValue(':password', $passwordHash, SQLITE3_TEXT);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        // Redirigir al login después de un registro exitoso
        header("Location: login.php");
        exit();
    } else {
        die("Error al registrar el usuario.");
    }
}
?>

<!doctype html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="css/style.css">
        <title>Práctica RA3 - Register</title>
    </head>
    <body>
        <header>
            <h1>Register</h1>
        </header>
        <main class="player">
            <form action="#" method="post">
                <label>Username:</label>
                <input type="text" name="username" required>
                <label>Password:</label>
                <input type="password" name="password" required>
                <input type="submit" value="Register">
            </form>
            <form action="#" method="post" class="menu-form">
                <a href="list_players.php">Back to list</a>
            </form>
        </main>
        <footer class="listado">
            <img src="images/logo-iesra-cadiz-color-blanco.png">
            <h4>Puesta en producción segura</h4>
            <Please <a href="http://www.donate.co?amount=100&amp;destination=ACMEScouting/"> donate</a> >
        </footer>
    </body>
</html>


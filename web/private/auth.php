<?php
require_once dirname(__FILE__) . '/conf.php';

session_start();

// Configura las cookies de sesión de forma segura
ini_set('session.cookie_secure', '1');  // Asegura que la cookie solo se transmita sobre HTTPS
ini_set('session.cookie_httponly', '1');  // Evita el acceso a la cookie desde JavaScript
ini_set('session.use_strict_mode', '1');  // Usa un modo estricto para las sesiones

// Regenera el ID de sesión para prevenir ataques de fijación de sesión
session_regenerate_id(true);

$userId = FALSE;

// Función para validar las credenciales de usuario
function areUserAndPasswordValid($user, $password) {
    global $db, $userId;

    $query = SQLite3::escapeString("SELECT userId, password FROM users WHERE username = '$user'");

    $result = $db->query($query) or die("Invalid query: " . $query . ". Field user introduced is: " . $user);
    $row = $result->fetchArray();

    if ($row) {
        // Verificar la contraseña utilizando password_verify()
        if (password_verify($password, $row['password'])) {
            $userId = $row['userId'];
            $_SESSION['userId'] = $userId;
            $_SESSION['username'] = $user;
            return true;
        }
    }
    return false;
}

// En caso de login
if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (areUserAndPasswordValid($username, $password)) {
        // Redirigir al usuario a la página protegida
        header("Location: list_players.php");
        exit();
    } else {
        $error = "Invalid user or password.<br>";
    }
}

// Logout del sistema
if (isset($_POST['Logout'])) {
    // Eliminar variables de sesión
    session_unset();
    
    // Destruir la sesión
    session_destroy();

    // Eliminar cookies relacionadas con la sesión
    setcookie(session_name(), '', time() - 3600, '/');

    // Redirigir a la página de login
    header("Location: index.php");
    exit();
}

// Verificar si el usuario ya está autenticado
if (isset($_SESSION['userId'])) {
    $login_ok = TRUE;
    $error = "";
} else {
    $login_ok = FALSE;
    $error = "This page requires you to be logged in.<br>";
}

// Si el login no es válido, mostrar el formulario
if ($login_ok == FALSE) {

?>
    <!doctype html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="css/style.css">
        <title>Práctica RA3 - Authentication page</title>
    </head>
    <body>
    <header class="auth">
        <h1>Authentication page</h1>
    </header>
    <section class="auth">
        <div class="message">
            <?= isset($error) ? $error : "" ?>
        </div>
        <section>
            <div>
                <h2>Login</h2>
                <form action="#" method="post">
                    <label>User</label>
                    <input type="text" name="username" required><br>
                    <label>Password</label>
                    <input type="password" name="password" required><br>
                    <input type="submit" value="Login">
                </form>
            </div>

            <div>
                <h2>Logout</h2>
                <form action="#" method="post">
                    <input type="submit" name="Logout" value="Logout">
                </form>
            </div>
        </section>
    </section>
    <footer>
        <h4>Puesta en producción segura</h4>
        <Please <a href="http://www.donate.co?amount=100&amp;destination=ACMEScouting/"> donate</a> >
    </footer>
    </body>
    </html>

<?php
    exit (0);
}
?>


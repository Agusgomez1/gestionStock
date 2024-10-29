<?php
include 'includes/config.php';
include 'includes/functions.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $usuario = validarUsuario($conn, $username, $password);

    if ($usuario) {
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $usuario['role'];
        header("Location: home.php");
    } else {
        $error = "Usuario o contraseña incorrectos";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="css/styles.css">
</head>

<body class="login">
    <div class="formulario">
        <form method="post" action="login.php">
            <h2>Iniciar Sesión</h2>
            <?php if (isset($error)) : ?>
                <p style="color:red;"><?php echo $error; ?></p>
            <?php endif; ?>

            <label for="exampleInputUser" class="form-label">Nombre de usuario</label>
            <input type="text" name="username" class="form-control" id="exampleInputUser" aria-describedby="emailHelp" required>

            <label for="inputPassword5" class="form-label">Contraseña</label>
            <input type="password" name="password" id="inputPassword5" class="form-control" aria-describedby="passwordHelpBlock" required>

            <input type="submit" class="btn btn-primary" value="Iniciar Sesión">

            <p>¿No tienes una cuenta? <a href="register.php">Regístrate aquí</a></p>
        </form>

    </div>
</body>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

</html>
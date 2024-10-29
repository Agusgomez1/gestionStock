<?php
include 'includes/config.php';
include 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    if (!usuarioExiste($conn, $username)) {
        if (registrarUsuario($conn, $username, $password, $email, $role)) {
            echo "Usuario registrado exitosamente.";
            header("Location: login.php");
        } else {
            $error = "Error al registrar usuario";
        }
    } else {
        $error = "El nombre de usuario ya está en uso";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Registro</title>
    <link rel="stylesheet" type="text/css" href="css/style_register.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

</head>

<body>
    <div class="formulario">
    <form method="post" action="register.php">
        <h2>Registro de Usuario</h2>
        <?php if (isset($error)) : ?>
            <p style="color:red;"><?php echo $error; ?></p>
        <?php endif; ?>
        <label for="exampleInputUser" class="form-label">Nombre de usuario</label>
        <input type="text" name="username" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" require>

        <label for="inputPassword5" class="form-label">Contraseña</label>
        <input type="password" name="password" id="inputPassword5" class="form-control" aria-describedby="passwordHelpBlock" require>

        <label for="inputEmail" class="form-label">Email</label>
        <input type="email" name="email" id="inputEmail" class="form-control" aria-describedby="passwordHelpBlock" require>

        <label class="form-label">Rol</label>

        <select class="form-select" name="role" aria-label="Default select example">
            <option value="admin">Admin</option>
            <option value="user">Usuario</option>
        </select>

        <input type="submit" class="btn btn-primary" value="Registrar">
        
    <p>¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a></p>
    </form>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

</html>
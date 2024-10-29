<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$nombre = $email = $mensaje = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = htmlspecialchars(trim($_POST['nombre']));
    $email = htmlspecialchars(trim($_POST['email']));
    $mensaje = htmlspecialchars(trim($_POST['mensaje']));
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Contacto</title>
    <link rel="stylesheet" type="text/css" href="css/contact.css">
</head>

<body>
    <?php include 'templates/header.php'; ?>
    
    <div class="contact-container">
        <h1>Contacto</h1>
        
        <form method="post" action="contact.php" class="contact-form">
            <div class="form-group">
                <label>Nombre:</label>
                <input type="text" name="nombre" value="<?php echo $nombre; ?>" required><br>
            </div>
            
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" value="<?php echo $email; ?>" required><br>
            </div>
            
            <div class="form-group">
                <label>Mensaje:</label>
                <textarea name="mensaje" required><?php echo $mensaje; ?></textarea><br>
            </div>
            
            <input type="submit" value="Enviar">
        </form>
        
        <?php if ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
            <div class="response">
                <h3>Consulta enviada:</h3>
                <p><strong>Nombre:</strong> <?php echo $nombre; ?></p>
                <p><strong>Email:</strong> <?php echo $email; ?></p>
                <p><strong>Mensaje:</strong> <?php echo nl2br($mensaje); ?></p>
            </div>
        <?php endif; ?>
    </div>
    <?php include 'templates/footer.php'; ?>

</body>

</html>

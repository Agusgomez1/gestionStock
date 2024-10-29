<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
    <link rel="stylesheet" type="text/css" href="css/home.css">
</head>
<body>
    <?php include 'templates/header.php'; ?>
    
    <div class="home-container">
        <h1>Bienvenido, <?php echo $_SESSION['username']; ?></h1>
        <div class="card-container">
            <div class="card">
                <a href="products.php">
                    <img src="src/administrar_productos.webp" alt="Administrar Productos">
                    <h2>Administrar Productos</h2>
                </a>
            </div>
            <div class="card">
                <a href="contact.php">
                    <img src="src/contact.webp" alt="Contacto">
                    <h2>Contacto</h2>
                </a>
            </div>
            <div class="card">
                <a href="stock.php">
                    <img src="src/stock.webp" alt="Stock">
                    <h2>Stock</h2>
                </a>
            </div>
        </div>
    </div>
    <?php include 'templates/footer.php'; ?>
</body>

</html>



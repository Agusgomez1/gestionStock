<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Conectar a la base de datos
include 'includes/config.php';

$search = "";
if (isset($_GET['search'])) {
    $search = htmlspecialchars(trim($_GET['search']));
}

// Obtener la lista de productos
$sql = "SELECT * FROM products";
if ($search) {
    $sql .= " WHERE name LIKE '%$search%'";
}
$result = $conn->query($sql);
$products = $result->fetch_all(MYSQLI_ASSOC);

if (isset($_GET['ajax'])) {
    // Devolver resultados como JSON para AJAX
    echo json_encode($products);
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Stock de Productos</title>
    <link rel="stylesheet" type="text/css" href="css/stock.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#search').on('input', function() {
                var search = $(this).val();
                $.ajax({
                    url: 'stock.php',
                    type: 'GET',
                    data: { search: search, ajax: true },
                    success: function(data) {
                        var products = JSON.parse(data);
                        var output = '';
                        products.forEach(function(product) {
                            output += '<tr>';
                            output += '<td>' + product.id + '</td>';
                            output += '<td>' + product.name + '</td>';
                            output += '<td>' + product.type + '</td>';
                            output += '<td>' + product.stock + '</td>';
                            output += '<td>$' + parseFloat(product.price).toFixed(2) + '</td>';
                            if (product.image) {
                                output += '<td><img src="uploads/' + product.image + '" alt="' + product.name + '" width="50"></td>';
                            } else {
                                output += '<td></td>';
                            }
                            output += '</tr>';
                        });
                        $('#product-table tbody').html(output);
                    }
                });
            });
        });
    </script>
</head>

<body>
    <?php include 'templates/header.php'; ?>
    
    <div class="stock-container">
        <h1>Stock de Productos</h1>
        
        <div class="search-form">
            <input type="text" id="search" name="search" placeholder="Buscar productos" value="<?php echo $search; ?>">
        </div>

        <table id="product-table" class="product-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>Stock</th>
                    <th>Precio</th>
                    <th>Imagen</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product) : ?>
                    <tr>
                        <td><?php echo $product['id']; ?></td>
                        <td><?php echo $product['name']; ?></td>
                        <td><?php echo $product['type']; ?></td>
                        <td><?php echo $product['stock']; ?></td>
                        <td>$<?php echo number_format($product['price'], 2); ?></td>
                        <td>
                            <?php if ($product['image']) : ?>
                                <img src="uploads/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" width="50">
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php include 'templates/footer.php'; ?>
</body>

</html>

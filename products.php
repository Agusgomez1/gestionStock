<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'includes/config.php';

$username = $_SESSION['username'];
$role = $_SESSION['role'];

$search = "";
if (isset($_GET['search'])) {
    $search = htmlspecialchars(trim($_GET['search']));
}

// Manejo de acciones del formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_stock'])) {
        // Lógica para actualizar el stock
        $product_id = (int)$_POST['product_id'];
        $new_stock = (int)$_POST['stock'];

        if ($new_stock >= 0) {
            $stmt = $conn->prepare("UPDATE products SET stock = ? WHERE id = ?");
            $stmt->bind_param("ii", $new_stock, $product_id);
            $stmt->execute();
        } else {
            echo "El stock no puede ser menor a 0.";
        }
    } elseif (isset($_POST['add_product']) && $role == 'admin') {
        // Lógica para agregar un nuevo producto (solo admin)
        $product_name = mysqli_real_escape_string($conn, htmlspecialchars(trim($_POST['product_name'])));
        $product_type = mysqli_real_escape_string($conn, htmlspecialchars(trim($_POST['product_type'])));
        $product_stock = (int)$_POST['product_stock'];
        $product_price = (float)$_POST['product_price'];

        // Manejo de la subida de archivos
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
            $allowed_ext = array("jpg", "jpeg", "png", "gif");
            $file_name = $_FILES['product_image']['name'];
            $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);

            if (in_array($file_ext, $allowed_ext)) {
                $file_tmp = $_FILES['product_image']['tmp_name'];
                $file_new_name = uniqid() . "." . $file_ext;
                $file_dest = "uploads/" . $file_new_name;

                if (move_uploaded_file($file_tmp, $file_dest)) {
                    $stmt = $conn->prepare("INSERT INTO products (name, type, stock, price, image) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssids", $product_name, $product_type, $product_stock, $product_price, $file_new_name);

                    try {
                        $stmt->execute();
                    } catch (mysqli_sql_exception $e) {
                        if ($e->getCode() == 1062) {
                            echo "El nombre del producto ya existe. Por favor, elija otro nombre.";
                        } else {
                            echo "Error al agregar el producto: " . $e->getMessage();
                        }
                    }
                } else {
                    echo "Error al subir la imagen.";
                }
            } else {
                echo "Formato de imagen no permitido.";
            }
        } else {
            echo "Error en la carga de la imagen.";
        }
    } elseif (isset($_POST['delete_product']) && $role == 'admin') {
        // Lógica para eliminar un producto (solo admin)
        $product_id = (int)$_POST['product_id'];

        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
    }
}

// Paginación y Filtro
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 5;
$offset = ($page - 1) * $perPage;

$total = $conn->query("SELECT COUNT(*) as total FROM products")->fetch_assoc()['total'];
$totalPages = ceil($total / $perPage);

$sql = "SELECT * FROM products LIMIT $offset, $perPage";
if ($search) {
    $sql = "SELECT * FROM products WHERE name LIKE '%$search%' LIMIT $offset, $perPage";
}
$result = $conn->query($sql);
$products = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Administrar Productos</title>
    <link rel="stylesheet" type="text/css" href="css/products.css">
</head>

<body>
    <?php include 'templates/header.php'; ?>

    <div class="products-container">
        <h1>Administrar Productos</h1>

        <form method="get" action="products.php" class="search-form">
            <input type="text" name="search" placeholder="Buscar productos" value="<?php echo $search; ?>">
            <button type="submit">Buscar</button>
        </form>

        <!-- Lista de productos -->
        <table class="product-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>Stock</th>
                    <th>Precio</th>
                    <th>Imagen</th>
                    <?php if ($role == 'admin') : ?>
                        <th>Acciones</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product) : ?>
                    <tr>
                        <td><?php echo $product['id']; ?></td>
                        <td><?php echo $product['name']; ?></td>
                        <td><?php echo $product['type']; ?></td>
                        <td>
                            <form method="post" action="products.php">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <input type="number" name="stock" value="<?php echo $product['stock']; ?>" min="0">
                                <input type="submit" name="update_stock" value="Actualizar Stock">
                            </form>
                        </td>
                        <td>$<?php echo number_format($product['price'], 2); ?></td>
                        <td>
                            <?php if ($product['image']) : ?>
                                <img src="uploads/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" width="50">
                            <?php endif; ?>
                        </td>
                        <?php if ($role == 'admin') : ?>
                            <td>
                                <form method="post" action="products.php">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <input type="submit" name="delete_product" value="Eliminar">
                                </form>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
        </div>

        <?php if ($role == 'admin') : ?>
            <!-- Formulario para agregar nuevo producto -->
            <h2>Agregar Nuevo Producto</h2>
            <form method="post" action="products.php" enctype="multipart/form-data" class="add-product-form">
                <div class="form-group">
                    <label>Nombre del Producto:</label>
                    <input type="text" name="product_name" required><br>
                </div>

                <div class="form-group">
                    <label>Tipo de Producto:</label>
                    <input type="text" name="product_type" required><br>
                </div>

                <div class="form-group">
                    <label>Stock Inicial:</label>
                    <input type="number" name="product_stock" required min="0"><br>
                </div>

                <div class="form-group">
                    <label>Precio Unitario:</label>
                    <input type="number" step="0.01" name="product_price" required min="0"><br>
                </div>

                <div class="form-group">
                    <label>Imagen del Producto:</label>
                    <input type="file" name="product_image" accept="image/*" required><br>
                </div>

                <input type="submit" name="add_product" value="Agregar Producto">
            </form>
        <?php endif; ?>
    </div>
    <?php include 'templates/footer.php'; ?>

</body>

</html>
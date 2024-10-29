<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Conectar a la base de datos
include 'includes/config.php';

$username = $_SESSION['username'];

// Obtener los datos del usuario
$stmt = $conn->prepare("SELECT id, username, email, name, profile_image FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Manejo de acciones del formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_name = htmlspecialchars(trim($_POST['name']));
    $new_email = htmlspecialchars(trim($_POST['email']));
    $user_id = $user['id'];
    $profile_image = $user['profile_image'];

    // Manejo de la subida de archivos
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $allowed_ext = array("jpg", "jpeg", "png", "gif");
        $file_name = $_FILES['profile_image']['name'];
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);

        if (in_array($file_ext, $allowed_ext)) {
            $file_tmp = $_FILES['profile_image']['tmp_name'];
            $file_new_name = uniqid() . "." . $file_ext;
            $file_dest = "uploads/" . $file_new_name;
            
            if (move_uploaded_file($file_tmp, $file_dest)) {
                $profile_image = $file_new_name;
            } else {
                echo "Error al subir la imagen.";
            }
        } else {
            echo "Formato de imagen no permitido.";
        }
    }

    // Actualizar los datos del usuario
    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, profile_image = ? WHERE id = ?");
    $stmt->bind_param("sssi", $new_name, $new_email, $profile_image, $user_id);

    if ($stmt->execute()) {
        echo "Datos actualizados correctamente.";
        // Actualizar los datos en la sesión
        $user['name'] = $new_name;
        $user['email'] = $new_email;
        $user['profile_image'] = $profile_image;
    } else {
        echo "Error al actualizar los datos.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Perfil de Usuario</title>
    <link rel="stylesheet" type="text/css" href="css/perfil.css">
</head>
<body>
    <?php include 'templates/header.php'; ?>
    
    <div class="profile-container">
        <h1>Perfil de Usuario</h1>
        <form method="post" action="perfil.php" enctype="multipart/form-data" class="profile-form">
            <div class="form-group">
                <label>Nombre de Usuario:</label>
                <input type="text" name="username" value="<?php echo $user['username']; ?>" disabled><br>
            </div>

            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" value="<?php echo $user['email']; ?>" required><br>
            </div>

            <div class="form-group">
                <label>Nombre:</label>
                <input type="text" name="name" value="<?php echo $user['name']; ?>" required><br>
            </div>

            <div class="form-group">
                <label>Foto de Perfil:</label>
                <input type="file" name="profile_image" accept="image/*"><br>
                <?php if ($user['profile_image']) : ?>
                    <img src="uploads/<?php echo $user['profile_image']; ?>" alt="Foto de Perfil" width="100">
                <?php endif; ?>
            </div>

            <input type="submit" value="Actualizar Datos">
        </form>
    </div>
    <?php include 'templates/footer.php'; ?>

</body>
</html>

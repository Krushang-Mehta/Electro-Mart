<?php
// Include the header
include 'header.php';

// Database connection
$host = 'localhost';
$dbname = 'electro-mart';  // Your database name
$username = 'root'; // Default username for XAMPP MySQL
$password = '';     // Default password is empty

$message = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if product form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $itemBrand = $_POST['item_brand'];
        $itemName = $_POST['item_name'];
        $itemPrice = $_POST['item_price'];
        $itemRegister = date('Y-m-d H:i:s');

        // Handle file upload
        if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] == UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['item_image']['tmp_name'];
            $fileName = $_FILES['item_image']['name'];
            $fileSize = $_FILES['item_image']['size'];
            $fileType = $_FILES['item_image']['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            // Generate a unique name for the file
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $uploadFileDir = './uploads/';
            $dest_path = $uploadFileDir . $newFileName;

            // Create the uploads directory if it doesn't exist
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $itemImage = $dest_path;
            } else {
                $message = "Error uploading the image.";
            }
        } else {
            $itemImage = ''; // Handle case when no image is uploaded
        }

        // Prepare SQL statement
        $stmt = $conn->prepare("INSERT INTO product (item_brand, item_name, item_price, item_image, item_register) VALUES (:item_brand, :item_name, :item_price, :item_image, :item_register)");
        $stmt->bindParam(':item_brand', $itemBrand);
        $stmt->bindParam(':item_name', $itemName);
        $stmt->bindParam(':item_price', $itemPrice);
        $stmt->bindParam(':item_image', $itemImage);
        $stmt->bindParam(':item_register', $itemRegister);
        $stmt->execute();

        $message = "Product added successfully!";
    }
} catch(PDOException $e) {
    $message = "Connection failed: " . $e->getMessage();
}
?>

<!-- start #main-site -->
<main id="main-site">
    <div class="container mt-5">
        <form action="" method="POST" enctype="multipart/form-data">
            <h2>Add Product</h2>
            <?php if (!empty($message)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            <div class="form-group">
                <label for="item_brand">Brand:</label>
                <input type="text" id="item_brand" name="item_brand" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="item_name">Name:</label>
                <input type="text" id="item_name" name="item_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="item_price">Price:</label>
                <input type="text" id="item_price" name="item_price" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="item_image">Image:</label>
                <input type="file" id="item_image" name="item_image" class="form-control-file" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Product</button><br>
        </form>
    </div>
</main>
<!-- !start #main-site -->

<!-- Include footer -->
<?php include 'footer.php'; ?>

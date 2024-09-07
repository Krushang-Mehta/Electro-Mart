<?php
// Include the header
include 'header.php';

// Database connection and form handling logic
$host = 'localhost';
$dbname = 'electro-mart';  // Your database name
$username = 'root'; // Default username for XAMPP MySQL
$password = '';     // Default password is empty

$message = '';
$formDisabled = false;
$isVendor = false;  // Flag to check if the user is a vendor

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $formUsername = $_POST['username'];
        $formPassword = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hashing the password for security
        $formRole = $_POST['role'];

        // Prepare SQL statement
        $stmt = $conn->prepare("INSERT INTO user (user_name, pasword, role) VALUES (:username, :password, :role)");
        $stmt->bindParam(':username', $formUsername);
        $stmt->bindParam(':password', $formPassword);
        $stmt->bindParam(':role', $formRole);
        $stmt->execute();

        $message = "Registration successful!";
        $formDisabled = true; // Disable the form after successful registration

        // Check if the user is a vendor
        if ($formRole === 'vendor') {
            $isVendor = true;
        }
    }
} catch(PDOException $e) {
    $message = "Connection failed: " . $e->getMessage();
}
?>

<!-- start #main-site -->
<main id="main-site">
    <div class="container mt-5">
        <?php if ($formDisabled): ?>
            <!-- Display success message and disable form -->
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
            <?php if ($isVendor): ?>
                <a href="add_product.php" class="btn btn-primary">Add Product</a>
            <?php endif; ?>
        <?php else: ?>
            <!-- Registration form -->
            <form action="" method="POST">
                <h2>Register</h2>
                <?php if (!empty($message)): ?>
                    <div class="message"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="role">Role:</label>
                    <select id="role" name="role" class="form-control">
                        <option value="vendor">Vendor</option>
                        <option value="customer">Customer</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Register</button>
            </form>
            <br>
            <br>
        <?php endif; ?>
    </div>
</main>
<!-- !start #main-site -->

<!-- Include footer if registration is not successful -->
<?php if (!$formDisabled): ?>
    <?php include 'footer.php'; ?>
<?php endif; ?>

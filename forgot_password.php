<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = [];
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $token = bin2hex(random_bytes(32));
        $token_expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        try {
            $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE email = ?");
            $stmt->execute([$token, $token_expiry, $email]);
            
            if ($stmt->rowCount() > 0) {
                // Send reset email (implement your email sending logic here)
                $reset_link = "http://yourwebsite.com/reset_password.php?token=" . $token;
                
                // For demonstration, we'll show the link
                $success = "Reset link has been sent to your email: " . $reset_link;
            } else {
                $errors[] = "Email not found in our records";
            }
        } catch (PDOException $e) {
            $errors[] = "An error occurred. Please try again.";
        }
    } else {
        $errors[] = "Invalid email format";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-md w-96">
            <h2 class="text-2xl font-bold mb-6 text-center">Reset Password</h2>
            
            <?php if (!empty($errors)): ?>
                <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
                    <?php foreach($errors as $error): ?>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($success)): ?>
                <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email Address</label>
                    <input type="email" id="email" name="email" required 
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500"
                           value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
                </div>
                <button type="submit" 
                        class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 focus:outline-none">
                    Send Reset Link
                </button>
            </form>
            <p class="mt-4 text-center">
                <a href="login.php" class="text-blue-500">Back to Login</a>
            </p>
        </div>
    </div>
</body>
</html>
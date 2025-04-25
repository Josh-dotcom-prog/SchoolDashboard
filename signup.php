<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = [];

    // Sanitize and validate inputs
    $fullname = trim(htmlspecialchars($_POST['fullname'] ?? '', ENT_QUOTES, 'UTF-8'));
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        $errors[] = "Email already registered";
    }

    // Validate password strength
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    }

    if (empty($errors)) {
        try {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$fullname, $email, $password_hash]);

            header("Location: login.php?signup=success");
            exit();
        } catch (PDOException $e) {
            $errors[] = "Registration failed. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-md w-96">
            <h2 class="text-2xl font-bold mb-6 text-center">Sign Up</h2>

            <!-- Error Messages Display -->
            <?php if (!empty($errors)): ?>
                <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="signup.php" method="POST" id="signupForm">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="fullname">Full Name</label>
                    <input type="text" id="fullname" name="fullname" required
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500"
                        value="<?php echo isset($fullname) ? htmlspecialchars($fullname) : ''; ?>">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email</label>
                    <input type="email" id="email" name="email" required
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500"
                        value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="password">Password</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required
                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                        <button type="button" onclick="togglePassword()"
                            class="absolute right-3 top-2 text-gray-600">
                            Show
                        </button>
                    </div>
                    <p class="text-sm text-gray-600 mt-1">Must be at least 8 characters long</p>
                </div>
                <button type="submit" id="submitBtn"
                    class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 focus:outline-none">
                    Sign Up
                </button>
            </form>
            <p class="mt-4 text-center">Already have an account? <a href="login.php" class="text-blue-500">Login</a></p>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleBtn = document.querySelector('button[onclick="togglePassword()"]');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleBtn.textContent = 'Hide';
            } else {
                passwordInput.type = 'password';
                toggleBtn.textContent = 'Show';
            }
        }
    </script>
</body>

</html>
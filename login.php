<?php
session_start();
require 'db.php';

$error = "";
$success = "";

if (isset($_GET['signup']) && $_GET['signup'] == 'success') {
    $success = "Welcome! Your account is ready.";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['name'] = $user['name']; // Store name in session
        header("Location: index.php");
        exit();
    } else {
        $error = "Oops! Something went wrong.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - Expensio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Quicksand', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-rose-100 via-pink-100 to-purple-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white/80 backdrop-blur-md p-10 rounded-[2.5rem] shadow-xl border border-white w-full max-w-md">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-rose-50 text-rose-400 rounded-full mb-4 shadow-inner">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-gray-700">Hello Again!</h2>
            <p class="text-gray-400 mt-2">Sign in to your account</p>
        </div>

        <?php if($error): ?>
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-100 text-rose-500 text-sm text-center">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if($success): ?>
            <div class="mb-6 p-4 rounded-2xl bg-green-50 border border-green-100 text-green-600 text-sm text-center">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <div>
                <label class="block text-sm font-semibold text-gray-500 mb-1 ml-1">Username</label>
                <input type="text" name="username" required placeholder="Your username"
                    class="w-full px-5 py-4 bg-gray-50/50 border border-rose-100 rounded-2xl focus:ring-4 focus:ring-rose-200 focus:border-rose-300 outline-none transition-all placeholder-gray-300">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-500 mb-1 ml-1">Password</label>
                <input type="password" name="password" required placeholder="••••••••"
                    class="w-full px-5 py-4 bg-gray-50/50 border border-rose-100 rounded-2xl focus:ring-4 focus:ring-rose-200 focus:border-rose-300 outline-none transition-all placeholder-gray-300">
            </div>
            
            <button type="submit" class="w-full bg-gradient-to-r from-rose-400 to-pink-500 hover:from-rose-500 hover:to-pink-600 text-white font-bold py-4 rounded-2xl shadow-lg shadow-rose-200 transition-all active:scale-95">
                Sign In
            </button>
        </form>

        <div class="mt-8 text-center">
            <p class="text-gray-400">New here? 
                <a href="register.php" class="text-rose-400 font-bold hover:text-rose-500 ml-1">Create Account</a>
            </p>
        </div>
    </div>
</body>
</html>
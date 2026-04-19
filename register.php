<?php
require 'db.php';

$message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check_user = mysqli_query($conn, "SELECT id FROM users WHERE username = '$username'");
    
    if (mysqli_num_rows($check_user) > 0) {
        $message = "Username already taken. Please choose another.";
    } else {
        $sql = "INSERT INTO users (name, email, username, password) VALUES ('$name', '$email', '$username', '$password')";
        if (mysqli_query($conn, $sql)) {
            header("Location: login.php?signup=success");
            exit();
        } else {
            $message = "Error: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - Expensio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Quicksand', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-rose-100 via-pink-100 to-purple-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white/80 backdrop-blur-md p-10 rounded-[2.5rem] shadow-xl border border-white w-full max-w-md my-10">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-rose-50 text-rose-400 rounded-full mb-4 shadow-inner">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
            </div>
            <h2 class="text-3xl font-bold bg-gradient-to-r from-rose-400 to-pink-500 bg-clip-text text-transparent">Join Expensio</h2>
            <p class="text-gray-400 mt-2">Start your lovely financial journey</p>
        </div>

        <?php if($message): ?>
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-100 text-rose-500 text-sm text-center font-bold">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-semibold text-gray-500 mb-1 ml-1">Full Name</label>
                <input type="text" name="name" required placeholder="Your lovely name"
                    class="w-full px-5 py-3 bg-white/50 border border-rose-100 rounded-2xl focus:ring-4 focus:ring-rose-200 focus:border-rose-300 outline-none transition-all placeholder-gray-300 font-medium">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-500 mb-1 ml-1">Email</label>
                <input type="email" name="email" required placeholder="hello@example.com"
                    class="w-full px-5 py-3 bg-white/50 border border-rose-100 rounded-2xl focus:ring-4 focus:ring-rose-200 focus:border-rose-300 outline-none transition-all placeholder-gray-300 font-medium">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-500 mb-1 ml-1">Username</label>
                <input type="text" name="username" required placeholder="Choose a username"
                    class="w-full px-5 py-3 bg-white/50 border border-rose-100 rounded-2xl focus:ring-4 focus:ring-rose-200 focus:border-rose-300 outline-none transition-all placeholder-gray-300 font-medium">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-500 mb-1 ml-1">Password</label>
                <input type="password" name="password" required placeholder="••••••••"
                    class="w-full px-5 py-3 bg-white/50 border border-rose-100 rounded-2xl focus:ring-4 focus:ring-rose-200 focus:border-rose-300 outline-none transition-all placeholder-gray-300 font-medium">
            </div>
            
            <button type="submit" class="w-full bg-gradient-to-r from-rose-400 to-pink-500 hover:from-rose-500 hover:to-pink-600 text-white font-bold py-4 rounded-2xl shadow-lg shadow-rose-200 transition-all active:scale-95 mt-4">
                Sign Up
            </button>
        </form>

        <div class="mt-8 text-center">
            <p class="text-gray-400">Already with us? 
                <a href="login.php" class="text-rose-400 font-bold hover:text-rose-500 ml-1">Sign In</a>
            </p>
        </div>
    </div>
</body>
</html>
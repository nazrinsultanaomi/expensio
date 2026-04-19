<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch latest user info
$user_res = mysqli_query($conn, "SELECT name, budget FROM users WHERE id = $user_id");
$user_data = mysqli_fetch_assoc($user_res);
$display_name = $user_data['name'] ?? $_SESSION['username'];
$budget_limit = $user_data['budget'] ?? 0;

// Handle Update Budget
if (isset($_POST['set_budget'])) {
    $new_budget = $_POST['budget_amount'];
    mysqli_query($conn, "UPDATE users SET budget = '$new_budget' WHERE id = $user_id");
    header("Location: index.php");
}

// Handle Add Transaction
if (isset($_POST['add_transaction'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $amount = $_POST['amount'];
    $type = $_POST['type'];
    $category = $_POST['category'];
    $date = $_POST['date'];

    $sql = "INSERT INTO transactions (user_id, title, amount, type, category, date) VALUES ('$user_id', '$title', '$amount', '$type', '$category', '$date')";
    mysqli_query($conn, $sql);
    header("Location: index.php");
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM transactions WHERE id = $id AND user_id = $user_id");
    header("Location: index.php");
}

// Calculations
$res_income = mysqli_query($conn, "SELECT SUM(amount) as total FROM transactions WHERE user_id = $user_id AND type = 'income'");
$total_income = mysqli_fetch_assoc($res_income)['total'] ?? 0;

$res_expense = mysqli_query($conn, "SELECT SUM(amount) as total FROM transactions WHERE user_id = $user_id AND type = 'expense'");
$total_expense = mysqli_fetch_assoc($res_expense)['total'] ?? 0;

$balance = $total_income - $total_expense;

// Fetch Transactions
$transactions = mysqli_query($conn, "SELECT * FROM transactions WHERE user_id = $user_id ORDER BY date DESC");

// Chart Data (Vibrant category differentiation)
$chart_query = mysqli_query($conn, "SELECT category, SUM(amount) as total FROM transactions WHERE user_id = $user_id AND type = 'expense' GROUP BY category");
$categories = [];
$amounts = [];
while($row = mysqli_fetch_assoc($chart_query)) {
    $categories[] = $row['category'];
    $amounts[] = $row['total'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expensio Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Quicksand', sans-serif; }
        .blossom-gradient { background: linear-gradient(135deg, #ffafbd 0%, #ffc3a0 100%); }
    </style>
</head>
<body class="bg-rose-50/50 min-h-screen">
    <nav class="bg-white/80 backdrop-blur-md shadow-sm p-4 sticky top-0 z-50 border-b border-rose-100">
        <div class="max-w-6xl mx-auto flex justify-between items-center px-4">
            <h1 class="text-2xl font-bold bg-gradient-to-r from-rose-400 to-pink-500 bg-clip-text text-transparent">Expensio</h1>
            <div class="flex items-center gap-4">
                <div class="text-right hidden sm:block">
                    <p class="text-[10px] text-gray-400 uppercase font-bold tracking-tighter">Your Account</p>
                    <span class="text-gray-600 font-bold text-sm">Hi, <?php echo htmlspecialchars($display_name); ?> ✨</span>
                </div>
                <a href="logout.php" class="bg-rose-100 text-rose-500 px-5 py-2 rounded-full text-xs font-bold hover:bg-rose-200 transition-all shadow-sm">Logout</a>
            </div>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto p-6">
        <!-- Summary Section -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-7 rounded-[2rem] shadow-sm border border-rose-50">
                <p class="text-gray-400 text-[10px] font-bold uppercase tracking-[0.2em] mb-2">Total Income</p>
                <p class="text-3xl font-bold text-emerald-400">৳<?php echo number_format($total_income, 2); ?></p>
            </div>
            <div class="bg-white p-7 rounded-[2rem] shadow-sm border border-rose-50 relative overflow-hidden">
                <p class="text-gray-400 text-[10px] font-bold uppercase tracking-[0.2em] mb-2">Total Expenses</p>
                <p class="text-3xl font-bold text-rose-400">৳<?php echo number_format($total_expense, 2); ?></p>
                <?php if($budget_limit > 0 && $total_expense > $budget_limit): ?>
                    <div class="mt-2 flex items-center gap-1 text-[10px] text-rose-600 font-black animate-bounce">
                        <span>⚠️ OVER BUDGET (LIMIT: ৳<?php echo number_format($budget_limit); ?>)</span>
                    </div>
                <?php endif; ?>
            </div>
            <div class="bg-white p-7 rounded-[2rem] shadow-sm border border-rose-50">
                <p class="text-gray-400 text-[10px] font-bold uppercase tracking-[0.2em] mb-2">Current Balance</p>
                <p class="text-3xl font-bold text-indigo-400">৳<?php echo number_format($balance, 2); ?></p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="space-y-6">
                <!-- Budget Setting Form -->
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-rose-50">
                    <h3 class="text-lg font-bold text-gray-700 mb-4 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Daily Limit
                    </h3>
                    <form method="POST">
                        <input type="hidden" name="set_budget" value="1">
                        <div class="mb-4">
                            <input type="number" step="0.01" name="budget_amount" value="<?php echo $budget_limit; ?>" required 
                                class="w-full bg-rose-50/50 border-none rounded-2xl p-4 text-sm focus:ring-2 focus:ring-rose-200 outline-none font-bold text-gray-600">
                        </div>
                        <button type="submit" class="w-full bg-rose-400 text-white p-4 rounded-2xl hover:bg-rose-500 text-sm font-bold shadow-lg shadow-rose-100 transition-all active:scale-95">Update Budget</button>
                    </form>
                </div>

                <!-- Transaction Entry Form -->
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-rose-50">
                    <h3 class="text-lg font-bold text-gray-700 mb-4 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        New Entry
                    </h3>
                    <form method="POST" class="space-y-4">
                        <input type="hidden" name="add_transaction" value="1">
                        <input type="text" name="title" placeholder="What for?" required class="w-full bg-rose-50/50 border-none rounded-2xl p-4 text-sm outline-none focus:ring-2 focus:ring-rose-200">
                        <input type="number" step="0.01" name="amount" placeholder="Amount (৳)" required class="w-full bg-rose-50/50 border-none rounded-2xl p-4 text-sm outline-none focus:ring-2 focus:ring-rose-200">
                        
                        <div class="grid grid-cols-2 gap-3">
                            <select name="type" class="bg-rose-50/50 border-none rounded-2xl p-4 text-xs font-bold outline-none cursor-pointer">
                                <option value="income">Income 💰</option>
                                <option value="expense" selected>Expense 💸</option>
                            </select>
                            <select name="category" class="bg-rose-50/50 border-none rounded-2xl p-4 text-xs font-bold outline-none cursor-pointer">
                                <option value="Food">Food 🍕</option>
                                <option value="Shopping">Shopping 🛍️</option>
                                <option value="Study">Study 📚</option>
                                <option value="Travel">Travel ✈️</option>
                                <option value="Gift">Gift 🎁</option>
                                <option value="Salary">Salary 💵</option>
                                <option value="Beauty">Beauty 💄</option>
                                <option value="Other">Other ✨</option>
                            </select>
                        </div>
                        
                        <input type="date" name="date" required value="<?php echo date('Y-m-d'); ?>" class="w-full bg-rose-50/50 border-none rounded-2xl p-4 text-sm outline-none focus:ring-2 focus:ring-rose-200">
                        <button type="submit" class="w-full bg-gradient-to-r from-rose-400 to-pink-500 text-white p-4 rounded-2xl hover:opacity-90 font-bold transition shadow-lg shadow-rose-100 active:scale-95">Save Transaction</button>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-6">
                <!-- Differentiable Chart -->
                <?php if(!empty($categories)): ?>
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-rose-50">
                    <h3 class="text-lg font-bold text-gray-700 mb-6 text-center">Spending Categories</h3>
                    <div class="h-64 flex justify-center">
                        <canvas id="expenseChart"></canvas>
                    </div>
                </div>
                <?php endif; ?>

                <!-- History Table -->
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-rose-50 overflow-hidden">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-rose-50/30">
                                <th class="p-6 text-[10px] font-black text-rose-300 uppercase tracking-widest">Detail</th>
                                <th class="p-6 text-[10px] font-black text-rose-300 uppercase tracking-widest">Tag</th>
                                <th class="p-6 text-[10px] font-black text-rose-300 uppercase tracking-widest">Amount</th>
                                <th class="p-6 text-[10px] font-black text-rose-300 uppercase tracking-widest text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-rose-50">
                            <?php while($row = mysqli_fetch_assoc($transactions)): ?>
                            <tr class="hover:bg-rose-50/20 transition-all">
                                <td class="p-6">
                                    <div class="font-bold text-gray-700 text-sm"><?php echo htmlspecialchars($row['title']); ?></div>
                                    <div class="text-[9px] text-gray-300 font-bold uppercase mt-1"><?php echo date('M d, Y', strtotime($row['date'])); ?></div>
                                </td>
                                <td class="p-6">
                                    <span class="text-[10px] px-3 py-1 bg-white border border-rose-50 rounded-full text-gray-400 font-bold">
                                        <?php echo $row['category']; ?>
                                    </span>
                                </td>
                                <td class="p-6 font-bold text-sm <?php echo $row['type'] == 'income' ? 'text-emerald-400' : 'text-rose-400'; ?>">
                                    <?php echo $row['type'] == 'income' ? '৳' : '-৳'; ?><?php echo number_format($row['amount'], 2); ?>
                                </td>
                                <td class="p-6 text-right">
                                    <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Remove this transaction?')" 
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-rose-50 text-rose-200 hover:bg-rose-400 hover:text-white transition-all">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script>
        const ctx = document.getElementById('expenseChart');
        if(ctx) {
            // Updated "Flower Garden" palette: High contrast, distinct colors
            const flowerPalette = [
                '#FF4D6D', // Deep Rose
                '#A06CD5', // Vibrant Lavender
                '#00B4D8', // Sky Blue
                '#2DCC70', // Fresh Emerald
                '#FFD166', // Sunny Yellow
                '#FF9F1C', // Juicy Orange
                '#9B59B6', // Amethyst
                '#16A085'  // Teal
            ];

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: <?php echo json_encode($categories); ?>,
                    datasets: [{
                        data: <?php echo json_encode($amounts); ?>,
                        backgroundColor: flowerPalette,
                        hoverBackgroundColor: flowerPalette,
                        borderWidth: 5,
                        borderColor: '#ffffff',
                        hoverOffset: 15
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: { 
                            position: 'bottom',
                            labels: { 
                                usePointStyle: true, 
                                padding: 25, 
                                font: { family: 'Quicksand', size: 11, weight: 'bold' },
                                color: '#9ca3af'
                            } 
                        },
                        tooltip: {
                            backgroundColor: '#fff',
                            titleColor: '#374151',
                            bodyColor: '#374151',
                            borderColor: '#fef2f2',
                            borderWidth: 1,
                            padding: 12,
                            displayColors: true,
                            callbacks: {
                                label: function(context) {
                                    return ' ৳' + context.raw.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }
    </script>
</body>
</html>
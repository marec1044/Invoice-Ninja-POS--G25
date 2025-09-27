<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

class UserSystem {
    private $db;
    
    public function __construct() {
        try {
            $this->db = new PDO('mysql:host=localhost;dbname=ntipay;charset=utf8', 'root', '');
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            throw new Exception("Database connection failed");
        }
    }
    
    public function getUserInfo($userId) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("getUserInfo error: " . $e->getMessage());
            throw new Exception("Failed to get user information");
        }
    }
    
    public function getUserTransactions($userId, $limit = 10) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM payment_transactions 
                WHERE user_id = ? 
                ORDER BY payment_date DESC 
                LIMIT ?
            ");
            $stmt->execute([$userId, (int)$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("getUserTransactions error: " . $e->getMessage());
            return []; // إرجاع مصفوفة فارغة بدلاً من خطأ
        }
    }
    
    public function getUserBills($userId, $status = 'all') {
        try {
            if ($status === 'all') {
                $stmt = $this->db->prepare("SELECT * FROM bills WHERE user_id = ? ORDER BY due_date DESC");
                $stmt->execute([$userId]);
            } else {
                $stmt = $this->db->prepare("SELECT * FROM bills WHERE user_id = ? AND status = ? ORDER BY due_date DESC");
                $stmt->execute([$userId, $status]);
            }
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("getUserBills error: " . $e->getMessage());
            return []; // إرجاع مصفوفة فارغة بدلاً من خطأ
        }
    }
    
    public function updateUserInfo($userId, $data) {
        try {
            $stmt = $this->db->prepare("
                UPDATE users 
                SET name = ?, email = ?, phone = ? 
                WHERE id = ?
            ");
            return $stmt->execute([$data['name'], $data['email'], $data['phone'], $userId]);
        } catch (PDOException $e) {
            error_log("updateUserInfo error: " . $e->getMessage());
            throw new Exception("Failed to update user information");
        }
    }
    
    public function getBillsStatistics($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    status,
                    COUNT(*) as count,
                    COALESCE(SUM(amount), 0) as total_amount
                FROM bills 
                WHERE user_id = ? 
                GROUP BY status
            ");
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("getBillsStatistics error: " . $e->getMessage());
            return []; // إرجاع مصفوفة فارغة بدلاً من خطأ
        }
    }
}

// إصلاح مشكلة الحصول على user_id
$userId = null;
if (isset($_SESSION['user']['id'])) {
    $userId = $_SESSION['user']['id'];
} elseif (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
} else {
    // محاولة الحصول على user_id من البيانات المخزنة في الجلسة
    if (isset($_SESSION['user']) && is_array($_SESSION['user'])) {
        $userArray = $_SESSION['user'];
        $userId = $userArray['id'] ?? ($userArray['user_id'] ?? null);
    }
}

// إذا لم نجد user_id، إعادة توجيه لتسجيل الدخول
if (!$userId) {
    error_log("User ID not found in session");
    session_destroy();
    header("Location: login.php");
    exit;
}

try {
    $userSystem = new UserSystem();
} catch (Exception $e) {
    error_log("UserSystem initialization failed: " . $e->getMessage());
    die("System error. Please try again later.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? '';
    
    try {
        switch ($action) {
            case 'get_user_info':
                $userInfo = $userSystem->getUserInfo($userId);
                if (!$userInfo) {
                    throw new Exception("User not found");
                }
                
                $transactions = $userSystem->getUserTransactions($userId);
                $bills = $userSystem->getUserBills($userId);
                $stats = $userSystem->getBillsStatistics($userId);
                
                echo json_encode([
                    'success' => true,
                    'user' => $userInfo,
                    'transactions' => $transactions,
                    'bills' => $bills,
                    'statistics' => $stats
                ]);
                break;
                
            case 'update_profile':
                if (!isset($input['data']) || !is_array($input['data'])) {
                    throw new Exception("Invalid data provided");
                }
                
                $result = $userSystem->updateUserInfo($userId, $input['data']);
                echo json_encode([
                    'success' => $result,
                    'message' => $result ? 'Profile updated successfully' : 'Failed to update profile'
                ]);
                break;
                
            case 'get_transactions':
                $limit = isset($input['limit']) ? (int)$input['limit'] : 20;
                $transactions = $userSystem->getUserTransactions($userId, $limit);
                echo json_encode([
                    'success' => true,
                    'transactions' => $transactions
                ]);
                break;
                
            default:
                throw new Exception('Invalid action');
        }
    } catch (Exception $e) {
        error_log("API error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
    
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - NTI Pay</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2a1b9a;
            --primary-light: #001195;
            --primary-dark: #2f5e96;
            --accent-color: #758acd;
            --text-on-primary: #ffffff;
            --text-primary: #333333;
            --background-light: #f5f0fa;
            --background-white: #ffffff;
            --border-color: #dee2e6;
            --success-color: #28a745;
            --error-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
            --transition-speed: 0.3s;
            --font-family: "Poppins", sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-family);
            background-color: var(--background-light);
            color: var(--text-primary);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .page-header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: white;
            padding: 40px 20px;
            margin: -20px -20px 30px -20px;
            border-radius: 0 0 15px 15px;
            text-align: center;
        }

        .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .page-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .profile-card {
            background: var(--background-white);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            height: fit-content;
        }

        .profile-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--border-color);
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: white;
            font-size: 3rem;
            font-weight: bold;
        }

        .profile-name {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 5px;
        }

        .profile-email {
            color: var(--text-primary);
            opacity: 0.8;
        }

        .profile-info {
            margin-bottom: 20px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px dotted var(--border-color);
        }

        .info-label {
            font-weight: 500;
            color: var(--text-primary);
        }

        .info-value {
            color: var(--primary-color);
            font-weight: 600;
        }

        .balance-display {
            background: linear-gradient(135deg, var(--success-color), #20c997);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin: 20px 0;
        }

        .balance-label {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-bottom: 5px;
        }

        .balance-amount {
            font-size: 2rem;
            font-weight: bold;
        }

        .quick-stats {
            background: var(--background-white);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .stats-header {
            font-size: 1.3rem;
            color: var(--primary-color);
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--border-color);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--background-light);
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            border: 1px solid var(--border-color);
            transition: all var(--transition-speed) ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(42, 27, 154, 0.15);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 0.9rem;
            color: var(--text-primary);
            opacity: 0.8;
        }

        .tabs-container {
            margin-top: 30px;
        }

        .tabs-nav {
            display: flex;
            background: var(--background-white);
            border-radius: 10px 10px 0 0;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .tab-btn {
            flex: 1;
            padding: 15px 20px;
            background: transparent;
            border: none;
            cursor: pointer;
            font-family: var(--font-family);
            font-size: 1rem;
            font-weight: 500;
            color: var(--text-primary);
            transition: all var(--transition-speed) ease;
            border-bottom: 3px solid transparent;
        }

        .tab-btn.active {
            background: var(--primary-color);
            color: white;
            border-bottom-color: var(--accent-color);
        }

        .tab-btn:hover:not(.active) {
            background: var(--background-light);
        }

        .tab-content {
            background: var(--background-white);
            padding: 30px;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            min-height: 400px;
        }

        .tab-pane {
            display: none;
        }

        .tab-pane.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        .transactions-list {
            max-height: 500px;
            overflow-y: auto;
        }

        .transaction-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid var(--border-color);
        }

        .transaction-info {
            flex: 1;
        }

        .transaction-receipt {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 3px;
        }

        .transaction-date {
            font-size: 0.9rem;
            color: var(--text-primary);
            opacity: 0.7;
        }

        .transaction-amount {
            font-weight: bold;
            color: var(--success-color);
            font-size: 1.1rem;
        }

        .bills-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .bill-card {
            background: var(--background-light);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 20px;
            transition: all var(--transition-speed) ease;
        }

        .bill-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .bill-status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .status-paid {
            background: #d4edda;
            color: #155724;
        }

        .status-unpaid {
            background: #fff3cd;
            color: #856404;
        }

        .status-overdue {
            background: #f8d7da;
            color: #721c24;
        }

        .bill-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .bill-details {
            font-size: 0.9rem;
            color: var(--text-primary);
            margin-bottom: 15px;
        }

        .bill-amount {
            font-size: 1.3rem;
            font-weight: bold;
            color: var(--primary-color);
            text-align: right;
        }

        .profile-form {
            max-width: 500px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-primary);
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-family: var(--font-family);
            font-size: 1rem;
            transition: all var(--transition-speed) ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(42, 27, 154, 0.1);
        }

        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 25px;
            font-family: var(--font-family);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all var(--transition-speed) ease;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .btn-success {
            background: var(--success-color);
            color: white;
        }

        .btn-info {
            background: var(--info-color);
            color: white;
        }

        .btn-secondary {
            background: var(--border-color);
            color: var(--text-primary);
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: var(--text-primary);
            opacity: 0.7;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.5;
        }

        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .page-title {
                font-size: 2rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .tabs-nav {
                flex-wrap: wrap;
            }

            .tab-btn {
                min-width: 50%;
            }

            .bills-grid {
                grid-template-columns: 1fr;
            }

            .action-buttons {
                flex-direction: column;
            }

            .transaction-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .loading {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid var(--border-color);
            border-top: 4px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .success-message {
            background: var(--success-color);
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
        }

        .error-message {
            background: var(--error-color);
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
        }
    </style>
</head>
<body>
    <?php require '../payment/navbar.php'; ?>

    <div class="container">
        <div class="page-header">
            <h1 class="page-title">My Account</h1>
            <p class="page-subtitle">Manage your profile, view transactions, and track your bills</p>
        </div>

        <div class="success-message" id="successMessage">
            Profile updated successfully!
        </div>

        <div class="error-message" id="errorMessage">
            An error occurred. Please try again.
        </div>

        <div class="dashboard-grid">
            <div class="profile-card">
                <div class="profile-header">
                    <div class="profile-avatar" id="profileAvatar">
                        U
                    </div>
                    <div class="profile-name" id="profileName">Loading...</div>
                    <div class="profile-email" id="profileEmail">Loading...</div>
                </div>

                <div class="profile-info">
                    <div class="info-item">
                        <span class="info-label">User ID:</span>
                        <span class="info-value" id="userId"><?php echo $userId; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Member Since:</span>
                        <span class="info-value" id="memberSince">-</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Phone:</span>
                        <span class="info-value" id="userPhone">-</span>
                    </div>
                </div>

                <div class="balance-display">
                    <div class="balance-label">Current Balance</div>
                    <div class="balance-amount" id="userBalance">0 NTI</div>
                </div>

                <div class="action-buttons">
                    <a href="pay2.php" class="btn btn-primary">
                        <i class="fas fa-credit-card"></i>
                        Pay Bills
                    </a>
                    <button class="btn btn-info" onclick="downloadStatement()">
                        <i class="fas fa-download"></i>
                        Statement
                    </button>
                </div>
            </div>

            <div class="quick-stats">
                <div class="stats-header">
                    <i class="fas fa-chart-bar"></i>
                    Account Overview
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number" id="totalBills">0</div>
                        <div class="stat-label">Total Bills</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="paidBills">0</div>
                        <div class="stat-label">Paid Bills</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="pendingBills">0</div>
                        <div class="stat-label">Pending Bills</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="totalPaid">0 NTI</div>
                        <div class="stat-label">Total Paid</div>
                    </div>
                </div>

                <div class="action-buttons">
                    <a href="welcome.php" class="btn btn-secondary">
                        <i class="fas fa-home"></i>
                        Home
                    </a>
                    <a href="../payment/pay2.php" class="btn btn-success">
                        <i class="fas fa-plus"></i>
                        New Payment
                    </a>
                </div>
            </div>
        </div>

        <div class="tabs-container">
            <div class="tabs-nav">
                <button class="tab-btn active" onclick="switchTab('transactions')">
                    <i class="fas fa-history"></i>
                    Transaction History
                </button>
                <button class="tab-btn" onclick="switchTab('bills')">
                    <i class="fas fa-file-invoice"></i>
                    My Bills
                </button>
                <button class="tab-btn" onclick="switchTab('profile')">
                    <i class="fas fa-user-edit"></i>
                    Edit Profile
                </button>
            </div>

            <div class="tab-content">
                <div class="tab-pane active" id="transactionsTab">
                    <h3 style="color: var(--primary-color); margin-bottom: 20px;">
                        <i class="fas fa-history"></i>
                        Recent Transactions
                    </h3>
                    <div class="transactions-list" id="transactionsList">
                        <div class="loading">
                            <div class="spinner"></div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="billsTab">
                    <h3 style="color: var(--primary-color); margin-bottom: 20px;">
                        <i class="fas fa-file-invoice"></i>
                        All Bills
                    </h3>
                    <div class="bills-grid" id="billsList">
                        <div class="loading">
                            <div class="spinner"></div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="profileTab">
                    <h3 style="color: var(--primary-color); margin-bottom: 20px;">
                        <i class="fas fa-user-edit"></i>
                        Edit Profile Information
                    </h3>
                    <form class="profile-form" id="profileForm">
                        <div class="form-group">
                            <label class="form-label" for="editName">Full Name</label>
                            <input type="text" id="editName" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="editEmail">Email Address</label>
                            <input type="email" id="editEmail" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="editPhone">Phone Number</label>
                            <input type="tel" id="editPhone" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Update Profile
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let userData = {};
        let currentTab = 'transactions';

        document.addEventListener('DOMContentLoaded', function() {
            loadUserData();
            setupEventListeners();
        });

        async function loadUserData() {
            try {
                const response = await fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ action: 'get_user_info' })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    userData = data;
                    displayUserInfo();
                    displayStatistics();
                    displayTransactions();
                    displayBills();
                } else {
                    throw new Error(data.error || 'Failed to load user data');
                }
            } catch (error) {
                console.error('Error loading user data:', error);
                showError('Failed to load user data. Please refresh the page.');
            }
        }

        function displayUserInfo() {
            const user = userData.user;
            
            document.getElementById('profileName').textContent = user.name || 'User';
            document.getElementById('profileEmail').textContent = user.email || 'No email';
            document.getElementById('userPhone').textContent = user.phone || 'Not provided';
            document.getElementById('userBalance').textContent = (user.balance || 0) + ' NTI';
            document.getElementById('memberSince').textContent = user.created_at ? 
                new Date(user.created_at).toLocaleDateString() : 'Unknown';
            
            const avatar = document.getElementById('profileAvatar');
            avatar.textContent = user.name ? user.name.charAt(0).toUpperCase() : 'U';
            
            document.getElementById('editName').value = user.name || '';
            document.getElementById('editEmail').value = user.email || '';
            document.getElementById('editPhone').value = user.phone || '';
        }

        function displayStatistics() {
            const stats = userData.statistics || [];
            let totalBills = 0;
            let paidBills = 0;
            let pendingBills = 0;
            let totalPaid = 0;

            stats.forEach(stat => {
                totalBills += parseInt(stat.count);
                if (stat.status === 'paid') {
                    paidBills = parseInt(stat.count);
                    totalPaid = parseFloat(stat.total_amount);
                } else if (stat.status === 'unpaid' || stat.status === 'overdue') {
                    pendingBills += parseInt(stat.count);
                }
            });

            document.getElementById('totalBills').textContent = totalBills;
            document.getElementById('paidBills').textContent = paidBills;
            document.getElementById('pendingBills').textContent = pendingBills;
            document.getElementById('totalPaid').textContent = totalPaid + ' NTI';
        }

        function displayTransactions() {
            const transactions = userData.transactions || [];
            const container = document.getElementById('transactionsList');

            if (transactions.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-receipt"></i>
                        <p>No transactions found</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = transactions.map(transaction => `
                <div class="transaction-item">
                    <div class="transaction-info">
                        <div class="transaction-receipt">${transaction.receipt_number}</div>
                        <div class="transaction-date">
                            ${new Date(transaction.payment_date).toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'short',
                                day: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            })}
                        </div>
                    </div>
                    <div class="transaction-amount">${transaction.total_amount} NTI</div>
                </div>
            `).join('');
        }

        function displayBills() {
            const bills = userData.bills || [];
            const container = document.getElementById('billsList');

            if (bills.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-file-invoice"></i>
                        <p>No bills found</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = bills.map(bill => `
                <div class="bill-card">
                    <div class="bill-status status-${bill.status}">${bill.status.charAt(0).toUpperCase() + bill.status.slice(1)}</div>
                    <div class="bill-title">${bill.type}</div>
                    <div class="bill-details">
                        <strong>Invoice:</strong> ${bill.number}<br>
                        <strong>Due Date:</strong> ${new Date(bill.due_date).toLocaleDateString()}<br>
                        <strong>Description:</strong> ${bill.description}
                    </div>
                    <div class="bill-amount">${bill.amount} NTI</div>
                </div>
            `).join('');
        }

        function switchTab(tabName) {
            const tabButtons = document.querySelectorAll('.tab-btn');
            const tabPanes = document.querySelectorAll('.tab-pane');
            
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabPanes.forEach(pane => pane.classList.remove('active'));
            
            document.querySelector(`[onclick="switchTab('${tabName}')"]`).classList.add('active');
            document.getElementById(tabName + 'Tab').classList.add('active');
            
            currentTab = tabName;
        }

        function setupEventListeners() {
            const profileForm = document.getElementById('profileForm');
            profileForm.addEventListener('submit', handleProfileUpdate);
        }

        async function handleProfileUpdate(event) {
            event.preventDefault();
            
            const formData = {
                name: document.getElementById('editName').value,
                email: document.getElementById('editEmail').value,
                phone: document.getElementById('editPhone').value
            };

            try {
                const response = await fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ 
                        action: 'update_profile', 
                        data: formData 
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showSuccess(data.message);
                    userData.user = { ...userData.user, ...formData };
                    displayUserInfo();
                } else {
                    throw new Error(data.error || 'Failed to update profile');
                }
            } catch (error) {
                console.error('Error updating profile:', error);
                showError('Failed to update profile. Please try again.');
            }
        }

        function showSuccess(message) {
            const successMsg = document.getElementById('successMessage');
            successMsg.textContent = message;
            successMsg.style.display = 'block';
            
            setTimeout(() => {
                successMsg.style.display = 'none';
            }, 3000);
        }

        function showError(message) {
            const errorMsg = document.getElementById('errorMessage');
            errorMsg.textContent = message;
            errorMsg.style.display = 'block';
            
            setTimeout(() => {
                errorMsg.style.display = 'none';
            }, 5000);
        }

        function downloadStatement() {
            const user = userData.user;
            const transactions = userData.transactions || [];
            
            if (transactions.length === 0) {
                alert('No transactions available for statement generation.');
                return;
            }

            const htmlContent = `
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <title>Account Statement - NTI Pay</title>
                    <style>
                        body { 
                            font-family: 'Arial', sans-serif; 
                            margin: 20px; 
                            color: #333; 
                        }
                        .header { 
                            text-align: center; 
                            border-bottom: 2px solid #2a1b9a; 
                            padding-bottom: 1rem; 
                            margin-bottom: 2rem; 
                        }
                        .title { 
                            color: #2a1b9a; 
                            font-size: 2rem; 
                            margin-bottom: 0.5rem; 
                        }
                        .user-info { 
                            background: #f5f0fa; 
                            padding: 1rem; 
                            border-radius: 8px; 
                            margin-bottom: 2rem; 
                        }
                        .statement-table { 
                            width: 100%; 
                            border-collapse: collapse; 
                            margin-bottom: 2rem; 
                        }
                        .statement-table th, .statement-table td { 
                            padding: 12px; 
                            text-align: left; 
                            border-bottom: 1px solid #dee2e6; 
                        }
                        .statement-table th { 
                            background-color: #2a1b9a; 
                            color: white; 
                        }
                        .summary { 
                            background: #2a1b9a; 
                            color: white; 
                            padding: 1rem; 
                            border-radius: 8px; 
                            margin-top: 2rem; 
                        }
                        @media print {
                            body { margin: 0; }
                        }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h1 class="title">Account Statement</h1>
                        <p>Generated on ${new Date().toLocaleDateString('en-US')}</p>
                    </div>
                    
                    <div class="user-info">
                        <h3>Account Holder Information</h3>
                        <p><strong>Name:</strong> ${user.name || 'N/A'}</p>
                        <p><strong>Email:</strong> ${user.email || 'N/A'}</p>
                        <p><strong>User ID:</strong> ${user.id}</p>
                        <p><strong>Current Balance:</strong> ${user.balance || 0} NTI</p>
                    </div>

                    <h3>Transaction History</h3>
                    <table class="statement-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Receipt Number</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${transactions.map(transaction => `
                                <tr>
                                    <td>${new Date(transaction.payment_date).toLocaleDateString('en-US')}</td>
                                    <td>${transaction.receipt_number}</td>
                                    <td>${transaction.total_amount} NTI</td>
                                    <td>Completed</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>

                    <div class="summary">
                        <h3>Account Summary</h3>
                        <p><strong>Total Transactions:</strong> ${transactions.length}</p>
                        <p><strong>Total Amount Paid:</strong> ${transactions.reduce((sum, t) => sum + parseFloat(t.total_amount), 0)} NTI</p>
                        <p><strong>Current Balance:</strong> ${user.balance || 0} NTI</p>
                    </div>

                    <div style="text-align: center; margin-top: 2rem; color: #666; font-size: 0.9rem;">
                        Generated by NTI Pay - ${new Date().toLocaleString('en-US')}
                    </div>
                </body>
                </html>
            `;

            const blob = new Blob([htmlContent], { type: 'text/html' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `NTIPay_Statement_${user.name || 'User'}_${new Date().toISOString().split('T')[0]}.html`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        }

        // Keyboard shortcuts and animations
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && currentTab !== 'transactions') {
                switchTab('transactions');
            }
        });

        // Intersection observer for animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Apply animations to elements
        const animatedElements = document.querySelectorAll('.profile-card, .quick-stats, .tab-content');
        animatedElements.forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(el);
        });

        // Add hover effects to stat cards
        document.querySelectorAll('.stat-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Add hover effects to bill cards
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                document.querySelectorAll('.bill-card').forEach(card => {
                    card.addEventListener('mouseenter', function() {
                        this.style.transform = 'translateY(-5px) scale(1.02)';
                        this.style.boxShadow = '0 15px 35px rgba(42, 27, 154, 0.2)';
                    });
                    
                    card.addEventListener('mouseleave', function() {
                        this.style.transform = 'translateY(0) scale(1)';
                        this.style.boxShadow = '0 8px 25px rgba(0, 0, 0, 0.15)';
                    });
                });
            }, 1000);
        });

        // Add click animation to buttons
        document.querySelectorAll('.btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                if (!this.classList.contains('tab-btn')) {
                    const ripple = document.createElement('span');
                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;
                    
                    ripple.style.cssText = `
                        position: absolute;
                        width: ${size}px;
                        height: ${size}px;
                        left: ${x}px;
                        top: ${y}px;
                        background: rgba(255, 255, 255, 0.4);
                        border-radius: 50%;
                        transform: scale(0);
                        animation: ripple 0.6s linear;
                        pointer-events: none;
                    `;
                    
                    this.style.position = 'relative';
                    this.style.overflow = 'hidden';
                    this.appendChild(ripple);
                    
                    setTimeout(() => ripple.remove(), 600);
                }
            });
        });

        // Add ripple animation CSS
        const rippleStyle = document.createElement('style');
        rippleStyle.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
            
            .transaction-item:hover {
                background: var(--background-light);
                border-radius: 8px;
                transition: background 0.3s ease;
            }
            
            .form-control:focus {
                transform: scale(1.02);
            }
        `;
        document.head.appendChild(rippleStyle);
    </script>
</body>
</html>
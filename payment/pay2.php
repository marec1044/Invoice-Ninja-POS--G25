<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// تسجيل الأخطاء للتصحيح
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

class PaymentSystem {
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
    
    public function getUserBalance($userId) {
        try {
            $stmt = $this->db->prepare("SELECT balance FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? floatval($result['balance']) : 0;
        } catch (PDOException $e) {
            error_log("getUserBalance error: " . $e->getMessage());
            return 0; // إرجاع 0 بدلاً من خطأ
        }
    }
    
    public function updateUserBalance($userId, $newBalance) {
        try {
            $stmt = $this->db->prepare("UPDATE users SET balance = ? WHERE id = ?");
            return $stmt->execute([$newBalance, $userId]);
        } catch (PDOException $e) {
            error_log("updateUserBalance error: " . $e->getMessage());
            throw new Exception("Failed to update balance");
        }
    }
    
    public function getBills($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM bills 
                WHERE user_id = ? AND status IN ('unpaid', 'overdue') 
                ORDER BY due_date ASC
            ");
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("getBills error: " . $e->getMessage());
            return []; // إرجاع مصفوفة فارغة بدلاً من خطأ
        }
    }
    
    public function getBillsByIds($billIds) {
        if (empty($billIds) || !is_array($billIds)) {
            return [];
        }
        
        try {
            // تنظيف bill IDs والتأكد أنها أرقام
            $cleanIds = array_filter(array_map('intval', $billIds));
            if (empty($cleanIds)) {
                return [];
            }
            
            $placeholders = str_repeat('?,', count($cleanIds) - 1) . '?';
            $stmt = $this->db->prepare("SELECT * FROM bills WHERE id IN ($placeholders)");
            $stmt->execute($cleanIds);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("getBillsByIds error: " . $e->getMessage());
            return [];
        }
    }
    
    public function markBillsAsPaid($billIds) {
        if (empty($billIds) || !is_array($billIds)) {
            return false;
        }
        
        try {
            $cleanIds = array_filter(array_map('intval', $billIds));
            if (empty($cleanIds)) {
                return false;
            }
            
            $placeholders = str_repeat('?,', count($cleanIds) - 1) . '?';
            $stmt = $this->db->prepare("UPDATE bills SET status = 'paid', paid_date = NOW() WHERE id IN ($placeholders)");
            return $stmt->execute($cleanIds);
        } catch (PDOException $e) {
            error_log("markBillsAsPaid error: " . $e->getMessage());
            throw new Exception("Failed to update bill status");
        }
    }
    
    public function createPaymentTransaction($userId, $billIds, $totalAmount) {
        try {
            $receiptNumber = 'REC-' . date('Y') . '-' . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
            
            $stmt = $this->db->prepare("
                INSERT INTO payment_transactions (user_id, receipt_number, total_amount, payment_date, bill_ids) 
                VALUES (?, ?, ?, NOW(), ?)
            ");
            
            $billIdsJson = json_encode($billIds);
            $stmt->execute([$userId, $receiptNumber, $totalAmount, $billIdsJson]);
            
            return $receiptNumber;
        } catch (PDOException $e) {
            error_log("createPaymentTransaction error: " . $e->getMessage());
            throw new Exception("Failed to create transaction record");
        }
    }
    
    public function getUserInfo($userId) {
        try {
            $stmt = $this->db->prepare("SELECT name, email FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("getUserInfo error: " . $e->getMessage());
            return null;
        }
    }
    
    // وظائف إضافية لإدارة التحويلات المصرفية
    public function beginTransaction() {
        return $this->db->beginTransaction();
    }
    
    public function commit() {
        return $this->db->commit();
    }
    
    public function rollback() {
        return $this->db->rollback();
    }
}

// إصلاح مشكلة الحصول على user_id
$userId = null;

// التحقق من وجود user_id في طرق مختلفة
if (isset($_SESSION['user']['id'])) {
    $userId = intval($_SESSION['user']['id']);
} elseif (isset($_SESSION['user_id'])) {
    $userId = intval($_SESSION['user_id']);
} elseif (isset($_SESSION['user']) && is_array($_SESSION['user'])) {
    $userArray = $_SESSION['user'];
    if (isset($userArray['id'])) {
        $userId = intval($userArray['id']);
    } elseif (isset($userArray['user_id'])) {
        $userId = intval($userArray['user_id']);
    }
}

// إذا لم نجد user_id، تسجيل الخطأ وإعادة التوجيه
if (!$userId || $userId <= 0) {
    error_log("User ID not found or invalid in session for payment. Session data: " . json_encode($_SESSION));
    session_destroy();
    header("Location: login.php?error=session_expired");
    exit;
}

error_log("Payment system - User ID: " . $userId); // للتتبع

try {
    $paymentSystem = new PaymentSystem();
} catch (Exception $e) {
    error_log("PaymentSystem initialization failed: " . $e->getMessage());
    http_response_code(500);
    die("System error. Please try again later.");
}

// معالجة طلبات AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    // التحقق من صحة JSON
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode([
            'success' => false,
            'error' => 'Invalid JSON data'
        ]);
        exit;
    }
    
    $action = isset($input['action']) ? trim($input['action']) : '';
    
    try {
        switch ($action) {
            case 'get_bills':
                $bills = $paymentSystem->getBills($userId);
                $balance = $paymentSystem->getUserBalance($userId);
                
                error_log("Bills loaded for user $userId: " . count($bills) . " bills, balance: $balance");
                
                echo json_encode([
                    'success' => true,
                    'bills' => $bills,
                    'balance' => $balance,
                    'user_id' => $userId // للتأكد من صحة المستخدم
                ]);
                break;
                
            case 'process_payment':
                $billIds = isset($input['bill_ids']) ? $input['bill_ids'] : [];
                
                if (empty($billIds) || !is_array($billIds)) {
                    throw new Exception('No bills selected or invalid selection');
                }
                
                // التحقق من صحة bill_ids (يجب أن تكون أرقام)
                $cleanBillIds = array_filter(array_map('intval', $billIds));
                if (empty($cleanBillIds)) {
                    throw new Exception('Invalid bill selection');
                }
                
                error_log("Processing payment for user $userId, bills: " . implode(',', $cleanBillIds));
                
                $selectedBills = $paymentSystem->getBillsByIds($cleanBillIds);
                if (empty($selectedBills)) {
                    throw new Exception('Selected bills not found');
                }
                
                // التأكد أن الفواتير تخص المستخدم الحالي
                foreach ($selectedBills as $bill) {
                    if (intval($bill['user_id']) !== $userId) {
                        throw new Exception('Unauthorized access to bills');
                    }
                }
                
                $totalAmount = array_sum(array_map('floatval', array_column($selectedBills, 'amount')));
                $currentBalance = $paymentSystem->getUserBalance($userId);
                
                if ($currentBalance < $totalAmount) {
                    throw new Exception('Insufficient balance. Current: ' . $currentBalance . ', Required: ' . $totalAmount);
                }
                
                // تنفيذ العملية في transaction
                $paymentSystem->beginTransaction();
                
                try {
                    $newBalance = $currentBalance - $totalAmount;
                    
                    if (!$paymentSystem->updateUserBalance($userId, $newBalance)) {
                        throw new Exception('Failed to update user balance');
                    }
                    
                    if (!$paymentSystem->markBillsAsPaid($cleanBillIds)) {
                        throw new Exception('Failed to update bill status');
                    }
                    
                    $receiptNumber = $paymentSystem->createPaymentTransaction($userId, $cleanBillIds, $totalAmount);
                    
                    $paymentSystem->commit();
                    
                    error_log("Payment completed successfully for user $userId, receipt: $receiptNumber");
                    
                    echo json_encode([
                        'success' => true,
                        'receipt_number' => $receiptNumber,
                        'new_balance' => $newBalance,
                        'total_paid' => $totalAmount,
                        'bills_count' => count($cleanBillIds)
                    ]);
                    
                } catch (Exception $e) {
                    $paymentSystem->rollback();
                    throw $e;
                }
                break;
                
            case 'get_user_info':
                $userInfo = $paymentSystem->getUserInfo($userId);
                if (!$userInfo) {
                    throw new Exception("User information not found");
                }
                echo json_encode([
                    'success' => true,
                    'user' => $userInfo
                ]);
                break;
                
            default:
                throw new Exception('Invalid or missing action');
        }
    } catch (Exception $e) {
        error_log("Payment API error for user $userId, action '$action': " . $e->getMessage());
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage(),
            'action' => $action
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="pay.css">
    <title>Pay Bills - NTIpay</title>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="app-container">
        <header class="app-header">
            <h1><i class="fas fa-file-invoice-dollar"></i> Pay Bills</h1>
            <p>Select the bills you want to pay and complete the payment process</p>
        </header>

        <section class="bills-section">
            <h2 class="section-title"><i class="fas fa-list"></i> Pending Bills</h2>
            
            <div class="bills-grid" id="billsGrid">
                <div style="text-align: center; color: var(--text-primary); font-style: italic; padding: 20px;">
                    <i class="fas fa-spinner fa-spin"></i> Loading bills...
                </div>
            </div>
        </section>

        <section class="payment-section">
            <h2 class="section-title"><i class="fas fa-credit-card"></i> Payment Details</h2>
            
            <div class="selected-bills" id="selectedBills">
                <p style="text-align: center; color: var(--text-primary); font-style: italic;">
                    No bills selected for payment
                </p>
            </div>

            <div class="payment-summary" id="paymentSummary" style="display: none;">
                <h3 style="color: var(--primary-color); margin-bottom: 1rem;">Payment Summary</h3>
                <div id="summaryDetails"></div>
                <div class="summary-row total">
                    <span>Total Amount:</span>
                    <span id="totalAmount">0 NTI</span>
                </div>
            </div>

            <div class="success-message" id="successMessage">
                Bills paid successfully!
            </div>

            <button class="payment-button" id="payButton" disabled>
                <i class="fas fa-wallet"></i> Pay Now
            </button>
        </section>
    </div>

    <div id="receiptModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" id="closeModal">&times;</span>
            
            <div class="receipt-header">
                <h2 class="receipt-title">Payment Receipt</h2>
                <p class="receipt-number" id="receiptNumber">Receipt #:</p>
                <p>Payment Date: <span id="paymentDate"></span></p>
            </div>

            <div class="receipt-section">
                <h3>Customer Information</h3>
                <div class="receipt-row">
                    <span>Customer Name:</span>
                    <span id="customerName"></span>
                </div>
                <div class="receipt-row">
                    <span>Customer ID:</span>
                    <span id="customerId"><?php echo $userId; ?></span>
                </div>
                <div class="receipt-row">
                    <span>Email:</span>
                    <span id="customerEmail"></span>
                </div>
            </div>

            <div class="receipt-section">
                <h3>Paid Bills Details</h3>
                <div id="paidBillsDetails"></div>
            </div>

            <div class="receipt-total">
                Total Amount Paid: <span id="receiptTotal">0 NTI</span>
            </div>

            <div class="receipt-actions">
                <button class="action-btn btn-print" onclick="printReceipt()">
                    <i class="fas fa-print"></i> Print
                </button>
                <button class="action-btn btn-share" onclick="shareReceipt()">
                    <i class="fas fa-share"></i> Share
                </button>
                <button class="action-btn btn-download" onclick="downloadReceipt()">
                    <i class="fas fa-download"></i> Download PDF
                </button>
            </div>
        </div>
    </div>

    <script>
        let billsData = [];
        let selectedBills = [];
        let userBalance = 0;
        let userInfo = {};

        document.addEventListener('DOMContentLoaded', function() {
            loadBills();
            loadUserInfo();
            setupEventListeners();
        });

        async function loadBills() {
            try {
                const response = await fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ action: 'get_bills' })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    billsData = data.bills;
                    userBalance = data.balance;
                    renderBills();
                    updateBalanceDisplay();
                } else {
                    throw new Error(data.error || 'Failed to load bills');
                }
            } catch (error) {
                console.error('Error loading bills:', error);
                document.getElementById('billsGrid').innerHTML = 
                    '<p style="text-align: center; color: red;">Error loading bills. Please refresh the page.</p>';
            }
        }

        async function loadUserInfo() {
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
                    userInfo = data.user;
                }
            } catch (error) {
                console.error('Error loading user info:', error);
            }
        }

        function renderBills() {
            const billsGrid = document.getElementById('billsGrid');
            
            if (billsData.length === 0) {
                billsGrid.innerHTML = '<p style="text-align: center; color: var(--text-primary); font-style: italic;">No pending bills found.</p>';
                return;
            }

            billsGrid.innerHTML = '';

            billsData.forEach(bill => {
                const billCard = document.createElement('div');
                billCard.className = 'bill-card';
                billCard.dataset.billId = bill.id;
                
                const statusClass = bill.status === 'overdue' ? 'status-overdue' : 'status-unpaid';
                const statusText = bill.status === 'overdue' ? 'Overdue' : 'Unpaid';

                billCard.innerHTML = `
                    <div class="bill-status ${statusClass}">${statusText}</div>
                    <div class="bill-info">
                        <h3>${bill.type}</h3>
                        <div class="bill-details">
                            <div class="bill-detail">
                                <span class="label">Invoice Number:</span>
                                <span class="value">${bill.number}</span>
                            </div>
                            <div class="bill-detail">
                                <span class="label">Issue Date:</span>
                                <span class="value">${bill.date}</span>
                            </div>
                            <div class="bill-detail">
                                <span class="label">Due Date:</span>
                                <span class="value">${bill.due_date}</span>
                            </div>
                            <div class="bill-detail">
                                <span class="label">Description:</span>
                                <span class="value">${bill.description}</span>
                            </div>
                        </div>
                        <div class="bill-amount">
                            <div class="amount-label">Amount Due</div>
                            <div class="amount-value">${bill.amount} NTI</div>
                        </div>
                    </div>
                `;

                billCard.addEventListener('click', () => toggleBillSelection(bill.id));
                billsGrid.appendChild(billCard);
            });
        }

        function toggleBillSelection(billId) {
            const billCard = document.querySelector(`[data-bill-id="${billId}"]`);
            const isSelected = selectedBills.includes(billId);

            if (isSelected) {
                selectedBills = selectedBills.filter(id => id !== billId);
                billCard.classList.remove('selected');
            } else {
                selectedBills.push(billId);
                billCard.classList.add('selected');
            }

            updatePaymentSection();
        }

        function updatePaymentSection() {
            const selectedBillsDiv = document.getElementById('selectedBills');
            const paymentSummary = document.getElementById('paymentSummary');
            const payButton = document.getElementById('payButton');

            if (selectedBills.length === 0) {
                selectedBillsDiv.innerHTML = `
                    <p style="text-align: center; color: var(--text-primary); font-style: italic;">
                        No bills selected for payment
                    </p>
                `;
                paymentSummary.style.display = 'none';
                payButton.disabled = true;
                return;
            }

            selectedBillsDiv.innerHTML = selectedBills.map(billId => {
                const bill = billsData.find(b => b.id == billId);
                return `
                    <div class="selected-bill-item">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <strong>${bill.type}</strong> - ${bill.number}
                                <br><small>${bill.description}</small>
                            </div>
                            <div style="color: var(--primary-color); font-weight: bold;">
                                ${bill.amount} NTI
                            </div>
                        </div>
                    </div>
                `;
            }).join('');

            const totalAmount = selectedBills.reduce((sum, billId) => {
                const bill = billsData.find(b => b.id == billId);
                return sum + parseFloat(bill.amount);
            }, 0);

            document.getElementById('summaryDetails').innerHTML = `
                <div class="summary-row">
                    <span>Number of Bills:</span>
                    <span>${selectedBills.length}</span>
                </div>
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span>${totalAmount} NTI</span>
                </div>
                <div class="summary-row">
                    <span>Current Balance:</span>
                    <span>${userBalance} NTI</span>
                </div>
            `;

            document.getElementById('totalAmount').textContent = `${totalAmount} NTI`;

            paymentSummary.style.display = 'block';
            payButton.disabled = totalAmount > userBalance;
            
            if (totalAmount > userBalance) {
                payButton.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Insufficient Balance';
                payButton.style.background = '#dc3545';
            } else {
                payButton.innerHTML = '<i class="fas fa-wallet"></i> Pay Now';
                payButton.style.background = '';
            }
        }

        function setupEventListeners() {
            const payButton = document.getElementById('payButton');
            const closeModal = document.getElementById('closeModal');
            const receiptModal = document.getElementById('receiptModal');

            payButton.addEventListener('click', processPayment);
            
            closeModal.addEventListener('click', () => {
                receiptModal.style.display = 'none';
            });

            window.addEventListener('click', (e) => {
                if (e.target === receiptModal) {
                    receiptModal.style.display = 'none';
                }
            });
        }

        async function processPayment() {
            if (selectedBills.length === 0) return;

            showLoadingAnimation();

            try {
                const response = await fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ 
                        action: 'process_payment', 
                        bill_ids: selectedBills 
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    userBalance = data.new_balance;
                    updateBalanceDisplay();
                    
                    const successMessage = document.getElementById('successMessage');
                    successMessage.style.display = 'block';

                    generateReceipt(data.receipt_number, data.total_paid);

                    selectedBills.forEach(billId => {
                        const billCard = document.querySelector(`[data-bill-id="${billId}"]`);
                        billCard.style.transition = 'all 0.5s ease';
                        billCard.style.opacity = '0';
                        billCard.style.transform = 'translateX(100px)';
                        setTimeout(() => {
                            billCard.style.display = 'none';
                        }, 500);
                    });

                    billsData = billsData.filter(bill => !selectedBills.includes(bill.id));
                    selectedBills = [];
                    updatePaymentSection();

                    setTimeout(() => {
                        successMessage.style.display = 'none';
                    }, 3000);

                    setTimeout(() => {
                        document.getElementById('receiptModal').style.display = 'block';
                    }, 1000);

                } else {
                    throw new Error(data.error || 'Payment failed');
                }
            } catch (error) {
                console.error('Payment error:', error);
                alert('Payment failed: ' + error.message);
            }
        }

        function generateReceipt(receiptNumber, totalAmount) {
            const now = new Date();
            const paymentDate = now.toLocaleDateString('en-US');

            document.getElementById('receiptNumber').textContent = `Receipt #: ${receiptNumber}`;
            document.getElementById('paymentDate').textContent = paymentDate;
            document.getElementById('customerName').textContent = userInfo.name || 'N/A';
            document.getElementById('customerEmail').textContent = userInfo.email || 'N/A';

            const paidBillsDetails = document.getElementById('paidBillsDetails');
            
            paidBillsDetails.innerHTML = selectedBills.map(billId => {
                const bill = billsData.find(b => b.id == billId);
                return `
                    <div class="receipt-row">
                        <span>${bill.number} - ${bill.description}</span>
                        <span>${bill.amount} NTI</span>
                    </div>
                `;
            }).join('');

            document.getElementById('receiptTotal').textContent = `${totalAmount} NTI`;
        }

        function updateBalanceDisplay() {
            const existingBalance = document.querySelector('.wallet-balance');
            if (existingBalance) {
                existingBalance.remove();
            }
            
            const balanceDiv = document.createElement('div');
            balanceDiv.className = 'wallet-balance';
            balanceDiv.style.cssText = `
                position: fixed;
                top: 90px;
                right: 20px;
                background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
                color: white;
                padding: 10px 15px;
                border-radius: 25px;
                font-size: 0.9rem;
                font-weight: bold;
                z-index: 100;
                box-shadow: 0 4px 15px rgba(42, 27, 154, 0.3);
            `;
            balanceDiv.innerHTML = `<i class="fas fa-wallet"></i> Balance: ${userBalance} NTI`;
            document.body.appendChild(balanceDiv);
        }

        function printReceipt() {
            const receiptContent = document.querySelector('.modal-content').cloneNode(true);
            receiptContent.querySelector('.close-btn').remove();
            receiptContent.querySelector('.receipt-actions').remove();

            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                    <head>
                        <title>Payment Receipt</title>
                        <style>
                            body { font-family: Arial, sans-serif; margin: 20px; }
                            .receipt-header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 1rem; margin-bottom: 1.5rem; }
                            .receipt-title { color: #2a1b9a; font-size: 1.8rem; margin-bottom: 0.5rem; }
                            .receipt-section { margin-bottom: 1.5rem; }
                            .receipt-section h3 { color: #2a1b9a; border-bottom: 1px solid #dee2e6; padding-bottom: 0.5rem; margin-bottom: 1rem; }
                            .receipt-row { display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px dotted #dee2e6; }
                            .receipt-total { background: #2a1b9a; color: white; padding: 1rem; border-radius: 8px; margin: 1rem 0; text-align: center; font-size: 1.2rem; font-weight: bold; }
                        </style>
                    </head>
                    <body>
                        ${receiptContent.innerHTML}
                    </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.print();
        }

        function shareReceipt() {
            if (navigator.share) {
                navigator.share({
                    title: 'Payment Receipt',
                    text: 'Payment Receipt from NTIpay',
                    url: window.location.href
                });
            } else {
                const receiptText = `Payment Receipt - NTIpay\n${document.getElementById('receiptNumber').textContent}\nPayment Date: ${document.getElementById('paymentDate').textContent}\nTotal Amount: ${document.getElementById('receiptTotal').textContent}`;
                const dummy = document.createElement('textarea');
                document.body.appendChild(dummy);
                dummy.value = receiptText;
                dummy.select();
                document.execCommand('copy');
                document.body.removeChild(dummy);
                alert('Receipt details copied to clipboard');
            }
        }

        function downloadReceipt() {
            const receiptContent = document.querySelector('.modal-content').cloneNode(true);
            receiptContent.querySelector('.close-btn').remove();
            receiptContent.querySelector('.receipt-actions').remove();

            const htmlContent = `
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <title>Payment Receipt</title>
                    <style>
                        body { 
                            font-family: 'Arial', sans-serif; 
                            margin: 20px; 
                            color: #333; 
                        }
                        .receipt-header { 
                            text-align: center; 
                            border-bottom: 2px solid #2a1b9a; 
                            padding-bottom: 1rem; 
                            margin-bottom: 1.5rem; 
                        }
                        .receipt-title { 
                            color: #2a1b9a; 
                            font-size: 1.8rem; 
                            margin-bottom: 0.5rem; 
                        }
                        .receipt-section { 
                            margin-bottom: 1.5rem; 
                        }
                        .receipt-section h3 { 
                            color: #2a1b9a; 
                            border-bottom: 1px solid #dee2e6; 
                            padding-bottom: 0.5rem; 
                            margin-bottom: 1rem; 
                        }
                        .receipt-row { 
                            display: flex; 
                            justify-content: space-between; 
                            padding: 0.5rem 0; 
                            border-bottom: 1px dotted #dee2e6; 
                        }
                        .receipt-total { 
                            background: #2a1b9a; 
                            color: white; 
                            padding: 1rem; 
                            border-radius: 8px; 
                            margin: 1rem 0; 
                            text-align: center; 
                            font-size: 1.2rem; 
                            font-weight: bold; 
                        }
                        @media print {
                            body { margin: 0; }
                        }
                    </style>
                </head>
                <body>
                    ${receiptContent.innerHTML}
                    <div style="text-align: center; margin-top: 2rem; color: #666; font-size: 0.9rem;">
                        Generated by NTIpay - ${new Date().toLocaleString('en-US')}
                    </div>
                </body>
                </html>
            `;

            const blob = new Blob([htmlContent], { type: 'text/html' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `receipt_${document.getElementById('receiptNumber').textContent.replace('#', '').replace('Receipt #: ', '')}.html`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        }

        document.documentElement.style.scrollBehavior = 'smooth';

        function showLoadingAnimation() {
            const payButton = document.getElementById('payButton');
            const originalText = payButton.innerHTML;
            payButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            payButton.disabled = true;
            
            setTimeout(() => {
                payButton.innerHTML = originalText;
                payButton.disabled = selectedBills.length === 0;
            }, 2000);
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('receiptModal');
                if (modal.style.display === 'block') {
                    modal.style.display = 'none';
                }
            }
        });
    </script>
</body>
</html>
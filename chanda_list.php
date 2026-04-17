<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'config.php';

$user_role = $_SESSION['user_role'];
$user_id = $_SESSION['user_id'];

// ফিল্টার
$month_filter = isset($_GET['month']) ? $_GET['month'] : '';
$year_filter = isset($_GET['year']) ? $_GET['year'] : date('Y');

$where = "";
if($month_filter) $where .= " AND c.month = '$month_filter'";
if($year_filter) $where .= " AND c.year = '$year_filter'";

// অ্যাডমিন সব দেখবে, সাধারণ সদস্য শুধু নিজের দেখবে
if($user_role == 'admin') {
    $query = "SELECT c.*, m.name FROM chanda c 
              JOIN members m ON c.member_id = m.id 
              WHERE 1=1 $where 
              ORDER BY c.date DESC";
} else {
    $query = "SELECT c.*, m.name FROM chanda c 
              JOIN members m ON c.member_id = m.id 
              WHERE c.member_id = $user_id $where 
              ORDER BY c.date DESC";
}

$result = mysqli_query($conn, $query);

// মোট টাকার যোগফল
$total_query = "SELECT SUM(amount) as total FROM chanda c 
                JOIN members m ON c.member_id = m.id 
                WHERE 1=1 $where";
$total_result = mysqli_query($conn, $total_query);
$total_amount = mysqli_fetch_assoc($total_result)['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>মাসিক চাঁদার তালিকা - ভোরের আলো সমবায় সমিতি</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: #f0f2f5;
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-success text-white">
            <h4>💰 মাসিক চাঁদার তালিকা</h4>
        </div>
        <div class="card-body">
            
            <!-- ফিল্টার ফর্ম -->
            <form method="GET" class="row mb-4">
                <div class="col-md-3">
                    <label>মাস</label>
                    <select name="month" class="form-control">
                        <option value="">সব মাস</option>
                        <option value="জানুয়ারি" <?php echo $month_filter=='জানুয়ারি'?'selected':''; ?>>জানুয়ারি</option>
                        <option value="ফেব্রুয়ারি" <?php echo $month_filter=='ফেব্রুয়ারি'?'selected':''; ?>>ফেব্রুয়ারি</option>
                        <option value="মার্চ" <?php echo $month_filter=='মার্চ'?'selected':''; ?>>মার্চ</option>
                        <option value="এপ্রিল" <?php echo $month_filter=='এপ্রিল'?'selected':''; ?>>এপ্রিল</option>
                        <option value="মে" <?php echo $month_filter=='মে'?'selected':''; ?>>মে</option>
                        <option value="জুন" <?php echo $month_filter=='জুন'?'selected':''; ?>>জুন</option>
                        <option value="জুলাই" <?php echo $month_filter=='জুলাই'?'selected':''; ?>>জুলাই</option>
                        <option value="আগস্ট" <?php echo $month_filter=='আগস্ট'?'selected':''; ?>>আগস্ট</option>
                        <option value="সেপ্টেম্বর" <?php echo $month_filter=='সেপ্টেম্বর'?'selected':''; ?>>সেপ্টেম্বর</option>
                        <option value="অক্টোবর" <?php echo $month_filter=='অক্টোবর'?'selected':''; ?>>অক্টোবর</option>
                        <option value="নভেম্বর" <?php echo $month_filter=='নভেম্বর'?'selected':''; ?>>নভেম্বর</option>
                        <option value="ডিসেম্বর" <?php echo $month_filter=='ডিসেম্বর'?'selected':''; ?>>ডিসেম্বর</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>সাল</label>
                    <select name="year" class="form-control">
                        <option value="2024" <?php echo $year_filter=='2024'?'selected':''; ?>>2024</option>
                        <option value="2025" <?php echo $year_filter=='2025'?'selected':''; ?>>2025</option>
                        <option value="2026" <?php echo $year_filter=='2026'?'selected':''; ?>>2026</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>&nbsp;</label><br>
                    <button type="submit" class="btn btn-primary">🔍 ফিল্টার করুন</button>
                    <a href="chanda_list.php" class="btn btn-secondary">↺ রিসেট</a>
                </div>
            </form>
            
            <!-- মোট টাকা -->
            <div class="alert alert-info">
                <strong>📊 মোট চাঁদা:</strong> <?php echo number_format($total_amount); ?> টাকা
            </div>
            
            <!-- চাঁদার তালিকা -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>সদস্যের নাম</th>
                            <th>পরিমাণ (টাকা)</th>
                            <th>মাস</th>
                            <th>সাল</th>
                            <th>তারিখ</th>
                            <th>মন্তব্য</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $sl = 1;
                        while($row = mysqli_fetch_assoc($result)): 
                        ?>
                        <tr>
                            <td><?php echo $sl++; ?></td>
                            <td><?php echo $row['name']; ?></td>
                            <td><strong><?php echo number_format($row['amount']); ?></strong></td>
                            <td><?php echo $row['month']; ?></td>
                            <td><?php echo $row['year']; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($row['date'])); ?></td>
                            <td><?php echo $row['remarks'] ?: '-'; ?></td>
                        </tr>
                        <?php endwhile; ?>
                        
                        <?php if(mysqli_num_rows($result) == 0): ?>
                        <tr>
                            <td colspan="7" class="text-center">⚠️ কোনো চাঁদার তথ্য পাওয়া যায়নি</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- শুধু অ্যাডমিন দেখতে পাবে "নতুন চাঁদা যোগ" বাটন -->
            <?php if($user_role == 'admin'): ?>
            <div class="mt-3">
                <a href="chanda_add.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> নতুন চাঁদা যোগ করুন
                </a>
            </div>
            <?php endif; ?>
            
            <div class="mt-2">
                <a href="dashboard.php" class="btn btn-secondary">🔙 ড্যাশবোর্ডে ফিরুন</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
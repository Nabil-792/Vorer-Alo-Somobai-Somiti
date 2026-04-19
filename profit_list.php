<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'config.php';

$user_role = $_SESSION['user_role'];
$user_name = $_SESSION['user_name'];

$year = isset($_GET['year']) ? $_GET['year'] : '';

$sql = "SELECT * FROM yearly_profit WHERE 1=1";
if($year != '') {
    $sql .= " AND year = '$year'";
}
$sql .= " ORDER BY year DESC";
$result = mysqli_query($conn, $sql);

// মোট লাভ
$total_sql = "SELECT SUM(amount) as total FROM yearly_profit";
if($year != '') {
    $total_sql .= " WHERE year = '$year'";
}
$total_result = mysqli_query($conn, $total_sql);
$total_amount = mysqli_fetch_assoc($total_result)['total'] ?? 0;

// বছরভিত্তিক সারাংশ
$summary_sql = "SELECT year, SUM(amount) as total, COUNT(*) as count 
                FROM yearly_profit 
                GROUP BY year 
                ORDER BY year DESC";
$summary_result = mysqli_query($conn, $summary_sql);
?>

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>বার্ষিক লাভ - ভোরের আলো সমবায় সমিতি</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        .main-card {
            border-radius: 25px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .card-header-custom {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            padding: 25px 30px;
        }
        .total-card {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 25px;
            text-align: center;
            color: white;
        }
        .summary-card {
            background: #e3f2fd;
            border-radius: 15px;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        .summary-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card main-card">
        
        <div class="card-header-custom text-white">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h3><i class="fas fa-chart-line me-2"></i> বার্ষিক লাভের তালিকা</h3>
                    <p class="mb-0">স্বাগতম, <?php echo $user_name; ?> | 
                        <span class="badge bg-light text-dark"><?php echo $user_role == 'admin' ? 'প্রশাসক' : 'সদস্য'; ?></span>
                    </p>
                </div>
                <a href="dashboard.php" class="btn btn-light rounded-pill">
                    <i class="fas fa-arrow-left"></i> ড্যাশবোর্ড
                </a>
            </div>
        </div>
        
        <div class="card-body p-4">
            
            <!-- বছরভিত্তিক সারাংশ -->
            <?php if(mysqli_num_rows($summary_result) > 0): ?>
            <div class="row mb-4">
                <div class="col-12">
                    <h5 class="mb-3"><i class="fas fa-chart-bar me-2"></i> বছরভিত্তিক লাভের সারাংশ</h5>
                </div>
                <?php while($summary = mysqli_fetch_assoc($summary_result)): ?>
                <div class="col-md-3 mb-2">
                    <div class="summary-card" onclick="window.location.href='?year=<?php echo $summary['year']; ?>'">
                        <h4 class="mb-0"><?php echo $summary['year']; ?></h4>
                        <h5 class="text-success mt-2"><?php echo number_format($summary['total']); ?> টাকা</h5>
                        <small><?php echo $summary['count']; ?>টি এন্ট্রি</small>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <?php endif; ?>
            
            <!-- ফিল্টার -->
            <div class="filter-card mb-4">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">সাল নির্বাচন করুন</label>
                        <select name="year" class="form-control">
                            <option value="">সব বছর</option>
                            <option value="2024" <?php echo $year=='2024'?'selected':''; ?>>২০২৪</option>
                            <option value="2025" <?php echo $year=='2025'?'selected':''; ?>>২০২৫</option>
                            <option value="2026" <?php echo $year=='2026'?'selected':''; ?>>২০২৬</option>
                            <option value="2027" <?php echo $year=='2027'?'selected':''; ?>>২০২৭</option>
                            <option value="2028" <?php echo $year=='2028'?'selected':''; ?>>২০২৮</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">ফিল্টার করুন</button>
                        <a href="profit_list.php" class="btn btn-secondary ms-2">রিসেট</a>
                    </div>
                </form>
            </div>
            
            <!-- মোট লাভ -->
            <div class="total-card">
                <i class="fas fa-chart-line fa-2x mb-2"></i>
                <h4>মোট লাভ</h4>
                <h2><?php echo number_format($total_amount); ?> টাকা</h2>
                <?php if($year != ''): ?>
                <p class="mb-0"><?php echo $year; ?> সাল</p>
                <?php endif; ?>
            </div>
            
            <!-- টেবিল -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th width="5%">#</th>
                            <th>সাল</th>
                            <th>লাভের পরিমাণ (টাকা)</th>
                            <th>তারিখ</th>
                            <th>বিবরণ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $sl = 1;
                        if(mysqli_num_rows($result) > 0):
                            while($row = mysqli_fetch_assoc($result)):
                        ?>
                        <tr>
                            <td><?php echo $sl++; ?></td>
                            <td><strong><?php echo $row['year']; ?></strong></td>
                            <td><span class="badge bg-success"><?php echo number_format($row['amount']); ?> টাকা</span></td>
                            <td><?php echo date('d/m/Y', strtotime($row['date'])); ?></td>
                            <td><?php echo $row['description'] ?: '—'; ?></td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-2 d-block"></i>
                                <h5>কোনো লাভের তথ্য পাওয়া যায়নি</h5>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- শুধু অ্যাডমিন -->
            <?php if($user_role == 'admin'): ?>
            <div class="text-center mt-4">
                <a href="profit_add.php" class="btn btn-success btn-lg">
                    <i class="fas fa-plus-circle"></i> নতুন লাভ যোগ করুন
                </a>
            </div>
            <?php endif; ?>
            
        </div>
    </div>
</div>
</body>
</html>
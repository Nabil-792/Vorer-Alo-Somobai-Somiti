<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'config.php';

$user_role = $_SESSION['user_role'];

if($user_role != 'admin') {
    header("Location: dashboard.php");
    exit();
}

if(isset($_POST['save_profit'])) {
    $year = $_POST['year'];
    $amount = $_POST['amount'];
    $date = $_POST['date'];
    $description = $_POST['description'];
    
    // চেক করা আগে থেকে আছে কিনা
    $check = mysqli_query($conn, "SELECT id FROM yearly_profit WHERE year = '$year'");
    if(mysqli_num_rows($check) > 0) {
        $error = "এই বছরের লাভ already আছে! আপনি এডিট করুন।";
    } else {
        $insert = "INSERT INTO yearly_profit (year, amount, date, description) 
                   VALUES ('$year', '$amount', '$date', '$description')";
        if(mysqli_query($conn, $insert)) {
            $success = "✅ বার্ষিক লাভ সংরক্ষণ করা হয়েছে!";
        } else {
            $error = "❌ সমস্যা হয়েছে: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>বার্ষিক লাভ যোগ - ভোরের আলো সমবায় সমিতি</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        .card-custom {
            border-radius: 25px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card card-custom">
        <div class="card-header bg-primary text-white">
            <h3><i class="fas fa-chart-line me-2"></i> বার্ষিক লাভ যোগ করুন</h3>
            <small>শুধু প্রশাসক এই পেজ ব্যবহার করতে পারবেন</small>
        </div>
        <div class="card-body">
            <?php if(isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if(isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">সাল নির্বাচন করুন</label>
                    <select name="year" class="form-control" required>
                        <option value="2024">২০২৪</option>
                        <option value="2025">২০২৫</option>
                        <option value="2026" selected>২০২৬</option>
                        <option value="2027">২০২৭</option>
                        <option value="2028">২০২৮</option>
                        <option value="2029">২০২৯</option>
                        <option value="2030">২০৩০</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">লাভের পরিমাণ (টাকা)</label>
                    <input type="number" name="amount" class="form-control" placeholder="যেমন: 50000" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">তারিখ</label>
                    <input type="date" name="date" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">বিবরণ (ঐচ্ছিক)</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="যেমন: ব্যাংক সুদ, দোকান ভাড়া ইত্যাদি"></textarea>
                </div>
                <button type="submit" name="save_profit" class="btn btn-primary">
                    <i class="fas fa-save"></i> লাভ সংরক্ষণ করুন
                </button>
                <a href="profit_list.php" class="btn btn-success">
                    <i class="fas fa-list"></i> লাভের তালিকা দেখুন
                </a>
                <a href="dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> ড্যাশবোর্ড
                </a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
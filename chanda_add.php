<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'config.php';

$user_role = $_SESSION['user_role'];

// শুধু অ্যাডমিন চাঁদা যোগ করতে পারবে
if($user_role != 'admin') {
    header("Location: dashboard.php");
    exit();
}

// চাঁদা সংরক্ষণ
if(isset($_POST['save_chanda'])) {
    $member_id = $_POST['member_id'];
    $amount = $_POST['amount'];
    $date = $_POST['date'];
    $month = $_POST['month'];
    $year = $_POST['year'];
    $remarks = $_POST['remarks'];
    
    $insert = "INSERT INTO chanda (member_id, amount, date, month, year, remarks) 
               VALUES ('$member_id', '$amount', '$date', '$month', '$year', '$remarks')";
    if(mysqli_query($conn, $insert)) {
        $success = "✅ চাঁদা সংরক্ষণ করা হয়েছে!";
    } else {
        $error = "❌ সমস্যা হয়েছে: " . mysqli_error($conn);
    }
}

$members = mysqli_query($conn, "SELECT id, name FROM members ORDER BY name");
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>চাঁদা যোগ করুন - ভোরের আলো সমবায় সমিতি</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4>💰 মাসিক চাঁদা যোগ করুন</h4>
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
                    <label>সদস্য নির্বাচন করুন</label>
                    <select name="member_id" class="form-control" required>
                        <option value="">-- সদস্য নির্বাচন করুন --</option>
                        <?php while($row = mysqli_fetch_assoc($members)): ?>
                        <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label>টাকার পরিমাণ (টাকা)</label>
                    <input type="number" name="amount" class="form-control" required>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <label>মাস</label>
                        <select name="month" class="form-control" required>
                            <option value="জানুয়ারি">জানুয়ারি</option>
                            <option value="ফেব্রুয়ারি">ফেব্রুয়ারি</option>
                            <option value="মার্চ">মার্চ</option>
                            <option value="এপ্রিল">এপ্রিল</option>
                            <option value="মে">মে</option>
                            <option value="জুন">জুন</option>
                            <option value="জুলাই">জুলাই</option>
                            <option value="আগস্ট">আগস্ট</option>
                            <option value="সেপ্টেম্বর">সেপ্টেম্বর</option>
                            <option value="অক্টোবর">অক্টোবর</option>
                            <option value="নভেম্বর">নভেম্বর</option>
                            <option value="ডিসেম্বর">ডিসেম্বর</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>সাল</label>
                        <select name="year" class="form-control" required>
                            <option value="2024">2024</option>
                            <option value="2025">2025</option>
                            <option value="2026" selected>2026</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>তারিখ</label>
                        <input type="date" name="date" class="form-control" required>
                    </div>
                </div>
                <div class="mb-3 mt-3">
                    <label>মন্তব্য (ঐচ্ছিক)</label>
                    <textarea name="remarks" class="form-control" rows="2" placeholder="যেমন: জানুয়ারি মাসের চাঁদা"></textarea>
                </div>
                <button type="submit" name="save_chanda" class="btn btn-primary">💾 চাঁদা সংরক্ষণ করুন</button>
                <a href="chanda_list.php" class="btn btn-success">📋 চাঁদার তালিকা দেখুন</a>
                <a href="dashboard.php" class="btn btn-secondary">🔙 ড্যাশবোর্ডে ফিরুন</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
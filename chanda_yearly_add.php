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

if(isset($_POST['save_chanda'])) {
    $member_id = $_POST['member_id'];
    $amount = $_POST['amount'];
    $year = $_POST['year'];
    $remarks = $_POST['remarks'];
    
    // চেক করা আগে থেকে আছে কিনা
    $check = mysqli_query($conn, "SELECT id FROM yearly_chanda WHERE member_id = $member_id AND year = '$year'");
    if(mysqli_num_rows($check) > 0) {
        $error = "এই সদস্যের জন্য এই বছর already আছে! আপনি এডিট করুন।";
    } else {
        $insert = "INSERT INTO yearly_chanda (member_id, amount, year, remarks) 
                   VALUES ('$member_id', '$amount', '$year', '$remarks')";
        if(mysqli_query($conn, $insert)) {
            $success = "✅ বার্ষিক চাঁদা সংরক্ষণ করা হয়েছে!";
        } else {
            $error = "❌ সমস্যা হয়েছে: " . mysqli_error($conn);
        }
    }
}

$members = mysqli_query($conn, "SELECT id, name FROM members ORDER BY name");
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>বার্ষিক চাঁদা যোগ - ভোরের আলো সমবায় সমিতি</title>
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
            <h3><i class="fas fa-calendar-alt me-2"></i> বার্ষিক চাঁদা যোগ করুন</h3>
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
                    <label class="form-label">সদস্য নির্বাচন করুন</label>
                    <select name="member_id" class="form-control" required>
                        <option value="">-- সদস্য নির্বাচন করুন --</option>
                        <?php while($row = mysqli_fetch_assoc($members)): ?>
                        <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">টাকার পরিমাণ (টাকা)</label>
                    <input type="number" name="amount" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">সাল নির্বাচন করুন</label>
                    <select name="year" class="form-control" required>
                        <option value="2024">২০২৪</option>
                        <option value="2025">২০২৫</option>
                        <option value="2026" selected>২০২৬</option>
                        <option value="2027">২০২৭</option>
                        <option value="2028">২০২৮</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">মন্তব্য (ঐচ্ছিক)</label>
                    <textarea name="remarks" class="form-control" rows="2" placeholder="যেমন: বার্ষিক চাঁদা"></textarea>
                </div>
                <button type="submit" name="save_chanda" class="btn btn-primary">
                    <i class="fas fa-save"></i> সংরক্ষণ করুন
                </button>
                <a href="chanda_yearly_list.php" class="btn btn-success">
                    <i class="fas fa-list"></i> তালিকা দেখুন
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
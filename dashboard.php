<?php
include 'config.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_role = $_SESSION['user_role'];
$user_name = $_SESSION['user_name'];

// শুধু অ্যাডমিন নতুন সদস্য যোগ করতে পারবে
if(isset($_POST['add_member']) && $user_role == 'admin') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $password = md5($_POST['password']);
    $role = $_POST['role'];
    
    $check_email = mysqli_query($conn, "SELECT id FROM members WHERE email='$email'");
    if(mysqli_num_rows($check_email) > 0) {
        $error = "এই ইমেইল already আছে!";
    } else {
        $insert = "INSERT INTO members (name, email, phone, address, password, role) 
                   VALUES ('$name', '$email', '$phone', '$address', '$password', '$role')";
        if(mysqli_query($conn, $insert)) {
            $success = "✅ নতুন সদস্য যুক্ত হয়েছে!";
        } else {
            $error = "❌ সমস্যা হয়েছে: " . mysqli_error($conn);
        }
    }
}

// শুধু অ্যাডমিন সদস্য ডিলিট করতে পারবে
if(isset($_GET['delete']) && $user_role == 'admin') {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM members WHERE id=$id");
    header("Location: dashboard.php");
}

$members = mysqli_query($conn, "SELECT * FROM members ORDER BY id DESC");
$total_members = mysqli_num_rows($members);

// মোট চাঁদার পরিমাণ
$total_chanda = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) as total FROM chanda"))['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ভোরের আলো সমবায় সমিতি</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-stats {
            border-radius: 15px;
            transition: transform 0.3s;
        }
        .card-stats:hover {
            transform: translateY(-5px);
        }
        .nav-link:hover {
            background: rgba(255,255,255,0.2);
            border-radius: 10px;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- সাইডবার -->
        <div class="col-md-2 p-0">
            <div class="sidebar text-white p-3">
                <h4 class="text-center mb-4">🏛️ ভোরের আলো সমবায় সমিতি</h4>
                <hr>
                <div class="text-center mb-4">
                    <i class="fas fa-user-circle fa-3x"></i>
                    <h6 class="mt-2"><?php echo $user_name; ?></h6>
                    <span class="badge bg-light text-dark">
                        <?php echo $user_role == 'admin' ? 'প্রশাসক' : 'সদস্য'; ?>
                    </span>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item mb-2">
                        <a href="dashboard.php" class="nav-link text-white">
                            <i class="fas fa-tachometer-alt"></i> ড্যাশবোর্ড
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="chanda_list.php" class="nav-link text-white">
                            <i class="fas fa-money-bill-wave"></i> মাসিক চাঁদা
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="logout.php" class="nav-link text-white">
                            <i class="fas fa-sign-out-alt"></i> লগআউট
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- কন্টেন্ট -->
        <div class="col-md-10 p-4">
            <h2>স্বাগতম, <?php echo $user_name; ?> 👋</h2>
            <p class="text-muted">
                <?php if($user_role == 'admin'): ?>
                    আপনি প্রশাসক। সবকিছু দেখতে ও পরিচালনা করতে পারবেন।
                <?php else: ?>
                    আপনি সদস্য। শুধুমাত্র তথ্য দেখতে পারবেন, পরিবর্তন করতে পারবেন না।
                <?php endif; ?>
            </p>

            <!-- স্ট্যাটাস কার্ড -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card card-stats bg-primary text-white">
                        <div class="card-body">
                            <h5>মোট সদস্য</h5>
                            <h2><?php echo $total_members; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-stats bg-info text-white">
                        <div class="card-body">
                            <h5>মোট চাঁদা</h5>
                            <h2><?php echo number_format($total_chanda); ?> টাকা</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- নোটিফিকেশন -->
            <?php if(isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if(isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- নতুন সদস্য যোগ করার ফর্ম (শুধু অ্যাডমিন দেখতে পাবে) -->
            <?php if($user_role == 'admin'): ?>
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-user-plus"></i> নতুন সদস্য যুক্ত করুন
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <input type="text" name="name" class="form-control" placeholder="পূর্ণ নাম" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <input type="email" name="email" class="form-control" placeholder="ইমেইল" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <input type="text" name="phone" class="form-control" placeholder="ফোন নম্বর">
                            </div>
                            <div class="col-md-6 mb-2">
                                <input type="password" name="password" class="form-control" placeholder="পাসওয়ার্ড" required>
                            </div>
                            <div class="col-md-12 mb-2">
                                <textarea name="address" class="form-control" placeholder="ঠিকানা" rows="2"></textarea>
                            </div>
                            <div class="col-md-6 mb-2">
                                <select name="role" class="form-control">
                                    <option value="member">সাধারণ সদস্য</option>
                                    <option value="admin">প্রশাসক</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <button type="submit" name="add_member" class="btn btn-success">
                                    <i class="fas fa-save"></i> সদস্য সংরক্ষণ করুন
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            <!-- সদস্য তালিকা -->
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <i class="fas fa-list"></i> সকল সদস্যের তথ্য
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>নাম</th>
                                    <th>ইমেইল</th>
                                    <th>ফোন</th>
                                    <th>ঠিকানা</th>
                                    <th>ভূমিকা</th>
                                    <?php if($user_role == 'admin'): ?>
                                    <th>একশন</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = mysqli_fetch_assoc($members)): ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><?php echo $row['email']; ?></td>
                                    <td><?php echo $row['phone']; ?></td>
                                    <td><?php echo htmlspecialchars($row['address']); ?></td>
                                    <td>
                                        <span class="badge <?php echo $row['role'] == 'admin' ? 'bg-danger' : 'bg-info'; ?>">
                                            <?php echo $row['role'] == 'admin' ? 'প্রশাসক' : 'সদস্য'; ?>
                                        </span>
                                    </td>
                                    <?php if($user_role == 'admin'): ?>
                                    <td>
                                        <a href="?delete=<?php echo $row['id']; ?>" 
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('আপনি কি নিশ্চিত?');">
                                            <i class="fas fa-trash"></i> ডিলিট
                                        </a>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- সদস্যদের জন্য নোট -->
            <?php if($user_role != 'admin'): ?>
            <div class="alert alert-info mt-3">
                <i class="fas fa-info-circle"></i> <strong>বিঃদ্রঃ:</strong> আপনি সদস্য হিসেবে লগইন করেছেন। শুধুমাত্র তথ্য দেখতে পারবেন। কোনো তথ্য পরিবর্তন বা সংরক্ষণ করতে পারবেন না।
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
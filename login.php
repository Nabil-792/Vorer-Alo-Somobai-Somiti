<?php
session_start();
if(isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>সোমিটি সদস্য লগইন</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .card {
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card">
                <div class="card-header bg-white text-center pt-4">
                    <h3>🏛️ সোমিটি পোর্টাল</h3>
                    <p class="text-muted">সদস্যদের জন্য প্রাইভেট ড্যাশবোর্ড</p>
                </div>
                <div class="card-body p-4">
                    <?php if(isset($_GET['error'])): ?>
                        <div class="alert alert-danger">❌ ভুল ইমেইল বা পাসওয়ার্ড</div>
                    <?php endif; ?>
                    <form method="POST" action="auth.php">
                        <div class="mb-3">
                            <label class="form-label">📧 ইমেইল ঠিকানা</label>
                            <input type="email" name="email" class="form-control" required placeholder="admin@example.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">🔒 পাসওয়ার্ড</label>
                            <input type="password" name="password" class="form-control" required placeholder="পাসওয়ার্ড">
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2">🔓 লগইন করুন</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
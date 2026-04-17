# 🏛️ ভোরের আলো সমবায় সমিতি

একটি সম্পূর্ণ ওয়েবভিত্তিক সমিতি ব্যবস্থাপনা সিস্টেম।

## বৈশিষ্ট্যসমূহ
- 👥 সদস্য ব্যবস্থাপনা
- 🔐 লগইন সিস্টেম (অ্যাডমিন/সদস্য)
- 💰 মাসিক চাঁদা সংগ্রহ
- 📅 বার্ষিক চাঁদা সংগ্রহ
- 🔍 মাস ও সাল অনুযায়ী ফিল্টার
- 📊 ড্যাশবোর্ডে পরিসংখ্যান
- 👑 পদবি ব্যবস্থাপনা

## প্রযুক্তি
- PHP, MySQL, Bootstrap 5, Font Awesome

## ইনস্টলেশন

1. XAMPP ইনস্টল করুন
2. ফোল্ডার কপি করুন: `C:\xampp\htdocs\somiti_portal\`
3. phpMyAdmin-এ ডাটাবেজ তৈরি করুন
4. `config.php` ফাইল তৈরি করুন
5. চালু করুন: `http://localhost/somiti_portal/login.php`

## ডাটাবেজ SQL

```sql
CREATE DATABASE somiti_db;
USE somiti_db;

CREATE TABLE members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    position VARCHAR(50) DEFAULT 'সদস্য',
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(15),
    address TEXT,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'member') DEFAULT 'member'
);

CREATE TABLE chanda (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    date DATE NOT NULL,
    month VARCHAR(20) NOT NULL,
    year VARCHAR(4) NOT NULL,
    remarks TEXT
);

CREATE TABLE yearly_chanda (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    year VARCHAR(4) NOT NULL,
    date DATE NOT NULL,
    remarks TEXT
);

INSERT INTO members (name, email, password, role) 
VALUES ('প্রশাসক', 'admin@example.com', MD5('admin123'), 'admin');

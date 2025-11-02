CREATE DATABASE IF NOT EXISTS stk_admin;
USE stk_admin;

CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    full_name VARCHAR(300) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS donations (
    donate_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    donor_fullname VARCHAR(300),
    donor_email VARCHAR(100),
    donor_contact_no VARCHAR(15),
    donation_categ ENUM('Cash', 'Clothes', 'Goods', 'Specified') NOT NULL,
    donation_specified VARCHAR(100),
    donation_type ENUM('Parish Donation', 'Tithes', 'Charity') NOT NULL,
    mode_of_payment ENUM('Onsite', 'Gcash', 'Bank Transfer') NOT NULL,
    proof_of_donation VARCHAR(300),
    donation_amount DECIMAL(10, 2),
    donation_status ENUM('pending', 'verified', 'unverified') DEFAULT 'pending',
    date_posted TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    CHECK ((donation_categ != 'Specified') OR (donation_specified IS NOT NULL))
);



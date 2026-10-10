-- ============================================================
-- Remedy Investment — Full Database Schema
-- MySQL 8.x
-- ============================================================

CREATE DATABASE IF NOT EXISTS remedy_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE remedy_db;

-- ─── SETTINGS ───────────────────────────────────────────────
CREATE TABLE settings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(100) NOT NULL UNIQUE,
    `value` TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO settings (`key`, `value`) VALUES
('site_name', 'Remedy Investment'),
('site_email', 'support@remedyinvestment.com'),
('usdt_trc20_wallet', 'TYour_USDT_TRC20_WalletAddress_Here'),
('usdt_erc20_wallet', '0xYour_USDT_ERC20_WalletAddress_Here'),
('btc_wallet', 'YourBTCWalletAddressHere'),
('min_deposit', '10.00'),
('min_withdrawal', '5.00'),
('referral_bonus_percent', '5.00'),
('withdrawal_fee_percent', '2.00'),
('site_status', 'active'),
('maintenance_message', 'Site is under maintenance. Please check back soon.');

-- ─── USERS ──────────────────────────────────────────────────
CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(30),
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(80),
    last_name VARCHAR(80),
    role ENUM('user','admin') DEFAULT 'user',
    status ENUM('active','banned','pending') DEFAULT 'active',
    balance DECIMAL(18,2) DEFAULT 0.00,
    referral_code VARCHAR(20) NOT NULL UNIQUE,
    referred_by INT UNSIGNED DEFAULT NULL,
    avatar VARCHAR(255) DEFAULT NULL,
    email_verified_at TIMESTAMP NULL,
    last_login_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (referred_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Default admin account (password: Admin@1234)
INSERT INTO users (username, email, password_hash, first_name, last_name, role, status, referral_code)
VALUES ('admin', 'admin@remedy.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'User', 'admin', 'active', 'ADMIN0001');

-- ─── PASSWORD RESETS ────────────────────────────────────────
CREATE TABLE password_resets (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL,
    token VARCHAR(100) NOT NULL UNIQUE,
    expires_at TIMESTAMP NOT NULL,
    used TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_token (token),
    INDEX idx_email (email)
);

-- ─── TASKS ──────────────────────────────────────────────────
CREATE TABLE tasks (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    instructions TEXT,
    type ENUM('rate','watch','order','survey','social','other') DEFAULT 'other',
    reward DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    thumbnail VARCHAR(255),
    target_url VARCHAR(500),
    max_submissions INT UNSIGNED DEFAULT NULL COMMENT 'NULL = unlimited',
    total_submissions INT UNSIGNED DEFAULT 0,
    status ENUM('active','paused','completed') DEFAULT 'active',
    created_by INT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Sample tasks
INSERT INTO tasks (title, description, instructions, type, reward, status, created_by) VALUES
('Rate Our Product', 'Visit the product page and leave a 5-star rating.', 'Go to the product page, click the star rating, then submit a screenshot of your completed rating.', 'rate', 12.50, 'active', 1),
('Watch Our Promo Video', 'Watch our full promotional video and submit proof.', 'Watch the full video (do not skip), take a screenshot showing 100% completion.', 'watch', 8.00, 'active', 1),
('Complete Purchase Order', 'Place an order on our partner store. Order will be reimbursed.', 'Place the order, screenshot the confirmation page with your order number visible.', 'order', 25.00, 'active', 1),
('Follow Our Social Media', 'Follow our official social accounts and like 3 recent posts.', 'Follow all accounts listed, like 3 posts each, screenshot your follows.', 'social', 5.00, 'active', 1),
('Fill Customer Survey', 'Complete a short 5-minute survey about our services.', 'Complete all questions honestly. Submit the final confirmation page as proof.', 'survey', 10.00, 'active', 1);

-- ─── TASK SUBMISSIONS ───────────────────────────────────────
CREATE TABLE task_submissions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    task_id INT UNSIGNED NOT NULL,
    proof_type ENUM('screenshot','url','text') DEFAULT 'screenshot',
    proof_value TEXT NOT NULL COMMENT 'file path, URL, or text proof',
    notes TEXT,
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    reviewed_by INT UNSIGNED DEFAULT NULL,
    reviewed_at TIMESTAMP NULL,
    rejection_reason TEXT,
    reward_credited DECIMAL(10,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE KEY unique_user_task (user_id, task_id)
);

-- ─── TRANSACTIONS ────────────────────────────────────────────
CREATE TABLE transactions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    type ENUM('deposit','withdrawal','task_reward','referral_bonus','manual_credit','manual_debit') NOT NULL,
    amount DECIMAL(18,2) NOT NULL,
    fee DECIMAL(18,2) DEFAULT 0.00,
    net_amount DECIMAL(18,2) NOT NULL,
    balance_before DECIMAL(18,2) DEFAULT 0.00,
    balance_after DECIMAL(18,2) DEFAULT 0.00,
    currency VARCHAR(20) DEFAULT 'USD',
    crypto_type VARCHAR(20) COMMENT 'BTC, USDT-TRC20, USDT-ERC20',
    crypto_address VARCHAR(255),
    tx_hash VARCHAR(255),
    proof_file VARCHAR(255),
    status ENUM('pending','approved','rejected','cancelled') DEFAULT 'pending',
    reference VARCHAR(50) NOT NULL UNIQUE,
    note TEXT,
    reviewed_by INT UNSIGNED DEFAULT NULL,
    reviewed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user_type (user_id, type),
    INDEX idx_status (status),
    INDEX idx_reference (reference)
);

-- ─── REFERRALS ───────────────────────────────────────────────
CREATE TABLE referrals (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    referrer_id INT UNSIGNED NOT NULL COMMENT 'user who shared the code',
    referee_id INT UNSIGNED NOT NULL COMMENT 'user who signed up with code',
    bonus_amount DECIMAL(10,2) DEFAULT 0.00,
    bonus_paid TINYINT(1) DEFAULT 0,
    bonus_paid_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (referrer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (referee_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_referral (referrer_id, referee_id)
);

-- ─── NOTIFICATIONS ────────────────────────────────────────────
CREATE TABLE notifications (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('info','success','warning','error') DEFAULT 'info',
    is_read TINYINT(1) DEFAULT 0,
    link VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_read (user_id, is_read)
);

-- ─── SESSIONS (optional - if using DB sessions) ─────────────
-- Using PHP file sessions by default, this is a reference only
-- CREATE TABLE sessions (
--     id VARCHAR(128) PRIMARY KEY,
--     user_id INT UNSIGNED,
--     ip_address VARCHAR(45),
--     user_agent TEXT,
--     payload TEXT,
--     last_activity TIMESTAMP
-- );

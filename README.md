# Remedy Investment — High-Yield Task & Crypto Platform

A full-stack PHP 8 & MySQL web application replicated from the reference specification at `https://task.sbrbot.sbs/login`.

[![Deploy on Railway](https://railway.app/button.svg)](https://railway.app/template)

---

## ⚡ Zero-Stress 1-Click Railway Deployment

This project is pre-configured for automated Railway deployment:
- **Auto-Configuring Port:** Dynamic `$PORT` detection for Railway.
- **Auto-Detecting MySQL:** Automatically recognizes Railway's `MYSQL_URL` / `MYSQLHOST` environment variables.
- **Auto-Seeding Database:** Automatically executes `database/schema.sql` on first boot (no manual SQL importing needed!).
- **GitHub Auto-Deploy:** Every push to this repository automatically triggers a Railway build and deployment.

### How to Deploy on Railway:
1. Open [railway.app](https://railway.app) and click **"New Project"**.
2. Select **"Deploy from GitHub repo"** and choose `rabbithole385/Remedy`.
3. In the project canvas, click **"Add a Service"** &rarr; **"Database"** &rarr; **"Add MySQL"**.
4. Railway will automatically link MySQL and spin up the container!
5. In your web service settings, click **"Generate Domain"** under **Networking**.

> **Note:** If Railway prompts that your trial is expired, upgrade to the Hobby plan ($5/mo) in your [Railway Account Settings](https://railway.app/account) to unblock service creation.


## 🚀 Key Features

### 1. User Authentication & Security
- User registration with validation and referral attribution.
- Login with session fixation protection & password hashing (`BCRYPT`).
- Password recovery via tokens.
- CSRF token validation on all form posts.
- Role-based access control (`user` vs `admin`).
- Account suspension / ban mechanism.

### 2. Earning Task System
- Task discovery with reward preview and categories (Rate, Watch, Order, Survey, Social).
- Step-by-step instructions & direct target links.
- Proof submission with file upload (JPEG/PNG/WebP screenshots up to 5MB) or URL/text verification.
- Admin verification queue: One-click approval credits user balance; rejection notifies user with reason.

### 3. Financial & Crypto System
- Multi-currency crypto deposit support: **USDT (TRC-20)**, **USDT (ERC-20)**, and **Bitcoin (BTC)**.
- Deposit verification queue: Users submit TX hash and receipt; admins review and confirm.
- Withdrawal request processing with dynamic fee calculations (e.g. 2%) and wallet address checking.
- Automated balance refund on rejected withdrawals.
- Full ledger audit log with unique transaction references.

### 4. Referral / Affiliate Program
- Unique referral links (`/register?ref=CODE`) and referral codes with 1-click clipboard copy.
- Configurable referral bonus % credited upon referee's first deposit.
- Downline members table with earnings tracking.

### 5. SuperAdmin Console (`/admin`)
- Overview stats: Total users, total deposits, total payouts, pending tasks.
- User management: Search, toggle active/banned status, manual balance credit/debit with audit notes.
- Task CRUD: Create, edit, pause, and delete tasks.
- Task submissions review with full screenshot lightbox preview.
- Crypto deposit & withdrawal queues.
- System settings: Update receiving crypto wallet addresses, fees, and minimum thresholds.

---

## 🛠️ Technology Stack
- **Backend:** Native PHP 8.x (No bloated dependencies, ultra-fast and easily hostable on Apache, Nginx, cPanel, or VPS)
- **Database:** MySQL 8.x (PDO with prepared statements)
- **Frontend:** Tailwind CSS (Modern dark glassmorphism), Alpine.js, Lucide Icons, Chart.js, Google Fonts (Inter)

---

## 📦 Installation & Setup

### 1. Database Setup
1. Create a MySQL database named `remedy_db` (or custom name).
2. Import `database/schema.sql`.
3. Update database credentials in `src/config/config.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'remedy_db');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```

### 2. Default Admin Account
- **URL:** `/login` or `/admin`
- **Email / Username:** `admin@remedy.com` or `admin`
- **Password:** `Admin@1234`

### 3. Running Locally
Using PHP built-in web server:
```bash
php -S localhost:8000 -t public
```
Or open in XAMPP/WAMP/Laragon:
```
http://localhost/Remedy/public
```

---

## 📂 Project Structure
```
Remedy/
├── public/                  ← Web root
│   ├── index.php            ← Master router
│   ├── setup_db.php         ← Automated installer / diagnostic page
│   ├── 404.php              ← Custom 404 page
│   ├── .htaccess            ← Public URL rewrite rules
│   └── uploads/             ← Uploaded screenshots & receipts
├── src/
│   ├── config/              ← DB connection & application configuration
│   ├── helpers/             ← Auth, CSRF, Flash, Redirect, Crypto helpers
│   ├── models/              ← User, Task, Transaction, Referral, Notification, Setting
│   └── controllers/         ← Auth, Dashboard, Task, Finance, Referral, Profile, Admin
├── views/
│   ├── layouts/             ← Guest, App, and Admin master layouts
│   ├── auth/                ← Login, Register, Forgot & Reset Password
│   ├── dashboard/           ← Overview, Tasks, Wallet, Deposit, Withdraw, Referral, Profile
│   └── admin/               ← Users, Tasks, Submissions, Deposits, Withdrawals, Settings
├── database/
│   └── schema.sql           ← Complete MySQL schema with sample seed data
└── .htaccess                ← Root URL rewrite rules
```

# 📦 StockSense — Product Inventory Manager
**Stack:** HTML · CSS · JavaScript · PHP · MySQL (XAMPP)**

---

## 📁 Project Structure

```
inventory_app/
├── index.html              ← Main frontend (login, register, dashboard)
├── config/
│   └── db.php              ← Database connection
├── api/
│   ├── auth.php            ← Register / Login / Logout API
│   └── products.php        ← CRUD API for products
└── database/
    └── inventory_db.sql    ← SQL to create database & tables
```

---

## ✅ STEP 1 — Install & Start XAMPP

1. Download XAMPP from: https://www.apachefriends.org
2. Install and open the **XAMPP Control Panel**
3. Click **Start** next to **Apache**
4. Click **Start** next to **MySQL**
5. Both should show green status bars

---

## ✅ STEP 2 — Create the Database in phpMyAdmin

1. Open your browser and go to: **http://localhost/phpmyadmin**
2. Click **"New"** in the left sidebar to create a database
3. Type `inventory_db` → click **Create**
4. Click on `inventory_db` in the sidebar
5. Click the **SQL** tab at the top
6. Open the file `database/inventory_db.sql` from this project
7. Copy **all** the SQL code and paste it into the SQL box
8. Click **Go** to run it
9. You should now see two tables: `users` and `products` ✅

---

## ✅ STEP 3 — Copy Project to XAMPP htdocs

1. Open File Explorer / Finder
2. Navigate to your XAMPP installation folder:
   - **Windows:** `C:\xampp\htdocs\`
   - **Mac:** `/Applications/XAMPP/htdocs/`
3. Copy the entire `inventory_app` folder into `htdocs`
4. Final path should be: `C:\xampp\htdocs\inventory_app\`

---

## ✅ STEP 4 — Open in VS Code

1. Open **VS Code**
2. Click **File → Open Folder**
3. Navigate to `C:\xampp\htdocs\inventory_app` and open it
4. You'll see all files in the Explorer sidebar

**Recommended VS Code Extensions:**
- **PHP Intelephense** — PHP code intelligence
- **Live Server** *(note: use localhost, not Live Server for PHP)*
- **GitLens** — Enhanced GitHub integration
- **Prettier** — Code formatting

---

## ✅ STEP 5 — Test the App

1. Open your browser
2. Go to: **http://localhost/inventory_app**
3. You should see the StockSense login page
4. Click **Register** to create your first account
5. Fill in your name, email, and password
6. Click **Sign In** with your credentials
7. You're in the dashboard! Click **+ Add Product** to add inventory items

---

## ✅ STEP 6 — Push to GitHub

### 6a. Create a Repository on GitHub
1. Go to **https://github.com** and sign in
2. Click **"+"** → **New repository**
3. Name it: `inventory-app` (or your preference)
4. Leave it **Public** or **Private**
5. Do NOT initialize with README (we already have one)
6. Click **Create repository**

### 6b. Initialize Git in VS Code
1. In VS Code, open the **Terminal** (`Ctrl + `` ` ``)
2. Run these commands one by one:

```bash
# 1. Initialize git repository
git init

# 2. Add all files to staging
git add .

# 3. First commit
git commit -m "Initial commit: StockSense inventory app"

# 4. Add your GitHub remote (replace YOUR_USERNAME and YOUR_REPO)
git remote add origin https://github.com/YOUR_USERNAME/YOUR_REPO.git

# 5. Push to GitHub
git branch -M main
git push -u origin main
```

3. A browser window may open asking you to log in to GitHub — do so
4. Refresh your GitHub repo page — all files should appear ✅

### 6c. Create a .gitignore (optional but recommended)
Create a file named `.gitignore` in the root with:
```
# Ignore sensitive config (if you put credentials here)
config/db.php

# Ignore OS files
.DS_Store
Thumbs.db
```

---

## 🔐 How Auth Works

| Step | What Happens |
|------|-------------|
| Register | Password is hashed with `password_hash()` (bcrypt) and stored in DB |
| Login | `password_verify()` checks the hash — never stores plain passwords |
| Session | PHP `$_SESSION` stores the logged-in user's ID and name |
| Auth Guard | `products.php` checks `$_SESSION['user_id']` on every request |
| Logout | `session_destroy()` clears the session |

---

## 📡 API Endpoints

### Auth API — `api/auth.php` (POST)

| Action | Body | Response |
|--------|------|----------|
| `register` | `{action, fullname, email, password}` | `{success, message}` |
| `login`    | `{action, email, password}`           | `{success, message, user}` |
| `logout`   | `{action}`                            | `{success, message}` |

### Products API — `api/products.php`

| Method | Params | Description |
|--------|--------|-------------|
| GET    | `?search=&category=` | Get all products (with stats) |
| POST   | `{name, category, quantity, price, description}` | Add product |
| PUT    | `{id, name, category, quantity, price, description}` | Update product |
| DELETE | `{id}` | Delete product |

---

## 🐛 Troubleshooting

| Problem | Fix |
|---------|-----|
| Blank page | Check Apache & MySQL are running in XAMPP |
| "Database connection failed" | Make sure `inventory_db` exists in phpMyAdmin |
| "Unauthorized" error | Session expired — log in again |
| 404 on `api/auth.php` | Make sure the folder is inside `htdocs/inventory_app/` |
| Git push asks for password | Use a GitHub Personal Access Token instead of password |

---

## 🎯 Features

- ✅ User Registration with bcrypt password hashing
- ✅ Secure Login with PHP sessions
- ✅ Add, Edit, Delete products
- ✅ Search products by name/description
- ✅ Filter by category
- ✅ Live inventory stats (total products, units, value in ₱)
- ✅ Color-coded quantity badges (low stock warning)
- ✅ Each user sees only their own inventory
- ✅ Responsive dark UI

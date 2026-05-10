<?php
// ============================================================
//  api/auth.php  — Register & Login endpoints
// ============================================================

session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/db.php';

$data   = json_decode(file_get_contents('php://input'), true);
$action = $data['action'] ?? '';

// ── REGISTER ──────────────────────────────────────────────
if ($action === 'register') {
    $fullname = trim($data['fullname'] ?? '');
    $email    = trim($data['email']    ?? '');
    $password =       $data['password'] ?? '';

    if (!$fullname || !$email || !$password) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
        exit;
    }
    if (strlen($password) < 6) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters.']);
        exit;
    }

    $db = getDB();

    // Check duplicate email
    $stmt = $db->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Email already registered.']);
        exit;
    }

    $hash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $db->prepare('INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)');
    $stmt->execute([$fullname, $email, $hash]);

    echo json_encode(['success' => true, 'message' => 'Account created successfully! Please log in.']);
    exit;
}

// ── LOGIN ─────────────────────────────────────────────────
if ($action === 'login') {
    $email    = trim($data['email']    ?? '');
    $password =       $data['password'] ?? '';

    if (!$email || !$password) {
        echo json_encode(['success' => false, 'message' => 'Email and password are required.']);
        exit;
    }

    $db   = getDB();
    $stmt = $db->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
        exit;
    }

    $_SESSION['user_id']  = $user['id'];
    $_SESSION['fullname'] = $user['fullname'];
    $_SESSION['email']    = $user['email'];

    echo json_encode([
        'success'  => true,
        'message'  => 'Login successful.',
        'user'     => [
            'id'       => $user['id'],
            'fullname' => $user['fullname'],
            'email'    => $user['email'],
        ]
    ]);
    exit;
}

// ── LOGOUT ────────────────────────────────────────────────
if ($action === 'logout') {
    session_destroy();
    echo json_encode(['success' => true, 'message' => 'Logged out.']);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Unknown action.']);

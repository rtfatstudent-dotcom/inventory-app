<?php
// ============================================================
//  api/products.php  — CRUD for products
// ============================================================

session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/db.php';

// Auth guard
if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized. Please log in.']);
    exit;
}

$userId = (int) $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];
$db     = getDB();

// ── GET all products ──────────────────────────────────────
if ($method === 'GET') {
    $search   = $_GET['search']   ?? '';
    $category = $_GET['category'] ?? '';

    $sql    = 'SELECT * FROM products WHERE user_id = ?';
    $params = [$userId];

    if ($search) {
        $sql    .= ' AND (name LIKE ? OR description LIKE ?)';
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    if ($category) {
        $sql    .= ' AND category = ?';
        $params[] = $category;
    }

    $sql .= ' ORDER BY created_at DESC';
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll();

    // Summary stats
    $totalStmt = $db->prepare('SELECT COUNT(*) as total, SUM(quantity) as totalQty, SUM(price * quantity) as totalValue FROM products WHERE user_id = ?');
    $totalStmt->execute([$userId]);
    $stats = $totalStmt->fetch();

    echo json_encode([
        'success'  => true,
        'products' => $products,
        'stats'    => $stats,
    ]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

// ── CREATE product ────────────────────────────────────────
if ($method === 'POST') {
    $name        = trim($data['name']        ?? '');
    $category    = trim($data['category']    ?? '');
    $quantity    = (int)   ($data['quantity']    ?? 0);
    $price       = (float) ($data['price']       ?? 0);
    $description = trim($data['description'] ?? '');

    if (!$name || !$category) {
        echo json_encode(['success' => false, 'message' => 'Name and category are required.']);
        exit;
    }

    $stmt = $db->prepare('INSERT INTO products (user_id, name, category, quantity, price, description) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->execute([$userId, $name, $category, $quantity, $price, $description]);

    echo json_encode(['success' => true, 'message' => 'Product added.', 'id' => $db->lastInsertId()]);
    exit;
}

// ── UPDATE product ────────────────────────────────────────
if ($method === 'PUT') {
    $id          = (int)   ($data['id']          ?? 0);
    $name        = trim($data['name']        ?? '');
    $category    = trim($data['category']    ?? '');
    $quantity    = (int)   ($data['quantity']    ?? 0);
    $price       = (float) ($data['price']       ?? 0);
    $description = trim($data['description'] ?? '');

    if (!$id || !$name || !$category) {
        echo json_encode(['success' => false, 'message' => 'ID, name and category are required.']);
        exit;
    }

    $stmt = $db->prepare('UPDATE products SET name=?, category=?, quantity=?, price=?, description=? WHERE id=? AND user_id=?');
    $stmt->execute([$name, $category, $quantity, $price, $description, $id, $userId]);

    echo json_encode(['success' => true, 'message' => 'Product updated.']);
    exit;
}

// ── DELETE product ────────────────────────────────────────
if ($method === 'DELETE') {
    $id = (int) ($data['id'] ?? 0);
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Product ID required.']);
        exit;
    }

    $stmt = $db->prepare('DELETE FROM products WHERE id = ? AND user_id = ?');
    $stmt->execute([$id, $userId]);

    echo json_encode(['success' => true, 'message' => 'Product deleted.']);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Method not supported.']);

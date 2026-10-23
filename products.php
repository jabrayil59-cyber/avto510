<?php
require_once 'config.php';

header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];
$db = getDB();

// Məhsul əlavə et
if ($method === 'POST') {
    $data        = json_decode(file_get_contents('php://input'), true);
    $name        = trim($data['name'] ?? '');
    $category_id = (int)($data['category_id'] ?? 0);
    $description = trim($data['description'] ?? '');
    $price       = (float)($data['price'] ?? 0);
    $stock       = (int)($data['stock'] ?? 0);
    $image       = trim($data['image'] ?? '');

    if ($name === '' || !$category_id) {
        echo json_encode(['success' => false, 'error' => 'Ad və kateqoriya mütləqdir']);
        exit;
    }

    $stmt = $db->prepare("INSERT INTO products (name, category_id, description, price, stock, image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $category_id, $description ?: null, $price, $stock, $image ?: null]);
    echo json_encode(['success' => true, 'id' => $db->lastInsertId(), 'message' => 'Məhsul əlavə edildi']);
}

// Bütün məhsulları gətir (kateqoriyaya görə filter mümkündür)
elseif ($method === 'GET' && empty($_GET['id'])) {
    if (!empty($_GET['category_id'])) {
        $stmt = $db->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.category_id = ? ORDER BY p.id ASC");
        $stmt->execute([(int)$_GET['category_id']]);
    } else {
        $stmt = $db->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id ASC");
    }
    echo json_encode($stmt->fetchAll());
}

// Tək məhsul gətir
elseif ($method === 'GET' && !empty($_GET['id'])) {
    $stmt = $db->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
    $stmt->execute([(int)$_GET['id']]);
    $product = $stmt->fetch();
    echo json_encode($product ?: ['error' => 'Tapılmadı']);
}

// Məhsul yenilə
elseif ($method === 'PUT') {
    $data        = json_decode(file_get_contents('php://input'), true);
    $id          = (int)($data['id'] ?? 0);
    $name        = trim($data['name'] ?? '');
    $category_id = (int)($data['category_id'] ?? 0);
    $description = trim($data['description'] ?? '');
    $price       = (float)($data['price'] ?? 0);
    $stock       = (int)($data['stock'] ?? 0);
    $image       = trim($data['image'] ?? '');

    if (!$id || $name === '' || !$category_id) {
        echo json_encode(['success' => false, 'error' => 'ID, ad və kateqoriya mütləqdir']);
        exit;
    }

    $stmt = $db->prepare("UPDATE products SET name=?, category_id=?, description=?, price=?, stock=?, image=? WHERE id=?");
    $stmt->execute([$name, $category_id, $description ?: null, $price, $stock, $image ?: null, $id]);
    echo json_encode(['success' => true, 'message' => 'Məhsul yeniləndi']);
}

// Məhsul sil
elseif ($method === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = (int)($data['id'] ?? 0);
    if (!$id) {
        echo json_encode(['success' => false, 'error' => 'ID çatışmır']);
        exit;
    }
    $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(['success' => true, 'message' => 'Məhsul silindi']);
}

else {
    echo json_encode(['error' => 'Bilinməyən sorğu']);
}
?>

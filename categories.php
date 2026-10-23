<?php
require_once 'config.php';

header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];
$db = getDB();

// Kateqoriya əlavə et
if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $name      = trim($data['name'] ?? '');
    $parent_id = !empty($data['parent_id']) ? (int)$data['parent_id'] : null;
    $image     = trim($data['image'] ?? '');

    if ($name === '') {
        echo json_encode(['success' => false, 'error' => 'Kateqoriya adı boş ola bilməz']);
        exit;
    }

    $stmt = $db->prepare("INSERT INTO categories (name, parent_id, image) VALUES (?, ?, ?)");
    $stmt->execute([$name, $parent_id, $image ?: null]);
    echo json_encode(['success' => true, 'id' => $db->lastInsertId(), 'message' => 'Kateqoriya əlavə edildi']);
}

// Bütün kateqoriyaları gətir
elseif ($method === 'GET' && empty($_GET['id'])) {
    $stmt = $db->query("SELECT * FROM categories ORDER BY id ASC");
    echo json_encode($stmt->fetchAll());
}

// Tək kateqoriya gətir
elseif ($method === 'GET' && !empty($_GET['id'])) {
    $stmt = $db->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([(int)$_GET['id']]);
    $cat = $stmt->fetch();
    echo json_encode($cat ?: ['error' => 'Tapılmadı']);
}

// Kateqoriya yenilə
elseif ($method === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id        = (int)($data['id'] ?? 0);
    $name      = trim($data['name'] ?? '');
    $parent_id = !empty($data['parent_id']) ? (int)$data['parent_id'] : null;
    $image     = trim($data['image'] ?? '');

    if (!$id || $name === '') {
        echo json_encode(['success' => false, 'error' => 'ID və ya ad çatışmır']);
        exit;
    }

    $stmt = $db->prepare("UPDATE categories SET name=?, parent_id=?, image=? WHERE id=?");
    $stmt->execute([$name, $parent_id, $image ?: null, $id]);
    echo json_encode(['success' => true, 'message' => 'Kateqoriya yeniləndi']);
}

// Kateqoriya sil
elseif ($method === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = (int)($data['id'] ?? 0);
    if (!$id) {
        echo json_encode(['success' => false, 'error' => 'ID çatışmır']);
        exit;
    }
    $stmt = $db->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(['success' => true, 'message' => 'Kateqoriya silindi']);
}

else {
    echo json_encode(['error' => 'Bilinməyən sorğu']);
}
?>

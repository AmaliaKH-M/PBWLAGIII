<?php
require_once '../config/kosmarket_db.php';
require_once '../classes/Product.php';

header('Content-Type: application/json');

if (!isset($_GET['q']) || empty(trim($_GET['q']))) {
    echo json_encode([]);
    exit;
}

$query = trim($_GET['q']);

try {
    $database = new Database();
    $db = $database->getConnection();
    $product = new Product($db);
    
    // Search products and categories
    $sql = "SELECT DISTINCT p.judul as title, 'product' as type, p.id_produk as id
            FROM produk p 
            WHERE p.status = 'tersedia' 
            AND (p.judul LIKE ? OR p.deskripsi LIKE ?)
            
            UNION
            
            SELECT DISTINCT k.nama_kategori as title, 'category' as type, k.id_kategori as id
            FROM kategori k 
            WHERE k.nama_kategori LIKE ?
            
            LIMIT 8";
    
    $stmt = $db->prepare($sql);
    $searchTerm = '%' . $query . '%';
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
    
    $suggestions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($suggestions);
    
} catch(Exception $e) {
    echo json_encode([]);
}
?>
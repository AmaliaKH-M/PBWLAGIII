<?php
// Helper functions untuk KosMarket
// File ini berisi semua function yang digunakan berulang

if (!function_exists('isLoggedIn')) {
    function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
}

if (!function_exists('formatRupiah')) {
    function formatRupiah($angka) {
        return 'Rp ' . number_format($angka, 0, ',', '.');
    }
}

if (!function_exists('getCategoryEmoji')) {
    function getCategoryEmoji($category) {
        $emojis = [
            'Elektronik' => '📱',
            'Pakaian' => '👕',
            'Buku & Alat Tulis' => '📚',
            'Furniture' => '🪑',
            'Peralatan Dapur' => '🍳',
            'Olahraga' => '⚽',
            'Kecantikan' => '💄',
            'Lainnya' => '📦'
        ];
        return $emojis[$category] ?? '📦';
    }
}

if (!function_exists('validateSTISEmail')) {
    function validateSTISEmail($email) {
        return preg_match('/^[0-9]{9}@stis\.ac\.id$/', $email);
    }
}

if (!function_exists('generateSlug')) {
    function generateSlug($text) {
        // Replace non-alphanumeric characters with hyphens
        $slug = preg_replace('/[^a-zA-Z0-9\s]/', '', $text);
        $slug = preg_replace('/\s+/', '-', trim($slug));
        return strtolower($slug);
    }
}

if (!function_exists('generateAngkatan')) {
    function generateAngkatan($email) {
        // Extract angkatan from email format: 123456789@stis.ac.id (9 digit)
        if (preg_match('/^(\d{9})@stis\.ac\.id$/', $email, $matches)) {
            return substr($matches[1], 0, 2);
        }
        return '00';
    }
}

if (!function_exists('timeAgo')) {
    function timeAgo($datetime) {
        $time = time() - strtotime($datetime);
        
        if ($time < 60) return 'Baru saja';
        if ($time < 3600) return floor($time/60) . ' menit lalu';
        if ($time < 86400) return floor($time/3600) . ' jam lalu';
        if ($time < 2592000) return floor($time/86400) . ' hari lalu';
        if ($time < 31104000) return floor($time/2592000) . ' bulan lalu';
        return floor($time/31104000) . ' tahun lalu';
    }
}
?>
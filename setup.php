<?php
header('Content-Type: application/json');

// Configuration MySQL depuis variables d'environnement
$host = getenv('DB_HOST');
$dbname = getenv('DB_NAME');
$username = getenv('DB_USER');
$password = getenv('DB_PASSWORD');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Créer la table cmw_pending_votes
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS cmw_pending_votes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            player_uuid VARCHAR(36) NOT NULL,
            site_name VARCHAR(100),
            vote_time DATETIME DEFAULT CURRENT_TIMESTAMP,
            delivered BOOLEAN DEFAULT FALSE,
            delivered_at DATETIME NULL,
            INDEX idx_uuid (player_uuid),
            INDEX idx_delivered (delivered)
        )
    ");
    
    // Créer la table cmw_pending_shop
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS cmw_pending_shop (
            id INT AUTO_INCREMENT PRIMARY KEY,
            player_uuid VARCHAR(36) NOT NULL,
            item_name VARCHAR(255),
            item_id VARCHAR(100),
            quantity INT DEFAULT 1,
            purchase_time DATETIME DEFAULT CURRENT_TIMESTAMP,
            delivered BOOLEAN DEFAULT FALSE,
            delivered_at DATETIME NULL,
            INDEX idx_uuid (player_uuid),
            INDEX idx_delivered (delivered)
        )
    ");
    
    // Créer la table cmw_players
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS cmw_players (
            uuid VARCHAR(36) PRIMARY KEY,
            username VARCHAR(16) NOT NULL,
            is_online BOOLEAN DEFAULT FALSE,
            last_join DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_username (username),
            INDEX idx_online (is_online)
        )
    ");
    
    echo json_encode([
        'success' => true,
        'message' => 'Tables créées avec succès !',
        'tables' => ['cmw_pending_votes', 'cmw_pending_shop', 'cmw_players']
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>

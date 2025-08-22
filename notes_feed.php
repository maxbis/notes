<?php
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';

// Set JSON content type
header('Content-Type: application/json');

// Only allow GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

try {
    $db = Database::getInstance();
    
    // Get parameters
    $search = $_GET['q'] ?? '';
    $sort = $_GET['sort'] ?? 'updated';
    $offset = (int)($_GET['offset'] ?? 0);
    $limit = 20;
    
    // Validate parameters
    if ($offset < 0) $offset = 0;
    if (!in_array($sort, ['updated', 'created'])) $sort = 'updated';
    
    // Get notes
    $notes = $db->getNotes($search, $sort, $offset, $limit);
    $totalNotes = $db->getNotesCount($search);
    
    // Check if there are more notes
    $hasMore = ($offset + $limit) < $totalNotes;
    
    // Format notes for response
    $formattedNotes = [];
    foreach ($notes as $note) {
        $formattedNotes[] = [
            'hash_id' => $note['hash_id'],
            'title' => $note['title'],
            'content' => $note['content'],
            'created_at' => $note['created_at'],
            'updated_at' => $note['updated_at']
        ];
    }
    
    // Return JSON response
    echo json_encode([
        'success' => true,
        'notes' => $formattedNotes,
        'hasMore' => $hasMore,
        'total' => $totalNotes,
        'offset' => $offset,
        'limit' => $limit
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => defined('DEBUG_MODE') && DEBUG_MODE ? $e->getMessage() : 'Internal server error',
        'success' => false
    ]);
}

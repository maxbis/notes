<?php
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Location: index.php');
    exit;
}

$hashId = $_POST['hash_id'] ?? '';
$csrfToken = $_POST['csrf_token'] ?? '';

// Validate CSRF token
if (!verifyCSRFToken($csrfToken)) {
    $errorMessage = 'Invalid request. Please try again.';
    header('Location: index.php?error=' . urlencode($errorMessage));
    exit;
}

if (empty($hashId)) {
    $errorMessage = 'Note ID is required.';
    header('Location: index.php?error=' . urlencode($errorMessage));
    exit;
}

try {
    $db = Database::getInstance();
    
    // Check if note exists
    if (!$db->noteExists($hashId)) {
        $errorMessage = 'Note not found.';
        header('Location: index.php?error=' . urlencode($errorMessage));
        exit;
    }
    
    // Delete the note
    if ($db->deleteNote($hashId)) {
        $successMessage = 'Note deleted successfully.';
        header('Location: index.php?success=' . urlencode($successMessage));
    } else {
        $errorMessage = 'Failed to delete note. Please try again.';
        header('Location: index.php?error=' . urlencode($errorMessage));
    }
    
} catch (Exception $e) {
    $errorMessage = defined('DEBUG_MODE') && DEBUG_MODE ? $e->getMessage() : 'An error occurred while deleting the note.';
    header('Location: index.php?error=' . urlencode($errorMessage));
}

exit;

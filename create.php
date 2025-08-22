<?php
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';

try {
    $db = Database::getInstance();
    
    // Create a new blank note
    $title = 'untitled';
    $content = '';
    
    $hashId = $db->createNote($title, $content);
    
    // Redirect to the edit page
    header("Location: note.php?note=" . $hashId);
    exit;
    
} catch (Exception $e) {
    // If creation fails, show error
    $pageTitle = 'Error Creating Note';
    include 'includes/header.php';
    ?>
    
    <div class="text-center py-12">
        <div class="mx-auto h-24 w-24 text-red-400 mb-4">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Failed to Create Note</h3>
        <p class="text-gray-500 mb-6">
            <?php echo defined('DEBUG_MODE') && DEBUG_MODE ? e($e->getMessage()) : 'An error occurred while creating your note. Please try again.'; ?>
        </p>
        <div class="flex space-x-3 justify-center">
            <a href="index.php" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-medium transition-colors">
                Back to Notes
            </a>
            <a href="create.php" class="bg-note-blue hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                Try Again
            </a>
        </div>
    </div>
    
    <?php
    include 'includes/footer.php';
}
?>

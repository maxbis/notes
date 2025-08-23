<?php
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';

$hashId = $_GET['note'] ?? '';
$errorMessage = '';
$successMessage = '';

// Success message is now set directly in the form processing

$note = null;

if (empty($hashId)) {
    header('Location: index.php');
    exit;
}

try {
    $db = Database::getInstance();
    
        // Handle form submission (unified save function)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            $errorMessage = 'Invalid request. Please try again.';
        } else {
            $title = trim($_POST['title'] ?? '');
            $content = trim($_POST['content'] ?? '');
            $isAutoSave = isset($_POST['auto_save']) && $_POST['auto_save'] === '1';
            
            // Validate content
            if (empty($content)) {
                $errorMessage = 'Note content cannot be empty.';
            } elseif (!validateContentLength($content)) {
                $maxLength = defined('CONTENT_MAX_CHARS') ? CONTENT_MAX_CHARS : 10000;
                $errorMessage = "Note content cannot exceed {$maxLength} characters.";
            } else {
                // Auto-generate title if empty
                if (empty($title)) {
                    $title = generateTitleFromContent($content);
                }
                
                // Check if note exists
                if ($db->noteExists($hashId)) {
                    // Update existing note
                    if ($db->updateNote($hashId, $title, $content)) {
                        // Success - note was saved
                        
                        // Return JSON for all AJAX requests (both auto-save and manual save)
                        header('Content-Type: application/json');
                        echo json_encode([
                            'success' => true,
                            'message' => $isAutoSave ? 'Note auto-saved successfully' : 'Note saved successfully',
                            'timestamp' => date('Y-m-d H:i:s')
                        ]);
                        exit;
                    } else {
                        $errorMessage = 'Failed to update note. Please try again.';
                        
                        // Return JSON for all AJAX requests
                        header('Content-Type: application/json');
                        echo json_encode([
                            'success' => false,
                            'message' => 'Failed to save note'
                        ]);
                        exit;
                    }
                } else {
                    $errorMessage = 'Note not found.';
                    
                    // Return JSON for all AJAX requests
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false,
                        'message' => 'Note not found'
                    ]);
                    exit;
                }
            }
            
            // For all AJAX requests with validation errors, return JSON response
            if (!empty($errorMessage)) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => $errorMessage
                ]);
                exit;
            }
        }
    }
    
    // Get note data
    $note = $db->getNoteByHash($hashId);
    if (!$note) {
        http_response_code(404);
        $pageTitle = 'Note Not Found';
        include 'includes/header.php';
        ?>
        <div class="note-container min-h-screen py-8 px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl mx-auto">
                <div class="note-card-inner rounded-2xl shadow-xl p-12 text-center">
                    <div class="mx-auto h-24 w-24 text-note-gray mb-6">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-3">Note Not Found</h3>
                    <p class="text-gray-600 mb-8 text-lg">The note you're looking for doesn't exist or has been deleted.</p>
                    <a href="index.php" class="bg-gradient-to-r from-note-blue to-note-purple hover:from-blue-600 hover:to-purple-600 text-white px-8 py-4 rounded-full font-medium transition-all duration-300 hover:scale-105 shadow-md hover:shadow-lg inline-block">
                        ← Back to Notes
                    </a>
                </div>
            </div>
        </div>
        <?php
        include 'includes/footer.php';
        exit;
    }
    
    $pageTitle = $note['title'];
    
} catch (Exception $e) {
    $errorMessage = $e->getMessage();
    $note = null;
}

include 'includes/header.php';
?>

<!-- Note Container with Gradient Background -->
<div class="note-container min-h-screen sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto">
        <!-- Note Card Container -->
        <div class="note-card-inner rounded-2xl shadow-xl p-8 space-y-4">
            
            <!-- Note Header -->
            <div class="flex items-center justify-between">
                <div class="flex-1 mr-6">
                    <input 
                        type="text" 
                        id="title" 
                        name="title" 
                        value="<?php echo e($note['title']); ?>"
                        class="title-input text-3xl font-bold text-gray-800 bg-transparent border-none outline-none focus:ring-2 focus:ring-note-blue/20 focus:border-none px-0 py-0 resize-none w-full"
                        placeholder="Enter note title..."
                        style="resize: none;"
                    >
                </div>
                
                <button 
                    type="button" 
                    id="save-button"
                    class="save-button bg-gradient-to-r from-gray-400 to-gray-500 text-gray-600 px-6 py-1 rounded-full font-medium transition-all duration-300 text-sm min-w-[120px] shadow-md opacity-50 cursor-not-allowed mr-2"
                    disabled
                    onclick="performManualSave()"
                >
                    Saved
                </button>
            </div>

            <!-- Note Form -->
            <div class="space-y-6">
                <!-- Content Input -->
                <div>
                    <div class="relative">
                        <textarea 
                            id="content" 
                            name="content" 
                            rows="24"
                            class="content-textarea w-full px-6 py-4 border border-gray-200/60 rounded-xl focus:ring-2 focus:ring-note-blue/20 focus:border-note-blue/30 font-mono text-sm bg-white/50 backdrop-blur-sm"
                            placeholder="Write your note here... Use #tags to organize your notes."
                            oninput="updateCharCounter()"
                        ><?php echo e($note['content']); ?></textarea>
                        
                        <!-- Character Counter -->
                        <div class="absolute bottom-4 right-4">
                            <span 
                                id="char-counter" 
                                class="char-counter text-sm px-3 py-2 rounded-full bg-white/80 border border-gray-200/60 shadow-sm"
                            >
                                <?php echo getRemainingChars($note['content']); ?> remaining
                            </span>
                        </div>
                    </div>
                    
                    <!-- Note Info -->
                    <div class="mt-6 pt-4 border-t border-gray-200/40">
                        <!-- Desktop Layout -->
                        <div class="hidden md:flex items-center justify-between text-sm text-gray-600">
                            <div class="flex items-center space-x-2">
                                <span class="text-gray-500">Note ID:</span>
                                <a href="note.php?note=<?php echo e($note['hash_id']); ?>" 
                                class="font-mono text-note-blue hover:text-blue-600 hover:underline transition-all duration-300 hover:scale-105 px-2 py-1 rounded-lg bg-soft-blue/50 hover:bg-soft-blue/70"
                                title="Click to copy URL">
                                    <?php echo e($note['hash_id']); ?>
                                </a>
                            </div>
                            
                            <div class="flex items-center space-x-4 text-gray-500">
                                <span class="flex items-center space-x-1">
                                    <span class="w-2 h-2 bg-note-green rounded-full"></span>
                                    <span>Created: <?php echo formatTimestamp($note['created_at']); ?></span>
                                </span>
                                <span>•</span>
                                <span class="flex items-center space-x-1">
                                    <span class="w-2 h-2 bg-note-blue rounded-full"></span>
                                    <span>Updated: <?php echo formatTimestamp($note['updated_at']); ?></span>
                                </span>
                            </div>
                        </div>

                        <!-- Mobile Layout -->
                        <div class="md:hidden space-y-4">
                            <!-- Note ID -->
                            <div class="flex items-center justify-center space-x-2">
                                <span class="text-gray-500">Note ID:</span>
                                <a href="note.php?note=<?php echo e($note['hash_id']); ?>" 
                                class="font-mono text-note-blue hover:text-blue-600 hover:underline transition-all duration-300 hover:scale-105 px-2 py-1 rounded-lg bg-soft-blue/50 hover:bg-soft-blue/70"
                                title="Click to copy URL">
                                    <?php echo e($note['hash_id']); ?>
                                </a>
                            </div>
                            
                            <!-- Timestamps -->
                            <div class="flex items-center justify-center space-x-4 text-sm text-gray-500">
                                <span class="flex items-center space-x-1">
                                    <span class="w-2 h-2 bg-note-green rounded-full"></span>
                                    <span>Created: <?php echo formatTimestamp($note['created_at']); ?></span>
                                </span>
                                <span>•</span>
                                <span class="flex items-center space-x-1">
                                    <span class="w-2 h-2 bg-note-blue rounded-full"></span>
                                    <span>Updated: <?php echo formatTimestamp($note['updated_at']); ?></span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
// Character counter functionality
function updateCharCounter() {
    const content = document.getElementById('content').value;
    const counter = document.getElementById('char-counter');
    const remaining = <?php echo defined('CONTENT_MAX_CHARS') ? CONTENT_MAX_CHARS : 10000; ?> - content.length;
    
    counter.textContent = remaining + ' remaining';
    counter.className = 'char-counter text-sm px-2 py-1 rounded bg-white border border-gray-200';
    
    if (remaining <= 0) {
        counter.className += ' at-limit';
        counter.textContent = 'Character limit reached';
    } else if (remaining <= 100) {
        counter.className += ' near-limit';
    }
}

// True Auto-save functionality
let autoSaveTimer;
let lastSavedContent = '';
let lastSavedTitle = '';
let isAutoSaving = false;
let hasUnsavedChanges = false;
const contentTextarea = document.getElementById('content');
const titleInput = document.getElementById('title');
const saveButton = document.getElementById('save-button');

// Initialize last saved content
document.addEventListener('DOMContentLoaded', function() {
    if (contentTextarea) {
        lastSavedContent = contentTextarea.value;
        lastSavedTitle = titleInput.value;
        contentTextarea.focus();
    }
});

function updateSaveButtonState() {
    const currentContent = contentTextarea.value;
    const currentTitle = titleInput.value;
    
    // Check if there are unsaved changes
    hasUnsavedChanges = (currentContent !== lastSavedContent || currentTitle !== lastSavedTitle);
    
    if (hasUnsavedChanges) {
        // Enable button and show active gradient color
        saveButton.disabled = false;
        saveButton.className = 'save-button bg-gradient-to-r from-note-red to-note-orange hover:from-red-600 hover:to-orange-600 text-white px-6 py-1 rounded-full font-medium transition-all duration-300 text-sm min-w-[120px] shadow-md hover:shadow-lg mr-2 cursor-pointer';
        saveButton.textContent = 'Save Note';
    } else {
        // Disable button and show grey color
        saveButton.disabled = true;
        saveButton.className = 'save-button bg-gradient-to-r from-gray-400 to-gray-500 text-gray-600 px-6 py-1 rounded-full font-medium transition-all duration-300 text-sm min-w-[120px] shadow-md opacity-50 cursor-not-allowed mr-2';
        saveButton.textContent = 'Saved';
    }
}

function setupAutoSave() {
    [contentTextarea, titleInput].forEach(element => {
        element.addEventListener('input', () => {
            // Update button state immediately when typing
            updateSaveButtonState();
            
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(() => {
                performAutoSave();
            }, 3000); // Auto-save after 3 seconds of inactivity
        });
    });
}

// Unified AJAX save function
function performAjaxSave(isAutoSave = false) {
    if (isAutoSaving) return; // Prevent multiple simultaneous saves
    
    const currentContent = contentTextarea.value;
    const currentTitle = titleInput.value;
    
    // Check if content has actually changed
    if (currentContent === lastSavedContent && currentTitle === lastSavedTitle) {
        return; // No changes to save
    }
    
    isAutoSaving = true;
    
    // Show save indicator
    let originalText = saveButton.textContent;
    let originalClass = saveButton.className;
    
    if (isAutoSave) {
        saveButton.textContent = 'Auto-saving...';
    } else {
        saveButton.textContent = 'Saving...';
    }
    saveButton.disabled = true;
    saveButton.className = 'save-button bg-gradient-to-r from-gray-500 to-gray-600 text-white px-6 py-1 rounded-full font-medium transition-all duration-300 text-sm min-w-[120px] shadow-md opacity-75 cursor-not-allowed mr-2';
    
    // Prepare form data
    const formData = new FormData();
    formData.append('csrf_token', '<?php echo generateCSRFToken(); ?>');
    formData.append('title', currentTitle);
    formData.append('content', currentContent);
    formData.append('auto_save', isAutoSave ? '1' : '0');
    
    // Perform AJAX save
    fetch('note.php?note=<?php echo e($hashId); ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        // Check if save was successful
        if (data.success) {
            // Update last saved content
            lastSavedContent = currentContent;
            lastSavedTitle = currentTitle;
            
            // Show success indicator
            if (isAutoSave) {
                saveButton.textContent = 'Auto-saved!';
            } else {
                saveButton.textContent = 'Saved!';
            }
            saveButton.className = 'save-button bg-gradient-to-r from-note-green to-note-blue hover:from-green-600 hover:to-blue-600 text-white px-6 py-1 rounded-full font-medium transition-all duration-300 text-sm min-w-[120px] shadow-md hover:shadow-lg mr-2 cursor-pointer';
            
            setTimeout(() => {
                updateSaveButtonState(); // This will set button to "Saved" state
            }, 2000);
            
            // Update timestamps
            updateTimestamps();
        } else {
            // Show error indicator
            saveButton.textContent = 'Save failed';
            saveButton.className = 'save-button bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white px-6 py-1 rounded-full font-medium transition-all duration-300 text-sm min-w-[120px] shadow-md hover:shadow-lg mr-2 cursor-pointer';
            
            setTimeout(() => {
                updateSaveButtonState(); // This will re-enable button if there are still changes
            }, 3000);
        }
    })
    .catch(error => {
        console.error('Save error:', error);
        // Show error indicator
        saveButton.textContent = 'Save failed';
        saveButton.className = 'save-button bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white px-6 py-1 rounded-full font-medium transition-all duration-300 text-sm min-w-[120px] shadow-md hover:shadow-lg mr-2 cursor-pointer';
        
        setTimeout(() => {
            updateSaveButtonState(); // This will re-enable button if there are still changes
        }, 3000);
    })
    .finally(() => {
        isAutoSaving = false;
    });
}

// Auto-save function (calls unified AJAX save)
function performAutoSave() {
    performAjaxSave(true);
}

function updateTimestamps() {
    // Update the updated timestamp to show "Just now"
    const spans = document.querySelectorAll('span');
    spans.forEach(span => {
        if (span.textContent.includes('Updated:')) {
            span.textContent = 'Updated: Just now';
        }
    });
}

// Initialize auto-save
setupAutoSave();

// Manual save function (calls unified AJAX save)
function performManualSave() {
    performAjaxSave(false);
}

// Save on page unload if there are unsaved changes
window.addEventListener('beforeunload', function(e) {

    if (hasUnsavedChanges) {
        // Perform a synchronous save attempt
        const currentContent = contentTextarea.value;
        const currentTitle = titleInput.value;
        
        // Create a synchronous XMLHttpRequest to save before leaving
        const xhr = new XMLHttpRequest();
        xhr.open('POST', window.location.href, false); // Synchronous request
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        
        const formData = new URLSearchParams();
        formData.append('csrf_token', '<?php echo generateCSRFToken(); ?>');
        formData.append('title', currentTitle);
        formData.append('content', currentContent);
        formData.append('auto_save', '1');
        
        try {
            xhr.send(formData.toString());
            // Note: We can't guarantee the save completed due to page navigation
        } catch (error) {
            // If save fails, we can't prevent navigation anyway
        }
        
        // Show confirmation dialog to user
        e.preventDefault();
        e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
        return e.returnValue;
    }
});

</script>

<?php include 'includes/footer.php'; ?>

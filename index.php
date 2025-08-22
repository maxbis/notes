<?php
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';

$pageTitle = 'All Notes';

try {
    $db = Database::getInstance();
    
    // Get search and sort parameters
    $search = $_GET['q'] ?? '';
    $sort = $_GET['sort'] ?? 'updated';
    
    // Validate sort parameter
    if (!in_array($sort, ['updated', 'created'])) {
        $sort = 'updated';
    }
    
    // Get initial notes
    $notes = $db->getNotes($search, $sort, 0, 20);
    $totalNotes = $db->getNotesCount($search);
    
} catch (Exception $e) {
    $errorMessage = $e->getMessage();
    $notes = [];
    $totalNotes = 0;
}

include 'includes/header.php';
?>

<!-- Search and Sort Controls -->
<div class="mb-8">
    <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
        <!-- Search Form -->
        <form method="GET" action="index.php" class="flex-1 max-w-md">
            <div class="relative">
                <input 
                    type="text" 
                    name="q" 
                    value="<?php echo e($search); ?>"
                    placeholder="Search title or content..." 
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-note-blue focus:border-transparent"
                >
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
        </form>
        
        <!-- Sort Dropdown -->
        <div class="flex items-center space-x-2">
            <label for="sort" class="text-sm font-medium text-gray-700">Sort by:</label>
            <select 
                id="sort" 
                class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-note-blue focus:border-transparent"
                onchange="changeSort(this.value)"
            >
                <option value="updated" <?php echo $sort === 'updated' ? 'selected' : ''; ?>>Last Edited</option>
                <option value="created" <?php echo $sort === 'created' ? 'selected' : ''; ?>>Date Created</option>
            </select>
        </div>
    </div>
</div>

<!-- Notes List -->
<div id="notes-container">
    <?php if (empty($notes)): ?>
        <!-- Empty State -->
        <div class="text-center py-12">
            <div class="mx-auto h-24 w-24 text-gray-400 mb-4">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">
                <?php echo empty($search) ? 'No notes yet' : 'No notes found'; ?>
            </h3>
            <p class="text-gray-500 mb-6">
                <?php if (empty($search)): ?>
                    Get started by creating your first note.
                <?php else: ?>
                    Try adjusting your search terms or create a new note.
                <?php endif; ?>
            </p>
            <?php if (empty($search)): ?>
                <a href="create.php" class="bg-note-blue hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                    Create Your First Note
                </a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <!-- Notes Grid -->
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3" id="notes-grid">
            <?php foreach ($notes as $note): ?>
                <div class="note-card bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex justify-between items-start mb-3">
                        <h3 class="text-lg font-semibold text-gray-900 line-clamp-2">
                            <a href="note.php?note=<?php echo e($note['hash_id']); ?>" class="hover:text-note-blue transition-colors">
                                <?php echo e($note['title']); ?>
                            </a>
                        </h3>
                        <button 
                            onclick="deleteNote('<?php echo e($note['hash_id']); ?>', '<?php echo e($note['title']); ?>')"
                            class="text-gray-400 hover:text-red-500 transition-colors p-1"
                            title="Delete note"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                        <?php echo getContentPreview($note['content']); ?>
                    </p>
                    
                    <div class="flex items-center justify-between text-xs text-gray-500">
                        <span><?php echo formatTimestamp($note['updated_at']); ?></span>
                        <span class="text-note-blue font-medium"><?php echo e($note['hash_id']); ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Load More Indicator -->
        <?php if (count($notes) < $totalNotes): ?>
            <div class="text-center mt-8" id="load-more-container">
                <button 
                    id="load-more-btn"
                    onclick="loadMoreNotes()"
                    class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-medium transition-colors"
                >
                    Load More Notes
                </button>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<!-- Delete Confirmation Modal -->
<div id="delete-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <div class="flex items-center mb-4">
                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
            </div>
            <div class="text-center">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Delete Note</h3>
                <p class="text-sm text-gray-500 mb-6">
                    Are you sure you want to delete "<span id="delete-note-title"></span>"? This action cannot be undone.
                </p>
                <div class="flex space-x-3">
                    <button 
                        onclick="hideDeleteModal()"
                        class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors"
                    >
                        Cancel
                    </button>
                    <button 
                        id="confirm-delete-btn"
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors"
                    >
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Global variables for infinite scroll
let currentOffset = <?php echo count($notes); ?>;
let isLoading = false;
let hasMoreNotes = <?php echo count($notes) < $totalNotes ? 'true' : 'false'; ?>;
const currentSearch = '<?php echo e($search); ?>';
const currentSort = '<?php echo e($sort); ?>';

// Change sort and reload
function changeSort(sortValue) {
    const url = new URL(window.location);
    url.searchParams.set('sort', sortValue);
    if (currentSearch) {
        url.searchParams.set('q', currentSearch);
    }
    window.location.href = url.toString();
}

// Delete note functions
function deleteNote(hashId, title) {
    document.getElementById('delete-note-title').textContent = title;
    document.getElementById('delete-modal').classList.remove('hidden');
    
    document.getElementById('confirm-delete-btn').onclick = function() {
        performDelete(hashId);
    };
}

function hideDeleteModal() {
    document.getElementById('delete-modal').classList.add('hidden');
}

function performDelete(hashId) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'delete.php';
    
    const hashInput = document.createElement('input');
    hashInput.type = 'hidden';
    hashInput.name = 'hash_id';
    hashInput.value = hashId;
    
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = 'csrf_token';
    csrfInput.value = '<?php echo generateCSRFToken(); ?>';
    
    form.appendChild(hashInput);
    form.appendChild(csrfInput);
    document.body.appendChild(form);
    form.submit();
}

// Infinite scroll functions
function loadMoreNotes() {
    if (isLoading || !hasMoreNotes) return;
    
    isLoading = true;
    const loadMoreBtn = document.getElementById('load-more-btn');
    loadMoreBtn.textContent = 'Loading...';
    loadMoreBtn.disabled = true;
    
    fetch(`notes_feed.php?offset=${currentOffset}&sort=${currentSort}&q=${encodeURIComponent(currentSearch)}`)
        .then(response => response.json())
        .then(data => {
            if (data.notes && data.notes.length > 0) {
                appendNotes(data.notes);
                currentOffset += data.notes.length;
                hasMoreNotes = data.hasMore;
                
                if (!data.hasMore) {
                    document.getElementById('load-more-container').style.display = 'none';
                }
            } else {
                hasMoreNotes = false;
                document.getElementById('load-more-container').style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error loading more notes:', error);
        })
        .finally(() => {
            isLoading = false;
            loadMoreBtn.textContent = 'Load More Notes';
            loadMoreBtn.disabled = false;
        });
}

function appendNotes(notes) {
    const grid = document.getElementById('notes-grid');
    
    notes.forEach(note => {
        const noteElement = createNoteElement(note);
        grid.appendChild(noteElement);
    });
}

function createNoteElement(note) {
    const div = document.createElement('div');
    div.className = 'note-card bg-white rounded-lg shadow-sm border border-gray-200 p-6';
    
    const title = note.title.length > 50 ? note.title.substring(0, 50) + '...' : note.title;
    const preview = note.content.length > 150 ? note.content.substring(0, 147) + '...' : note.content;
    
    div.innerHTML = `
        <div class="flex justify-between items-start mb-3">
            <h3 class="text-lg font-semibold text-gray-900 line-clamp-2">
                <a href="note.php?note=${note.hash_id}" class="hover:text-note-blue transition-colors">
                    ${title}
                </a>
            </h3>
            <button 
                onclick="deleteNote('${note.hash_id}', '${title}')"
                class="text-gray-400 hover:text-red-500 transition-colors p-1"
                title="Delete note"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </button>
        </div>
        
        <p class="text-gray-600 text-sm mb-4 line-clamp-3">
            ${preview}
        </p>
        
        <div class="flex items-center justify-between text-xs text-gray-500">
            <span>${formatTimestamp(note.updated_at)}</span>
            <span class="text-note-blue font-medium">${note.hash_id}</span>
        </div>
    `;
    
    return div;
}

function formatTimestamp(timestamp) {
    const date = new Date(timestamp);
    const now = new Date();
    const diff = now - date;
    
    const minutes = Math.floor(diff / 60000);
    const hours = Math.floor(diff / 3600000);
    const days = Math.floor(diff / 86400000);
    
    if (minutes < 1) return 'Just now';
    if (minutes < 60) return `${minutes} min ago`;
    if (hours < 24) return `${hours}h ago`;
    if (days === 1) return 'Yesterday';
    if (days < 7) return `${days} days ago`;
    
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}

// Intersection Observer for infinite scroll
if ('IntersectionObserver' in window && hasMoreNotes) {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !isLoading && hasMoreNotes) {
                loadMoreNotes();
            }
        });
    });
    
    const loadMoreContainer = document.getElementById('load-more-container');
    if (loadMoreContainer) {
        observer.observe(loadMoreContainer);
    }
}
</script>

<?php include 'includes/footer.php'; ?>

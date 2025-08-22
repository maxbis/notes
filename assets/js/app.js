// Notes App JavaScript functionality

// Utility functions
const NotesApp = {
    // Format timestamp for display
    formatTimestamp: function(timestamp) {
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
    },
    
    // Debounce function for search
    debounce: function(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },
    
    // Show notification
    showNotification: function(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
            type === 'success' ? 'bg-green-500 text-white' :
            type === 'error' ? 'bg-red-500 text-white' :
            'bg-blue-500 text-white'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    },
    
    // Confirm action
    confirm: function(message, callback) {
        if (confirm(message)) {
            callback();
        }
    }
};

// Auto-save functionality for note editing
if (document.getElementById('content')) {
    let autoSaveTimer;
    const contentTextarea = document.getElementById('content');
    const titleInput = document.getElementById('title');
    
    function setupAutoSave() {
        [contentTextarea, titleInput].forEach(element => {
            element.addEventListener('input', () => {
                clearTimeout(autoSaveTimer);
                autoSaveTimer = setTimeout(() => {
                    // Show auto-save indicator
                    const saveBtn = document.querySelector('button[type="submit"]');
                    if (saveBtn) {
                        const originalText = saveBtn.textContent;
                        saveBtn.textContent = 'Auto-saving...';
                        saveBtn.disabled = true;
                        
                        setTimeout(() => {
                            saveBtn.textContent = originalText;
                            saveBtn.disabled = false;
                        }, 1000);
                    }
                }, 2000); // Auto-save after 2 seconds of inactivity
            });
        });
    }
    
    setupAutoSave();
}

// Character counter functionality
if (document.getElementById('content')) {
    function updateCharCounter() {
        const content = document.getElementById('content').value;
        const counter = document.getElementById('char-counter');
        const maxLength = window.maxContentLength || 10000;
        const remaining = maxLength - content.length;
        
        if (counter) {
            counter.textContent = remaining + ' remaining';
            counter.className = 'char-counter text-sm px-2 py-1 rounded bg-white border border-gray-200';
            
            if (remaining <= 0) {
                counter.className += ' at-limit';
                counter.textContent = 'Character limit reached';
            } else if (remaining <= 100) {
                counter.className += ' near-limit';
            }
        }
    }
    
    // Add event listener if not already present
    const contentTextarea = document.getElementById('content');
    if (contentTextarea && !contentTextarea.hasAttribute('data-counter-initialized')) {
        contentTextarea.setAttribute('data-counter-initialized', 'true');
        contentTextarea.addEventListener('input', updateCharCounter);
    }
}

// Search functionality enhancement
if (document.querySelector('input[name="q"]')) {
    const searchInput = document.querySelector('input[name="q"]');
    
    // Add search suggestions for hashtags
    const hashtagSuggestions = [
        '#work', '#personal', '#ideas', '#todo', '#meeting', '#project', 
        '#important', '#urgent', '#followup', '#reference', '#draft'
    ];
    
    // Create suggestion dropdown
    const suggestionDropdown = document.createElement('div');
    suggestionDropdown.className = 'absolute top-full left-0 right-0 bg-white border border-gray-300 rounded-lg shadow-lg z-10 hidden';
    suggestionDropdown.innerHTML = `
        <div class="p-2">
            <div class="text-xs text-gray-500 mb-2">Popular hashtags:</div>
            <div class="flex flex-wrap gap-2">
                ${hashtagSuggestions.map(tag => 
                    `<button class="text-blue-600 hover:bg-blue-50 px-2 py-1 rounded text-sm" onclick="insertHashtag('${tag}')">${tag}</button>`
                ).join('')}
            </div>
        </div>
    `;
    
    searchInput.parentNode.appendChild(suggestionDropdown);
    
    // Show/hide suggestions
    searchInput.addEventListener('focus', () => {
        suggestionDropdown.classList.remove('hidden');
    });
    
    searchInput.addEventListener('blur', () => {
        setTimeout(() => {
            suggestionDropdown.classList.add('hidden');
        }, 200);
    });
    
    // Insert hashtag function
    window.insertHashtag = function(tag) {
        searchInput.value = tag;
        searchInput.focus();
        suggestionDropdown.classList.add('hidden');
    };
}

// Keyboard shortcuts (optional enhancement)
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + N for new note
    if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
        e.preventDefault();
        window.location.href = 'create.php';
    }
    
    // Ctrl/Cmd + K for search focus
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        const searchInput = document.querySelector('input[name="q"]');
        if (searchInput) {
            searchInput.focus();
            searchInput.select();
        }
    }
    
    // Escape to close modals
    if (e.key === 'Escape') {
        const modals = document.querySelectorAll('.modal, [id*="modal"]');
        modals.forEach(modal => {
            if (!modal.classList.contains('hidden')) {
                modal.classList.add('hidden');
            }
        });
    }
});

// Initialize app when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Focus search input if it exists and no search term
    const searchInput = document.querySelector('input[name="q"]');
    if (searchInput && !searchInput.value) {
        searchInput.focus();
    }
    
    // Focus content textarea if it exists
    const contentTextarea = document.getElementById('content');
    if (contentTextarea) {
        contentTextarea.focus();
    }
    
    // Add loading states to forms
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Saving...';
            }
        });
    });
});

// Export for use in other scripts
window.NotesApp = NotesApp;

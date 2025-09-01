/**
 * Text Replacement Manager
 * Handles real-time text replacements in textareas
 */

class TextReplacementManager {
    constructor() {
        this.replacements = [
            {
                trigger: 'ddd',
                replacement: () => '*** ' + this.getCurrentDate(),
                description: 'Replace with current date'
            },
            {
                trigger: '[]',
                replacement: '🔴',
                description: 'Replace with checkbox'
            },
            {
                trigger: '[ ]',
                replacement: '🔴',
                description: 'Replace with checkbox'
            },
            {
                trigger: '[x]',
                replacement: '✅',
                description: 'Replace with checked checkbox'
            },
            {
                trigger: '[!!]',
                replacement: '‼️',
                description: 'Replace with double exclamation mark'
            },
            {   
                trigger: '[!]',
                replacement: '⚠️',
                description: 'Replace with exclamation mark'
            },
            {
                trigger: '[?]',
                replacement: '❓',
                description: 'Replace with questionmark checkbox'
            },
            {
                trigger: '[->]',
                replacement: '👉',
                description: 'Replace with arrow'
            },
            {
                trigger: 'xxx',
                replacement: (textarea) => this.toggleCheckboxesOnLine(textarea),
                description: 'Toggle all checkboxes on current line and replace xxx with current date'
            },
        ];
        
        this.initialized = false;
    }
    
    /**
     * Get current date in dd/mm format
     */
    getCurrentDate() {
        const now = new Date();
        const day = now.getDate().toString().padStart(2, '0');
        const month = (now.getMonth() + 1).toString().padStart(2, '0');
        return `${day}/${month}`;
    }
    
    /**
     * Initialize text replacement for all textareas
     */
    init() {
        if (this.initialized) return;
        
        // Find all textareas
        const textareas = document.querySelectorAll('textarea');
        textareas.forEach(textarea => {
            this.setupTextarea(textarea);
        });
        
        // Watch for dynamically added textareas
        this.observeDOM();
        
        this.initialized = true;
        console.log('Text replacement manager initialized');
    }
    
    /**
     * Setup text replacement for a specific textarea
     */
    setupTextarea(textarea) {
        if (textarea.hasAttribute('data-replacement-initialized')) return;
        
        textarea.setAttribute('data-replacement-initialized', 'true');
        
        // Only add event listeners if the auto-save system is ready
        // This prevents interference with initial state
        if (typeof window.lastSavedContent !== 'undefined') {
            textarea.addEventListener('input', (e) => {
                this.handleInput(e);
            });
            
            textarea.addEventListener('keydown', (e) => {
                this.handleKeydown(e);
            });
        } else {
            // Wait for auto-save system to be ready
            const checkReady = () => {
                if (typeof window.lastSavedContent !== 'undefined') {
                    textarea.addEventListener('input', (e) => {
                        this.handleInput(e);
                    });
                    
                    textarea.addEventListener('keydown', (e) => {
                        this.handleKeydown(e);
                    });
                } else {
                    setTimeout(checkReady, 100);
                }
            };
            checkReady();
        }
    }
    
    /**
     * Handle input events for text replacement
     */
    handleInput(e) {
        const textarea = e.target;
        const cursorPos = textarea.selectionStart;
        const value = textarea.value;
        
        // Check each replacement
        this.replacements.forEach(replacement => {
            const trigger = replacement.trigger;
            const triggerLength = trigger.length;
            
            // Check if trigger is at cursor position
            if (cursorPos >= triggerLength) {
                const beforeCursor = value.substring(cursorPos - triggerLength, cursorPos);
                
                if (beforeCursor === trigger) {
                    this.performReplacement(textarea, replacement, cursorPos, triggerLength);
                }
            }
        });
    }
    
    /**
     * Handle keydown events for special cases
     */
    handleKeydown(e) {
        const textarea = e.target;
        const cursorPos = textarea.selectionStart;
        const value = textarea.value;
        
        // Handle backspace for emoji replacements
        if (e.key === 'Backspace') {
            const beforeCursor = value.substring(cursorPos - 1, cursorPos);
            if (beforeCursor === '🔴' || beforeCursor === '☐' || beforeCursor === '⚠️' || 
                beforeCursor === '😊' || beforeCursor === '😢' || beforeCursor === '😉' || 
                beforeCursor === '😃' || beforeCursor === '❤️') {
                // Prevent backspace from deleting just part of the emoji
                e.preventDefault();
                const newValue = value.substring(0, cursorPos - 1) + value.substring(cursorPos);
                textarea.value = newValue;
                textarea.setSelectionRange(cursorPos - 1, cursorPos - 1);
                this.triggerSaveEvent(textarea);
            }
        }
    }
    
    /**
     * Perform the actual text replacement
     */
    performReplacement(textarea, replacement, cursorPos, triggerLength) {
        const value = textarea.value;
        const beforeTrigger = value.substring(0, cursorPos - triggerLength);
        const afterCursor = value.substring(cursorPos);
        
        // Get replacement text
        let replacementText;
        if (typeof replacement.replacement === 'function') {
            if (replacement.trigger === 'xxx') {
                // Special handling for xxx - toggle checkboxes on line and replace xxx with date
                const finalValue = replacement.replacement(textarea);
                textarea.value = finalValue;
                
                // Get the adjusted cursor position from toggleCheckboxesOnLine
                const newCursorPos = this.getAdjustedCursorPosition(textarea, cursorPos, triggerLength);
                textarea.setSelectionRange(newCursorPos, newCursorPos);
                
                // Trigger save event
                this.triggerSaveEvent(textarea);
                
                // Log replacement
                console.log(`Text replacement: "${replacement.trigger}" → line toggled and xxx replaced with date`);
                return;
            } else {
                replacementText = replacement.replacement(textarea);
            }
        } else {
            replacementText = replacement.replacement;
        }
        
        // Create new value
        const newValue = beforeTrigger + replacementText + afterCursor;
        
        // Update textarea
        textarea.value = newValue;
        
        // Calculate new cursor position
        const newCursorPos = cursorPos - triggerLength + replacementText.length;
        
        // Set cursor position
        textarea.setSelectionRange(newCursorPos, newCursorPos);
        
        // Trigger save event
        this.triggerSaveEvent(textarea);
        
        // Log replacement
        console.log(`Text replacement: "${replacement.trigger}" → "${replacementText}"`);
    }
    
    /**
     * Trigger save event for auto-save integration
     */
    triggerSaveEvent(textarea) {
        // Create a custom event that can be listened to by save managers
        const saveEvent = new CustomEvent('textReplacement', {
            detail: {
                element: textarea,
                timestamp: new Date().toISOString()
            }
        });
        
        textarea.dispatchEvent(saveEvent);
        
        // Also trigger input event for existing auto-save systems
        const inputEvent = new Event('input', { bubbles: true });
        textarea.dispatchEvent(inputEvent);
    }
    
    /**
     * Watch for dynamically added textareas
     */
    observeDOM() {
        if (typeof MutationObserver !== 'undefined') {
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    mutation.addedNodes.forEach((node) => {
                        if (node.nodeType === Node.ELEMENT_NODE) {
                            // Check if the added node is a textarea
                            if (node.tagName === 'TEXTAREA') {
                                this.setupTextarea(node);
                            }
                            
                            // Check for textareas within the added node
                            const textareas = node.querySelectorAll ? node.querySelectorAll('textarea') : [];
                            textareas.forEach(textarea => {
                                this.setupTextarea(textarea);
                            });
                        }
                    });
                });
            });
            
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        }
    }
    
    /**
     * Add a new replacement rule
     */
    addReplacement(trigger, replacement, description = '') {
        this.replacements.push({
            trigger,
            replacement,
            description
        });
        console.log(`Added replacement rule: "${trigger}" → "${description || 'custom replacement'}"`);
    }
    
    /**
     * Remove a replacement rule
     */
    removeReplacement(trigger) {
        const index = this.replacements.findIndex(r => r.trigger === trigger);
        if (index !== -1) {
            this.replacements.splice(index, 1);
            console.log(`Removed replacement rule: "${trigger}"`);
        }
    }
    
    /**
     * Get all current replacement rules
     */
    getReplacements() {
        return [...this.replacements];
    }

    /**
     * Toggle all checkboxes on the current line
     */
    toggleCheckboxesOnLine(textarea) {
        const cursorPos = textarea.selectionStart;
        const value = textarea.value;
        
        // Find the current line
        const beforeCursor = value.substring(0, cursorPos);
        const afterCursor = value.substring(cursorPos);
        
        // Find line boundaries
        const lastNewlineBefore = beforeCursor.lastIndexOf('\n');
        const nextNewlineAfter = afterCursor.indexOf('\n');
        
        const lineStart = lastNewlineBefore === -1 ? 0 : lastNewlineBefore + 1;
        const lineEnd = nextNewlineAfter === -1 ? value.length : cursorPos + nextNewlineAfter;
        
        const currentLine = value.substring(lineStart, lineEnd);
        
        // Toggle checkboxes in the line
        let newLine = currentLine;
        let hasChanges = false;
        
        // Toggle 🔴 to ✅ and ✅ to 🔴
        if (currentLine.includes('🔴')) {
            newLine = newLine.replace(/🔴/g, '✅');
            hasChanges = true;
        }
        if (currentLine.includes('✅')) {
            newLine = newLine.replace(/✅/g, '🔴');
            hasChanges = true;
        }
        
        let finalValue = value;
        let adjustedCursorPos = cursorPos;
        
        if (hasChanges) {
            // Create the new value with toggled checkboxes
            finalValue = value.substring(0, lineStart) + newLine + value.substring(lineEnd);
            
            // Calculate how much the line length changed
            const lengthDiff = newLine.length - currentLine.length;
            adjustedCursorPos = cursorPos + lengthDiff;
        }
        
        // Replace the xxx trigger text with current date
        const triggerLength = 3; // length of 'xxx'
        const currentDate = this.getCurrentDate();
        const beforeTrigger = finalValue.substring(0, adjustedCursorPos - triggerLength);
        const afterTrigger = finalValue.substring(adjustedCursorPos);
        finalValue = beforeTrigger + currentDate + afterTrigger;
        
        // Always position cursor at the end of the line after date replacement
        const newLineEnd = finalValue.indexOf('\n', adjustedCursorPos - triggerLength);
        const endOfLinePos = newLineEnd === -1 ? finalValue.length : newLineEnd;
        
        return finalValue; // Return the final value for performReplacement to use
    }
    
    /**
     * Get adjusted cursor position after xxx replacement
     */
    getAdjustedCursorPosition(textarea, originalCursorPos, triggerLength) {
        const value = textarea.value;
        const beforeCursor = value.substring(0, originalCursorPos);
        
        // Find the current line start
        const lastNewlineBefore = beforeCursor.lastIndexOf('\n');
        const lineStart = lastNewlineBefore === -1 ? 0 : lastNewlineBefore + 1;
        
        // Find the end of the current line
        const afterCursor = value.substring(originalCursorPos);
        const nextNewlineAfter = afterCursor.indexOf('\n');
        const lineEnd = nextNewlineAfter === -1 ? value.length : originalCursorPos + nextNewlineAfter;
        
        // Always return the end of the line position
        return lineEnd;
    }
}

// Create global instance
const textReplacementManager = new TextReplacementManager();

// Auto-initialize when DOM is ready, but wait a bit longer to ensure other systems are initialized
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        // Wait a bit longer to ensure auto-save system is initialized
        setTimeout(() => {
            textReplacementManager.init();
        }, 200);
    });
} else {
    // DOM is already ready, wait a bit longer
    setTimeout(() => {
        textReplacementManager.init();
    }, 200);
}

// Export for use in other scripts
window.TextReplacementManager = TextReplacementManager;
window.textReplacementManager = textReplacementManager;

# Text Replacement System

A real-time text replacement system for textareas that automatically converts trigger text into formatted replacements.

## Features

- **Real-time replacement**: Text is replaced as you type
- **Cursor management**: Cursor position is maintained after replacement
- **Auto-save integration**: Triggers save events after each replacement
- **Extensible**: Easy to add new replacement rules
- **Cross-browser compatible**: Works in all modern browsers

## Built-in Replacements

### 1. Date Replacement
- **Trigger**: `ddd`
- **Replacement**: Current date in format `*** dd mm` (e.g., `*** 12 aug`)
- **Description**: Automatically inserts the current date when you type the trigger

### 2. Checkbox Replacements
- **Trigger**: `[]` → `🔴` (filled checkbox)
- **Trigger**: `[ ]` → `🔴` (empty checkbox)
- **Trigger**: `[x]` → `✅` (checked checkbox)
- **Trigger**: `[!]` → `⚠️` (warning checkbox)
- **Trigger**: `[?]` → `❓` (question checkbox)

### 3. Special Symbols
- **Trigger**: `[!!]` → `‼️` (double exclamation mark)
- **Trigger**: `[->]` → `👉` (pointing arrow)

### 4. Line Toggle (Special Feature)
- **Trigger**: `xxx`
- **Action**: Toggles all checkboxes on the current line
- **Description**: 
  - `☑️` becomes `✅` and vice versa
  - `[ ]` becomes `[x]` and vice versa
  - Removes the `xxx` trigger text
  - Works on the line where the cursor is positioned

## How It Works

The system monitors textarea input events and checks if the current cursor position contains any of the defined trigger patterns. When a trigger is detected, it:

1. Replaces the trigger text with the replacement
2. Positions the cursor at the end of the replacement
3. Triggers save events for auto-save integration
4. Logs the replacement for debugging

## Installation

1. Include the script in your HTML:
```html
<script src="assets/js/text-replacement-manager.js"></script>
```

2. The system automatically initializes and works with all textareas on the page.

## Usage

### Basic Usage
Simply type the trigger text in any textarea, and it will be automatically replaced:

```text
Type: ddd
Result: *** 12 aug

Type: []
Result: 🔴

Type: [x]
Result: ✅

Type: [!!]
Result: ‼️

Type: [->]
Result: 👉

Type: xxx (on a line with checkboxes)
Result: Toggles all checkboxes on that line
```

### Line Toggle Example
The `xxx` trigger is special - it toggles all checkboxes on the current line:

```text
Before typing 'xxx':
[ ] Buy groceries
[x] Call mom
[ ] Walk the dog

After typing 'xxx' on the first line:
[x] Buy groceries
[x] Call mom
[ ] Walk the dog

After typing 'xxx' on the second line:
[x] Buy groceries
[ ] Call mom
[ ] Walk the dog
```

### Adding Custom Replacements

You can add your own replacement rules programmatically:

```javascript
// Add a simple text replacement
textReplacementManager.addReplacement('hello', 'Hi there!', 'Greeting replacement');

// Add a function-based replacement
textReplacementManager.addReplacement('time', () => new Date().toLocaleTimeString(), 'Current time');

// Add an emoji replacement
textReplacementManager.addReplacement(':)', '😊', 'Smile emoji');
```

### Removing Replacements

```javascript
// Remove a specific replacement rule
textReplacementManager.removeReplacement('hello');
```

### Getting All Replacements

```javascript
// Get all current replacement rules
const rules = textReplacementManager.getReplacements();
console.log(rules);
```

## API Reference

### TextReplacementManager Class

#### Constructor
Creates a new text replacement manager instance.

#### Methods

##### `init()`
Initializes the text replacement system for all textareas on the page.

##### `setupTextarea(textarea)`
Sets up text replacement for a specific textarea element.

##### `addReplacement(trigger, replacement, description)`
Adds a new replacement rule.
- `trigger` (string): The text that triggers the replacement
- `replacement` (string|function): The replacement text or function that returns replacement text
- `description` (string, optional): Description of the replacement rule

##### `removeReplacement(trigger)`
Removes a replacement rule by trigger text.

##### `getReplacements()`
Returns an array of all current replacement rules.

##### `getCurrentDate()`
Returns the current date in the format used by the date replacement.

## Events

The system dispatches custom events that you can listen to:

```javascript
// Listen for text replacement events
document.addEventListener('textReplacement', (e) => {
    console.log('Text replacement triggered:', e.detail);
    // e.detail.element - The textarea element
    // e.detail.timestamp - When the replacement occurred
});
```

## Integration with Auto-save

The text replacement system automatically triggers save events after each replacement:

1. **Custom event**: Dispatches `textReplacement` event
2. **Input event**: Triggers standard `input` event for existing auto-save systems
3. **Save button state**: Can be integrated with save button managers

## Extending the System

### Adding New Replacement Types

1. **Simple text replacement**:
```javascript
textReplacementManager.addReplacement('trigger', 'replacement', 'description');
```

2. **Function-based replacement**:
```javascript
textReplacementManager.addReplacement('trigger', () => {
    // Your logic here
    return 'dynamic replacement';
}, 'description');
```

3. **Date/time replacements**:
```javascript
textReplacementManager.addReplacement('now', () => {
    return new Date().toLocaleString();
}, 'Current date and time');
```

4. **Emoji shortcuts**:
```javascript
textReplacementManager.addReplacement('heart', '❤️', 'Heart emoji');
textReplacementManager.addReplacement('star', '⭐', 'Star emoji');
```

5. **Common phrases**:
```javascript
textReplacementManager.addReplacement('ty', 'Thank you!', 'Thank you shortcut');
textReplacementManager.addReplacement('np', 'No problem!', 'No problem shortcut');
```

### Advanced Customization

You can extend the `TextReplacementManager` class to add more sophisticated functionality:

```javascript
class CustomTextReplacementManager extends TextReplacementManager {
    constructor() {
        super();
        
        // Add custom replacements
        this.addReplacement('weather', () => this.getWeatherInfo(), 'Weather info');
    }
    
    getWeatherInfo() {
        // Your weather API logic here
        return '☀️ 72°F Sunny';
    }
}

// Use the custom manager
const customManager = new CustomTextReplacementManager();
customManager.init();
```

## Browser Compatibility

- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+

## Performance Considerations

- The system uses efficient string matching algorithms
- Event listeners are properly managed to prevent memory leaks
- DOM observation is used for dynamically added textareas
- Replacements are processed only when necessary

## Troubleshooting

### Common Issues

1. **Replacements not working**:
   - Check if the script is properly loaded
   - Verify the textarea has the correct ID
   - Check browser console for errors

2. **Cursor position issues**:
   - Ensure the textarea is properly focused
   - Check for conflicting JavaScript code

3. **Auto-save not triggering**:
   - Verify the textarea has input event listeners
   - Check if save events are being dispatched

### Debug Mode

Enable console logging to see replacement operations:

```javascript
// The system automatically logs replacements to console
// Look for: "Text replacement: 'trigger' → 'replacement'"
```

## Examples

### Todo List with Checkboxes
```javascript
// Add checkbox shortcuts for todo items
textReplacementManager.addReplacement('[ ]', '☐', 'Empty checkbox');
textReplacementManager.addReplacement('[x]', '☑️', 'Checked checkbox');
textReplacementManager.addReplacement('[!]', '⚠️', 'Important checkbox');
```

### Date and Time Shortcuts
```javascript
// Add various date/time shortcuts
textReplacementManager.addReplacement('today', () => {
    const today = new Date();
    return today.toLocaleDateString();
}, 'Today\'s date');

textReplacementManager.addReplacement('now', () => {
    return new Date().toLocaleTimeString();
}, 'Current time');
```

### Emoji Shortcuts
```javascript
// Add emoji shortcuts
const emojis = {
    ':)': '😊',
    ':(': '😢',
    ';)': '😉',
    ':D': '😃',
    '<3': '❤️',
    ':)': '😊'
};

Object.entries(emojis).forEach(([trigger, emoji]) => {
    textReplacementManager.addReplacement(trigger, emoji, `${trigger} emoji`);
});
```

## License

This text replacement system is part of the Notes application and follows the same licensing terms.

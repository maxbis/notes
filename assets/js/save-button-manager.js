/**
 * Save Button Manager
 * Centralized management of save button states and styling
 */
const SaveButtonManager = {
  // All possible button states with their configurations
  states: {
    saved: {
      classes: 'save-button btn-save btn-save--saved',
      text: 'Saved',
      disabled: true
    },
    unsaved: {
      classes: 'save-button btn-save btn-save--unsaved',
      text: 'Save',
      disabled: false
    },
    saving: {
      classes: 'save-button btn-save btn-save--saving',
      text: 'Saving...',
      disabled: true
    },
    autoSaving: {
      classes: 'save-button btn-save btn-save--auto-saving',
      text: 'Auto-saving...',
      disabled: true
    },
    success: {
      classes: 'save-button btn-save btn-save--success',
      text: 'Saved!',
      disabled: false
    },
    autoSaved: {
      classes: 'save-button btn-save btn-save--auto-saved',
      text: 'Auto-saved!',
      disabled: false
    },
    error: {
      classes: 'save-button btn-save btn-save--error',
      text: 'Save failed',
      disabled: false
    }
  },

  /**
   * Set the button to a specific state
   * @param {HTMLElement} button - The save button element
   * @param {string} stateName - The name of the state to apply
   */
  setState(button, stateName) {
    const state = this.states[stateName];
    if (!state) {
      console.warn(`Unknown save button state: ${stateName}`);
      return;
    }

    button.className = state.classes;
    button.textContent = state.text;
    button.disabled = state.disabled;
  },

  /**
   * Get the current state of a button
   * @param {HTMLElement} button - The save button element
   * @returns {string|null} - The current state name or null if not found
   */
  getCurrentState(button) {
    for (const [stateName, state] of Object.entries(this.states)) {
      if (button.className.includes(`btn-save--${stateName}`)) {
        return stateName;
      }
    }
    return null;
  },

  /**
   * Check if a button is in a specific state
   * @param {HTMLElement} button - The save button element
   * @param {string} stateName - The state name to check
   * @returns {boolean} - True if the button is in the specified state
   */
  isInState(button, stateName) {
    return this.getCurrentState(button) === stateName;
  },

  /**
   * Reset button to saved state after a delay
   * @param {HTMLElement} button - The save button element
   * @param {number} delay - Delay in milliseconds (default: 2000)
   */
  resetToSavedAfterDelay(button, delay = 2000) {
    setTimeout(() => {
      this.setState(button, 'saved');
    }, delay);
  },

  /**
   * Show success state briefly then reset to saved
   * @param {HTMLElement} button - The save button element
   * @param {string} successText - Text to show in success state
   * @param {number} showDuration - How long to show success state (default: 2000)
   */
  showSuccessThenReset(button, successText = 'Saved!', showDuration = 2000) {
    const successState = { ...this.states.success };
    successState.text = successText;
    
    button.className = successState.classes;
    button.textContent = successState.text;
    button.disabled = successState.disabled;
    
    this.resetToSavedAfterDelay(button, showDuration);
  },

  /**
   * Show error state briefly then reset to unsaved if there are changes
   * @param {HTMLElement} button - The save button element
   * @param {string} errorText - Text to show in error state
   * @param {number} showDuration - How long to show error state (default: 3000)
   * @param {boolean} hasUnsavedChanges - Whether to reset to unsaved state
   */
  showErrorThenReset(button, errorText = 'Save failed', showDuration = 3000, hasUnsavedChanges = false) {
    const errorState = { ...this.states.error };
    errorState.text = errorText;
    
    button.className = errorState.classes;
    button.textContent = errorState.text;
    button.disabled = errorState.disabled;
    
    setTimeout(() => {
      if (hasUnsavedChanges) {
        this.setState(button, 'unsaved');
      } else {
        this.setState(button, 'saved');
      }
    }, showDuration);
  }
};

// Export for use in other modules (if using modules)
if (typeof module !== 'undefined' && module.exports) {
  module.exports = SaveButtonManager;
}

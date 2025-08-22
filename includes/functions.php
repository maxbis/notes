<?php
// Utility functions for the notes application

/**
 * Generate a title from content if title is empty
 * Uses the first word of 5+ characters, or "untitled" if none found
 */
function generateTitleFromContent($content) {
    if (empty(trim($content))) {
        return 'untitled';
    }
    
    // Split content into words and find first word with 5+ characters
    $words = preg_split('/\s+/', trim($content));
    
    foreach ($words as $word) {
        $cleanWord = preg_replace('/[^\w]/', '', $word);
        if (strlen($cleanWord) >= 5) {
            return ucfirst(strtolower($cleanWord));
        }
    }
    
    return 'untitled';
}

/**
 * Escape HTML output for security
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Format timestamp for display
 */
function formatTimestamp($timestamp) {
    $date = new DateTime($timestamp);
    $now = new DateTime();
    $diff = $now->diff($date);
    
    if ($diff->days == 0) {
        if ($diff->h == 0) {
            if ($diff->i == 0) {
                return 'Just now';
            }
            return $diff->i . ' min ago';
        }
        return $diff->h . 'h ago';
    } elseif ($diff->days == 1) {
        return 'Yesterday';
    } elseif ($diff->days < 7) {
        return $diff->days . ' days ago';
    } else {
        return $date->format('M j, Y');
    }
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Validate content length
 */
function validateContentLength($content) {
    $maxLength = defined('CONTENT_MAX_CHARS') ? CONTENT_MAX_CHARS : 10000;
    return strlen($content) <= $maxLength;
}

/**
 * Get content preview (first few lines)
 */
function getContentPreview($content, $maxLines = 2) {
    $lines = explode("\n", $content);
    $preview = array_slice($lines, 0, $maxLines);
    $previewText = implode("\n", $preview);
    
    // Truncate if too long
    if (strlen($previewText) > 150) {
        $previewText = substr($previewText, 0, 147) . '...';
    }
    
    return $previewText;
}

/**
 * Highlight hashtags in content
 */
function highlightHashtags($content) {
    return preg_replace('/#(\w+)/', '<span class="text-blue-600 font-medium">#$1</span>', e($content));
}

/**
 * Get remaining characters count
 */
function getRemainingChars($content) {
    $maxLength = defined('CONTENT_MAX_CHARS') ? CONTENT_MAX_CHARS : 10000;
    return $maxLength - strlen($content);
}

/**
 * Check if content is near limit
 */
function isNearLimit($content) {
    $remaining = getRemainingChars($content);
    return $remaining <= 100;
}

/**
 * Redirect to another page
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Get current URL parameters
 */
function getCurrentParams() {
    $params = [];
    if (!empty($_GET['q'])) $params['q'] = $_GET['q'];
    if (!empty($_GET['sort'])) $params['sort'] = $_GET['sort'];
    return $params;
}

/**
 * Build URL with parameters
 */
function buildUrl($base, $params = []) {
    if (empty($params)) {
        return $base;
    }
    
    $query = http_build_query($params);
    return $base . '?' . $query;
}

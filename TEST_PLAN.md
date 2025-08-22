# Notes App Test Plan

## Overview
This document outlines the manual testing procedures for the Notes App to ensure all features work correctly across different environments.

## Test Environment Requirements
- PHP 8.1, 8.2, or 8.3
- MariaDB 10.2+ or MySQL 5.7+
- Apache web server with mod_rewrite enabled
- Modern web browser (Chrome, Firefox, Safari, Edge)

## Pre-Test Setup
1. Import database schema from `database/schema.sql`
2. Copy `config.example.php` to `.env.php` and configure database credentials
3. Ensure all files are accessible via web server
4. Clear browser cache and cookies

## Test Cases

### 1. Basic CRUD Operations

#### 1.1 Create Note
**Objective**: Verify note creation functionality
**Steps**:
1. Navigate to index page
2. Click "New Note" button
3. Verify redirect to note.php with new hash_id
4. Verify note has "untitled" title and empty content
5. Add content and save
6. Verify note appears in index list

**Expected Result**: Note created successfully with auto-generated hash_id

#### 1.2 Read Note
**Objective**: Verify note viewing functionality
**Steps**:
1. Click on existing note title from index
2. Verify note loads with correct title and content
3. Verify timestamps are displayed correctly
4. Verify hash_id is visible

**Expected Result**: Note displays correctly with all information

#### 1.3 Update Note
**Objective**: Verify note editing functionality
**Steps**:
1. Open existing note for editing
2. Modify title and content
3. Click "Save Note"
4. Verify changes are saved
5. Verify updated_at timestamp changes
6. Return to index and verify changes appear

**Expected Result**: Note updates successfully with new timestamp

#### 1.4 Delete Note
**Objective**: Verify note deletion functionality
**Steps**:
1. Click delete icon on note card
2. Verify confirmation modal appears
3. Confirm deletion
4. Verify note disappears from list
5. Verify success message appears

**Expected Result**: Note deleted successfully with confirmation

### 2. Title Auto-fill Rule

#### 2.1 Empty Title with Long Content
**Objective**: Verify title generation from content
**Steps**:
1. Create new note
2. Leave title empty
3. Add content with word ≥5 characters (e.g., "Meeting notes for project")
4. Save note
5. Verify title becomes "Meeting"

**Expected Result**: Title auto-generated from first word ≥5 characters

#### 2.2 Empty Title with Short Content
**Objective**: Verify fallback to "untitled"
**Steps**:
1. Create new note
2. Leave title empty
3. Add content with only short words (e.g., "Hi there")
4. Save note
5. Verify title becomes "untitled"

**Expected Result**: Title set to "untitled" when no suitable word found

#### 2.3 Title with Special Characters
**Objective**: Verify title generation handles special characters
**Steps**:
1. Create note with content containing special characters
2. Test with content like "Hello-world! @#$%"
3. Verify title generation works correctly

**Expected Result**: Title generated correctly ignoring special characters

### 3. Search Functionality

#### 3.1 Plain Text Search
**Objective**: Verify basic search functionality
**Steps**:
1. Create notes with different content
2. Use search bar to find specific words
3. Test case-insensitive search
4. Verify results update in real-time

**Expected Result**: Search finds notes containing search terms

#### 3.2 Hashtag Search
**Objective**: Verify hashtag search functionality
**Steps**:
1. Create notes with #work, #personal, #ideas hashtags
2. Search for "#work"
3. Verify only notes with #work appear
4. Test search without # symbol
5. Test partial hashtag matches

**Expected Result**: Hashtag search works correctly

#### 3.3 Search Edge Cases
**Objective**: Verify search handles edge cases
**Steps**:
1. Search for empty string
2. Search for very long terms
3. Search for special characters
4. Search for non-existent terms

**Expected Result**: Search handles edge cases gracefully

### 4. Sorting Functionality

#### 4.1 Sort by Last Edited
**Objective**: Verify default sorting
**Steps**:
1. Create multiple notes
2. Edit different notes at different times
3. Verify notes appear in last-edited order
4. Verify sort dropdown shows "Last Edited" selected

**Expected Result**: Notes sorted by last edited (default)

#### 4.2 Sort by Date Created
**Objective**: Verify alternative sorting
**Steps**:
1. Change sort dropdown to "Date Created"
2. Verify notes reorder by creation date
3. Verify URL updates with sort parameter
4. Test with search filters applied

**Expected Result**: Notes sorted by creation date

#### 4.3 Sort Persistence
**Objective**: Verify sort preference persists
**Steps**:
1. Change sort order
2. Navigate away and return
3. Verify sort preference maintained
4. Test with browser refresh

**Expected Result**: Sort preference persists across navigation

### 5. Infinite Scroll

#### 5.1 Basic Infinite Scroll
**Objective**: Verify infinite scroll loads more notes
**Steps**:
1. Create more than 20 notes
2. Scroll to bottom of page
3. Verify "Load More Notes" button appears
4. Click button and verify additional notes load
5. Verify offset updates correctly

**Expected Result**: Infinite scroll loads additional notes

#### 5.2 Infinite Scroll with Search
**Objective**: Verify infinite scroll works with search filters
**Steps**:
1. Apply search filter
2. Scroll to load more results
3. Verify search term maintained in AJAX request
4. Verify results respect search filter

**Expected Result**: Infinite scroll respects search filters

#### 5.3 Infinite Scroll with Sorting
**Objective**: Verify infinite scroll works with sorting
**Steps**:
1. Change sort order
2. Scroll to load more results
3. Verify sort parameter maintained in AJAX request
4. Verify results respect sort order

**Expected Result**: Infinite scroll respects sort order

### 6. Content Validation

#### 6.1 Character Limit
**Objective**: Verify content length validation
**Steps**:
1. Create note with content near 10,000 character limit
2. Verify character counter shows remaining characters
3. Try to exceed limit
4. Verify error message appears
5. Verify save is prevented

**Expected Result**: Content length validation works correctly

#### 6.2 Character Counter
**Objective**: Verify character counter functionality
**Steps**:
1. Type in content textarea
2. Verify counter updates in real-time
3. Verify counter changes color when near limit
4. Verify counter shows "Character limit reached" at limit

**Expected Result**: Character counter works correctly

### 7. Security Features

#### 7.1 CSRF Protection
**Objective**: Verify CSRF token protection
**Steps**:
1. Open note edit page
2. View page source
3. Verify CSRF token in form
4. Try to submit form without token
5. Verify error message

**Expected Result**: CSRF protection works correctly

#### 7.2 Input Validation
**Objective**: Verify input sanitization
**Steps**:
1. Create note with HTML tags
2. Create note with JavaScript code
3. Verify content is displayed as plain text
4. Verify no script execution

**Expected Result**: Input properly sanitized

#### 7.3 SQL Injection Prevention
**Objective**: Verify SQL injection protection
**Steps**:
1. Try to inject SQL in search terms
2. Try to inject SQL in note content
3. Verify no database errors
4. Verify application continues to function

**Expected Result**: SQL injection attempts blocked

### 8. User Experience

#### 8.1 Responsive Design
**Objective**: Verify responsive layout
**Steps**:
1. Test on desktop browser
2. Test on tablet (resize browser)
3. Test on mobile (resize browser)
4. Verify layout adapts correctly

**Expected Result**: Layout responsive across devices

#### 8.2 Loading States
**Objective**: Verify loading indicators
**Steps**:
1. Submit forms and verify loading states
2. Load more notes and verify loading indicator
3. Verify buttons show appropriate states

**Expected Result**: Loading states provide user feedback

#### 8.3 Error Handling
**Objective**: Verify error messages
**Steps**:
1. Test with database connection issues
2. Test with invalid note IDs
3. Verify user-friendly error messages
4. Verify error recovery options

**Expected Result**: Errors handled gracefully with helpful messages

### 9. Performance Testing

#### 9.1 Large Dataset
**Objective**: Verify performance with many notes
**Steps**:
1. Create 100+ notes
2. Test search performance
3. Test infinite scroll performance
4. Verify reasonable response times

**Expected Result**: Application performs well with large datasets

#### 9.2 Search Performance
**Objective**: Verify search speed
**Steps**:
1. Test search with various terms
2. Verify search results appear quickly
3. Test search with special characters
4. Verify no timeout issues

**Expected Result**: Search performs quickly

### 10. Cross-Browser Testing

#### 10.1 Browser Compatibility
**Objective**: Verify cross-browser functionality
**Steps**:
1. Test on Chrome (latest)
2. Test on Firefox (latest)
3. Test on Safari (latest)
4. Test on Edge (latest)

**Expected Result**: Application works consistently across browsers

## Test Execution

### Test Schedule
- **Phase 1**: Basic CRUD operations (1-2 hours)
- **Phase 2**: Search and sorting (1-2 hours)
- **Phase 3**: Infinite scroll and validation (1-2 hours)
- **Phase 4**: Security and UX testing (1-2 hours)
- **Phase 5**: Performance and cross-browser (1-2 hours)

### Test Data Requirements
- At least 25 test notes with varied content
- Notes with hashtags (#work, #personal, #ideas)
- Notes with different creation and edit times
- Notes with content near character limits

### Bug Reporting
For each bug found, document:
1. **Bug ID**: Unique identifier
2. **Description**: Clear description of the issue
3. **Steps to Reproduce**: Detailed reproduction steps
4. **Expected Result**: What should happen
5. **Actual Result**: What actually happened
6. **Environment**: Browser, OS, PHP version
7. **Severity**: Critical, High, Medium, Low

## Success Criteria
- All test cases pass
- No critical or high-severity bugs
- Application functions correctly on all supported browsers
- Performance meets acceptable standards
- Security features working correctly

## Post-Test Activities
1. Document all bugs found
2. Verify bug fixes
3. Re-run failed test cases
4. Update test plan based on findings
5. Prepare test summary report

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title><?php echo isset($pageTitle) ? e($pageTitle) . ' - Notes App' : 'Notes App'; ?></title>
    
    <!-- Favicons -->
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png">
    <link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'note-red': '#F87171',
                        'note-blue': '#60A5FA',
                        'note-gray': '#9CA3AF',
                        'note-green': '#34D399',
                        'note-purple': '#A78BFA',
                        'note-orange': '#FBBF24',
                        'soft-blue': '#DBEAFE',
                        'soft-gray': '#F3F4F6',
                        'soft-white': '#FAFAFA'
                    }
                }
            }
        }
    </script>
    
    <!-- Custom CSS -->
    <style>
        .note-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }
        .note-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .content-textarea {
            resize: vertical;
            min-height: 300px;
            transition: all 0.2s ease-in-out;
        }
        .content-textarea:focus {
            transform: scale(1.002);
            box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.1);
        }
        .char-counter {
            transition: all 0.3s ease-in-out;
            backdrop-filter: blur(8px);
        }
        .char-counter.near-limit {
            color: #F59E0B;
            background: rgba(245, 158, 11, 0.1);
        }
        .char-counter.at-limit {
            color: #EF4444;
            background: rgba(239, 68, 68, 0.1);
        }
        
        .title-input {
            transition: all 0.2s ease-in-out;
        }
        .title-input:focus {
            transform: scale(1.01);
        }
        
        .save-button {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .save-button:hover:not(:disabled) {
            transform: scale(1.05);
        }
        .save-button:active:not(:disabled) {
            transform: scale(0.98);
        }
        
        .note-container {
            background: linear-gradient(135deg, #FAFAFA 0%, #F3F4F6 100%);
        }
        
        .note-card-inner {
            background: linear-gradient(135deg, #FFFFFF 0%, #FAFAFA 100%);
            border: 1px solid rgba(229, 231, 235, 0.8);
        }
        
        /* Mobile-specific fixes */
        @media (max-width: 768px) {
            body {
                overflow-x: hidden;
                width: 100%;
                max-width: 100vw;
            }
            
            .container, main {
                max-width: 100%;
                overflow-x: hidden;
            }
            
            .content-textarea {
                max-width: 100%;
                box-sizing: border-box;
                overflow-x: hidden;
            }
            
            input[type="text"] {
                max-width: 100%;
                box-sizing: border-box;
                overflow-x: hidden;
            }
        }
    </style>
</head>

<body class="bg-gradient-to-br from-soft-white via-gray-50 to-soft-gray min-h-screen">
    <!-- Navigation Header -->
    <nav class="bg-white/80 backdrop-blur-sm shadow-sm border-b border-gray-200/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-10">
                <div class="flex items-center">
                    <a href="index.php" class="text-xl font-bold text-gray-800 hover:text-note-blue transition-all duration-300 hover:scale-105">
                        📝 Notes App
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="create.php" class="bg-gradient-to-r from-note-blue to-note-purple hover:from-blue-600 hover:to-purple-600 text-white px-4 py-1 rounded-full font-medium transition-all duration-300 hover:scale-105 shadow-md hover:shadow-lg text-sm mr-2">
                        ✨ New
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Container -->
    <main class="max-w-7xl mx-auto px-0 sm:px-0 lg:px-8">
        <?php if (isset($errorMessage) && !empty(trim($errorMessage))): ?>
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-800"><?php echo e($errorMessage); ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($successMessage) && !empty(trim($successMessage))): ?>
            <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-800"><?php echo e($successMessage); ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

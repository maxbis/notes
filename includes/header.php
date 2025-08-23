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
                        'note-red': '#EF4444',
                        'note-blue': '#3B82F6',
                        'note-gray': '#6B7280',
                    }
                }
            }
        }
    </script>
    
    <!-- Custom CSS -->
    <style>
        .note-card {
            transition: all 0.2s ease-in-out;
        }
        .note-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .content-textarea {
            resize: vertical;
            min-height: 300px;
        }
        .char-counter {
            transition: color 0.2s ease-in-out;
        }
        .char-counter.near-limit {
            color: #F59E0B;
        }
        .char-counter.at-limit {
            color: #EF4444;
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

<body class="bg-gray-50 min-h-screen">
    <!-- Navigation Header -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="index.php" class="text-xl font-bold text-gray-900 hover:text-note-blue transition-colors">
                        📝 Notes App
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="create.php" class="bg-note-blue hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg font-medium transition-colors text-sm min-w-[100px]">
                        New Note
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Container -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
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

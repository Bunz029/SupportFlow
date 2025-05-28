<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SupportFlow') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Tailwind CSS -->
        <script src="https://unpkg.com/tailwindcss-jit-cdn"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            primary: {
                                50: '#f5f3ff',
                                100: '#ede9fe',
                                200: '#ddd6fe',
                                300: '#c4b5fd',
                                400: '#a78bfa',
                                500: '#8b5cf6',
                                600: '#7c3aed',
                                700: '#6d28d9',
                                800: '#5b21b6',
                                900: '#4c1d95'
                            }
                        },
                        fontFamily: {
                            sans: ['Inter', 'sans-serif']
                        }
                    }
                }
            }
        </script>

        <!-- Fallback mechanism -->
        <script>
            setTimeout(() => {
                if (!window.tailwind) {
                    console.warn('Primary Tailwind CDN failed, loading fallback...');
                    const script = document.createElement('script');
                    script.src = 'https://unpkg.com/tailwindcss-jit-cdn';
                    document.head.appendChild(script);
                }
            }, 1000);
        </script>

        <!-- Critical CSS -->
        <style>
            /* Minimal styles to prevent FOUC */
            .font-sans { font-family: Inter, system-ui, sans-serif; }
            .bg-gray-50 { background-color: #F9FAFB; }
            .bg-white { background-color: #FFFFFF; }
            
            body {
                font-family: 'Inter', sans-serif;
            }
            
            .gradient-bg {
                background: linear-gradient(120deg, #7c3aed 0%, #6366f1 100%);
            }
            
            .auth-gradient {
                background: linear-gradient(135deg, #f9fafb 0%, #ede9fe 100%);
            }

            .form-input-focus {
                transition: all 0.3s ease;
            }
            
            .form-input-focus:focus {
                border-color: #8b5cf6;
                box-shadow: 0 0 0 2px rgba(139, 92, 246, 0.1);
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 auth-gradient">
            <div>
                <a href="/" class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                    </svg>
                    <span class="ml-2 text-3xl font-bold text-purple-600">SupportFlow</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-8 py-8 bg-white shadow-xl overflow-hidden sm:rounded-xl relative">
                <div class="absolute -inset-1 bg-gradient-to-r from-purple-500 to-indigo-500 rounded-xl blur opacity-10"></div>
                <div class="relative">
                    {{ $slot }}
                </div>
            </div>
        </div>

        <style>
            /* Add these styles to ensure form elements are visible */
            .form-input, .form-textarea, .form-select {
                @apply border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-md shadow-sm;
            }
            
            /* Style submit buttons */
            [type='submit'] {
                @apply bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-4 rounded-md transition duration-150 ease-in-out;
            }
            
            /* Style links */
            .text-link {
                @apply text-purple-600 hover:text-purple-700 font-medium;
            }
        </style>
    </body>
</html>

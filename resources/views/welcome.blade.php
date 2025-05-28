<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SupportFlow - Intelligent Support Management</title>
    
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

    <!-- Custom Styles -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .gradient-bg {
            background: linear-gradient(120deg, #7c3aed 0%, #6366f1 100%);
        }
        
        .hero-gradient {
            background: linear-gradient(100deg, #f9fafb 50%, #ede9fe 100%);
        }
        
        .feature-card {
            transition: all 0.3s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
        }
        
        .role-card {
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
        }
        
        .role-card:hover {
            border-color: #8b5cf6;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        .navbar-fixed {
            backdrop-filter: blur(8px);
            background-color: rgba(255, 255, 255, 0.8);
        }
    </style>
</head>
<body class="antialiased bg-gray-100">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="fixed w-full z-50 navbar-fixed border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-20 items-center">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                        </svg>
                        <span class="ml-2 text-2xl font-bold text-purple-600">SupportFlow</span>
                    </div>
                    <div class="hidden md:flex items-center space-x-8">
                        <a href="#features" class="text-gray-700 hover:text-purple-600 font-medium">Features</a>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-purple-600 font-medium">Log in</a>
                        <a href="{{ route('register') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-5 py-2.5 rounded-lg font-medium transition duration-300">Register as Client</a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section id="hero" class="hero-gradient pt-32 pb-20">
            <div class="max-w-7xl mx-auto flex flex-col-reverse md:flex-row items-center px-4 sm:px-6 lg:px-8">
                <div class="md:w-1/2 w-full md:pr-12 text-left mt-12 md:mt-0">
                    <div class="inline-block px-3 py-1 rounded-full bg-purple-100 text-purple-700 font-medium text-sm mb-6">
                        Client Support Portal
                    </div>
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 mb-6 leading-tight">
                        Get World-Class <span class="text-purple-600">Customer Support</span> Experience
                    </h1>
                    <p class="text-lg text-gray-600 mb-8 leading-relaxed">
                        Join our support platform to get quick assistance, track your tickets, and access our knowledge base - all in one place.
                    </p>
                    <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                        <a href="{{ route('register') }}" class="flex items-center justify-center bg-purple-600 hover:bg-purple-700 text-white px-8 py-3.5 rounded-lg font-semibold text-lg shadow-lg hover:shadow-xl transition duration-300">
                            Get Started
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    </div>
                    <div class="mt-10 flex items-center">
                        <div class="flex -space-x-2">
                            <img src="https://ui-avatars.com/api/?name=User1" class="w-10 h-10 rounded-full border-2 border-white" alt="User">
                            <img src="https://ui-avatars.com/api/?name=User2" class="w-10 h-10 rounded-full border-2 border-white" alt="User">
                            <img src="https://ui-avatars.com/api/?name=User3" class="w-10 h-10 rounded-full border-2 border-white" alt="User">
                        </div>
                        <div class="ml-4">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            </div>
                            <p class="text-sm text-gray-600">Trusted by <span class="font-semibold">500+</span> companies</p>
                        </div>
                    </div>
                </div>
                <div class="md:w-1/2 w-full flex justify-center">
                    <div class="relative">
                        <div class="absolute -inset-1 bg-gradient-to-r from-primary-500 to-indigo-500 rounded-xl blur opacity-30"></div>
                        <div class="relative rounded-xl shadow-2xl w-full max-w-2xl border border-gray-200 bg-white p-8">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 600" class="w-full h-full">
                                <!-- Definitions -->
                                <defs>
                                    <pattern id="grid" width="30" height="30" patternUnits="userSpaceOnUse">
                                        <path d="M 30 0 L 0 0 0 30" fill="none" stroke="#f3f4f6" stroke-width="1"/>
                                    </pattern>
                                    <linearGradient id="purpleGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" style="stop-color:#8b5cf6;stop-opacity:1" />
                                        <stop offset="100%" style="stop-color:#6d28d9;stop-opacity:1" />
                                    </linearGradient>
                                    <linearGradient id="lightPurpleGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" style="stop-color:#c4b5fd;stop-opacity:1" />
                                        <stop offset="100%" style="stop-color:#a78bfa;stop-opacity:1" />
                                    </linearGradient>
                                </defs>

                                <!-- Background -->
                                <rect width="800" height="600" fill="white"/>
                                <rect width="800" height="600" fill="url(#grid)"/>
                                
                                <!-- Decorative Circles -->
                                <circle cx="100" cy="100" r="80" fill="#f3f4f6" opacity="0.3"/>
                                <circle cx="700" cy="500" r="100" fill="#f3f4f6" opacity="0.3"/>
                                <circle cx="400" cy="50" r="30" fill="#c4b5fd" opacity="0.2"/>
                                <circle cx="750" cy="150" r="40" fill="#8b5cf6" opacity="0.2"/>
                                
                                <!-- Support Agent -->
                                <g transform="translate(150, 200)">
                                    <!-- Body -->
                                    <circle cx="80" cy="80" r="60" fill="url(#purpleGradient)"/>
                                    <!-- Head -->
                                    <circle cx="80" cy="50" r="30" fill="#f3f4f6"/>
                                    <!-- Headset -->
                                    <path d="M50 50 Q35 50 35 65 L35 85 Q35 95 45 95 L55 95" 
                                          fill="none" stroke="#8b5cf6" stroke-width="6" stroke-linecap="round"/>
                                    <!-- Face -->
                                    <circle cx="70" cy="45" r="4" fill="#8b5cf6"/>
                                    <circle cx="90" cy="45" r="4" fill="#8b5cf6"/>
                                    <path d="M65 60 Q80 70 95 60" fill="none" stroke="#8b5cf6" stroke-width="3" stroke-linecap="round"/>
                                    <!-- Desktop -->
                                    <rect x="40" y="120" width="80" height="60" rx="5" fill="#e5e7eb"/>
                                    <rect x="45" y="125" width="70" height="45" rx="3" fill="white"/>
                                </g>

                                <!-- Customer -->
                                <g transform="translate(500, 200)">
                                    <!-- Body -->
                                    <circle cx="80" cy="80" r="60" fill="url(#lightPurpleGradient)"/>
                                    <!-- Head -->
                                    <circle cx="80" cy="50" r="30" fill="#f3f4f6"/>
                                    <!-- Face -->
                                    <circle cx="70" cy="45" r="4" fill="#8b5cf6"/>
                                    <circle cx="90" cy="45" r="4" fill="#8b5cf6"/>
                                    <path d="M65 60 Q80 70 95 60" fill="none" stroke="#8b5cf6" stroke-width="3" stroke-linecap="round"/>
                                    <!-- Mobile Device -->
                                    <rect x="60" y="120" width="40" height="60" rx="5" fill="#e5e7eb"/>
                                    <rect x="65" y="125" width="30" height="45" rx="3" fill="white"/>
                                </g>

                                <!-- Chat Bubbles -->
                                <!-- From Agent -->
                                <g transform="translate(90, 110)">
                                    <path d="M0 20 
                                           C-20 20 -20 20 -20 40
                                           L-20 80
                                           C-20 100 -20 100 0 100
                                           L100 100
                                           L110 120
                                           L120 100
                                           L140 100
                                           C160 100 160 100 160 80
                                           L160 40
                                           C160 20 160 20 140 20
                                           Z" 
                                          fill="url(#purpleGradient)"/>
                                    <rect x="0" y="45" width="120" height="8" fill="white" rx="4"/>
                                    <rect x="0" y="65" width="140" height="8" fill="white" rx="4"/>
                                </g>

                                <!-- From Customer -->
                                <g transform="translate(320, 110)">
                                    <path d="M400 20
                                           C420 20 420 20 420 40
                                           L420 80
                                           C420 100 420 100 400 100
                                           L300 100
                                           L290 120
                                           L280 100
                                           L260 100
                                           C240 100 240 100 240 80
                                           L240 40
                                           C240 20 240 20 260 20
                                           Z"
                                          fill="url(#lightPurpleGradient)"/>
                                    <rect x="260" y="45" width="140" height="8" fill="white" rx="4"/>
                                    <rect x="260" y="65" width="120" height="8" fill="white" rx="4"/>
                                </g>

                                <!-- Connection Lines -->
                                <g transform="translate(0, 0)" opacity="0.5">
                                    <path d="M200 400 Q400 500 600 400" stroke="#8b5cf6" stroke-width="3" fill="none" stroke-dasharray="8 8"/>
                                    <path d="M200 410 Q400 510 600 410" stroke="#c4b5fd" stroke-width="2" fill="none" stroke-dasharray="8 8"/>
                                    <circle cx="200" cy="400" r="5" fill="#8b5cf6"/>
                                    <circle cx="600" cy="400" r="5" fill="#8b5cf6"/>
                                    <circle cx="400" cy="470" r="5" fill="#8b5cf6"/>
                                </g>

                                <!-- Flying Icons -->
                                <g transform="translate(350, 100)">
                                    <circle cx="0" cy="0" r="15" fill="#8b5cf6" opacity="0.2"/>
                                    <circle cx="100" cy="50" r="10" fill="#c4b5fd" opacity="0.2"/>
                                    <circle cx="-50" cy="100" r="12" fill="#8b5cf6" opacity="0.2"/>
                                </g>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <span class="inline-block px-3 py-1 rounded-full bg-primary-100 text-primary-700 font-medium text-sm mb-4">
                        Client Features
                    </span>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Everything you need as a client</h2>
                    <p class="text-lg text-gray-600">Access support services and manage your tickets with our comprehensive client portal.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Ticket Management -->
                    <div class="feature-card bg-white rounded-xl p-6 shadow-lg border border-gray-100">
                        <div class="bg-primary-100 text-primary-600 p-3 rounded-xl inline-block mb-5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-xl mb-3 text-gray-900">Submit & Track Tickets</h3>
                        <p class="text-gray-600 mb-5">Create support tickets and track their progress in real-time.</p>
                        <ul class="space-y-2">
                            <li class="flex items-center text-gray-600">
                                <svg class="h-5 w-5 text-green-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                Easy Ticket Creation
                            </li>
                            <li class="flex items-center text-gray-600">
                                <svg class="h-5 w-5 text-green-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                Real-time Updates
                            </li>
                            <li class="flex items-center text-gray-600">
                                <svg class="h-5 w-5 text-green-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                File Attachments
                            </li>
                        </ul>
                    </div>
                    <!-- Knowledge Base -->
                    <div class="feature-card bg-white rounded-xl p-6 shadow-lg border border-gray-100">
                        <div class="bg-primary-100 text-primary-600 p-3 rounded-xl inline-block mb-5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-xl mb-3 text-gray-900">Self-Service Resources</h3>
                        <p class="text-gray-600 mb-5">Access our knowledge base for instant solutions to common questions.</p>
                        <ul class="space-y-2">
                            <li class="flex items-center text-gray-600">
                                <svg class="h-5 w-5 text-green-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                Helpful Articles
                            </li>
                            <li class="flex items-center text-gray-600">
                                <svg class="h-5 w-5 text-green-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                Quick Search
                            </li>
                            <li class="flex items-center text-gray-600">
                                <svg class="h-5 w-5 text-green-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                Step-by-step Guides
                            </li>
                        </ul>
                    </div>
                    <!-- Communication Hub -->
                    <div class="feature-card bg-white rounded-xl p-6 shadow-lg border border-gray-100">
                        <div class="bg-primary-100 text-primary-600 p-3 rounded-xl inline-block mb-5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-xl mb-3 text-gray-900">Direct Communication</h3>
                        <p class="text-gray-600 mb-5">Communicate directly with our support team through your tickets.</p>
                        <ul class="space-y-2">
                            <li class="flex items-center text-gray-600">
                                <svg class="h-5 w-5 text-green-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                Instant Messaging
                            </li>
                            <li class="flex items-center text-gray-600">
                                <svg class="h-5 w-5 text-green-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                Status Updates
                            </li>
                            <li class="flex items-center text-gray-600">
                                <svg class="h-5 w-5 text-green-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                Email Notifications
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works Section -->
        <section id="how-it-works" class="py-20 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <span class="inline-block px-3 py-1 rounded-full bg-primary-100 text-primary-700 font-medium text-sm mb-4">
                        Getting Started
                    </span>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Start Using SupportFlow</h2>
                    <p class="text-lg text-gray-600">Get started with our client support portal in three simple steps.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Step 1 -->
                    <div class="relative">
                        <div class="absolute left-0 top-0 w-8 h-8 bg-primary-100 text-primary-600 rounded-full flex items-center justify-center font-bold">1</div>
                        <div class="pl-12">
                            <h3 class="text-xl font-bold text-gray-900 mb-3">Register Your Account</h3>
                            <p class="text-gray-600">Create your client account with your company details.</p>
                        </div>
                    </div>
                    <!-- Step 2 -->
                    <div class="relative">
                        <div class="absolute left-0 top-0 w-8 h-8 bg-primary-100 text-primary-600 rounded-full flex items-center justify-center font-bold">2</div>
                        <div class="pl-12">
                            <h3 class="text-xl font-bold text-gray-900 mb-3">Submit Support Request</h3>
                            <p class="text-gray-600">Create a detailed support ticket describing your issue or question.</p>
                        </div>
                    </div>
                    <!-- Step 3 -->
                    <div class="relative">
                        <div class="absolute left-0 top-0 w-8 h-8 bg-primary-100 text-primary-600 rounded-full flex items-center justify-center font-bold">3</div>
                        <div class="pl-12">
                            <h3 class="text-xl font-bold text-gray-900 mb-3">Get Support</h3>
                            <p class="text-gray-600">Receive assistance from our dedicated support team and track progress.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-100">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="flex items-center mb-4 md:mb-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                        </svg>
                        <span class="ml-2 text-xl font-bold text-gray-900">SupportFlow</span>
                    </div>
                    <div class="flex space-x-6">
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-purple-600">Login</a>
                        <a href="{{ route('register') }}" class="text-gray-600 hover:text-purple-600">Register as Client</a>
                    </div>
                </div>
                <div class="mt-8 border-t border-gray-100 pt-8 text-center">
                    <p class="text-gray-500">&copy; {{ date('Y') }} SupportFlow. All rights reserved.</p>
                    <p class="text-sm text-gray-400 mt-2">For support agent or admin access, please contact your system administrator.</p>
                </div>
            </div>
        </footer>
    </div>
</body>
</html> 
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Lindela Travel & Tours') }} - Login</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body {
                font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #000000 0%, #1a1a1a 30%, #2a2418 50%, #1a1a1a 70%, #000000 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 2rem;
            }

            .login-card {
                background: white;
                border-radius: 1rem;
                box-shadow: 0 25px 50px rgba(0, 0, 0, 0.8), 0 0 0 1px rgba(255, 221, 0, 0.1);
                overflow: hidden;
                max-width: 900px;
                width: 100%;
                display: flex;
                min-height: 500px;
            }

            .logo-panel {
                flex: 1;
                background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 50%, #1a1a1a 100%);
                background-size: contain;
                background-position: center;
                background-repeat: no-repeat;
                position: relative;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                padding: 3rem;
                color: #ffdd00;
            }

            .logo-overlay {
                position: absolute;
                inset: 0;
                background: transparent;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                text-align: center;
            }

            .quote-text {
                font-size: 2.2rem;
                font-weight: 700;
                line-height: 1.3;
                text-align: center;
                margin-top: 2rem;
                text-shadow: 0 2px 10px rgba(255, 255, 255, 0.8);
                color: #d97706;
                filter: contrast(1.2);
            }

            .subtitle-text {
                font-size: 1.1rem;
                margin-top: 1rem;
                color: #92400e;
                font-weight: 600;
                text-shadow: 0 1px 5px rgba(255, 255, 255, 0.8);
                filter: contrast(1.3);
            }

            .login-panel {
                flex: 1;
                background: linear-gradient(135deg, #111111 0%, #1a1a1a 50%, #111111 100%);
                padding: 3rem;
                display: flex;
                flex-direction: column;
                justify-content: center;
                color: #ffffff;
                position: relative;
                box-shadow: inset 0 0 50px rgba(0, 0, 0, 0.3);
            }

            .login-header {
                text-align: center;
                margin-bottom: 2rem;
            }

            .logo-container {
                margin-bottom: 1.5rem;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .login-logo {
                max-width: 280px;
                max-height: 100px;
                width: auto;
                height: auto;
                object-fit: contain;
                filter: drop-shadow(0 2px 10px rgba(251, 191, 36, 0.3));
            }

            .company-title {
                font-size: 1.4rem;
                font-weight: 700;
                letter-spacing: 1px;
                border: 2px solid #ffdd00;
                padding: 0.75rem 1.5rem;
                display: inline-block;
                margin-bottom: 1rem;
                color: #ffdd00;
                text-shadow: 0 0 10px rgba(255, 221, 0, 0.5);
            }

            .system-subtitle {
                font-size: 0.9rem;
                color: #ffffff;
                font-weight: 500;
                margin-bottom: 2rem;
                text-shadow: 0 1px 3px rgba(0, 0, 0, 0.8);
            }

            .form-group {
                margin-bottom: 1.5rem;
                position: relative;
            }

            .form-input {
                width: 100%;
                padding: 1rem 1rem 1rem 3rem;
                border: none;
                border-bottom: 2px solid rgba(255, 221, 0, 0.8);
                background: rgba(255, 255, 255, 0.02);
                color: #ffffff;
                font-size: 1rem;
                transition: all 0.3s ease;
                text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
                border-radius: 0.25rem;
            }

            .form-input:focus {
                outline: none;
                border-bottom-color: #ffdd00;
                background: rgba(255, 221, 0, 0.08);
                box-shadow: 0 0 15px rgba(255, 221, 0, 0.2);
            }

            .form-input::placeholder {
                color: rgba(255, 255, 255, 0.8);
                text-shadow: none;
            }

            .input-icon {
                position: absolute;
                left: 0.75rem;
                top: 50%;
                transform: translateY(-50%);
                color: #ffdd00;
                width: 20px;
                height: 20px;
                display: flex;
                align-items: center;
                justify-content: center;
                filter: drop-shadow(0 0 3px rgba(255, 221, 0, 0.6));
            }

            .input-icon svg {
                width: 20px;
                height: 20px;
                shape-rendering: geometricPrecision;
                transform: translateZ(0);
                backface-visibility: hidden;
            }

            .field-toggle {
                position: absolute;
                right: 0.75rem;
                top: 50%;
                transform: translateY(-50%);
                color: #ffdd00;
                cursor: pointer;
                transition: color 0.3s ease;
                width: 20px;
                height: 20px;
                display: flex;
                align-items: center;
                justify-content: center;
                filter: drop-shadow(0 0 3px rgba(255, 221, 0, 0.6));
            }

            .field-toggle:hover {
                color: #ffffff;
                filter: drop-shadow(0 0 8px rgba(255, 255, 255, 0.8));
                transform: translateY(-50%) scale(1.1);
            }

            .field-toggle svg {
                width: 20px;
                height: 20px;
                shape-rendering: geometricPrecision;
                transform: translateZ(0);
                backface-visibility: hidden;
            }

            .forgot-password {
                text-align: right;
                margin-bottom: 2rem;
            }

            .forgot-password a {
                color: #ffdd00;
                text-decoration: none;
                font-size: 0.9rem;
                transition: all 0.3s ease;
                text-shadow: 0 1px 3px rgba(0, 0, 0, 0.8);
            }

            .forgot-password a:hover {
                color: #ffffff;
                text-shadow: 0 0 8px rgba(255, 255, 255, 0.8);
            }

            .enter-button {
                width: 100%;
                padding: 1rem;
                background: linear-gradient(135deg, #ffdd00, #ffa500);
                color: #000000;
                border: none;
                border-radius: 2rem;
                font-size: 1rem;
                font-weight: 700;
                letter-spacing: 1px;
                cursor: pointer;
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
                box-shadow: 0 4px 15px rgba(255, 221, 0, 0.4);
            }

            .enter-button:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(255, 221, 0, 0.6);
                background: linear-gradient(135deg, #ffffff, #ffdd00);
            }

            /* Paper Airplane Animation */
            .paper-airplane {
                position: fixed;
                width: 30px;
                height: 30px;
                pointer-events: none;
                z-index: 9999;
                opacity: 0;
                color: #ffdd00;
                filter: drop-shadow(0 0 5px rgba(255, 221, 0, 0.8));
            }

            .paper-airplane.flying {
                opacity: 1;
                animation: flyAcrossScreen 2s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
            }

            @keyframes flyAcrossScreen {
                0% { 
                    transform: translate(-50px, 0) rotate(0deg) scale(0.5); 
                    opacity: 1; 
                }
                25% { 
                    transform: translate(200px, -100px) rotate(15deg) scale(0.8); 
                }
                50% { 
                    transform: translate(500px, -150px) rotate(25deg) scale(1); 
                }
                75% { 
                    transform: translate(800px, -100px) rotate(35deg) scale(1.2); 
                }
                100% { 
                    transform: translate(1200px, -50px) rotate(45deg) scale(0.8); 
                    opacity: 0; 
                }
            }

            .success-message {
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                background: linear-gradient(135deg, #ffdd00, #ffa500);
                color: #000000;
                padding: 1.5rem 2rem;
                border-radius: 1rem;
                font-weight: 700;
                z-index: 10000;
                opacity: 0;
                scale: 0.8;
                transition: all 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5), 0 0 20px rgba(255, 221, 0, 0.6);
                border: 2px solid #ffffff;
            }

            .success-message.show {
                opacity: 1;
                scale: 1;
            }

            .airplane-icon {
                transition: transform 0.3s ease;
            }

            .enter-button:hover .airplane-icon {
                transform: translateX(5px);
            }

            /* Responsive Design */
            @media (max-width: 768px) {
                body {
                    padding: 1rem;
                }
                
                .login-card {
                    flex-direction: column;
                    min-height: auto;
                }
                
                .logo-panel {
                    min-height: 200px;
                    padding: 2rem;
                }
                
                .quote-text {
                    font-size: 1.6rem;
                }
                
                .login-panel {
                    padding: 2rem;
                }

                .login-logo {
                    max-width: 220px;
                    max-height: 60px;
                }
            }
        </style>
    </head>
    <body>
        <!-- Paper Airplane for Animation -->
        <div class="paper-airplane" id="paperAirplane">
            <svg viewBox="0 0 24 24" fill="currentColor" class="w-full h-full">
                <path d="M2 21L23 12L2 3V10L17 12L2 14V21Z"/>
            </svg>
        </div>

        <!-- Success Message -->
        <div class="success-message" id="successMessage">
            ✈️ Welcome aboard! Login Successfully...
        </div>

        <div class="login-card">
            <!-- Left Panel - Logo Background -->
            <div class="logo-panel" style="background-image: url('{{ asset('assets/Mobile encryption-pana.png') }}');">
                
            </div>

            <!-- Right Panel - Login Form -->
            <div class="login-panel">
                <div class="login-header">
                    <div class="logo-container">
                        <img src="{{ asset('assets/Lindela Shake White Text.png') }}" alt="Lindela Travel & Tours" class="login-logo">
                    </div>
                    <div class="system-subtitle">Credential Management System</div>
                </div>

                <!-- Login Form -->
                {{ $slot }}
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const loginForm = document.querySelector('form');
                const paperAirplane = document.getElementById('paperAirplane');
                const successMessage = document.getElementById('successMessage');
                
                if (loginForm) {
                    loginForm.addEventListener('submit', function(e) {
                        // Get the login button position
                        const loginBtn = loginForm.querySelector('button[type="submit"]');
                        const btnRect = loginBtn.getBoundingClientRect();
                        
                        // Position paper airplane at button location
                        paperAirplane.style.left = btnRect.left + 'px';
                        paperAirplane.style.top = btnRect.top + 'px';
                        
                        // Start airplane animation
                        paperAirplane.classList.add('flying');
                        
                        // Show success message after a delay
                        setTimeout(() => {
                            successMessage.classList.add('show');
                        }, 1000);
                        
                        // Clean up animations
                        setTimeout(() => {
                            paperAirplane.classList.remove('flying');
                            successMessage.classList.remove('show');
                        }, 3000);
                    });
                }

                // Email field toggle functionality (NEW)
                const emailToggles = document.querySelectorAll('[data-field="email"] .field-toggle');
                emailToggles.forEach(toggle => {
                    const emailInput = toggle.parentElement.querySelector('input[type="email"]');
                    if (emailInput) {
                        toggle.addEventListener('click', function() {
                            const currentType = emailInput.getAttribute('type');
                            const newType = currentType === 'email' ? 'text' : 'email';
                            emailInput.setAttribute('type', newType);
                            updateToggleIcon(toggle, newType === 'text');
                        });
                    }
                });

                // Password toggle functionality (IMPROVED)
                const passwordToggles = document.querySelectorAll('[data-field="password"] .field-toggle, .password-toggle');
                passwordToggles.forEach(toggle => {
                    const passwordInput = toggle.parentElement.querySelector('input[type="password"], input[name*="password"]');
                    if (passwordInput) {
                        toggle.addEventListener('click', function() {
                            const currentType = passwordInput.getAttribute('type');
                            const newType = currentType === 'password' ? 'text' : 'password';
                            passwordInput.setAttribute('type', newType);
                            updateToggleIcon(toggle, newType === 'text');
                        });
                    }
                });

                // Generic toggle for any input field with toggle icon
                const allToggles = document.querySelectorAll('.field-toggle');
                allToggles.forEach(toggle => {
                    if (!toggle.hasAttribute('data-initialized')) {
                        toggle.setAttribute('data-initialized', 'true');
                        const input = toggle.parentElement.querySelector('input');
                        if (input) {
                            toggle.addEventListener('click', function() {
                                const currentType = input.getAttribute('type');
                                let newType;
                                
                                // Handle different input types
                                if (currentType === 'password') {
                                    newType = 'text';
                                } else if (currentType === 'text' && input.name.includes('password')) {
                                    newType = 'password';
                                } else if (currentType === 'email') {
                                    newType = 'text';
                                } else if (currentType === 'text' && input.name.includes('email')) {
                                    newType = 'email';
                                } else {
                                    // Default toggle between text and password
                                    newType = currentType === 'text' ? 'password' : 'text';
                                }
                                
                                input.setAttribute('type', newType);
                                updateToggleIcon(toggle, newType === 'text');
                            });
                        }
                    }
                });

                // Function to update toggle icon (IMPROVED)
                function updateToggleIcon(toggleElement, isVisible) {
                    if (isVisible) {
                        // Show "eye closed" icon
                        toggleElement.innerHTML = `
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="filter: drop-shadow(0 0 3px rgba(255, 221, 0, 0.6));">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
                            </svg>
                        `;
                    } else {
                        // Show "eye open" icon
                        toggleElement.innerHTML = `
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="filter: drop-shadow(0 0 3px rgba(255, 221, 0, 0.6));">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        `;
                    }
                }
            });
        </script>
    </body>
</html>
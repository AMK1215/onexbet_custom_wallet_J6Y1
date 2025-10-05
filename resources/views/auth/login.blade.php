<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ONEXBET | Login</title>

    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/adminlte.min.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }

        body, html {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            overflow-x: hidden;
        }

        .login-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
        }

        #rainbow-bg {
            position: fixed;
            top: 0; 
            left: 0; 
            width: 100vw; 
            height: 100vh;
            z-index: 0;
            pointer-events: none;
            opacity: 0.6;
        }

        .login-container {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 420px;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-logo {
            margin-bottom: 1rem;
        }

        .login-logo h1 {
            color: #fff;
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
            letter-spacing: -0.02em;
        }

        .login-subtitle {
            color: rgba(255,255,255,0.9);
            font-size: 1.1rem;
            font-weight: 400;
            margin: 0;
            text-shadow: 0 1px 5px rgba(0,0,0,0.2);
        }

        .login-card {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1), 0 0 0 1px rgba(255,255,255,0.2);
            padding: 2.5rem;
            border: none;
            transition: all 0.3s ease;
        }

        .login-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 25px 50px rgba(0,0,0,0.15), 0 0 0 1px rgba(255,255,255,0.3);
        }

        .login-card-body {
            padding: 0;
        }

        .welcome-text {
            text-align: center;
            margin-bottom: 2rem;
            color: #374151;
            font-size: 1.1rem;
            font-weight: 500;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .input-group {
            position: relative;
        }

        .form-control {
            height: 56px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 0 1rem 0 3rem;
            font-size: 1rem;
            font-weight: 400;
            transition: all 0.3s ease;
            background: #fff;
        }

        .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            outline: none;
        }

        .form-control::placeholder {
            color: #9ca3af;
            font-weight: 400;
        }

        .input-group-text {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6b7280;
            z-index: 3;
            padding: 0;
        }

        .input-group-append {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            z-index: 3;
        }

        .input-group-append .input-group-text {
            position: static;
            transform: none;
            background: none;
            border: none;
            color: #6b7280;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .input-group-append .input-group-text:hover {
            color: #3b82f6;
        }

        .form-control.is-invalid {
            border-color: #ef4444;
        }

        .form-control.is-invalid:focus {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }

        .invalid-feedback {
            display: block;
            margin-top: 0.5rem;
            color: #ef4444;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .icheck-primary {
            display: flex;
            align-items: center;
        }

        .icheck-primary input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin-right: 0.5rem;
            accent-color: #3b82f6;
        }

        .icheck-primary label {
            color: #6b7280;
            font-size: 0.9rem;
            font-weight: 500;
            margin: 0;
            cursor: pointer;
        }

        .forgot-password {
            color: #3b82f6;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .forgot-password:hover {
            color: #2563eb;
            text-decoration: none;
        }

        .btn-login {
            width: 100%;
            height: 56px;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }

        .alert-danger {
            background: #fef2f2;
            color: #dc2626;
            border-left: 4px solid #ef4444;
        }

        .alert .close {
            background: none;
            border: none;
            font-size: 1.25rem;
            color: #dc2626;
            opacity: 0.7;
            transition: opacity 0.3s ease;
        }

        .alert .close:hover {
            opacity: 1;
        }

        /* Mobile optimizations */
        @media (max-width: 768px) {
            .login-page {
                padding: 1rem;
            }

            .login-container {
                max-width: 100%;
            }

            .login-card {
                padding: 2rem 1.5rem;
                border-radius: 16px;
            }

            .login-logo h1 {
                font-size: 2rem;
            }

            .login-subtitle {
                font-size: 1rem;
            }

            .form-control {
                height: 52px;
                font-size: 16px; /* Prevents zoom on iOS */
            }

            .btn-login {
                height: 52px;
            }

            .remember-forgot {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 1.5rem 1rem;
            }

            .login-logo h1 {
                font-size: 1.75rem;
            }
        }

        /* Loading state */
        .btn-login.loading {
            pointer-events: none;
            opacity: 0.8;
        }

        .btn-login.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin-left: -10px;
            margin-top: -10px;
            border: 2px solid transparent;
            border-top-color: #ffffff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Accessibility improvements */
        .form-control:focus,
        .btn-login:focus {
            outline: 2px solid #3b82f6;
            outline-offset: 2px;
        }

        /* High contrast mode support */
        @media (prefers-contrast: high) {
            .login-card {
                background: #fff;
                border: 2px solid #000;
            }
            
            .form-control {
                border-color: #000;
            }
        }

        /* Reduced motion support */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>

<body class="hold-transition login-page">
<canvas id="rainbow-bg"></canvas>
    <div class="login-container">
        <div class="login-header">
            <div class="login-logo">
                <h1>ONEXBET</h1>
            </div>
            <p class="login-subtitle">Welcome back to your account</p>
        </div>
        
        <div class="login-card">
            <div class="login-card-body">
                <p class="welcome-text">Sign in to continue</p>
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                
                <form method="POST" action="{{ route('login') }}" id="loginForm">
                    @csrf
                    
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                            <input id="user_name" type="text"
                                class="form-control @error('user_name') is-invalid @enderror" 
                                name="user_name"
                                value="{{ old('user_name') }}" 
                                required 
                                placeholder="Enter your username" 
                                autofocus
                                autocomplete="username">
                            @error('user_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                            <input id="password" type="password"
                                class="form-control @error('password') is-invalid @enderror" 
                                name="password" 
                                required
                                placeholder="Enter your password"
                                autocomplete="current-password">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-eye" onclick="togglePassword()" id="eyeToggle"
                                        style="cursor: pointer;" title="Toggle password visibility"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="remember-forgot">
                        <div class="icheck-primary">
                            <input type="checkbox" id="remember" name="remember">
                            <label for="remember">
                                Remember me
                            </label>
                        </div>
                        <a href="#" class="forgot-password">Forgot password?</a>
                    </div>
                    
                    <button type="submit" class="btn-login" id="loginBtn">
                        <span class="btn-text">Sign In</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- chat box -->
        <!-- Telegram Chat Box -->


    <!-- chat box end -->


    <script>
        // Modern password toggle function
        function togglePassword() {
            const passwordInput = document.getElementById("password");
            const eyeToggle = document.getElementById("eyeToggle");
            
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                eyeToggle.classList.remove('fa-eye');
                eyeToggle.classList.add('fa-eye-slash');
                eyeToggle.setAttribute('title', 'Hide password');
            } else {
                passwordInput.type = "password";
                eyeToggle.classList.remove('fa-eye-slash');
                eyeToggle.classList.add('fa-eye');
                eyeToggle.setAttribute('title', 'Show password');
            }
        }

        // Enhanced form submission with loading state
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('loginBtn');
            const btnText = submitBtn.querySelector('.btn-text');
            
            // Add loading state
            submitBtn.classList.add('loading');
            btnText.textContent = 'Signing in...';
            submitBtn.disabled = true;
            
            // Re-enable after 5 seconds as fallback
            setTimeout(() => {
                submitBtn.classList.remove('loading');
                btnText.textContent = 'Sign In';
                submitBtn.disabled = false;
            }, 5000);
        });

        // Enhanced input focus effects
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                if (!this.value) {
                    this.parentElement.classList.remove('focused');
                }
            });
        });

        // Auto-dismiss alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    if (alert && alert.parentNode) {
                        alert.style.opacity = '0';
                        alert.style.transform = 'translateY(-10px)';
                        setTimeout(() => {
                            if (alert.parentNode) {
                                alert.remove();
                            }
                        }, 300);
                    }
                }, 5000);
            });
        });

        // Keyboard navigation improvements
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && e.target.tagName !== 'BUTTON') {
                const form = document.getElementById('loginForm');
                const inputs = Array.from(form.querySelectorAll('input'));
                const currentIndex = inputs.indexOf(e.target);
                
                if (currentIndex < inputs.length - 1) {
                    e.preventDefault();
                    inputs[currentIndex + 1].focus();
                }
            }
        });
    </script>

    <!-- chat box -->
    

    <!-- chat box -->

<script>
        // Optimized rainbow background animation
        class RainbowBackground {
            constructor() {
                this.canvas = document.getElementById('rainbow-bg');
                this.ctx = this.canvas.getContext('2d');
                this.colors = [
                    "#FF0000", "#FF7F00", "#FFFF00", "#00FF00", 
                    "#00FFFF", "#0000FF", "#8B00FF", "#FF00FF", "#FF0000"
                ];
                this.t = 0;
                this.isAnimating = true;
                this.lastTime = 0;
                this.frameCount = 0;
                
                this.init();
            }
            
            init() {
                this.resizeCanvas();
                window.addEventListener('resize', this.debounce(() => this.resizeCanvas(), 250));
                
                // Check for reduced motion preference
                if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                    this.drawStaticBackground();
                    return;
                }
                
                this.animate();
            }
            
            resizeCanvas() {
                const dpr = window.devicePixelRatio || 1;
                this.canvas.width = window.innerWidth * dpr;
                this.canvas.height = window.innerHeight * dpr;
                this.ctx.scale(dpr, dpr);
                this.canvas.style.width = window.innerWidth + 'px';
                this.canvas.style.height = window.innerHeight + 'px';
            }
            
            drawWaves() {
                this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
                
                const amplitude = Math.min(75, window.innerHeight * 0.1);
                const waveCount = Math.min(6, Math.floor(window.innerHeight / 100));
                const heightUnit = window.innerHeight / (waveCount + 1);
                
                for (let i = 0; i < waveCount; i++) {
                    this.ctx.beginPath();
                    
                    // Optimize path drawing
                    const step = Math.max(2, Math.floor(window.innerWidth / 200));
                    for (let x = 0; x <= window.innerWidth; x += step) {
                        const angle = (x / (180 + i * 10)) + this.t * (0.5 + 0.15 * i);
                        const y = Math.sin(angle + i * 2) * amplitude + (i + 1) * heightUnit;
                        
                        if (x === 0) {
                            this.ctx.moveTo(x, y);
                        } else {
                            this.ctx.lineTo(x, y);
                        }
                    }
                    
                    this.ctx.lineTo(window.innerWidth, window.innerHeight);
                    this.ctx.lineTo(0, window.innerHeight);
                    this.ctx.closePath();
                    
                    // Create gradient
                    const grad = this.ctx.createLinearGradient(0, 0, window.innerWidth, 0);
                    const colorOffset = i;
                    const colorStops = 5;
                    
                    for (let j = 0; j < colorStops; j++) {
                        const idx = (colorOffset + j) % this.colors.length;
                        grad.addColorStop(j / (colorStops - 1), this.colors[idx]);
                    }
                    
                    this.ctx.fillStyle = grad;
                    this.ctx.globalAlpha = 0.3 + 0.1 * Math.sin(this.t + i * 2);
                    this.ctx.fill();
                }
                
                this.ctx.globalAlpha = 1;
            }
            
            drawStaticBackground() {
                this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
                
                const grad = this.ctx.createLinearGradient(0, 0, 0, window.innerHeight);
                grad.addColorStop(0, '#667eea');
                grad.addColorStop(0.5, '#764ba2');
                grad.addColorStop(1, '#f093fb');
                
                this.ctx.fillStyle = grad;
                this.ctx.fillRect(0, 0, window.innerWidth, window.innerHeight);
            }
            
            animate(currentTime = 0) {
                if (!this.isAnimating) return;
                
                // Throttle animation to 60fps max
                if (currentTime - this.lastTime >= 16) {
                    this.drawWaves();
                    this.t += 0.008; // Slightly slower for better performance
                    this.lastTime = currentTime;
                    this.frameCount++;
                }
                
                requestAnimationFrame((time) => this.animate(time));
            }
            
            stop() {
                this.isAnimating = false;
            }
            
            start() {
                this.isAnimating = true;
                this.animate();
            }
            
            debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }
        }
        
        // Initialize rainbow background
        const rainbowBg = new RainbowBackground();
        
        // Pause animation when page is not visible (performance optimization)
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                rainbowBg.stop();
            } else {
                rainbowBg.start();
            }
        });

    </script>

</body>

</html>

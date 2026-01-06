<!-- Premium SchoGMS Loading Screen -->
<div id="page-loader">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        #page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            background-size: 400% 400%;
            z-index: 99999;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            overflow: hidden;
            animation: gradientShift 8s ease infinite;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Floating Particles */
        .particles {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float 15s infinite ease-in-out;
        }

        .particle:nth-child(1) {
            width: 80px;
            height: 80px;
            left: 10%;
            animation-delay: 0s;
            animation-duration: 20s;
        }

        .particle:nth-child(2) {
            width: 120px;
            height: 120px;
            left: 20%;
            animation-delay: 2s;
            animation-duration: 18s;
        }

        .particle:nth-child(3) {
            width: 60px;
            height: 60px;
            left: 30%;
            animation-delay: 4s;
            animation-duration: 22s;
        }

        .particle:nth-child(4) {
            width: 100px;
            height: 100px;
            left: 50%;
            animation-delay: 1s;
            animation-duration: 19s;
        }

        .particle:nth-child(5) {
            width: 90px;
            height: 90px;
            left: 70%;
            animation-delay: 3s;
            animation-duration: 21s;
        }

        .particle:nth-child(6) {
            width: 70px;
            height: 70px;
            left: 85%;
            animation-delay: 5s;
            animation-duration: 17s;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(100vh) translateX(0) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 0.3;
            }
            90% {
                opacity: 0.3;
            }
            100% {
                transform: translateY(-100px) translateX(100px) rotate(360deg);
                opacity: 0;
            }
        }

        /* Main Loader Container */
        .loader-container {
            position: relative;
            text-align: center;
            z-index: 10;
            animation: fadeInUp 0.8s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Multi-layered Spinner */
        .spinner-wrapper {
            position: relative;
            width: 120px;
            height: 120px;
            margin: 0 auto 40px;
        }

        .spinner-outer {
            position: absolute;
            width: 120px;
            height: 120px;
            border: 4px solid rgba(255, 255, 255, 0.1);
            border-top: 4px solid #ffffff;
            border-right: 4px solid rgba(255, 255, 255, 0.5);
            border-radius: 50%;
            animation: spin 1.2s linear infinite;
        }

        .spinner-middle {
            position: absolute;
            top: 15px;
            left: 15px;
            width: 90px;
            height: 90px;
            border: 3px solid rgba(255, 255, 255, 0.1);
            border-top: 3px solid rgba(255, 255, 255, 0.8);
            border-right: 3px solid rgba(255, 255, 255, 0.4);
            border-radius: 50%;
            animation: spinReverse 0.9s linear infinite;
        }

        .spinner-inner {
            position: absolute;
            top: 30px;
            left: 30px;
            width: 60px;
            height: 60px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-top: 2px solid #ffffff;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }

        .spinner-core {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 20px;
            height: 20px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 50%;
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.6),
                        0 0 40px rgba(255, 255, 255, 0.4);
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        @keyframes spinReverse {
            from { transform: rotate(360deg); }
            to { transform: rotate(0deg); }
        }

        @keyframes pulse {
            0%, 100% {
                transform: translate(-50%, -50%) scale(1);
                opacity: 0.9;
            }
            50% {
                transform: translate(-50%, -50%) scale(1.2);
                opacity: 1;
            }
        }

        /* Logo/Brand Text */
        .loader-brand {
            font-size: 32px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 15px;
            letter-spacing: 3px;
            text-shadow: 0 2px 20px rgba(255, 255, 255, 0.3),
                         0 4px 40px rgba(255, 255, 255, 0.2);
            animation: glow 2s ease-in-out infinite alternate;
        }

        @keyframes glow {
            from {
                text-shadow: 0 2px 20px rgba(255, 255, 255, 0.3),
                             0 4px 40px rgba(255, 255, 255, 0.2);
            }
            to {
                text-shadow: 0 2px 30px rgba(255, 255, 255, 0.5),
                             0 4px 60px rgba(255, 255, 255, 0.4),
                             0 0 80px rgba(255, 255, 255, 0.2);
            }
        }

        /* Loading Text */
        .loader-text {
            color: #ffffff;
            font-size: 16px;
            font-weight: 400;
            margin-top: 20px;
            letter-spacing: 2px;
            text-transform: uppercase;
            opacity: 0.9;
        }

        /* Animated Dots */
        .loader-dots {
            display: inline-block;
            margin-left: 8px;
        }

        .loader-dots span {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #ffffff;
            margin: 0 4px;
            animation: dotBounce 1.4s ease-in-out infinite both;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
        }

        .loader-dots span:nth-child(1) {
            animation-delay: -0.32s;
        }

        .loader-dots span:nth-child(2) {
            animation-delay: -0.16s;
        }

        .loader-dots span:nth-child(3) {
            animation-delay: 0s;
        }

        @keyframes dotBounce {
            0%, 80%, 100% {
                transform: scale(0.8);
                opacity: 0.5;
            }
            40% {
                transform: scale(1.2);
                opacity: 1;
            }
        }

        /* Progress Bar */
        .progress-bar {
            width: 200px;
            height: 3px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            margin-top: 30px;
            overflow: hidden;
            position: relative;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #ffffff, rgba(255, 255, 255, 0.8));
            border-radius: 10px;
            width: 0%;
            animation: progress 2s ease-in-out infinite;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
        }

        @keyframes progress {
            0% {
                width: 0%;
                transform: translateX(-100%);
            }
            50% {
                width: 70%;
            }
            100% {
                width: 100%;
                transform: translateX(0%);
            }
        }

        /* Glassmorphism Effect */
        .glass-effect {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            pointer-events: none;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .loader-brand {
                font-size: 24px;
                letter-spacing: 2px;
            }

            .spinner-wrapper {
                width: 100px;
                height: 100px;
            }

            .spinner-outer {
                width: 100px;
                height: 100px;
            }

            .spinner-middle {
                width: 75px;
                height: 75px;
                top: 12.5px;
                left: 12.5px;
            }

            .spinner-inner {
                width: 50px;
                height: 50px;
                top: 25px;
                left: 25px;
            }
        }
    </style>

    <!-- Floating Particles Background -->
    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <!-- Glass Effect Overlay -->
    <div class="glass-effect"></div>

    <!-- Main Loader Content -->
    <div class="loader-container">
        <!-- Multi-layered Spinner -->
        <div class="spinner-wrapper">
            <div class="spinner-outer"></div>
            <div class="spinner-middle"></div>
            <div class="spinner-inner"></div>
            <div class="spinner-core"></div>
        </div>

        <!-- Brand Name -->
        <div class="loader-brand">SchoGMS</div>

        <!-- Loading Text with Dots -->
        <div class="loader-text">
            Loading<span class="loader-dots"><span></span><span></span><span></span></span>
        </div>

        <!-- Progress Bar -->
        <div class="progress-bar">
            <div class="progress-fill"></div>
        </div>
    </div>
</div>

<script>
    // Enhanced loader hide functionality
    let loaderHidden = false;

    function hideLoader() {
        if (loaderHidden) return;
        loaderHidden = true;
        
        const loader = document.getElementById('page-loader');
        if (loader) {
            loader.style.transition = 'opacity 0.6s cubic-bezier(0.4, 0, 0.2, 1), transform 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
            loader.style.opacity = '0';
            loader.style.transform = 'scale(0.95)';
            
            setTimeout(function() {
                loader.style.display = 'none';
            }, 600);
        }
    }

    // Hide when page is fully loaded
    if (document.readyState === 'complete') {
        setTimeout(hideLoader, 300);
    } else {
        window.addEventListener('load', function() {
            setTimeout(hideLoader, 300);
        });
    }

    // Fallback: Hide after 5 seconds
    setTimeout(function() {
        if (!loaderHidden) {
            hideLoader();
        }
    }, 5000);

    // Hide on DOMContentLoaded for faster pages
    document.addEventListener('DOMContentLoaded', function() {
        // Give it at least 800ms to show the beautiful animation
        setTimeout(function() {
            if (document.readyState === 'complete' && !loaderHidden) {
                setTimeout(hideLoader, 200);
            }
        }, 800);
    });
</script>

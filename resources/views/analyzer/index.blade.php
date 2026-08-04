<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Analyzer - Premium Performance Audit</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@500;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-dark: #0f172a;
            --bg-card: rgba(30, 41, 59, 0.7);
            --primary: #3b82f6;
            --primary-hover: #2563eb;
            --accent: #8b5cf6;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --border-color: rgba(255, 255, 255, 0.1);
            --glass-blur: blur(12px);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-dark);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            position: relative;
        }

        /* Animated Background Gradients */
        .bg-glow {
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.15) 0%, rgba(15, 23, 42, 0) 70%);
            top: -200px;
            left: -200px;
            z-index: -1;
            animation: float 10s ease-in-out infinite alternate;
        }

        .bg-glow-2 {
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(139, 92, 246, 0.15) 0%, rgba(15, 23, 42, 0) 70%);
            bottom: -100px;
            right: -100px;
            z-index: -1;
            animation: float 8s ease-in-out infinite alternate-reverse;
        }

        @keyframes float {
            0% { transform: translate(0, 0); }
            100% { transform: translate(50px, 50px); }
        }

        .container {
            width: 100%;
            max-width: 800px;
            padding: 40px 20px;
            z-index: 1;
        }

        .hero-title {
            font-family: 'Outfit', sans-serif;
            font-size: 4rem;
            text-align: center;
            margin-bottom: 10px;
            background: linear-gradient(135deg, #fff 0%, #a5b4fc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: fadeUp 0.8s ease-out forwards;
            opacity: 0;
        }

        .hero-subtitle {
            text-align: center;
            font-size: 1.2rem;
            color: var(--text-muted);
            margin-bottom: 50px;
            animation: fadeUp 0.8s ease-out 0.2s forwards;
            opacity: 0;
        }

        .analyzer-card {
            background: var(--bg-card);
            backdrop-filter: var(--glass-blur);
            -webkit-backdrop-filter: var(--glass-blur);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            animation: fadeUp 0.8s ease-out 0.4s forwards;
            opacity: 0;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .analyzer-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.6), 0 0 20px rgba(59, 130, 246, 0.2);
        }

        .form-group {
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: 500;
            color: #e2e8f0;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
        }

        .input-url {
            width: 100%;
            padding: 18px 24px;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            color: white;
            font-size: 1.1rem;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
            outline: none;
        }

        .input-url:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
            background: rgba(15, 23, 42, 0.9);
        }

        .input-url::placeholder {
            color: #64748b;
        }

        /* Modern Toggle Switch for Strategy */
        .device-toggle {
            display: flex;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 5px;
            position: relative;
        }

        .device-option {
            flex: 1;
            text-align: center;
            padding: 12px;
            cursor: pointer;
            z-index: 1;
            font-weight: 600;
            color: var(--text-muted);
            transition: color 0.3s ease;
        }

        .device-option.active {
            color: white;
        }

        .toggle-slider {
            position: absolute;
            top: 5px;
            left: 5px;
            width: calc(50% - 5px);
            height: calc(100% - 10px);
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border-radius: 12px;
            transition: transform 0.3s cubic-bezier(0.4, 0.0, 0.2, 1);
            z-index: 0;
        }

        input[type="hidden"]#strategy {
            display: none;
        }

        .btn-submit {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: white;
            border: none;
            border-radius: 16px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(59, 130, 246, 0.4);
        }

        .btn-submit:active {
            transform: translateY(1px);
        }

        .btn-submit::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: rgba(255, 255, 255, 0.2);
            transform: rotate(45deg) translateY(100%);
            transition: transform 0.6s ease;
        }

        .btn-submit:hover::after {
            transform: rotate(45deg) translateY(-100%);
        }

        /* Loading State */
        .loading-overlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.9);
            backdrop-filter: blur(8px);
            z-index: 9999;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.5s ease;
        }

        .loading-overlay.active {
            opacity: 1;
            pointer-events: all;
        }

        .loader-ring {
            width: 80px;
            height: 80px;
            border: 4px solid rgba(255, 255, 255, 0.1);
            border-left-color: var(--primary);
            border-right-color: var(--accent);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        .loading-text {
            margin-top: 30px;
            font-size: 1.2rem;
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: pulse 1.5s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .error-box {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
        }
        
        .error-box ul {
            margin-left: 20px;
            margin-top: 5px;
        }

        .auth-nav {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 100;
        }

        .btn-logout {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid var(--border-color);
            color: var(--text-main);
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }

        .btn-logout:hover {
            background: rgba(239, 68, 68, 0.2);
            border-color: rgba(239, 68, 68, 0.4);
            color: #fca5a5;
        }
    </style>
</head>
<body>
    @auth
    <div class="auth-nav">
        <form action="{{ route('logout') }}" method="POST" style="margin:0;">
            @csrf
            <button type="submit" class="btn-logout">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                Logout
            </button>
        </form>
    </div>
    @endauth

    <div class="bg-glow"></div>
    <div class="bg-glow-2"></div>

    <div class="container">
        <h1 class="hero-title">Web Analyzer</h1>
        <p class="hero-subtitle">Deep dive into performance, security scores, and advanced grade insights.</p>

        <div class="analyzer-card">
            @if($errors->any())
                <div class="error-box">
                    <strong>Terdapat kesalahan:</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('analyzer.process') }}" method="POST" id="analyzeForm">
                @csrf
                
                <div class="form-group">
                    <label>Device Strategy</label>
                    <div class="device-toggle" id="deviceToggle">
                        <div class="toggle-slider" id="toggleSlider"></div>
                        <div class="device-option active" data-val="DESKTOP">Desktop</div>
                        <div class="device-option" data-val="MOBILE">Mobile</div>
                    </div>
                    <input type="hidden" name="strategy" id="strategyInput" value="DESKTOP">
                </div>

                <div class="form-group">
                    <label for="url">Website URL</label>
                    <input type="url" class="input-url" id="url" name="url" placeholder="https://example.com" required autocomplete="off">
                </div>
                
                <button type="submit" class="btn-submit">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    Analyze Performance
                </button>
            </form>
        </div>
    </div>

    <!-- Fullscreen Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loader-ring"></div>
        <div class="loading-text" id="loadingText">Connecting to Analysis Engine...</div>
        <div style="color: var(--text-muted); font-size: 0.9rem; margin-top: 10px; max-width: 400px; text-align: center;">
            This deep analysis typically takes 15-45 seconds depending on the target website's complexity.
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggleOptions = document.querySelectorAll('.device-option');
            const slider = document.getElementById('toggleSlider');
            const hiddenInput = document.getElementById('strategyInput');
            
            toggleOptions.forEach((opt, index) => {
                opt.addEventListener('click', () => {
                    toggleOptions.forEach(o => o.classList.remove('active'));
                    opt.classList.add('active');
                    
                    if(index === 0) {
                        slider.style.transform = 'translateX(0)';
                    } else {
                        slider.style.transform = 'translateX(100%)';
                    }
                    
                    hiddenInput.value = opt.getAttribute('data-val');
                });
            });

            const form = document.getElementById('analyzeForm');
            const overlay = document.getElementById('loadingOverlay');
            const loadText = document.getElementById('loadingText');
            
            const loadingMessages = [
                "Running Performance Audits...",
                "Fetching Core Web Vitals (CrUX)...",
                "Calculating Grade Insights...",
                "Gathering Browser Timings...",
                "Analyzing Agentic Browsing capability...",
                "Almost there..."
            ];

            form.addEventListener('submit', () => {
                overlay.classList.add('active');
                
                let msgIdx = 0;
                setInterval(() => {
                    if (msgIdx < loadingMessages.length) {
                        loadText.textContent = loadingMessages[msgIdx];
                        msgIdx++;
                    }
                }, 4000);
            });
        });
    </script>
</body>
</html>
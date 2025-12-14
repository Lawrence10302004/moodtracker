<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta http-equiv="refresh" content="0;url=login.php">
  <title>Mood Tracker</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --bg: #f6f8f6;
      --card: #ffffff;
      --accent: #7c9d85;
      --accent-strong: #6a8b74;
      --shadow: 0 8px 24px rgba(0,0,0,0.08);
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    body {
      font-family: Inter, system-ui, Segoe UI, sans-serif;
      background: var(--bg);
      color: #1f2a1f;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .container {
      width: 100%;
      max-width: 500px;
      padding: 24px;
      text-align: center;
    }
    .card {
      background: var(--card);
      border-radius: 16px;
      padding: 48px 32px;
      box-shadow: var(--shadow);
      border: 1px solid #e8efe8;
    }
    .brand-icon {
      width: 64px;
      height: 64px;
      border-radius: 16px;
      background: var(--accent);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 24px;
    }
    .brand-icon svg {
      width: 36px;
      height: 36px;
      fill: white;
    }
    h1 {
      font-size: 32px;
      font-weight: 700;
      margin-bottom: 12px;
      color: #1f2a1f;
    }
    .subtitle {
      font-size: 16px;
      color: #54625a;
      margin-bottom: 32px;
      line-height: 1.5;
    }
    .cta-button {
      display: inline-block;
      padding: 14px 32px;
      background: var(--accent);
      color: #fff;
      text-decoration: none;
      border-radius: 10px;
      font-weight: 600;
      font-size: 16px;
      transition: all 0.2s ease;
    }
    .cta-button:hover {
      background: var(--accent-strong);
      box-shadow: 0 6px 14px rgba(106,139,116,0.16);
      transform: translateY(-1px);
    }
    .features {
      margin-top: 32px;
      padding-top: 32px;
      border-top: 1px solid #e8efe8;
      display: flex;
      gap: 24px;
      justify-content: center;
      flex-wrap: wrap;
    }
    .feature {
      flex: 1;
      min-width: 120px;
    }
    .feature-icon {
      width: 40px;
      height: 40px;
      border-radius: 10px;
      background: rgba(124,157,133,0.1);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 12px;
    }
    .feature-icon svg {
      width: 20px;
      height: 20px;
      fill: var(--accent);
    }
    .feature-title {
      font-weight: 600;
      font-size: 14px;
      color: #1f2a1f;
      margin-bottom: 4px;
    }
    .feature-text {
      font-size: 12px;
      color: #6b7280;
    }
    .spinner {
      display: inline-block;
      width: 20px;
      height: 20px;
      border: 3px solid rgba(124,157,133,0.3);
      border-radius: 50%;
      border-top-color: var(--accent);
      animation: spin 0.8s linear infinite;
      margin-top: 16px;
    }
    @keyframes spin {
      to { transform: rotate(360deg); }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="card">
      <div class="brand-icon">
        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" fill="white"/>
        </svg>
      </div>
      
      <h1>Mood Tracker</h1>
      <p class="subtitle">
        Track your daily emotions, discover patterns, and improve your mental wellbeing
      </p>
      
      <a href="login.php" class="cta-button">Get Started</a>
      
      <div class="features">
        <div class="feature">
          <div class="feature-icon">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" fill="currentColor"/>
            </svg>
          </div>
          <div class="feature-title">Daily Tracking</div>
          <div class="feature-text">Log your mood easily</div>
        </div>
        
        <div class="feature">
          <div class="feature-icon">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z" fill="currentColor"/>
            </svg>
          </div>
          <div class="feature-title">Insights</div>
          <div class="feature-text">Discover patterns</div>
        </div>
        
        <div class="feature">
          <div class="feature-icon">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" fill="currentColor"/>
            </svg>
          </div>
          <div class="feature-title">Private</div>
          <div class="feature-text">Your data is secure</div>
        </div>
      </div>
      
      <div class="spinner"></div>
    </div>
  </div>
  
  <script>
    // Fallback redirect in case meta refresh doesn't work
    setTimeout(function() {
      window.location.href = 'login.php';
    }, 60000);
  </script>
</body>
</html>
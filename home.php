<?php
// Require authentication to access home page
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Mood Tracker — Home</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root{
      --bg:#f6f8f6;
      --card:#ffffff;
      --accent:#7c9d85;
      --accent-strong:#6a8b74;
      --muted:#6b7280;
      --shadow:0 8px 24px rgba(0,0,0,0.08);
    }
   

    /* Custom Icon Styles */
    .icon-svg {
      width: 20px;
      height: 20px;
      fill: currentColor;
      flex-shrink: 0;
    }
    .brand-icon-svg {
      width: 24px;
      height: 24px;
      fill: white;
    }
    
    *{
      box-sizing:border-box;
      margin:0;
      padding:0}
    
    body{font-family:Inter,system-ui,Segoe UI,sans-serif;background:var(--bg);color:#1f2a1f;min-height:100vh;display:flex}
    a{text-decoration:none;color:inherit}
    /* Sidebar */
    .sidebar{width:250px;background:#ecf2ec;min-height:100vh;padding:20px 18px;display:flex;flex-direction:column;gap:12px;position:fixed;left:0;top:0;bottom:0}
    .brand{display:flex;align-items:center;gap:10px;margin-bottom:16px}
    .brand-icon{width:42px;height:42px;border-radius:12px;background:var(--accent);display:flex;align-items:center;justify-content:center;font-size:22px;color:#fff;font-weight:700}
    .brand-text{display:flex;flex-direction:column;gap:2px}
    .brand-title{font-weight:700;font-size:16px;color:#1f2a1f}
    .brand-sub{font-size:12px;color:#54625a}
    .nav{display:flex;flex-direction:column;gap:6px;margin-top:6px}
    .nav-item{display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:12px;cursor:pointer;color:#1f2a1f;font-weight:600;transition:all 0.18s ease;text-decoration:none}
    .nav-item:hover{background:#dde6df}
    .nav-item.active{background:var(--accent);color:#fff;box-shadow:0 6px 14px rgba(124,157,133,0.25)}
    .nav-icon{font-size:18px;width:22px;text-align:center}
    .welcome{margin-top:auto;background:#f5f5f5;border:1px solid #e0e0e0;border-radius:12px;padding:14px;display:flex;flex-direction:column;gap:10px;font-size:13px;color:#1f2a1f;box-shadow:0 1px 3px rgba(0,0,0,0.05)}
    /* Main */
    .main{margin-left:250px;flex:1;padding:26px 34px}
    .header-title{font-size:30px;font-weight:700;margin-bottom:6px;color:#1f2a1f}
    .header-sub{color:#6b7280;font-size:15px}
    .section-title{display:flex;align-items:center;gap:8px;font-weight:700;color:#2f3e32;margin-bottom:12px}
    .section-title .icon{font-size:18px}
    /* Layout grids */
    .grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:16px}
    .card{background:var(--card);border-radius:16px;padding:16px;box-shadow:0 6px 16px rgba(0,0,0,0.06);border:1px solid #e8efe8}
    /* Mood cards row */
    .mood-row{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:14px;margin-top:10px}
    .mood-card{display:flex;flex-direction:column;gap:10px;padding:14px;border-radius:14px;background:#f8faf8;border:1px solid #e3ebe3}
    .mood-top{display:flex;align-items:center;gap:12px}
    .mood-icon{width:46px;height:46px;border-radius:14px;background:#fff;display:flex;align-items:center;justify-content:center;font-size:22px;color:#7c9d85;box-shadow:inset 0 1px 2px rgba(0,0,0,0.06)}
    .label{font-weight:700;font-size:15px;color:#1f2a1f}
    .sub{font-size:13px;color:#6b7280}
    .meta{display:flex;gap:12px;font-size:13px;color:#6b7280}
    /* Combined score */
    .score-card{grid-column:span 2;background:linear-gradient(135deg,#7c9d85,#6a8b74);color:#fff;border-radius:16px;padding:18px 20px;display:flex;align-items:center;gap:20px;box-shadow:var(--shadow)}
    .score-number{font-size:46px;font-weight:700;line-height:1}
    .score-text{font-size:14px;opacity:0.92}
    /* Quick actions */
    .quick-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-top:6px}
    .quick-btn{background:#f6f7f6;border:1px solid #e5ede5;border-radius:14px;padding:16px 12px;display:flex;flex-direction:column;gap:10px;align-items:center;text-align:center;cursor:pointer;transition:all 0.15s ease;font-weight:700;color:#1f2a1f;white-space:normal;word-break:break-word;text-decoration:none;width:100%;border:none;font-family:Inter,sans-serif}
    .quick-btn:hover{background:#eef3ee;transform:translateY(-1px)}
    .quick-icon{width:40px;height:40px;border-radius:12px;background:#edf3ed;display:flex;align-items:center;justify-content:center;font-size:18px;color:#6f8b74;flex-shrink:0}
    @media(max-width:768px){
      .quick-grid{grid-template-columns:repeat(2,1fr)}
    }
    /* Insights */
    .insights-wrap{display:grid;grid-template-columns:2fr 1fr;gap:16px;align-items:start;margin-top:18px}
    .summary-card{background:#fff;border:1px solid #e8efe8;border-radius:16px;padding:16px;box-shadow:0 8px 20px rgba(0,0,0,0.06);overflow:hidden;word-wrap:break-word}
    .summary-row{margin-top:8px;color:#1f2a1f;font-size:14px;word-break:break-word}
    .summary-row strong{color:#1f2a1f}
    .reminder{margin-top:16px;background:#fff7e5;border:1px solid #ffe6b3;border-radius:16px;padding:14px;font-size:14px;color:#8b6a1f;word-wrap:break-word}
    .recommend{background:#ffffff;border:1px solid #e8efe8;border-radius:16px;padding:16px;box-shadow:0 8px 20px rgba(0,0,0,0.06);overflow:hidden}
    .rec-item{display:flex;align-items:flex-start;gap:10px;margin-bottom:10px;font-size:14px;color:#1f2a1f;word-break:break-word}
    .rec-icon{width:28px;height:28px;border-radius:10px;background:#f2f5f2;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0}
    .affirmation{margin-top:14px;background:#6f9078;color:#fff;padding:16px;border-radius:16px;text-align:center;font-style:italic;box-shadow:var(--shadow);word-wrap:break-word}
    @media(max-width:1024px){
      .insights-wrap{grid-template-columns:1fr}
      .score-card{grid-column:span 1}
    }

     /* Detector area */
    .detectors{display:grid;grid-template-columns:1fr 320px;gap:16px;margin-top:12px}
    .detector-card{border-radius:14px;padding:14px;background:#fff;border:1px solid #e8efe8}
    #videoHolder{position:relative;border-radius:12px;overflow:hidden;background:#000;min-height:220px;display:flex;align-items:center;justify-content:center}
    video#webcam{width:100%;height:100%;object-fit:cover;display:block}

    /* controls */
    .controls{display:flex;gap:10px;margin-top:10px}
    .toggle-btn{display:inline-flex;align-items:center;gap:8px;padding:8px 12px;border-radius:12px;border:none;background:#f4f6f4;cursor:pointer;font-weight:700;transition:all .15s}
    .toggle-btn.on{background:var(--accent);color:white;box-shadow:0 6px 14px rgba(106,139,116,0.16)}
    .indicator{width:12px;height:12px;border-radius:50%;background:#cbd5ca;border:2px solid white;box-shadow:0 2px 6px rgba(0,0,0,0.08)}
    .indicator.active{background:#4ee07a;box-shadow:0 6px 10px rgba(78,224,122,0.12)}
    .indicator.recording{background:#ff6b6b;box-shadow:0 6px 12px rgba(255,107,107,0.12);animation:pulse 1s infinite}
    @keyframes pulse {
      0% { transform: scale(1); opacity: 0.95 }
      50% { transform: scale(1.2); opacity: 0.7 }
      100% { transform: scale(1); opacity: 0.95 }
    }

    /* Score animation */
    .score-card{display:flex;align-items:center;gap:18px;border-radius:14px;padding:16px;background:linear-gradient(135deg,#7c9d85,#6a8b74);color:#fff;box-shadow:var(--shadow);font-weight:700}
    .score-num{font-size:42px;line-height:1}
    .small{font-size:13px;opacity:0.95}

    /* small text */
    .muted{color:var(--muted);font-size:13px}

    /* animation for cards */
    .fade-in { animation: fadeIn .45s ease both; }
    @keyframes fadeIn { from { opacity:0; transform: translateY(6px) } to { opacity:1; transform: translateY(0) } }

    @media (max-width: 980px) {
      .detectors{grid-template-columns:1fr}
      .main { padding: 16px }
    }
  </style>
</head>
<body>
  <aside class="sidebar">
    <div class="brand">
      <div class="brand-icon">
        <svg class="brand-icon-svg" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" fill="white"/>
        </svg>
      </div>
      <div class="brand-text">
        <div class="brand-title">Mood Tracker</div>
        <div class="brand-sub">Track your wellbeing</div>
      </div>
    </div>
   <nav class="nav">
      <a class="nav-item active" href="home.php">
        <span class="nav-icon">
          <svg class="icon-svg" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M3 9L12 2L21 9V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V9Z" fill="currentColor"/>
            <path d="M9 22V12H15V22" fill="white"/>
          </svg>
        </span>Home
      </a>
      <a class="nav-item" href="calendar.php">
        <span class="nav-icon">
          <svg class="icon-svg" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="3" y="4" width="18" height="18" rx="2" fill="currentColor"/>
            <path d="M16 2V6M8 2V6M3 10H21" stroke="white" stroke-width="2"/>
          </svg>
        </span>Calendar
      </a>
      <a class="nav-item" href="daily-log.php">
        <span class="nav-icon">
          <svg class="icon-svg" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M4 19.5C4 20.163 4.26339 20.7989 4.73223 21.2678C5.20107 21.7366 5.83696 22 6.5 22H20V2H6.5C5.83696 2 5.20107 2.26339 4.73223 2.73223C4.26339 3.20107 4 3.83696 4 4.5V19.5Z" fill="currentColor"/>
            <path d="M8 6H16M8 10H16M8 14H12" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
        </span>Daily Log
      </a>
      <a class="nav-item" href="calming-tools.php">
        <span class="nav-icon">
          <svg class="icon-svg" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" fill="currentColor"/>
          </svg>
        </span>Calming Tools
      </a>
      <a class="nav-item" href="flashcard.php">
        <span class="nav-icon">
          <svg class="icon-svg" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M21 15C21 15.5304 20.7893 16.0391 20.4142 16.4142C20.0391 16.7893 19.5304 17 19 17H7L3 21V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H19C19.5304 3 20.0391 3.21071 20.4142 3.58579C20.7893 3.96086 21 4.46957 21 5V15Z" fill="currentColor"/>
          </svg>
        </span>Flashcards
      </a>
      <a class="nav-item" href="progress.php">
        <span class="nav-icon">
          <svg class="icon-svg" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M18 20V10M12 20V4M6 20V14" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
          </svg>
        </span>Progress
      </a>
      <a class="nav-item" href="settings.php">
        <span class="nav-icon">
          <svg class="icon-svg" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="12" cy="12" r="3" fill="currentColor"/>
            <path d="M12 1V4M12 20V23M4.22 4.22L6.34 6.34M17.66 17.66L19.78 19.78M1 12H4M20 12H23M4.22 19.78L6.34 17.66M17.66 6.34L19.78 4.22" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
        </span>Settings
      </a>
    </nav>
    <div class="welcome" id="authSection">
      <div id="userInfo" style="display:none;flex-direction:column;gap:10px">
        <div style="font-size:12px;color:#6b7280;font-weight:400">Logged in as</div>
        <div id="username" style="font-weight:700;color:#1f2a1f;font-size:16px;line-height:1.3">—</div>
        <a href="logout.php" style="display:inline-block;padding:8px 14px;background:#6a8b74;color:#fff;border-radius:8px;font-size:13px;text-align:center;text-decoration:none;font-weight:500;width:100%;transition:background 0.2s" onmouseover="this.style.background='#5a7a64'" onmouseout="this.style.background='#6a8b74'">Log out</a>
      </div>
      <div id="authPrompt" style="text-align:center;gap:8px;display:flex;flex-direction:column">
        <div>Not logged in</div>
        <a href="login.php" style="padding:8px 12px;background:#7c9d85;color:#fff;border-radius:8px;font-size:13px;font-weight:600">Log in</a>
        <a href="register.php" style="padding:8px 12px;background:#e8efe8;color:#1f2a1f;border-radius:8px;font-size:13px;font-weight:600">Sign up</a>
      </div>
    </div>
  </aside>

  <main class="main">
    <header>
      <div class="header-title" id="greeting">Good Afternoon</div>
      <div class="header-sub">How are you feeling today?</div>
    </header>

    <section style="margin-top:20px">
      <div class="detectors fade-in">
      <div class="detector-card card">
        <div style="display:flex;align-items:center;justify-content:space-between">
          <div class="section-title">
          <svg class="icon-svg" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
  <path d="M12 2C12 2 8 6 8 10C8 12.21 9.79 14 12 14C14.21 14 16 12.21 16 10C16 6 12 2 12 2Z" fill="currentColor"/>
  <path d="M12 16C9.33 16 4 17.33 4 20V22H20V20C20 17.33 14.67 16 12 16Z" fill="currentColor"/>
  <circle cx="12" cy="10" r="2" fill="white"/>
</svg>
  
          <span>Live Detection</span></div>
          <div class="muted">Status: <span id="statusText">idle</span></div>
        </div>

        <div id="videoHolder" style="margin-top:12px">
          <video id="webcam" autoplay muted playsinline></video>
          <canvas id="overlay" style="position:absolute;left:0;top:0;pointer-events:none"></canvas>
        </div>

        <div class="controls">
          <button id="camToggle" class="toggle-btn"><span id="camIndicator" class="indicator"></span> Camera</button>
          <button id="micToggle" class="toggle-btn"><span id="micIndicator" class="indicator"></span> Microphone</button>
          <button id="saveBtn" class="toggle-btn" style="margin-left:auto">Save Now</button>
        </div>

        <div style="display:flex;gap:14px;margin-top:14px;align-items:center">
          <div class="score-card" style="padding:12px 16px">
            <div>
              <div style="font-size:13px;opacity:0.95">Combined Mood Score</div>
              <div id="scoreNum" class="score-num">—</div>
            </div>
            <div style="margin-left:auto;text-align:right">
              <div id="scoreText" class="small">No data</div>
            </div>
          </div>

          <div style="min-width:220px">
            <div class="muted">Face:</div>
            <div id="faceLabel" style="font-weight:700">—</div>
            <div class="muted" style="margin-top:6px">Audio:</div>
            <div id="audioLabel" style="font-weight:700">—</div>
          </div>
        </div>
      </div>

      <div style="display:flex;flex-direction:column;gap:12px">
        <div class="card detector-card fade-in">
          <div class="section-title">
          <svg class="icon-svg" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
  <path d="M3 3V21H21" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
  <path d="M7 17L11 13L15 16L20 9" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
  <circle cx="7" cy="17" r="1.5" fill="currentColor"/>
  <circle cx="11" cy="13" r="1.5" fill="currentColor"/>
  <circle cx="15" cy="16" r="1.5" fill="currentColor"/>
  <circle cx="20" cy="9" r="1.5" fill="currentColor"/>
</svg>  
          <span>Today's Summary</span></div>
          <div id="todaySummary" style="margin-top:8px;font-size:13px;line-height:1.6;word-break:break-word" class="muted">Loading...</div>
        </div>

        <div class="card detector-card fade-in">
          <div class="section-title">
          <svg class="icon-svg" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
  <circle cx="12" cy="12" r="3" fill="currentColor"/>
  <path d="M12 1V4M12 20V23M4.22 4.22L6.34 6.34M17.66 17.66L19.78 19.78M1 12H4M20 12H23M4.22 19.78L6.34 17.66M17.66 6.34L19.78 4.22" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
</svg>  
          <span>Instructions & Safety</span></div>
          <div style="margin-top:8px;font-size:13px;line-height:1.6;word-break:break-word" class="muted">
            <ul style="margin-left:16px">
              <li>Camera & mic run locally in your browser. No camera frames/audio are uploaded — only small emotion metadata is saved.</li>
              <li>For hosting: use HTTPS (required for getUserMedia). Set correct DB credentials on server.</li>
              <li>We store only the detected labels and scores; no raw images/audio are sent to the server.</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    </section>

    <section style="margin-top:16px">
      <div class="score-card">
        <div>
          <div class="label" style="color:#fff">Combined Mood Score</div>
          <div class="score-text">Calculated from all sources</div>
        </div>
        <div style="margin-left:auto;text-align:right">
          <div class="score-number">78</div>
          <div class="score-text">/ 100</div>
        </div>
      </div>
    </section>

    <section style="margin-top:18px">
      <div class="section-title">
      <svg class="icon-svg" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
  <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" fill="currentColor"/>
  <circle cx="12" cy="12" r="2" fill="white"/>
</svg>  
      <span>Quick Actions</span></div>
       <div class="quick-grid">
    <a class="quick-btn" href="daily-log.php">
      <div class="quick-icon">
        <svg class="icon-svg" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M4 19.5C4 20.163 4.26339 20.7989 4.73223 21.2678C5.20107 21.7366 5.83696 22 6.5 22H20V2H6.5C5.83696 2 5.20107 2.26339 4.73223 2.73223C4.26339 3.20107 4 3.83696 4 4.5V19.5Z" fill="currentColor"/>
          <path d="M8 6H16M8 10H16M8 14H12" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
      </div>
      <span>Diary</span>
    </a>
    <a class="quick-btn" href="calendar.php">
      <div class="quick-icon">
        <svg class="icon-svg" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <rect x="3" y="4" width="18" height="18" rx="2" fill="currentColor"/>
          <path d="M16 2V6M8 2V6M3 10H21" stroke="white" stroke-width="2"/>
        </svg>
      </div>
      <span>Calendar</span>
    </a>
    <a class="quick-btn" href="calming-tools.php">
      <div class="quick-icon">
        <svg class="icon-svg" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
         <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" fill="currentColor"/>
        </svg>
      </div>
      <span>Calming Tools</span>
    </a>
    <a class="quick-btn" href="flashcard.php">
      <div class="quick-icon">
        <svg class="icon-svg" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M21 15C21 15.5304 20.7893 16.0391 20.4142 16.4142C20.0391 16.7893 19.5304 17 19 17H7L3 21V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H19C19.5304 3 20.0391 3.21071 20.4142 3.58579C20.7893 3.96086 21 4.46957 21 5V15Z" fill="currentColor"/>
        </svg>
      </div>
      <span>Flashcards</span>
    </a>
    </section>

    <section style="margin-top:20px">
      <div class="section-title">
      <svg class="icon-svg" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
  <rect x="3" y="12" width="4" height="9" rx="1" fill="currentColor"/>
  <rect x="10" y="8" width="4" height="13" rx="1" fill="currentColor"/>
  <rect x="17" y="3" width="4" height="18" rx="1" fill="currentColor"/>
</svg>  
      <span>Today's Insights</span></div>
      <div class="insights-wrap">
        <div class="summary-card">
          <div class="label">Daily Summary</div>
          <div class="summary-row"><strong>Main Emotion:</strong> Happy and energized</div>
          <div class="summary-row"><strong>Energy Level:</strong> Above average (72%)</div>
          <div class="summary-row"><strong>Environment:</strong> Calm and quiet surroundings</div>
          <div class="summary-row" style="margin-top:10px;border-top:1px solid #eef2ee;padding-top:10px">
            <strong>Insights:</strong> Your positive facial expressions match your calm voice tone. Great alignment today!
          </div>
          <div class="reminder">Smart Reminder: You usually feel stressed around 9 PM. Try calming tools at 8:30 PM tonight.</div>
        </div>
        <div>
          <div class="recommend">
            <div class="label" style="margin-bottom:8px">Recommended for You</div>
            <div class="rec-item">
            <div class="rec-icon">
  <svg class="icon-svg" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M23 19C23 19.5304 22.7893 20.0391 22.4142 20.4142C22.0391 20.7893 21.5304 21 21 21H3C2.46957 21 1.96086 20.7893 1.58579 20.4142C1.21071 20.0391 1 19.5304 1 19V8C1 7.46957 1.21071 6.96086 1.58579 6.58579C1.96086 6.21071 2.46957 6 3 6H7L9 3H15L17 6H21C21.5304 6 22.0391 6.21071 22.4142 6.58579C22.7893 6.96086 23 7.46957 23 8V19Z" fill="currentColor"/>
    <circle cx="12" cy="13" r="3" fill="white"/>
  </svg>
</div>  
            <div>Capture this happy moment with a photo or journal entry</div></div>
            <div class="rec-item">
            <div class="rec-icon">
  <svg class="icon-svg" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" fill="currentColor"/>
    <circle cx="12" cy="12" r="2" fill="white"/>
  </svg>
</div>  
            <div>Your energy is high - great time for a creative activity</div></div>
            <div class="rec-item">
            <div class="rec-icon">
  <svg class="icon-svg" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M21 15C21 15.5304 20.7893 16.0391 20.4142 16.4142C20.0391 16.7893 19.5304 17 19 17H7L3 21V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H19C19.5304 3 20.0391 3.21071 20.4142 3.58579C20.7893 3.96086 21 4.46957 21 5V15Z" fill="currentColor"/>
  </svg>
</div>  
            <div>Reflect on what made today feel good</div></div>
          </div>
          <div class="affirmation">"You're doing amazing today" — Keep nurturing these positive feelings</div>
        </div>
      </div>
    </section>
  </main>


<script src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api/dist/face-api.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@3.21.0/dist/tf.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@tensorflow-models/speech-commands@0.4.0/dist/speech-commands.min.js"></script>

<script>
    // UI elements
  const camToggle = document.getElementById('camToggle');
  const micToggle = document.getElementById('micToggle');
  const camIndicator = document.getElementById('camIndicator');
  const micIndicator = document.getElementById('micIndicator');
  const webcamEl = document.getElementById('webcam');
  const overlay = document.getElementById('overlay');
  const statusText = document.getElementById('statusText');
  const faceLabel = document.getElementById('faceLabel');
  const audioLabel = document.getElementById('audioLabel');
  const scoreNum = document.getElementById('scoreNum');
  const scoreText = document.getElementById('scoreText');
  const saveBtn = document.getElementById('saveBtn');
  const todaySummary = document.getElementById('todaySummary');

  let camStream = null;
  let micStream = null;
  let audioContext = null;
  let analyser = null;
  let audioDataArray = null;
  let audioInterval = null;
  let faceInterval = null;
  let modelsLoaded = false;

  // weights for combined score (adjustable)
  const FACE_WEIGHT = 0.7;
  const AUDIO_WEIGHT = 0.3;

  // helpers
  function setStatus(s) { statusText.textContent = s; }
  function setCamActive(active){
    camToggle.classList.toggle('on', active);
    camIndicator.classList.toggle('active', active);
  }
  function setMicActive(active, recording=false){
    micToggle.classList.toggle('on', active);
    micIndicator.classList.toggle('active', active);
    micIndicator.classList.toggle('recording', recording);
  }

  async function loadModels() {
  const MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model/';
  setStatus('loading models...');
  await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
  await faceapi.nets.faceExpressionNet.loadFromUri(MODEL_URL);
  modelsLoaded = true;
  setStatus('idle');
}

  // Camera start/stop
  async function startCamera(){
    if (!modelsLoaded) await loadModels();
    if (camStream) return;
    try {
      camStream = await navigator.mediaDevices.getUserMedia({ video: { width: 640, height: 480 }, audio: false });
      webcamEl.srcObject = camStream;
      webcamEl.play();
      setCamActive(true);
      setStatus('camera on');
      startFaceLoop();
    } catch (e) {
      console.error(e);
      setStatus('camera blocked or not available');
    }
  }
  function stopCamera(){
    if (!camStream) return;
    const tracks = camStream.getTracks();
    tracks.forEach(t => t.stop());
    camStream = null;
    webcamEl.pause();
    webcamEl.srcObject = null;
    setCamActive(false);
    setStatus('camera off');
    stopFaceLoop();
    clearOverlay();
  }

  // Face detection loop
  function clearOverlay(){
    const ctx = overlay.getContext('2d');
    overlay.width = webcamEl.clientWidth;
    overlay.height = webcamEl.clientHeight;
    ctx.clearRect(0,0,overlay.width,overlay.height);
  }
  function startFaceLoop(){
    const options = new faceapi.TinyFaceDetectorOptions({ inputSize: 160, scoreThreshold: 0.5 });
    faceInterval = setInterval(async () => {
      if (!webcamEl || webcamEl.readyState < 2) return;
      const detections = await faceapi.detectSingleFace(webcamEl, options).withFaceExpressions();
      drawOverlay(detections);
      if (detections && detections.expressions) {
        const expressions = detections.expressions;
        // find top expression and confidence
        let top = { name: null, score: 0 };
        Object.keys(expressions).forEach(k => {
          if (expressions[k] > top.score) top = { name: k, score: expressions[k] };
        });
        faceLabel.textContent = `${top.name} (${Math.round(top.score*100)}%)`;
        computeAndShowScore(top.name, top.score, null, null);
      } else {
        faceLabel.textContent = 'No face';
      }
    }, 700); // every 700ms
  }
  function stopFaceLoop(){
    if (faceInterval) clearInterval(faceInterval);
    faceInterval = null;
  }
  function drawOverlay(detection){
    const ctx = overlay.getContext('2d');
    overlay.width = webcamEl.clientWidth;
    overlay.height = webcamEl.clientHeight;
    ctx.clearRect(0,0,overlay.width,overlay.height);
    if (!detection) return;
    const ratioX = overlay.width / webcamEl.videoWidth;
    const ratioY = overlay.height / webcamEl.videoHeight;
    const box = detection.detection.box;
    ctx.strokeStyle = 'rgba(255,255,255,0.9)';
    ctx.lineWidth = 2;
    ctx.strokeRect(box.x * ratioX, box.y * ratioY, box.width * ratioX, box.height * ratioY);
  }

  // Audio start/stop & simple RMS-based classifier
  async function startMic(){
    try {
      micStream = await navigator.mediaDevices.getUserMedia({ audio: true, video:false });
      setMicActive(true, true);
      audioContext = new (window.AudioContext || window.webkitAudioContext)();
      const source = audioContext.createMediaStreamSource(micStream);
      analyser = audioContext.createAnalyser();
      analyser.fftSize = 1024;
      source.connect(analyser);
      audioDataArray = new Uint8Array(analyser.fftSize);
      audioInterval = setInterval(sampleAudio, 300);
      setStatus('mic on');
    } catch (e) {
      console.error(e);
      setStatus('mic blocked or not available');
    }
  }
  function stopMic(){
    if (audioInterval) clearInterval(audioInterval);
    if (micStream) {
      micStream.getTracks().forEach(t => t.stop());
      micStream = null;
    }
    if (audioContext) {
      audioContext.close();
      audioContext = null;
    }
    setMicActive(false, false);
    setStatus('mic off');
  }

  function sampleAudio(){
    if (!analyser) return;
    analyser.getByteTimeDomainData(audioDataArray);
    // compute RMS
    let sum = 0;
    for (let i = 0; i < audioDataArray.length; i++) {
      const v = (audioDataArray[i] - 128) / 128;
      sum += v * v;
    }
    const rms = Math.sqrt(sum / audioDataArray.length); // 0..1
    // heuristic thresholds - tune for your environment
    let audioEmotion = 'neutral';
    let audioScore = Math.round(rms * 100);
    if (rms > 0.15) {
      audioEmotion = 'high_energy';
    } else if (rms > 0.06) {
      audioEmotion = 'neutral';
    } else {
      audioEmotion = 'calm';
    }
    audioLabel.textContent = `${audioEmotion} (${audioScore})`;
    computeAndShowScore(null, null, audioEmotion, audioScore);
  }

  // combine results: lastFace and lastAudio stored
  let lastFace = { name: null, score: 0 };
  let lastAudio = { name: null, score: 0 };
  let lastCombined = null;

  function computeAndShowScore(faceName, faceConf, audioName, audioScoreVal){
    if (faceName) { lastFace = { name: faceName, score: faceConf } }
    if (audioName) { lastAudio = { name: audioName, score: audioScoreVal } }

    // Map face expression to a 0-100 happiness-like metric (simple mapping)
    const faceMap = {
      happy: 90, neutral: 60, sad: 20, angry: 15, fearful: 10, disgusted: 20, surprised: 70
    };
    const faceVal = lastFace.name ? (faceMap[lastFace.name] || 50) * lastFace.score : null;

    // Map audio score (0..100) to 0..100, but calm should be mid-high
    const audioVal = lastAudio.name ? lastAudio.score : null;

    // If both present:
    let combined = 50;
    if (faceVal !== null && audioVal !== null) {
      combined = Math.round(( (faceVal * FACE_WEIGHT) + (audioVal * AUDIO_WEIGHT) ) / (FACE_WEIGHT + AUDIO_WEIGHT));
    } else if (faceVal !== null) {
      combined = Math.round(faceVal);
    } else if (audioVal !== null) {
      combined = Math.round(audioVal);
    } else {
      combined = null;
    }

    if (combined !== null) {
      animateScoreTo(combined);
      scoreText.textContent = `${combined} / 100`;
      lastCombined = combined;
      // live-update Today's Insights from current detections
      renderInsights({
        found: true,
        face_emotion: lastFace.name,
        face_confidence: Math.round((lastFace.score || 0) * 100),
        audio_emotion: lastAudio.name,
        audio_score: lastAudio.score,
        combined_score: lastCombined
      });
    }
  }

  // simple number animation
  let animInterval = null;
  function animateScoreTo(target){
    if (animInterval) clearInterval(animInterval);
    const start = parseInt(scoreNum.textContent) || 0;
    const stepCount = 12;
    let step = 0;
    const diff = target - start;
    animInterval = setInterval(() => {
      step++;
      const val = Math.round(start + diff * (step / stepCount));
      scoreNum.textContent = val;
      if (step >= stepCount) {
        clearInterval(animInterval);
        animInterval = null;
      }
    }, 30);
  }

  // save to backend
  async function saveNow(){
    const face = lastFace;
    const audio = lastAudio;
    const combined = parseInt(scoreNum.textContent) || 0;
    const payload = {
      face_emotion: face.name,
      face_confidence: face.score,
      audio_emotion: audio.name,
      audio_score: audio.score,
      combined_score: combined,
      meta: { userAgent: navigator.userAgent, selected_mood: face.name || audio.name || null }
    };
    try {
      const res = await fetch('api/save_mood.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      const json = await res.json();
      if (json.ok) {
        setStatus('saved');
        loadTodaySummary();
        // notify other views that today's mood changed
        try {
          const today = new Date().toISOString().slice(0,10);
          window.dispatchEvent(new CustomEvent('dayUpdated', { detail: { date: today } }));
          try { localStorage.setItem('dayUpdated', JSON.stringify({ date: today, ts: Date.now() })); } catch(e) {}
        } catch(e) { console.error('Error dispatching dayUpdated from home', e); }
      } else {
        console.error(json);
        setStatus('save failed');
      }
    } catch (e) {
      console.error(e);
      setStatus('save error');
    }
  }

  // load today's summary
  async function loadTodaySummary(){
    try {
      const res = await fetch('api/get_today_mood.php');
      if (res.status === 401) {
        todaySummary.innerHTML = '<div>Please <a href="/login.php">log in</a> to view saved data.</div>';
        return;
      }
      const j = await res.json();
      if (j.found && j.data) {
        const d = j.data;
        todaySummary.innerHTML = `
          <div><strong>${d.date} ${d.time}</strong></div>
          <div>Face: ${d.face_emotion} (${d.face_confidence ?? '—'})</div>
          <div>Audio: ${d.audio_emotion} (${d.audio_score ?? '—'})</div>
          <div>Combined: ${d.combined_score}</div>
        `;
      } else {
        todaySummary.innerHTML = '<div>No detections saved today.</div>';
      }
    } catch (e) {
      console.error(e);
      todaySummary.innerHTML = '<div>Unable to load summary.</div>';
    }
  }

  async function loadFullInsights() {
    try {
      const res = await fetch('api/get_insights.php');
      if (res.status === 401) {
        // not logged in — show prompt in summary card
        const summaryCard = document.querySelector('.summary-card');
        if (summaryCard) summaryCard.innerHTML = '<div class="muted">Please <a href="/login.php">log in</a> to view your insights.</div>';
        return;
      }
      const data = await res.json();
      // render insights (API fallback)
      renderInsights(data);

    } catch (e) {
        console.error("Insights load error:", e);
    }
}

  // Render insights UI from data object. Used for live updates and API data.
  function renderInsights(data) {
    if (!data || !data.found) return;

    // Update big combined score
    const bigScoreEl = document.querySelector('.score-number');
    if (bigScoreEl && data.combined_score != null) bigScoreEl.textContent = data.combined_score;

    // Normalize and update today's summary small card
    if (todaySummary) {
      const normalizeMoodName = (name) => {
        if (!name) return '—';
        const map = { happy:'Happy', joyful:'Joyful', calm:'Calm', peaceful:'Peaceful', neutral:'Neutral', sad:'Sad', angry:'Angry', stressed:'Stressed', anxious:'Anxious', tired:'Tired', fearful:'Anxious', disgusted:'Angry', surprised:'Confused', high_energy:'Joyful' };
        const k = String(name).toLowerCase();
        return map[k] || (k.charAt(0).toUpperCase() + k.slice(1));
      };
      todaySummary.innerHTML = `
        <div><strong>${new Date().toLocaleDateString()}</strong></div>
        <div>Face: ${normalizeMoodName(data.face_emotion)} (${data.face_confidence ?? '—'})</div>
        <div>Audio: ${normalizeMoodName(data.audio_emotion)} (${data.audio_score ?? '—'})</div>
        <div>Combined: ${data.combined_score ?? '—'}</div>
      `;
    }

    // Summary card
    const summaryCard = document.querySelector('.summary-card');
    if (summaryCard) {
      summaryCard.innerHTML = `
        <div class="label">Daily Summary</div>
        <div class="summary-row"><strong>Main Emotion:</strong> ${data.face_emotion ?? '—'}</div>
        <div class="summary-row"><strong>Energy Level:</strong> ${Math.min(100, data.audio_score ?? 0)}%</div>
        <div class="summary-row"><strong>Environment:</strong> Based on your audio levels</div>
        <div class="summary-row" style="margin-top:10px;border-top:1px solid #eef2ee;padding-top:10px">
          <strong>Insights:</strong> Your ${data.face_emotion ?? '—'} expression and ${data.audio_emotion ?? '—'} tone influenced your score.
        </div>
        <div class="reminder">Smart Reminder: Keep tracking your mood daily!</div>
      `;
    }

    // Recommendations
    const recommend = document.querySelector('.recommend');
    if (recommend) {
      let recHTML = '';
      const score = Number(data.combined_score) || 0;
      if (score >= 70) {
        recHTML += `<div class="rec-item"><div class="rec-icon">
  <svg class="icon-svg" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" fill="currentColor"/>
    <circle cx="12" cy="12" r="2" fill="white"/>
  </svg>
</div><div>You're doing great — keep the momentum!</div></div>`;
      } else if (score >= 40) {
        recHTML += `<div class="rec-item"><div class="rec-icon">
  <svg class="icon-svg" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
    <circle cx="12" cy="8" r="3" fill="currentColor"/>
    <path d="M12 11C12 11 8 13 8 16V20H16V16C16 13 12 11 12 11Z" fill="currentColor"/>
    <path d="M4 16L8 14M20 16L16 14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
  </svg>
</div><div>Try a calming tool to stabilize your emotions.</div></div>`;
      } else {
        recHTML += `<div class="rec-item"><div class="rec-icon">
  <svg class="icon-svg" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M17 21V19C17 17.9391 16.5786 16.9217 15.8284 16.1716C15.0783 15.4214 14.0609 15 13 15H5C3.93913 15 2.92172 15.4214 2.17157 16.1716C1.42143 16.9217 1 17.9391 1 19V21" fill="currentColor"/>
    <circle cx="9" cy="7" r="4" fill="currentColor"/>
    <path d="M23 21V19C22.9993 18.1137 22.7044 17.2528 22.1614 16.5523C21.6184 15.8519 20.8581 15.3516 20 15.13M16 3.13C16.8604 3.35031 17.623 3.85071 18.1676 4.55232C18.7122 5.25392 19.0078 6.11683 19.0078 7.005C19.0078 7.89318 18.7122 8.75608 18.1676 9.45769C17.623 10.1593 16.8604 10.6597 16 10.88" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
  </svg>
</div><div>Consider journaling or talking to someone you trust.</div></div>`;
      }
      recommend.innerHTML = `<div class="label" style="margin-bottom:8px">Recommended for You</div>${recHTML}`;
    }

    // Affirmation
    const aff = document.querySelector('.affirmation');
    if (aff) {
      const score = Number(data.combined_score) || 0;
      if (score >= 70) aff.textContent = `"You're shining today — keep going!"`;
      else if (score >= 40) aff.textContent = `"You're doing your best — and that’s enough."`;
      else aff.textContent = `"Take it slow — better days are coming."`;
    }
  }

  // React to updates from other views (modal, daily-log)
  window.addEventListener('dayUpdated', (ev) => {
    try {
      const d = ev.detail && ev.detail.date ? ev.detail.date : null;
      const today = new Date().toISOString().slice(0,10);
      if (!d) return;
      if (d === today) {
        loadTodaySummary();
        loadFullInsights();
        console.log('home: refreshed due to dayUpdated event');
      }
    } catch(e) { console.error('dayUpdated handler error (home)', e); }
  });

  // Cross-tab storage listener
  window.addEventListener('storage', (ev) => {
    try {
      if (ev.key !== 'dayUpdated') return;
      const payload = ev.newValue ? JSON.parse(ev.newValue) : null;
      const today = new Date().toISOString().slice(0,10);
      if (payload && payload.date === today) {
        loadTodaySummary();
        loadFullInsights();
        console.log('home: refreshed due to storage dayUpdated');
      }
    } catch(e) { console.error('storage handler error (home)', e); }
  });


  // Set dynamic greeting based on time of day
  function updateGreeting() {
    const hour = new Date().getHours();
    const greetingEl = document.getElementById('greeting');
    if (hour >= 5 && hour < 12) {
      greetingEl.textContent = 'Good Morning';
    } else if (hour >= 12 && hour < 17) {
      greetingEl.textContent = 'Good Afternoon';
    } else {
      greetingEl.textContent = 'Good Evening';
    }
  }
  updateGreeting();
  // Update greeting every minute
  setInterval(updateGreeting, 60000);

  // event listeners
  camToggle.addEventListener('click', () => {
    if (camStream) stopCamera(); else startCamera();
  });
  micToggle.addEventListener('click', () => {
    if (micStream) stopMic(); else startMic();
  });
  saveBtn.addEventListener('click', saveNow);

  // when face detection changes, update lastFace variable inside the detection loop
  // We'll make sure computeAndShowScore updates combined.

  // on unload: stop streams
  window.addEventListener('beforeunload', () => {
    stopCamera();
    stopMic();
  });

  // initial
  (async () => {
    await loadModels();
    // optionally auto-start camera/mic
    // startCamera();
    // startMic();
    // check auth status, then load saved insights if logged in
    try {
      const auth = await fetch('api/auth_status.php');
      const authJson = await auth.json();
      const userInfo = document.getElementById('userInfo');
      const authPrompt = document.getElementById('authPrompt');
      if (!authJson.logged_in) {
        // disable save button when not logged in
        saveBtn.disabled = true;
        saveBtn.textContent = 'Login to save';
        statusText.textContent = 'not logged in';
        authPrompt.style.display = 'flex';
        userInfo.style.display = 'none';
      } else {
        // show logged-in user info
        document.getElementById('username').textContent = authJson.user.username;
        userInfo.style.display = 'flex';
        authPrompt.style.display = 'none';
        saveBtn.disabled = false;
        saveBtn.textContent = 'Save Now';
        // load user-specific saved data
        loadTodaySummary();
        loadFullInsights();
      }
    } catch (e) {
      // if auth check fails, show login prompt
      console.error('Auth check error:', e);
      document.getElementById('authPrompt').style.display = 'flex';
      document.getElementById('userInfo').style.display = 'none';
      saveBtn.disabled = true;
      saveBtn.textContent = 'Login to save';
    }
    setStatus('idle');
  })();

</script>

</body>
</html>


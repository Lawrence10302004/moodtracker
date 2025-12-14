<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header('Location: /login.php');
  exit;
}
// Standalone Daily Log page matching the provided UI mock
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Daily Log</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root{
      --bg:#f6f8f6;
      --sidebar:#ecf2ec;
      --card:#ffffff;
      --muted:#6b7280;
      --accent:#7c9d85;
      --accent-strong:#6a8b74;
      --border:#e8efe8;
      --badge:#e8efe5;
      --pill:#eef3ea;
      --happy:#feeeba;
      --sad:#e4ecf9;
      --stress:#f9e4e4;
      --neutral:#f4f4f4;
    }

    /* Custom Icons */
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

    *{box-sizing:border-box;margin:0;padding:0;}
    body{font-family:Inter,system-ui,-apple-system,Segoe UI,sans-serif;background:var(--bg);color:#1f2a1f;display:flex;min-height:100vh;}

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
    .sidebar-footer{margin-top:auto;background:#f0f5f0;border:1px solid #dce6de;border-radius:12px;padding:12px;display:flex;gap:10px;align-items:center;}
    .welcome{margin-top:auto;background:#f5f5f5;border:1px solid #e0e0e0;border-radius:12px;padding:14px;display:flex;flex-direction:column;gap:10px;font-size:13px;color:#1f2a1f;box-shadow:0 1px 3px rgba(0,0,0,0.05)}
    .badge{width:36px;height:36px;border-radius:10px;background:var(--badge);display:flex;align-items:center;justify-content:center;font-size:18px;}
    .footer-text{display:flex;flex-direction:column;gap:2px;}
    .footer-title{font-size:13px;font-weight:700;color:#1f2a1f;}
    .footer-sub{font-size:12px;color:var(--muted);}

    /* Main */
    .main{margin-left:250px;flex:1;padding:26px 34px;min-height:100vh}
    .page-head{margin-bottom:22px;}
    .page-title{font-size:30px;font-weight:700;letter-spacing:-0.02em;margin-bottom:6px;color:#1f2a1f;}
    .page-sub{color:var(--muted);font-size:15px;}

    .top-grid{display:grid;grid-template-columns:2fr 1.1fr;gap:16px;align-items:start;}
    .card{background:var(--card);border-radius:18px;box-shadow:0 6px 18px rgba(0,0,0,0.06);border:1px solid var(--border);}
    .card-pad{padding:18px;}

    /* Detected moods */
    .detected-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;font-weight:700;}
    .mini-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;}
    .mini-card{border:1px solid var(--border);border-radius:16px;padding:14px;display:flex;flex-direction:column;gap:10px;}
    .mini-top{display:flex;align-items:center;justify-content:space-between;font-weight:700;color:#2d332e;}
    .mini-body{display:flex;align-items:center;gap:10px;}
    .emoji-box{width:42px;height:42px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:24px;}
    .status-text{font-weight:700;font-size:15px;}
    .status-sub{font-size:12px;color:var(--muted);}

    .score-bar{margin-top:12px;border-radius:14px;overflow:hidden;background:linear-gradient(90deg,#88a98e,#7a9f84);}
    .score-fill{height:44px;display:flex;align-items:center;justify-content:space-between;padding:0 14px;color:#fff;font-weight:700;}
    .score-label{font-size:12px;font-weight:600;opacity:0.9;}

    /* AI summary */
    .summary-card{display:flex;flex-direction:column;gap:12px;}
    .summary-title{font-weight:700;font-size:14px;display:flex;align-items:center;gap:8px;}
    .summary-body{font-size:13px;line-height:1.6;color:#2d332e;background:#f6f9f5;border:1px solid var(--border);border-radius:12px;padding:12px;}
    .btn{border:none;border-radius:14px;padding:12px 14px;font-size:14px;font-weight:700;display:flex;align-items:center;gap:10px;cursor:pointer;transition:all .2s;}
    .btn.primary{background:var(--accent);color:#fff;}
    .btn.primary:hover{transform:translateY(-1px);box-shadow:0 8px 16px rgba(17,24,39,0.08);}

    /* Mood selector */
    .section{margin-top:22px;}
    .section-title{font-weight:700;margin-bottom:10px;}
    .mood-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:10px;}
    .mood-btn{border:1px solid var(--border);background:#f7f8f6;border-radius:14px;padding:12px;display:flex;align-items:center;gap:10px;cursor:pointer;font-weight:700;transition:all .2s;}
    .mood-btn .face{width:40px;height:40px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px;}
    .mood-btn.active{border-color:#88a98e;background:#eaf2ea;box-shadow:0 8px 16px rgba(17,24,39,0.06);}

    /* Text area */
    .note-card{margin-top:12px;border:1px solid var(--border);background:#fff;border-radius:16px;padding:14px;display:flex;flex-direction:column;gap:12px;}
    .note-area{width:100%;min-height:120px;border:1px solid var(--border);border-radius:10px;padding:10px;font-family:inherit;font-size:14px;resize:vertical;background:#fdfefe;}
    .note-actions{display:flex;align-items:center;gap:10px;justify-content:space-between;}
    .chip-row{display:flex;flex-wrap:wrap;gap:8px;}
    .chip{border:1px solid var(--border);background:#f7f8f6;border-radius:999px;padding:8px 12px;font-weight:700;font-size:13px;color:#2d332e;cursor:pointer;transition:all .2s;}
    .chip.active{background:#e5efe6;border-color:#88a98e;}

    /* Camera/Mic indicators */
    .device-controls{display:flex;align-items:center;gap:12px;margin-bottom:16px;}
    .device-item{display:flex;align-items:center;gap:8px;background:#f7f8f6;border-radius:12px;padding:10px 12px;border:1px solid var(--border);}
    .device-status{width:12px;height:12px;border-radius:50%;animation:pulse 2s infinite;}
    .device-status.on{background:#22c55e;box-shadow:0 0 8px rgba(34,197,95,0.4);}
    .device-status.off{background:#ef4444;box-shadow:none;}
    .device-label{font-size:12px;font-weight:600;color:#1f2a1f;}
    .device-toggle{width:32px;height:18px;border-radius:999px;background:#ccc;cursor:pointer;position:relative;transition:all .2s;}
    .device-toggle.on{background:#22c55e;}
    .device-toggle-knob{width:16px;height:16px;border-radius:50%;background:#fff;position:absolute;top:1px;left:1px;transition:all .2s;}
    .device-toggle.on .device-toggle-knob{left:15px;}
    @keyframes pulse{0%,100%{opacity:1}50%{opacity:0.6}}

    /* Color wheel mood selector */
    .mood-selector-wrap{margin-bottom:16px;}
    .selector-tabs{display:flex;gap:8px;margin-bottom:12px;border-bottom:1px solid var(--border);}
    .selector-tab{padding:10px 14px;border:none;background:none;cursor:pointer;font-weight:600;color:var(--muted);border-bottom:2px solid transparent;transition:all .2s;}
    .selector-tab.active{color:#7c9d85;border-bottom-color:#7c9d85;}
    .color-wheel{display:grid;grid-template-columns:repeat(6,1fr);gap:10px;padding:12px 0;}
    .mood-option{width:70px;height:70px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:28px;cursor:pointer;border:3px solid transparent;transition:all .2s;}
    .mood-option:hover{transform:scale(1.08);}
    .mood-option.selected{border-color:#1f2a1f;box-shadow:0 4px 12px rgba(0,0,0,0.15);}

    @media(max-width:1024px){
      .top-grid{grid-template-columns:1fr;}
      .content{margin-left:0;padding:20px;}
      .sidebar{position:relative;width:100%;flex-direction:row;flex-wrap:wrap;gap:10px;align-items:center;border-right:none;border-bottom:1px solid var(--border);}
      .nav{flex-direction:row;flex-wrap:wrap;}
    }
    /* Post preview (Facebook-like) */
    .post-preview{border-radius:12px;border:1px solid var(--border);background:#fff;padding:12px;box-shadow:0 6px 18px rgba(0,0,0,0.04);}
    .post-header{display:flex;gap:10px;align-items:center;margin-bottom:8px}
    .post-avatar{width:40px;height:40px;border-radius:50%;background:var(--accent);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700}
    .post-author{font-weight:700}
    .post-date{font-size:12px;color:var(--muted)}
    .post-body{margin-top:6px;white-space:pre-wrap}
    .post-media{margin-top:10px;border-radius:8px;overflow:hidden}
    .post-media img, .post-media video{width:100%;height:auto;display:block}
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
      <a class="nav-item" href="home.php">
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
      <a class="nav-item active" href="daily-log.php">
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
    <div class="page-head">
      <div class="page-title">Daily Log</div>
      <div class="page-sub" id="dateDisplay">Monday, December 9, 2024</div>
    </div>
    <div id="dateNotice" style="margin-bottom:12px;color:#9a2a2a;font-weight:700"></div>

    <div class="top-grid">
      <section class="card card-pad">
        <div class="detected-head">
          <span>Detected Moods</span>
        </div>
        
        <!-- Camera & Mic Indicators + Live detector -->
        <div class="device-controls">
          <div class="device-item">
            <div class="device-status off" id="cameraStatus"></div>
            <span class="device-label">Camera</span>
            <div class="device-toggle" id="cameraToggle">
              <div class="device-toggle-knob"></div>
            </div>
          </div>
          <div class="device-item">
            <div class="device-status off" id="micStatus"></div>
            <span class="device-label">Microphone</span>
            <div class="device-toggle" id="micToggle">
              <div class="device-toggle-knob"></div>
            </div>
          </div>
        </div>

        <div id="videoHolder" style="margin-top:12px;position:relative;border-radius:12px;overflow:hidden;background:#000;min-height:180px;display:flex;align-items:center;justify-content:center">
          <video id="webcam" autoplay muted playsinline style="width:100%;height:100%;object-fit:cover;display:block"></video>
          <canvas id="overlay" style="position:absolute;left:0;top:0;pointer-events:none;width:100%;height:100%"></canvas>
        </div>

        <div style="display:flex;gap:10px;margin-top:10px">
          <div style="margin-left:auto;display:flex;align-items:center;gap:8px;color:var(--muted)"><span id="statusText">idle</span></div>
        </div>

        <div class="mini-grid">
          <div class="mini-card">
            <div class="mini-top">
              <span>Facial</span>
              <span style="width:18px;height:18px;border-radius:50%;border:2px solid #7fa287;display:flex;align-items:center;justify-content:center;color:#7fa287;font-size:12px;">•</span>
            </div>
            <div class="mini-body">
              <div class="emoji-box" id="faceEmoji" style="background:var(--happy);">😊</div>
              <div>
                <div class="status-text" id="faceEmotion">Happy</div>
                <div class="status-sub" id="faceConfidence">87%</div>
              </div>
            </div>
          </div>
          <div class="mini-card">
            <div class="mini-top">
              <span>Audio</span>
              <span style="width:18px;height:18px;border-radius:50%;border:2px solid #7fa287;display:flex;align-items:center;justify-content:center;color:#7fa287;font-size:12px;">•</span>
            </div>
            <div class="mini-body">
              <div class="emoji-box" id="audioEmoji" style="background:var(--neutral);">🎵</div>
              <div>
                <div class="status-text" id="audioEmotion">Neutral</div>
                <div class="status-sub" id="audioScore">—</div>
              </div>
            </div>
          </div>
        </div>

        <div class="score-bar">
          <div class="score-fill">
            <div>
              <div class="score-label">Combined Score</div>
              <div id="combinedLabel">Positive &amp; Calm</div>
            </div>
            <div style="font-size:22px;font-weight:800;" id="combinedScore">78</div>
          </div>
        </div>
      </section>

      <section class="card card-pad summary-card">
        <div class="summary-title">🤖 AI Summary</div>
        <div class="summary-body" id="aiSummary">
          You're feeling happy and calm today! Your facial expressions and voice tone both indicate positive energy. Your main influences were social interactions with friends. This is a great day – consider capturing these moments in photos or writing more about what made you feel good.
        </div>
      </section>
    </div>

    <section class="section">
      <div class="section-title">How do you feel?</div>
      
      <!-- Mood Selector Tabs -->
      <div class="mood-selector-wrap">
        <div class="selector-tabs">
          <button class="selector-tab active" data-tab="emoji">😊 Emoji</button>
          <button class="selector-tab" data-tab="wheel">🎨 Color Wheel</button>
        </div>
        
        <!-- Emoji Selector -->
        <div id="emojiTab" class="mood-grid" style="display:grid;">
          <button class="mood-btn active" data-mood="Happy">
            <div class="face" style="background:var(--happy);">😊</div><span>Happy</span>
          </button>
          <button class="mood-btn" data-mood="Calm">
            <div class="face" style="background:#eaf2ea;">🙂</div><span>Calm</span>
          </button>
          <button class="mood-btn" data-mood="Sad">
            <div class="face" style="background:var(--sad);">😢</div><span>Sad</span>
          </button>
          <button class="mood-btn" data-mood="Stressed">
            <div class="face" style="background:var(--stress);">😰</div><span>Stressed</span>
          </button>
          <button class="mood-btn" data-mood="Neutral">
            <div class="face" style="background:var(--neutral);">😐</div><span>Neutral</span>
          </button>
          <button class="mood-btn" data-mood="Tired">
            <div class="face" style="background:#f2eadf;">😴</div><span>Tired</span>
          </button>
        </div>

        <!-- Color Wheel Selector -->
        <div id="wheelTab" style="display:none;">
          <div class="color-wheel">
            <div class="mood-option" data-mood="Joyful" style="background:#FFD93D;color:#fff;">😄</div>
            <div class="mood-option" data-mood="Happy" style="background:#FFA500;color:#fff;">😊</div>
            <div class="mood-option" data-mood="Calm" style="background:#90EE90;color:#fff;">🙂</div>
            <div class="mood-option" data-mood="Peaceful" style="background:#87CEEB;color:#fff;">☮️</div>
            <div class="mood-option" data-mood="Sad" style="background:#4169E1;color:#fff;">😢</div>
            <div class="mood-option" data-mood="Angry" style="background:#DC143C;color:#fff;">😠</div>
            <div class="mood-option" data-mood="Stressed" style="background:#FF6347;color:#fff;">😰</div>
            <div class="mood-option" data-mood="Anxious" style="background:#FF69B4;color:#fff;">😰</div>
            <div class="mood-option" data-mood="Confused" style="background:#DAA520;color:#fff;">😕</div>
            <div class="mood-option" data-mood="Tired" style="background:#696969;color:#fff;">😴</div>
            <div class="mood-option" data-mood="Neutral" style="background:#A9A9A9;color:#fff;">😐</div>
            <div class="mood-option" data-mood="Loved" style="background:#FF1493;color:#fff;">🥰</div>
          </div>
        </div>
      </div>
    </section>

    <section class="section">
      <div class="section-title">Write Your Thoughts</div>
        <div class="note-card">
    <textarea id="diaryText" class="note-area" placeholder="How was your day? What happened?..."></textarea>
    <div class="note-actions">
      <div style="display:flex;align-items:center;gap:8px">
        <button class="btn primary" id="stt" aria-label="Start speech to text">
          <svg class="icon-svg" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="9" y="2" width="6" height="11" rx="3" stroke="currentColor" stroke-width="1.8" fill="none"/>
            <path d="M5 10v1a7 7 0 0014 0v-1" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <line x1="12" y1="18" x2="12" y2="22" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
          </svg>
          <span class="btn-label">Speech-to-Text</span>
        </button>
        <button class="btn primary" id="saveEntry" aria-label="Save diary entry">
          <svg class="icon-svg" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v13a2 2 0 01-2 2z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
            <path d="M7 3v6h8V3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M9 21v-6h6v6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          <span class="btn-label">Save Entry</span>
        </button>
        <label class="btn primary file-btn" for="mediaInput" style="cursor:pointer;align-items:center;">
          <svg class="icon-svg" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="3" y="5" width="18" height="16" rx="2" stroke="currentColor" stroke-width="1.8" fill="none"/>
            <circle cx="8.5" cy="10" r="1.5" fill="currentColor"/>
            <path d="M21 15l-5-5-6 6-3-3-4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          <span class="btn-label">Add Attachment</span>
          <input type="file" id="mediaInput" style="display:none;" accept="image/*,video/*">
        </label>
      </div>
      <div id="mediaPreview" style="margin-top:12px;display:flex;gap:8px;flex-wrap:wrap"></div>
    </div>
  </div>
    </section>

    <section class="section">
      <div class="section-title">What influenced your mood?</div>
      <div class="chip-row" id="chips">
        <div class="chip active" data-tag="Friends">Friends</div>
        <div class="chip" data-tag="School">School</div>
        <div class="chip" data-tag="Family">Family</div>
        <div class="chip" data-tag="Health">Health</div>
        <div class="chip" data-tag="Social">Social</div>
        <div class="chip" data-tag="Environment">Environment</div>
      </div>
    </section>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api/dist/face-api.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@3.21.0/dist/tf.min.js"></script>
  <script>
    // Determine which date this page is showing (URL param `date` or today)
    const urlParams = new URLSearchParams(window.location.search);
    // Get today's date in local timezone (not UTC)
    function getLocalDateString(date) {
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, '0');
      const day = String(date.getDate()).padStart(2, '0');
      return `${year}-${month}-${day}`;
    }

    let currentDate = urlParams.get('date') || getLocalDateString(new Date());

    function updateDateDisplay() {
      const parts = currentDate.split('-');
      const dt = new Date(parts[0], parts[1]-1, parts[2]);
      const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
      document.getElementById('dateDisplay').textContent = dt.toLocaleDateString('en-US', options);
    }
    updateDateDisplay();
    // Disable editing when viewing future dates
    (function enforceDateEditingRules(){
      const todayStr = getLocalDateString(new Date());
      if (currentDate > todayStr) {
        // show notice
        const notice = document.getElementById('dateNotice');
        if (notice) notice.textContent = 'Viewing a future date — creating or editing logs is disabled.';
        // disable controls
        document.getElementById('saveEntry')?.setAttribute('disabled','disabled');
        document.getElementById('stt')?.setAttribute('disabled','disabled');
        document.getElementById('mediaInput')?.setAttribute('disabled','disabled');
        document.querySelectorAll('.mood-btn, .mood-option, .chip').forEach(el=>{
          el.style.pointerEvents = 'none';
          el.style.opacity = '0.6';
        });
      }
    })();

    // Emotion emoji and color mapping
    const emotionMap = {
      'Happy': { emoji: '😊', bg: '#feeeba' },
      'Joyful': { emoji: '😄', bg: '#FFD93D' },
      'Calm': { emoji: '🙂', bg: '#eaf2ea' },
      'Peaceful': { emoji: '☮️', bg: '#87CEEB' },
      'Sad': { emoji: '😢', bg: '#e4ecf9' },
      'Angry': { emoji: '😠', bg: '#DC143C' },
      'Stressed': { emoji: '😰', bg: '#f9e4e4' },
      'Anxious': { emoji: '😰', bg: '#FF69B4' },
      'Confused': { emoji: '😕', bg: '#DAA520' },
      'Tired': { emoji: '😴', bg: '#f2eadf' },
      'Neutral': { emoji: '😐', bg: '#f4f4f4' },
      'Loved': { emoji: '🥰', bg: '#FF1493' }
    };

    // Normalize incoming emotion labels to the UI keys
    function normalizeMoodName(name) {
      if (!name) return null;
      const map = {
        happy: 'Happy', joyful: 'Joyful', calm: 'Calm', peaceful: 'Peaceful', neutral: 'Neutral',
        sad: 'Sad', angry: 'Angry', stressed: 'Stressed', anxious: 'Anxious', tired: 'Tired',
        fearful: 'Anxious', disgusted: 'Angry', surprised: 'Confused', high_energy: 'Joyful'
      };
      const k = String(name).toLowerCase();
      return map[k] || (k.charAt(0).toUpperCase() + k.slice(1));
    }

    // Camera & Mic state
    let deviceState = {
      camera: false,
      mic: false
    };

    // Device toggle handlers
    document.getElementById('cameraToggle').addEventListener('click', async () => {
      if (deviceState.camera) {
        stopCamera();
      } else {
        await startCamera();
      }
      updateAISummary();
    });

    document.getElementById('micToggle').addEventListener('click', async () => {
      if (deviceState.mic) {
        stopMic();
      } else {
        await startMic();
      }
      updateAISummary();
    });

    function updateDeviceStatus(device) {
      const isOn = deviceState[device];
      const statusEl = document.getElementById(device === 'camera' ? 'cameraStatus' : 'micStatus');
      const toggleEl = document.getElementById(device === 'camera' ? 'cameraToggle' : 'micToggle');
      
      if (isOn) {
        statusEl.classList.remove('off');
        statusEl.classList.add('on');
        toggleEl.classList.add('on');
      } else {
        statusEl.classList.remove('on');
        statusEl.classList.add('off');
        toggleEl.classList.remove('on');
      }
    }

    // Mood selector tabs
    document.querySelectorAll('.selector-tab').forEach(tab => {
      tab.addEventListener('click', () => {
        document.querySelectorAll('.selector-tab').forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        
        const tabName = tab.dataset.tab;
        document.getElementById('emojiTab').style.display = tabName === 'emoji' ? 'grid' : 'none';
        document.getElementById('wheelTab').style.display = tabName === 'wheel' ? 'block' : 'none';
      });
    });

    // Mood selection - both emoji and color wheel
    function handleMoodSelection(mood) {
      // Update emoji tab
      document.querySelectorAll('.mood-btn').forEach(b => b.classList.remove('active'));
      document.querySelector(`.mood-btn[data-mood="${mood}"]`)?.classList.add('active');
      
      // Update color wheel
      document.querySelectorAll('.mood-option').forEach(o => o.classList.remove('selected'));
      document.querySelector(`.mood-option[data-mood="${mood}"]`)?.classList.add('selected');
      
      updateAISummary();
    }

    document.getElementById('emojiTab').addEventListener('click', (e) => {
      const btn = e.target.closest('.mood-btn');
      if (!btn) return;
      handleMoodSelection(btn.dataset.mood);
    });

    document.getElementById('wheelTab').addEventListener('click', (e) => {
      const opt = e.target.closest('.mood-option');
      if (!opt) return;
      handleMoodSelection(opt.dataset.mood);
    });

    // track last saved diary id for media uploads
    let lastDiaryId = null;
    // selected media file for preview (client-only until uploaded)
    let selectedMediaFile = null;

    // Load today's mood and data
    async function loadTodayData() {
      try {
        const res = await fetch(`api/get_daily_log.php?date=${currentDate}`);
        if (res.status === 401) return;
        const data = await res.json();
        if (data.found && data.mood) {
          // Update detected moods
            let faceEmotion = data.mood.face_emotion || 'Unknown';
            const faceConf = data.mood.face_confidence || 0;
            // normalize before using in UI
            faceEmotion = normalizeMoodName(faceEmotion) || faceEmotion;
            document.getElementById('faceEmotion').textContent = faceEmotion;
            document.getElementById('faceConfidence').textContent = `${faceConf}%`;
          
            const emotionData = emotionMap[faceEmotion] || { emoji: '😶', bg: '#f4f4f4' };
          document.getElementById('faceEmoji').textContent = emotionData.emoji;
          document.getElementById('faceEmoji').style.background = emotionData.bg;

          let audioEmotion = data.mood.audio_emotion || 'Not detected';
          const audioScore = data.mood.audio_score || '—';
          audioEmotion = normalizeMoodName(audioEmotion) || audioEmotion;
          document.getElementById('audioEmotion').textContent = audioEmotion;
          document.getElementById('audioScore').textContent = typeof audioScore === 'number' ? `${audioScore}%` : audioScore;
          
          const audioData = emotionMap[audioEmotion] || { emoji: '🎵', bg: '#f4f4f4' };
          document.getElementById('audioEmoji').textContent = audioData.emoji;
          document.getElementById('audioEmoji').style.background = audioData.bg;

          // Update combined score
          const score = data.mood.combined_score || 78;
          document.getElementById('combinedScore').textContent = score;
          
          let label = 'Neutral';
          if (score >= 80) label = 'Excellent mood';
          else if (score >= 60) label = 'Positive & Calm';
          else if (score >= 40) label = 'Okay';
          else label = 'Challenging moment';
          document.getElementById('combinedLabel').textContent = label;

          // Load diary text
          if (data.diary && data.diary.content) {
            document.getElementById('diaryText').value = data.diary.content;
            if (data.diary.id) lastDiaryId = data.diary.id;
          }

          // Load tags
          if (data.tags && data.tags.length > 0) {
            document.querySelectorAll('.chip').forEach(c => c.classList.remove('active'));
            data.tags.forEach(tag => {
              const chip = document.querySelector(`.chip[data-tag="${tag}"]`);
              if (chip) chip.classList.add('active');
            });
          }
          // Apply selected mood: prefer mood.meta.selected_mood, else first tag, else mood.face_emotion
          try {
            let moodToSet = null;
            if (data.mood && data.mood.meta) {
              try { const m = JSON.parse(data.mood.meta); if (m && m.selected_mood) moodToSet = m.selected_mood; } catch(e) { /* ignore */ }
            }
            if (!moodToSet && data.tags && data.tags.length > 0) moodToSet = data.tags[0];
            if (!moodToSet && data.mood && data.mood.face_emotion) moodToSet = data.mood.face_emotion;
            if (moodToSet) {
              moodToSet = normalizeMoodName(moodToSet) || moodToSet;
              handleMoodSelection(moodToSet);
            }
          } catch(e) { console.error('Error applying mood selection:', e); }
        }
        updateAISummary();
      } catch (e) {
        console.error('Error loading daily log:', e);
      }
    }

    // Listen for calendar/modal saves and refresh today's data when the date matches
    window.addEventListener('dayUpdated', (ev) => {
      try {
        const d = ev.detail && ev.detail.date ? ev.detail.date : null;
        if (!d) return;
        if (d === currentDate) {
          loadTodayData();
          console.log('daily-log: refreshed due to dayUpdated event');
        }
      } catch (err) { console.error('dayUpdated handler error', err); }
    });

    // Also listen to storage events (cross-tab) for dayUpdated
    window.addEventListener('storage', (ev) => {
      try {
        if (ev.key !== 'dayUpdated') return;
        const payload = ev.newValue ? JSON.parse(ev.newValue) : null;
        if (!payload || !payload.date) return;
        if (payload.date === currentDate) {
          loadTodayData();
          console.log('daily-log: refreshed due to storage dayUpdated');
        }
      } catch (err) { console.error('storage handler error', err); }
    });

    // Auto-generate AI summary
    function updateAISummary() {
      const mood = document.querySelector('.mood-btn.active')?.dataset.mood || document.querySelector('.mood-option.selected')?.dataset.mood || 'Happy';
      const tags = Array.from(document.querySelectorAll('.chip.active')).map(c => c.dataset.tag);
      const cameraOn = deviceState.camera;
      const micOn = deviceState.mic;
      
      let summary = '';
      
      // Build summary based on mood and active devices
      if (mood === 'Happy' || mood === 'Joyful') {
        summary = `You're feeling happy today! ${cameraOn ? 'Your facial expressions show positive energy.' : ''} ${micOn ? 'Your voice tone is uplifting.' : ''} `;
      } else if (mood === 'Calm' || mood === 'Peaceful') {
        summary = `You're in a calm and peaceful state. ${cameraOn ? 'Your expressions reflect serenity.' : ''} ${micOn ? 'Your voice is relaxed.' : ''} `;
      } else if (mood === 'Sad') {
        summary = `You're feeling down. Consider reaching out to someone you trust. ${tags.length > 0 ? 'Factors affecting your mood: ' + tags.slice(0, 2).join(', ') : ''} `;
      } else if (mood === 'Stressed' || mood === 'Anxious') {
        summary = `You're experiencing stress or anxiety. Try some calming tools to relax. ${tags.length > 0 ? 'Main triggers: ' + tags.slice(0, 2).join(', ') : ''} `;
      } else if (mood === 'Tired') {
        summary = `You're feeling tired. Make sure to get enough rest and take care of yourself. `;
      } else {
        summary = `You're feeling ${mood.toLowerCase()}. `;
      }
      
      if (tags.length > 0) {
        summary += `Your mood was influenced by: ${tags.join(', ')}. `;
      }
      
      if (cameraOn && micOn) {
        summary += 'Both facial and audio analysis active.';
      } else if (cameraOn) {
        summary += 'Facial analysis is enabled.';
      } else if (micOn) {
        summary += 'Audio analysis is enabled.';
      } else {
        summary += 'Enable camera or microphone for more detailed mood analysis.';
      }
      
      document.getElementById('aiSummary').textContent = summary;
    }

    // Chip toggles
    document.getElementById('chips').addEventListener('click', (e) => {
      const chip = e.target.closest('.chip');
      if (!chip) return;
      chip.classList.toggle('active');
      updateAISummary();
    });

    // Speech-to-text
    document.getElementById('stt').addEventListener('click', () => {
      const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
      if (!SpeechRecognition) {
        alert('Speech recognition not supported in your browser');
        return;
      }
      const recognition = new SpeechRecognition();
      recognition.start();
      const sttLabel = document.querySelector('#stt .btn-label');
      if (sttLabel) sttLabel.textContent = 'Listening...';
      recognition.onresult = (e) => {
        const transcript = Array.from(e.results).map(r => r[0].transcript).join('');
        document.getElementById('diaryText').value += transcript + ' ';
        if (sttLabel) sttLabel.textContent = 'Speech-to-Text';
      };
      recognition.onerror = () => {
        if (sttLabel) sttLabel.textContent = 'Speech-to-Text';
      };
    });

    // File uploads with progress
    async function uploadMedia(file, diaryId) {
      console.log('📤 uploadMedia called with diaryId:', diaryId, 'file:', file.name);
      const formData = new FormData();
      formData.append('media', file);
      // prefer explicit diary id if available, otherwise send empty (server will accept NULL)
      if (diaryId) formData.append('diary_id', diaryId);
      formData.append('date', currentDate);
      
      try {
        const res = await fetch('api/upload_media.php', {
          method: 'POST',
          body: formData
        });
        const data = await res.json();
        if (data.ok) {
          console.log('✅ Media uploaded:', data);
          alert(`✅ ${data.type} uploaded successfully!`);
          return data;
        } else {
          console.error('❌ Upload failed:', data.error);
          alert('❌ Upload failed: ' + data.error);
        }
      } catch (e) {
        console.error('❌ Upload error:', e);
        alert('❌ Upload error: ' + e.message);
      }
    }

    document.getElementById('mediaInput').addEventListener('change', (e) => {
      const file = e.target.files[0];
      if (!file) return;
      
      if (file.size > 50 * 1024 * 1024) {
        alert('❌ File too large. Max 50MB');
        return;
      }
      
      // Show immediate preview for images (and small preview for video)
      const preview = document.getElementById('mediaPreview');
      preview.innerHTML = '';
      selectedMediaFile = file;
      
      const handleUpload = () => {
        console.log('📁 handleUpload called. lastDiaryId:', lastDiaryId);
        // If diary hasn't been saved yet, prompt user to save first to attach media.
        if (!lastDiaryId) {
          console.log('⚠️ No diary ID yet, prompting user to save first');
          if (confirm('Save diary first so media can attach to it? Click OK to save now.')) {
            // trigger save and then upload after success by listening for lastDiaryId
            console.log('Clicking save button...');
            document.getElementById('saveEntry').click();
            // wait up to 8s for lastDiaryId to be set
            const start = Date.now();
            const waitForId = setInterval(() => {
              console.log('Waiting... lastDiaryId:', lastDiaryId, 'elapsed:', Date.now() - start, 'ms');
              if (lastDiaryId) {
                clearInterval(waitForId);
                console.log('✅ Diary ID detected after save, uploading:', lastDiaryId);
                uploadMedia(file, lastDiaryId);
              } else if (Date.now() - start > 8000) {
                clearInterval(waitForId);
                console.error('❌ Timeout waiting for diary ID after 8s');
                // fallback: upload without diary link
                uploadMedia(file, null);
              }
            }, 200);
          } else {
            // user chose not to save now — upload without diary link
            console.log('ℹ️ User chose not to save, uploading without diary link');
            uploadMedia(file, null);
          }
        } else {
          console.log('✅ Diary ID exists, uploading:', lastDiaryId);
          uploadMedia(file, lastDiaryId);
        }
      };
      
      if (file.type && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = () => {
          const container = document.createElement('div');
          container.style.position = 'relative';
          container.style.display = 'inline-block';
          
          const img = document.createElement('img');
          img.src = reader.result;
          img.style.maxWidth = '220px';
          img.style.height = 'auto';
          img.style.borderRadius = '8px';
          img.style.boxShadow = '0 6px 14px rgba(0,0,0,0.06)';
          
          const clearBtn = document.createElement('button');
          clearBtn.textContent = '✕';
          clearBtn.type = 'button';
          clearBtn.style.position = 'absolute';
          clearBtn.style.top = '4px';
          clearBtn.style.right = '4px';
          clearBtn.style.width = '24px';
          clearBtn.style.height = '24px';
          clearBtn.style.borderRadius = '50%';
          clearBtn.style.border = 'none';
          clearBtn.style.background = '#ff6b6b';
          clearBtn.style.color = '#fff';
          clearBtn.style.cursor = 'pointer';
          clearBtn.style.fontSize = '14px';
          clearBtn.style.fontWeight = 'bold';
          clearBtn.style.display = 'flex';
          clearBtn.style.alignItems = 'center';
          clearBtn.style.justifyContent = 'center';
          clearBtn.onclick = () => {
            preview.innerHTML = '';
            selectedMediaFile = null;
            document.getElementById('mediaInput').value = '';
          };
          
          container.appendChild(img);
          container.appendChild(clearBtn);
          preview.appendChild(container);
          handleUpload();
        };
        reader.readAsDataURL(file);
      } else if (file.type && file.type.startsWith('video/')) {
        const container = document.createElement('div');
        container.style.position = 'relative';
        container.style.display = 'inline-block';
        
        const vid = document.createElement('video');
        vid.controls = true;
        vid.style.maxWidth = '220px';
        vid.style.borderRadius = '8px';
        const url = URL.createObjectURL(file);
        vid.src = url;
        
        const clearBtn = document.createElement('button');
        clearBtn.textContent = '✕';
        clearBtn.type = 'button';
        clearBtn.style.position = 'absolute';
        clearBtn.style.top = '4px';
        clearBtn.style.right = '4px';
        clearBtn.style.width = '24px';
        clearBtn.style.height = '24px';
        clearBtn.style.borderRadius = '50%';
        clearBtn.style.border = 'none';
        clearBtn.style.background = '#ff6b6b';
        clearBtn.style.color = '#fff';
        clearBtn.style.cursor = 'pointer';
        clearBtn.style.fontSize = '14px';
        clearBtn.style.fontWeight = 'bold';
        clearBtn.style.display = 'flex';
        clearBtn.style.alignItems = 'center';
        clearBtn.style.justifyContent = 'center';
        clearBtn.onclick = () => {
          preview.innerHTML = '';
          selectedMediaFile = null;
          document.getElementById('mediaInput').value = '';
        };
        
        container.appendChild(vid);
        container.appendChild(clearBtn);
        preview.appendChild(container);
        handleUpload();
      }
    });

    // Save entry - complete flow: diary → mood → tags
    document.getElementById('saveEntry').addEventListener('click', async () => {
      const mood = document.querySelector('.mood-btn.active')?.dataset.mood || document.querySelector('.mood-option.selected')?.dataset.mood || 'Unknown';
      const thoughts = document.getElementById('diaryText').value.trim();
      const tags = Array.from(document.querySelectorAll('.chip.active')).map(c => c.dataset.tag);
      const today = currentDate;

      if (!thoughts) {
        alert('Please write something in your diary');
        return;
      }

      // Disable button during save
      const saveBtn = document.getElementById('saveEntry');
      const originalText = saveBtn.textContent;
      saveBtn.disabled = true;
      saveBtn.textContent = '⏳ Saving...';

      try {
        // Step 1: Create diary entry
        const diaryRes = await fetch('api/save_diary.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: `content=${encodeURIComponent(thoughts)}&date=${today}`
        });
        const diaryData = await diaryRes.json();
        if (!diaryData.ok) throw new Error('Diary save failed: ' + diaryData.error);
        const diaryId = diaryData.id;
        // remember for media uploads
        lastDiaryId = diaryId;
        console.log('✅ Diary saved with ID:', lastDiaryId);

        // Step 2: Save mood log with device status and diary link
        const moodPayload = {
          date: currentDate,
          face_emotion: mood,
          face_confidence: 0,
          audio_emotion: mood,
          audio_score: 0,
          combined_score: 75,
          meta: {
            diary_id: diaryId,
            camera_enabled: deviceState.camera,
            mic_enabled: deviceState.mic,
            selected_mood: mood,
            saved_at: new Date().toISOString()
          }
        };

        const moodRes = await fetch('api/save_mood.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(moodPayload)
        });
        const moodData = await moodRes.json();
        if (!moodData.ok) throw new Error('Mood save failed: ' + moodData.error);
        const moodId = moodData.id;

        // Step 3: Save tags linked to mood_id
        if (tags.length > 0) {
          const tagsRes = await fetch('api/save_mood_tags.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `mood_id=${moodId}&date=${today}&tags=${tags.map(t => encodeURIComponent(t)).join('&tags=')}`
          });
          const tagsData = await tagsRes.json();
          if (!tagsData.ok) console.warn('Tags save warning:', tagsData.error);
        }

        saveBtn.textContent = '✅ Saved!';
        setTimeout(() => {
          saveBtn.disabled = false;
          saveBtn.textContent = originalText;
        }, 2000);

        await loadTodayData();
        // notify other views that this date was updated
        try {
          window.dispatchEvent(new CustomEvent('dayUpdated', { detail: { date: today } }));
          try { localStorage.setItem('dayUpdated', JSON.stringify({ date: today, ts: Date.now() })); } catch(e) {}
        } catch(e) { console.error('Error dispatching dayUpdated after saveEntry', e); }
      } catch (e) {
        console.error('Save error:', e);
        saveBtn.disabled = false;
        saveBtn.textContent = originalText;
        alert('Error saving entry: ' + e.message);
      }
    });

    // --- Live detection functions (adapted from home) ---
    const webcamEl = document.getElementById('webcam');
    const overlay = document.getElementById('overlay');
    const statusTextEl = document.getElementById('statusText');
    let camStream = null;
    let micStream = null;
    let audioContext = null;
    let analyser = null;
    let audioDataArray = null;
    let audioInterval = null;
    let faceInterval = null;
    let modelsLoaded = false;

    const FACE_WEIGHT = 0.7;
    const AUDIO_WEIGHT = 0.3;

    function setStatus(s) { if (statusTextEl) statusTextEl.textContent = s; }

    async function loadModels() {
      const MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model/';
      setStatus('loading models...');
      try {
        await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
        await faceapi.nets.faceExpressionNet.loadFromUri(MODEL_URL);
        modelsLoaded = true;
        setStatus('idle');
      } catch (e) {
        console.error('Model load error', e);
        setStatus('models failed');
      }
    }

    async function startCamera(){
      if (!modelsLoaded) await loadModels();
      if (camStream) return;
      try {
        camStream = await navigator.mediaDevices.getUserMedia({ video: { width: 640, height: 480 }, audio: false });
        webcamEl.srcObject = camStream;
        webcamEl.play();
        deviceState.camera = true;
        updateDeviceStatus('camera');
        setStatus('camera on');
        startFaceLoop();
      } catch (e) {
        console.error(e);
        setStatus('camera blocked or not available');
      }
    }
    function stopCamera(){
      if (!camStream) return;
      camStream.getTracks().forEach(t => t.stop());
      camStream = null;
      webcamEl.pause();
      webcamEl.srcObject = null;
      deviceState.camera = false;
      updateDeviceStatus('camera');
      setStatus('camera off');
      stopFaceLoop();
      clearOverlay();
    }

    function clearOverlay(){
      if (!overlay) return;
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
          let top = { name: null, score: 0 };
          Object.keys(expressions).forEach(k => { if (expressions[k] > top.score) top = { name: k, score: expressions[k] }; });
          // Update face UI
          const name = top.name ? top.name.charAt(0).toUpperCase() + top.name.slice(1) : '—';
          document.getElementById('faceEmotion').textContent = name;
          document.getElementById('faceConfidence').textContent = `${Math.round((top.score||0)*100)}%`;
          const em = emotionMap[name] || { emoji: '😶', bg: '#f4f4f4' };
          document.getElementById('faceEmoji').textContent = em.emoji;
          document.getElementById('faceEmoji').style.background = em.bg;
          computeAndShowScore(name, top.score, null, null);
        } else {
          document.getElementById('faceEmotion').textContent = 'No face';
        }
      }, 700);
    }
    function stopFaceLoop(){ if (faceInterval) clearInterval(faceInterval); faceInterval = null; }

    function drawOverlay(detection){
      if (!overlay || !webcamEl) return;
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

    async function startMic(){
      try {
        micStream = await navigator.mediaDevices.getUserMedia({ audio: true, video:false });
        deviceState.mic = true;
        updateDeviceStatus('mic');
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
      if (micStream) { micStream.getTracks().forEach(t => t.stop()); micStream = null; }
      if (audioContext) { audioContext.close(); audioContext = null; }
      deviceState.mic = false;
      updateDeviceStatus('mic');
      setStatus('mic off');
    }

    function sampleAudio(){
      if (!analyser) return;
      analyser.getByteTimeDomainData(audioDataArray);
      let sum = 0;
      for (let i = 0; i < audioDataArray.length; i++) {
        const v = (audioDataArray[i] - 128) / 128;
        sum += v * v;
      }
      const rms = Math.sqrt(sum / audioDataArray.length);
      let audioEmotion = 'neutral';
      let audioScore = Math.round(rms * 100);
      if (rms > 0.15) audioEmotion = 'high_energy';
      else if (rms > 0.06) audioEmotion = 'neutral';
      else audioEmotion = 'calm';
      document.getElementById('audioEmotion').textContent = audioEmotion;
      document.getElementById('audioScore').textContent = `${audioScore}%`;
      const audioMap = emotionMap[audioEmotion.charAt(0).toUpperCase() + audioEmotion.slice(1)] || { emoji: '🎵', bg: '#f4f4f4' };
      document.getElementById('audioEmoji').textContent = audioMap.emoji;
      document.getElementById('audioEmoji').style.background = audioMap.bg;
      computeAndShowScore(null, null, audioEmotion, audioScore);
    }

    let lastFace = { name: null, score: 0 };
    let lastAudio = { name: null, score: 0 };
    // track last detected mood to avoid frequent saves
    let lastDetectedMood = null;
    let lastSavedDetectedMood = null;
    let lastDetectionSavedAt = 0; // timestamp ms

    function computeAndShowScore(faceName, faceConf, audioName, audioScoreVal){
      if (faceName) lastFace = { name: faceName.toLowerCase(), score: faceConf };
      if (audioName) lastAudio = { name: audioName.toLowerCase(), score: audioScoreVal };
      const faceMap = { happy: 90, neutral: 60, sad: 20, angry: 15, fearful: 10, disgusted: 20, surprised: 70 };
      const faceVal = lastFace.name ? (faceMap[lastFace.name] || 50) * (lastFace.score || 1) : null;
      const audioVal = lastAudio.name ? lastAudio.score : null;
      let combined = null;
      if (faceVal !== null && audioVal !== null) {
        combined = Math.round(((faceVal * FACE_WEIGHT) + (audioVal * AUDIO_WEIGHT)) / (FACE_WEIGHT + AUDIO_WEIGHT));
      } else if (faceVal !== null) combined = Math.round(faceVal);
      else if (audioVal !== null) combined = Math.round(audioVal);
      if (combined !== null) {
        document.getElementById('combinedScore').textContent = combined;
        let label = 'Neutral';
        if (combined >= 80) label = 'Excellent mood';
        else if (combined >= 60) label = 'Positive & Calm';
        else if (combined >= 40) label = 'Okay';
        else label = 'Challenging moment';
        document.getElementById('combinedLabel').textContent = label;
        updateAISummary();
        // derive a user-friendly mood name from detection and normalize to available UI options
        const faceMapToUi = {
          happy: 'Happy', joyful: 'Joyful', calm: 'Calm', neutral: 'Neutral', sad: 'Sad', angry: 'Angry', fearful: 'Anxious', disgusted: 'Angry', surprised: 'Confused'
        };
        const audioMapToUi = { calm: 'Calm', neutral: 'Calm', high_energy: 'Joyful' };
        let moodFromFace = lastFace.name ? (faceMapToUi[lastFace.name.toLowerCase()] || null) : null;
        let moodFromAudio = lastAudio.name ? (audioMapToUi[lastAudio.name.toLowerCase()] || null) : null;
        let moodToSet = moodFromFace || moodFromAudio || (combined >= 80 ? 'Happy' : (combined >= 60 ? 'Calm' : (combined >= 40 ? 'Neutral' : 'Sad')));

        // Apply mood selection in UI
        try {
          if (moodToSet && moodToSet !== lastDetectedMood) {
            lastDetectedMood = moodToSet;
            console.log('Auto-detected mood applied:', moodToSet);
            handleMoodSelection(moodToSet);
          }
        } catch(e) { console.error('Error applying detected mood to UI', e); }

        // Persist mood if it changed or it's been a while (throttle saves)
        try {
          const now = Date.now();
          if (moodToSet && (moodToSet !== lastSavedDetectedMood || (now - lastDetectionSavedAt) > 30000)) {
            // prepare payload
            const payload = {
              date: currentDate,
              face_emotion: lastFace.name || moodToSet,
              face_confidence: Math.round((lastFace.score || 0) * 100),
              audio_emotion: lastAudio.name || moodToSet,
              audio_score: lastAudio.score || 0,
              combined_score: combined,
              meta: {
                selected_mood: moodToSet,
                camera_enabled: deviceState.camera,
                mic_enabled: deviceState.mic,
                diary_id: lastDiaryId || null,
                saved_at: new Date().toISOString()
              }
            };
            // send to save_mood.php
            (async () => {
              try {
                const res = await fetch('api/save_mood.php', {
                  method: 'POST',
                  headers: { 'Content-Type': 'application/json' },
                  body: JSON.stringify(payload)
                });
                const j = await res.json();
                if (j.ok) {
                  lastDetectionSavedAt = Date.now();
                  lastSavedDetectedMood = moodToSet;
                  // notify other views
                  try {
                    const latest = await fetch(`api/get_daily_log.php?date=${currentDate}`);
                    const data = latest.ok ? await latest.json() : {};
                    window.dispatchEvent(new CustomEvent('dayUpdated', { detail: { date: currentDate, data } }));
                    try { localStorage.setItem('dayUpdated', JSON.stringify({ date: currentDate, ts: Date.now() })); } catch(e) {}
                  } catch(e) { console.error('Error notifying after detection save', e); }
                }
              } catch(e) { console.error('Error auto-saving detected mood:', e); }
            })();
          }
        } catch(e) { console.error('Error during auto-save logic', e); }
      }
    }

    function animateScoreTo(){}

    // Stop streams on unload
    window.addEventListener('beforeunload', () => { stopCamera(); stopMic(); });

    // Load models and initial data
    (async () => { await loadModels(); loadTodayData(); })();
  </script>

  <script>
    async function updateAuthSection() {
      try {
        const response = await fetch('./api/auth_status.php');
        const data = await response.json();
        const userInfo = document.getElementById('userInfo');
        const authPrompt = document.getElementById('authPrompt');
        
        if (data.logged_in && data.user) {
          document.getElementById('username').textContent = data.user.username;
          userInfo.style.display = 'flex';
          authPrompt.style.display = 'none';
        } else {
          userInfo.style.display = 'none';
          authPrompt.style.display = 'block';
        }
      } catch (error) {
        console.error('Error checking auth status:', error);
      }
    }

    function logout() {
      fetch('./api/logout.php', { method: 'POST' })
        .then(() => {
          window.location.href = './login.php';
        })
        .catch(error => console.error('Error logging out:', error));
    }

    updateAuthSection();
  </script>
</body>
</html>


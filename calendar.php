<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header('Location: /login.php');
  exit;
}
// Standalone calendar view for the mood tracker UI
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Mood Calendar</title>
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
      --good:#fef7d1;
      --ok:#fef2d8;
      --low:#e8f1fd;
      --badge:#e8efe5;
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

    .welcome{margin-top:auto;background:#f5f5f5;border:1px solid #e0e0e0;border-radius:12px;padding:14px;display:flex;flex-direction:column;gap:10px;font-size:13px;color:#1f2a1f;box-shadow:0 1px 3px rgba(0,0,0,0.05)}
    /* Main */
    .main{margin-left:250px;flex:1;padding:26px 34px;min-height:100vh}
    .header-title{font-size:30px;font-weight:700;margin-bottom:6px;color:#1f2a1f}
    .header-sub{color:#6b7280;font-size:15px}
    .page-head{margin-bottom:22px;}
    .page-title{font-size:30px;font-weight:700;letter-spacing:-0.02em;margin-bottom:6px;color:#1f2a1f;}
    .page-sub{color:var(--muted);font-size:15px;}

    .layout{display:grid;grid-template-columns:2.1fr 1fr;gap:24px;align-items:start;}
    .card{background:var(--card);border-radius:18px;box-shadow:0 6px 18px rgba(0,0,0,0.06);border:1px solid var(--border);}

    /* Calendar card */
    .calendar-card{padding:18px;}
    .calendar-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;}
    .calendar-title{font-size:18px;font-weight:700;color:#2d332e;}
    .chevrons{display:flex;gap:10px;}
    .chevron{width:34px;height:34px;border-radius:10px;border:1px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:16px;background:#f6f8f4;cursor:pointer;transition:all .2s;}
    .chevron:hover{background:#eef3ea;}

    .dow{display:grid;grid-template-columns:repeat(7,1fr);gap:8px;margin-bottom:8px;color:var(--muted);font-size:13px;font-weight:600;text-align:center;}
    .grid{display:grid;grid-template-columns:repeat(7,1fr);gap:8px;}
    .day{background:#f8f9f7;border:1px solid var(--border);border-radius:14px;min-height:82px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;padding:10px;font-weight:700;color:#2d332e;transition:all .2s;}
    .day.empty{background:transparent;border:none;box-shadow:none;}
    .day:hover:not(.empty){transform:translateY(-2px);box-shadow:0 8px 20px rgba(17,24,39,0.06);}
    .day.good{background:var(--good);}
    .day.ok{background:var(--ok);}
    .day.low{background:var(--low);}
    .mood{font-size:14px;}
    .mood strong{display:block;font-size:26px;}
    .pill{display:flex;align-items:center;gap:4px;font-size:12px;color:#59705c;background:#e8efe5;border-radius:16px;padding:3px 8px;font-weight:600;}
    .legend-dot{width:9px;height:9px;border-radius:50%;}

    /* Right column */
    .stack{display:flex;flex-direction:column;gap:16px;}
    .legend-card{padding:16px;}
    .legend-title{font-weight:700;font-size:16px;margin-bottom:12px;color:#2d332e;}
    .legend-list{display:grid;gap:10px;}
    .legend-item{display:flex;align-items:center;gap:10px;color:#2d332e;font-weight:600;font-size:14px;}
    .legend-icon{width:28px;height:28px;border-radius:8px;background:#f0f2ef;display:flex;align-items:center;justify-content:center;font-size:15px;}

    .quick-card{padding:16px;}
    .quick-title{font-weight:700;font-size:16px;margin-bottom:14px;}
    .quick-actions{display:flex;flex-direction:column;gap:10px;}
    .quick-btn{border:none;border-radius:14px;padding:12px 14px;font-size:14px;font-weight:700;display:flex;align-items:center;gap:10px;cursor:pointer;transition:all .2s;}
    .quick-btn.primary{background:var(--accent);color:#fff;}
    .quick-btn.secondary{background:#e8f4eb;color:#14301c;border:1px solid #d4e4d6;}
    .quick-btn:hover{transform:translateY(-1px);box-shadow:0 6px 14px rgba(17,24,39,0.08);}

    /* Post preview (Facebook-like) */
    .post-preview{border-radius:12px;border:1px solid var(--border);background:#fff;padding:12px;box-shadow:0 6px 18px rgba(0,0,0,0.04);} 
    .post-header{display:flex;gap:10px;align-items:center;margin-bottom:8px}
    .post-avatar{width:40px;height:40px;border-radius:50%;background:var(--accent);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700}
    .post-author{font-weight:700}
    .post-date{font-size:12px;color:var(--muted)}
    .post-body{margin-top:6px;white-space:pre-wrap}
    .post-media{margin-top:10px;border-radius:8px;overflow:hidden}
    .post-media img, .post-media video{width:100%;height:auto;display:block}
    /* UI improvements */
    .toast{position:fixed;bottom:22px;right:22px;background:#2f3e32;color:#fff;padding:12px 14px;border-radius:10px;box-shadow:0 10px 30px rgba(0,0,0,0.18);opacity:0;pointer-events:none;transform:translateY(10px);transition:all .25s ease;z-index:120}
    .toast.show{opacity:1;pointer-events:auto;transform:translateY(0)}
    .toast.hide{opacity:0;transform:translateY(10px)}
    .mood-emoji-btn{transition:all .15s ease}
    .mood-emoji-btn.active{border:3px solid #7c9d85 !important;background:#f0f5f0 !important;transform:translateY(-1px)}
    #modalMoodDisplay{font-size:36px;font-weight:700}
    .modal-sticky-footer{position:sticky;bottom:-18px;background:linear-gradient(180deg,rgba(255,255,255,0) 0%,#fff 40%);padding-top:12px;margin-top:12px;display:flex;gap:10px;justify-content:flex-end}
    .btn-busy{opacity:0.7;cursor:wait}
    .spinner{width:16px;height:16px;border:2px solid rgba(255,255,255,0.6);border-top-color:#fff;border-radius:50%;display:inline-block;animation:spin 0.8s linear infinite;margin-left:6px;vertical-align:middle}
    @keyframes spin{to{transform:rotate(360deg)}}

    @media(max-width:1100px){
      .layout{grid-template-columns:1fr;}
    }
    @media(max-width:768px){
      body{flex-direction:column;}
      .sidebar{position:relative;width:100%;flex-direction:row;flex-wrap:wrap;gap:10px;align-items:center;border-right:none;border-bottom:1px solid var(--border);}
      .nav{flex-direction:row;flex-wrap:wrap;}
      .content{margin-left:0;padding:20px;}
      .page-title{font-size:24px;}
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
      <a class="nav-item" href="home.php">
        <span class="nav-icon">
          <svg class="icon-svg" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M3 9L12 2L21 9V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V9Z" fill="currentColor"/>
            <path d="M9 22V12H15V22" fill="white"/>
          </svg>
        </span>Home
      </a>
      <a class="nav-item active" href="calendar.php">
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
    <div class="page-head">
      <div class="page-title">Mood Calendar</div>
      <div class="page-sub">View your mood history and patterns</div>
    </div>

    <div class="layout">
      <section class="calendar-card card">
        <div class="calendar-head">
          <div class="calendar-title" id="monthLabel">December 2024</div>
          <div class="chevrons">
            <button class="chevron" id="prevBtn">‹</button>
            <button class="chevron" id="nextBtn">›</button>
          </div>
        </div>

        <div class="dow">
          <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
        </div>
        <div class="grid" id="calendarGrid"></div>
      </section>

      <div class="stack">
        <div class="legend-card card">
          <div class="legend-title">Legend</div>
          <div class="legend-list">
  <div class="legend-item">
    <div class="legend-icon">
      <svg class="icon-svg" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M4 19.5C4 20.163 4.26339 20.7989 4.73223 21.2678C5.20107 21.7366 5.83696 22 6.5 22H20V2H6.5C5.83696 2 5.20107 2.26339 4.73223 2.73223C4.26339 3.20107 4 3.83696 4 4.5V19.5Z" fill="currentColor"/>
        <path d="M8 6H16M8 10H16M8 14H12" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
      </svg>
    </div>
    <span>Diary Entry</span>
  </div>
  <div class="legend-item">
    <div class="legend-icon">
      <svg class="icon-svg" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M23 19C23 19.5304 22.7893 20.0391 22.4142 20.4142C22.0391 20.7893 21.5304 21 21 21H3C2.46957 21 1.96086 20.7893 1.58579 20.4142C1.21071 20.0391 1 19.5304 1 19V8C1 7.46957 1.21071 6.96086 1.58579 6.58579C1.96086 6.21071 2.46957 6 3 6H7L9 3H15L17 6H21C21.5304 6 22.0391 6.21071 22.4142 6.58579C22.7893 6.96086 23 7.46957 23 8V19Z" fill="currentColor"/>
        <circle cx="12" cy="13" r="3" fill="white"/>
      </svg>
    </div>
    <span>Photo/Video</span>
  </div>
  <div class="legend-item">
    <div class="legend-icon">
      <svg class="icon-svg" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="12" cy="7" r="4" fill="currentColor"/>
        <path d="M5.5 21C5.5 17.134 8.41015 14 12 14C15.5899 14 18.5 17.134 18.5 21" fill="currentColor"/>
      </svg>
    </div>
    <span>Face Detected</span>
  </div>
  <div class="legend-item">
    <div class="legend-icon">
      <svg class="icon-svg" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M12 2C10.3431 2 9 3.34315 9 5V12C9 13.6569 10.3431 15 12 15C13.6569 15 15 13.6569 15 12V5C15 3.34315 13.6569 2 12 2Z" fill="currentColor"/>
        <path d="M5 10V12C5 15.866 8.13401 19 12 19C15.866 19 19 15.866 19 12V10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        <path d="M12 19V22M8 22H16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
      </svg>
    </div>
    <span>Audio Detected</span>
  </div>
</div>
        </div>

        <div class="quick-card card">
          <div class="quick-title">Quick Actions</div>
          <div class="quick-actions">
              <button id="openToday" class="quick-btn primary"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M4 19.5C4 20.163 4.26339 20.7989 4.73223 21.2678C5.20107 21.7366 5.83696 22 6.5 22H20V2H6.5C5.83696 2 5.20107 2.26339 4.73223 2.73223C4.26339 3.20107 4 3.83696 4 4.5V19.5Z" fill="currentColor"/>
        <path d="M8 6H16M8 10H16M8 14H12" stroke="black" stroke-width="1.5" stroke-linecap="round"/>
      </svg> Open Today's Log</button>
              <button id="openCalm" class="quick-btn secondary"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" fill="currentColor"/>
          </svg> Calming Tools</button>
            </div>
        </div>


      </div>
    </div>
  </main>

  <!-- Diary preview modal (shown when opening a day with existing diary/media) -->
  <div id="diaryModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);align-items:center;justify-content:center;z-index:60">
    <div style="width:820px;max-width:92%;background:#fff;border-radius:12px;padding:18px;box-shadow:0 20px 50px rgba(0,0,0,0.3)">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
        <div style="font-weight:700;font-size:18px" id="modalDate">Date</div>
        <div style="display:flex;gap:8px;align-items:center">
          <span id="modalEditBadge" style="display:none;padding:6px 10px;border-radius:999px;background:#eef3ef;color:#2f3e32;font-weight:700;font-size:12px;border:1px solid #d6e2d9;">Editing</span>
          <button id="openFullEditor" class="quick-btn secondary" style="padding:8px 10px"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M11 4H4C3.46957 4 2.96086 4.21071 2.58579 4.58579C2.21071 4.96086 2 5.46957 2 6V20C2 20.5304 2.21071 21.0391 2.58579 21.4142C2.96086 21.7893 3.46957 22 4 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V13" fill="gray"/>
    <path d="M18.5 2.5C18.8978 2.1021 19.4374 1.87868 20 1.87868C20.5626 1.87868 21.1022 2.1021 21.5 2.5C21.8979 2.89782 22.1213 3.43739 22.1213 4C22.1213 4.56261 21.8979 5.10218 21.5 5.5L12 15L8 16L9 12L18.5 2.5Z" fill="black"/>
  </svg> Open Full Editor</button>
          <button id="closeModal" style="border:none;background:#eee;border-radius:8px;padding:6px 8px;cursor:pointer;width:36px;height:36px;display:flex;align-items:center;justify-content:center">✕</button>
        </div>
      </div>
      <div id="modalPostPreview" class="post-preview" style="display:none;margin-bottom:12px"></div>
      <div style="display:flex;gap:18px">
        <div style="flex:1">
          <div style="font-weight:700;margin-bottom:8px">Mood</div>
          <div id="modalMoodSelector" style="display:none;margin-bottom:12px">
            <div style="display:grid;grid-template-columns:repeat(6,1fr);gap:8px">
                  <button class="mood-emoji-btn" data-mood="Happy" style="padding:10px;border:2px solid #e8efe8;border-radius:12px;background:#fff;cursor:pointer;font-size:26px">😊</button>
                  <button class="mood-emoji-btn" data-mood="Calm" style="padding:10px;border:2px solid #e8efe8;border-radius:12px;background:#fff;cursor:pointer;font-size:26px">🙂</button>
                  <button class="mood-emoji-btn" data-mood="Sad" style="padding:10px;border:2px solid #e8efe8;border-radius:12px;background:#fff;cursor:pointer;font-size:26px">😢</button>
                  <button class="mood-emoji-btn" data-mood="Stressed" style="padding:10px;border:2px solid #e8efe8;border-radius:12px;background:#fff;cursor:pointer;font-size:26px">😰</button>
                  <button class="mood-emoji-btn" data-mood="Neutral" style="padding:10px;border:2px solid #e8efe8;border-radius:12px;background:#fff;cursor:pointer;font-size:26px">😐</button>
                  <button class="mood-emoji-btn" data-mood="Tired" style="padding:10px;border:2px solid #e8efe8;border-radius:12px;background:#fff;cursor:pointer;font-size:26px">😴</button>
            </div>
          </div>
          <div id="modalMoodDisplay" style="margin-bottom:12px;font-size:32px;text-align:center;padding:12px;background:#f6f8f6;border-radius:8px"></div>
          
          <div style="font-weight:700;margin-bottom:8px">Diary</div>
          <div id="modalDiaryWrap">
            <div id="modalDiaryText" style="white-space:pre-wrap;background:#f6f8f6;padding:12px;border-radius:8px;border:1px solid #eef2ec;min-height:120px;color:#1f2a1f"></div>
          </div>
          <div style="display:flex;gap:8px;margin-top:8px">
            <button id="editModalDiary" class="quick-btn secondary" style="padding:8px 10px">Edit</button>
            <button id="saveModalDiary" class="quick-btn primary" style="padding:8px 10px;display:none">Save</button>
          </div>
        </div>
        <div style="width:260px">
          <div style="font-weight:700;margin-bottom:8px">Attachments</div>
          <div id="modalMediaList" style="display:flex;flex-direction:column;gap:8px;max-height:240px;overflow:auto;margin-bottom:8px"></div>
          <input type="file" id="modalFileInput" accept="image/*,video/*" multiple style="display:none">
          <button id="addAttachmentBtn" class="quick-btn secondary" style="padding:8px 10px;width:100%;display:none;font-size:12px">+ Add Attachment</button>
        </div>
      </div>

      <div id="modalStickyFooter" class="modal-sticky-footer" style="display:none">
        <button id="cancelModalEdit" class="quick-btn secondary" style="padding:8px 12px">Cancel</button>
        <button id="saveModalFooter" class="quick-btn primary" style="padding:8px 12px">Save</button>
      </div>
    </div>
  </div>

  <div id="toastContainer" class="toast"></div>

  <script>
    const monthLabel = document.getElementById('monthLabel');
    const grid = document.getElementById('calendarGrid');
    let current = new Date();
    let moodMap = {}; // Will be populated from API

    function formatDate(date){
      // Use local date, not UTC
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, '0');
      const day = String(date.getDate()).padStart(2, '0');
      return `${year}-${month}-${day}`;
    }

    // Map combined score to emoji and color
    function getEmojiFromScore(score) {
      if (score >= 80) return { emoji: '😄', style: 'good' };
      if (score >= 60) return { emoji: '😊', style: 'good' };
      if (score >= 40) return { emoji: '🙂', style: 'ok' };
      if (score >= 20) return { emoji: '😐', style: 'ok' };
      return { emoji: '😢', style: 'low' };
    }

    // Map mood tag to emoji/style
    function getEmojiFromTag(tag) {
      const map = {
        'Happy':   { emoji: '😊', style: 'good' },
        'Calm':    { emoji: '🙂', style: 'ok' },
        'Sad':     { emoji: '😢', style: 'low' },
        'Stressed':{ emoji: '😰', style: 'low' },
        'Neutral': { emoji: '😐', style: 'ok' },
        'Tired':   { emoji: '😴', style: 'ok' }
      };
      return map[tag] || null;
    }

    async function loadMonthMoods() {
      try {
        const year = current.getFullYear();
        const month = String(current.getMonth() + 1).padStart(2, '0');
        const res = await fetch(`api/get_month_moods.php?year=${year}&month=${month}`);
        if (res.status === 401) return;
        const data = await res.json();
        
        moodMap = {};
        if (data.moods && Array.isArray(data.moods)) {
          data.moods.forEach(mood => {
            // Ensure date is in YYYY-MM-DD format
            let dateKey = mood.date;
            if (dateKey && typeof dateKey === 'string') {
              // Normalize date format (remove time if present)
              dateKey = dateKey.split(' ')[0];
            }
            if (!dateKey) return; // Skip if no date
            
            // Prefer selected_mood (from meta) if present, then tags, then score
            let moodInfo = getEmojiFromScore(mood.combined_score || 50);
            if (mood.selected_mood) {
              const selEmoji = getEmojiFromTag(mood.selected_mood);
              if (selEmoji) moodInfo = selEmoji;
            } else if (mood.tags) {
              const tagArr = typeof mood.tags === 'string' 
                ? mood.tags.split(',').map(t => t.trim()).filter(Boolean)
                : Array.isArray(mood.tags) ? mood.tags : [];
              if (tagArr.length > 0) {
                const tagEmoji = getEmojiFromTag(tagArr[0]);
                if (tagEmoji) moodInfo = tagEmoji;
              }
            }
            moodMap[dateKey] = {
              ...moodInfo,
              diary: (mood.has_diary && parseInt(mood.has_diary) > 0) || false,
              face: mood.face_emotion ? true : false,
              audio: mood.audio_emotion ? true : false,
              score: mood.combined_score
            };
          });
        }
        renderCalendar();
      } catch (e) {
        console.error('Error loading month moods:', e);
        renderCalendar();
      }
    }

    function renderCalendar(){
      const year = current.getFullYear();
      const month = current.getMonth();
      const start = new Date(year, month, 1);
      const end = new Date(year, month + 1, 0);
      const firstDay = start.getDay();
      const daysInMonth = end.getDate();

      const monthName = start.toLocaleDateString('en-US',{month:'long',year:'numeric'});
      monthLabel.textContent = monthName;

      grid.innerHTML = '';
      for(let i=0;i<firstDay;i++){
        const empty = document.createElement('div');
        empty.className='day empty';
        grid.appendChild(empty);
      }

      for(let d=1; d<=daysInMonth; d++){
        const cell = document.createElement('div');
        const date = new Date(year, month, d);
        const key = formatDate(date);
        const info = moodMap[key];
        cell.className = 'day' + (info ? ` ${info.style}` : '');
        cell.dataset.date = key;
        
        // Always show mood indicator if info exists, even if emoji is missing
        const hasMoodData = info && (info.emoji || info.score !== undefined);
        cell.innerHTML = `
          <div>${d}</div>
          ${hasMoodData ? `<div class="mood"><strong>${info.emoji || '😊'}</strong></div>
            <div class="pill">
              ${info.diary ? '<span class="legend-dot" style="background:#739d73"></span>' : ''}
              ${info.face ? '<span class="legend-dot" style="background:#f2ac57"></span>' : ''}
              ${info.audio ? '<span class="legend-dot" style="background:#78a3f7"></span>' : ''}
            </div>` : ''}
        `;
        grid.appendChild(cell);
      }
    }

    // Day click: show modal preview if diary exists, otherwise open daily-log for that date
    grid.addEventListener('click', async (e) => {
      const cell = e.target.closest('.day');
      if (!cell || cell.classList.contains('empty')) return;
      const date = cell.dataset.date;
      if (!date) return;
      const today = formatDate(new Date());
      if (date > today) {
        alert('You cannot add or edit logs for future dates.');
        return;
      }

      try {
        const res = await fetch(`api/get_daily_log.php?date=${date}`);
        if (res.status === 401) return;
        const data = await res.json();
        // If there's a diary or media, show modal preview; otherwise open full editor
        if ((data.diary && data.diary.content) || (data.media && data.media.length > 0)) {
          showDiaryModal(date, data);
        } else {
          window.location.href = `daily-log.php?date=${date}`;
        }
      } catch (err) {
        console.error('Error fetching daily log:', err);
        window.location.href = `daily-log.php?date=${date}`;
      }
    });

    document.getElementById('prevBtn').addEventListener('click',()=>{
      current.setMonth(current.getMonth()-1);
      loadMonthMoods();
    });
    document.getElementById('nextBtn').addEventListener('click',()=>{
      current.setMonth(current.getMonth()+1);
      loadMonthMoods();
    });

    // Update today's mood in sidebar
    
    loadMonthMoods();
    
    // Modal helpers
    const diaryModal = document.getElementById('diaryModal');
    const modalDate = document.getElementById('modalDate');
    const modalMediaList = document.getElementById('modalMediaList');
    const closeModal = document.getElementById('closeModal');
    const editModalBtn = document.getElementById('editModalDiary');
    const saveModalBtn = document.getElementById('saveModalDiary');
    const openFullBtn = document.getElementById('openFullEditor');
    const modalEditBadge = document.getElementById('modalEditBadge');
    const modalStickyFooter = document.getElementById('modalStickyFooter');
    const cancelModalEdit = document.getElementById('cancelModalEdit');
    const saveModalFooter = document.getElementById('saveModalFooter');
    const toastContainer = document.getElementById('toastContainer');
    let toastTimeout = null;
    let currentModalDate = null;
    let currentMediaData = [];
    let isEditMode = false;
    let currentMoodTag = null;
    let lastDiaryId = null;
    let isSaving = false;

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

    function showToast(message) {
      if (!toastContainer) return;
      if (toastTimeout) {
        clearTimeout(toastTimeout);
        toastTimeout = null;
      }
      toastContainer.textContent = message;
      toastContainer.classList.add('show');
      toastTimeout = setTimeout(() => {
        toastContainer.classList.add('hide');
        setTimeout(() => {
          toastContainer.classList.remove('show','hide');
        }, 200);
      }, 1600); // slightly faster auto-hide
    }

    closeModal.addEventListener('click', hideDiaryModal);
    diaryModal.addEventListener('click', (ev) => { if (ev.target === diaryModal) hideDiaryModal(); });

    function hideDiaryModal(){ 
      diaryModal.style.display = 'none'; 
      const m = document.getElementById('modalDiaryText'); 
      if (m) m.innerHTML = ''; 
      const mm = document.getElementById('modalMediaList'); 
      if (mm) mm.innerHTML = '';
      currentModalDate = null;
      currentMediaData = [];
      isEditMode = false;
    }
    
    function renderMediaList(mediaData, editMode = false) {
      modalMediaList.innerHTML = '';
      if (mediaData && mediaData.length > 0) {
        mediaData.forEach((m, index) => {
          const mediaItem = document.createElement('div');
          mediaItem.style.position = 'relative';
          mediaItem.style.marginBottom = '8px';
          
          if (m.media_type && (m.media_type.startsWith('image') || m.media_type === 'photo')) {
            const img = document.createElement('img');
            img.src = m.file_path;
            img.style.width = '100%';
            img.style.borderRadius = '8px';
            img.alt = 'attachment';
            mediaItem.appendChild(img);
          } else if (m.media_type && (m.media_type.startsWith('video') || m.media_type === 'video')) {
            const vid = document.createElement('video');
            vid.src = m.file_path;
            vid.controls = true;
            vid.style.width = '100%';
            vid.style.borderRadius = '8px';
            mediaItem.appendChild(vid);
          } else {
            // Fallback: show as link
            const link = document.createElement('a');
            link.href = m.file_path;
            link.textContent = m.file_path.split('/').pop() || 'Attachment';
            link.target = '_blank';
            link.style.display = 'block';
            link.style.padding = '8px';
            link.style.background = '#f6f8f6';
            link.style.borderRadius = '8px';
            link.style.textDecoration = 'none';
            link.style.color = '#1f2a1f';
            mediaItem.appendChild(link);
          }
          
          // Add delete button in edit mode
          if (editMode) {
            const deleteBtn = document.createElement('button');
            deleteBtn.textContent = '✕';
            deleteBtn.type = 'button';
            deleteBtn.style.position = 'absolute';
            deleteBtn.style.top = '4px';
            deleteBtn.style.right = '4px';
            deleteBtn.style.width = '28px';
            deleteBtn.style.height = '28px';
            deleteBtn.style.borderRadius = '50%';
            deleteBtn.style.border = 'none';
            deleteBtn.style.background = '#ff6b6b';
            deleteBtn.style.color = '#fff';
            deleteBtn.style.cursor = 'pointer';
            deleteBtn.style.fontSize = '16px';
            deleteBtn.style.fontWeight = 'bold';
            deleteBtn.style.display = 'flex';
            deleteBtn.style.alignItems = 'center';
            deleteBtn.style.justifyContent = 'center';
            deleteBtn.style.boxShadow = '0 2px 8px rgba(0,0,0,0.2)';
            deleteBtn.onclick = async () => {
              if (!confirm('Delete this attachment?')) return;
              try {
                const res = await fetch('api/delete_media.php', {
                  method: 'POST',
                  headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                  body: `media_id=${m.id}`
                });
                const result = await res.json();
                if (result.ok) {
                  // Remove from currentMediaData
                  currentMediaData = currentMediaData.filter(media => media.id !== m.id);
                  // Re-render media list
                  renderMediaList(currentMediaData, true);
                  // Refresh calendar
                  loadMonthMoods();
                } else {
                  alert('Error deleting attachment: ' + (result.error || 'Unknown error'));
                }
              } catch (err) {
                alert('Error deleting attachment: ' + err.message);
              }
            };
            mediaItem.appendChild(deleteBtn);
          }
          
          modalMediaList.appendChild(mediaItem);
        });
      } else {
        modalMediaList.innerHTML = '<div style="color:var(--muted)">No attachments</div>';
      }
    }

    async function showDiaryModal(date, data){
      currentModalDate = date;
      currentMediaData = data.media || [];
      isEditMode = false;
      lastDiaryId = data.diary?.id || null;
      
      modalDate.textContent = new Date(date).toLocaleDateString('en-US', { weekday:'long', year:'numeric', month:'long', day:'numeric' });
      
      // Load mood tags for this date
      try {
        const moodRes = await fetch(`api/get_daily_log.php?date=${date}`);
        if (moodRes.ok) {
          const moodData = await moodRes.json();
          // Prefer selected_mood in mood.meta, else first tag
          try {
            let mm = null;
            if (moodData.mood && moodData.mood.meta) {
              try { const parsed = JSON.parse(moodData.mood.meta); if (parsed && parsed.selected_mood) mm = parsed.selected_mood; } catch(e) {}
            }
            if (mm) {
              currentMoodTag = normalizeMoodName(mm) || mm;
            } else if (moodData.tags && moodData.tags.length > 0) {
              const firstTag = moodData.tags[0];
              currentMoodTag = (typeof firstTag === 'object' && firstTag.tag_name) ? firstTag.tag_name : firstTag;
              currentMoodTag = normalizeMoodName(currentMoodTag) || currentMoodTag;
            } else {
              currentMoodTag = null;
            }
            const moodEmoji = {
              'Happy': '😊', 'Calm': '🙂', 'Sad': '😢', 
              'Stressed': '😰', 'Neutral': '😐', 'Tired': '😴'
            };
            document.getElementById('modalMoodDisplay').textContent = currentMoodTag ? (moodEmoji[currentMoodTag] || '😊') : '—';
          } catch(e) {
            currentMoodTag = null;
            document.getElementById('modalMoodDisplay').textContent = '—';
          }
        }
      } catch(e) {
        console.error('Error loading mood tags:', e);
        currentMoodTag = null;
        document.getElementById('modalMoodDisplay').textContent = '—';
      }
      
      // diary text
      const mdt = document.getElementById('modalDiaryText');
      if (mdt) {
        if (data.diary && data.diary.content) {
          mdt.textContent = data.diary.content;
          mdt.dataset.diaryId = data.diary.id || '';
        } else {
          mdt.textContent = '(No diary content)';
          mdt.dataset.diaryId = '';
        }
      }

      // hide post-style preview (not using this design)
      const modalPost = document.getElementById('modalPostPreview');
      if (modalPost) {
        modalPost.style.display = 'none';
        modalPost.innerHTML = '';
      }

      // Render media list (not in edit mode initially)
      renderMediaList(currentMediaData, false);
      
      // Hide edit UI initially
      document.getElementById('modalMoodSelector').style.display = 'none';
      document.getElementById('addAttachmentBtn').style.display = 'none';
      document.getElementById('modalMoodDisplay').style.display = 'block';
      if (modalEditBadge) modalEditBadge.style.display = 'none';
      if (modalStickyFooter) modalStickyFooter.style.display = 'none';
      // reset save button states
      saveModalBtn.classList.remove('btn-busy');
      saveModalFooter.classList.remove('btn-busy');
      saveModalBtn.textContent = 'Save';
      saveModalFooter.textContent = 'Save';

      // wire buttons
      editModalBtn.style.display = 'inline-block';
      saveModalBtn.style.display = 'none';
      openFullBtn.onclick = () => { window.location.href = `daily-log.php?date=${date}`; };

      function enterEditMode() {
        isEditMode = true;
        if (modalEditBadge) modalEditBadge.style.display = 'inline-flex';
        if (modalStickyFooter) modalStickyFooter.style.display = 'flex';
        document.getElementById('modalMoodSelector').style.display = 'block';
        document.getElementById('modalMoodDisplay').style.display = 'none';
        // Highlight current mood
        document.querySelectorAll('.mood-emoji-btn').forEach(btn => {
          btn.classList.remove('active');
          if (btn.dataset.mood === currentMoodTag) {
            btn.classList.add('active');
          }
        });
        // Show add attachment button
        document.getElementById('addAttachmentBtn').style.display = 'block';
        
        // turn into editable textarea
        const modalTextEl = document.getElementById('modalDiaryText');
        const txt = modalTextEl ? modalTextEl.textContent : '';
        const ta = document.createElement('textarea');
        ta.id = 'modalDiaryTextarea';
        ta.style.width = '100%';
        ta.style.minHeight = '120px';
        ta.style.padding = '12px';
        ta.style.border = '1px solid #d4e4d6';
        ta.style.borderRadius = '8px';
        ta.style.fontFamily = 'Inter, system-ui, -apple-system, Segoe UI, sans-serif';
        ta.style.fontSize = '14px';
        ta.style.lineHeight = '1.6';
        ta.style.color = '#1f2a1f';
        ta.style.background = '#fff';
        ta.style.boxShadow = '0 1px 3px rgba(0,0,0,0.05)';
        ta.style.transition = 'all 0.2s ease';
        ta.style.resize = 'vertical';
        ta.onmouseover = function() { this.style.borderColor = '#7c9d85'; };
        ta.onmouseout = function() { this.style.borderColor = '#d4e4d6'; };
        ta.onfocus = function() { this.style.borderColor = '#7c9d85'; this.style.boxShadow = '0 2px 8px rgba(124,157,133,0.15)'; };
        ta.onblur = function() { this.style.borderColor = '#d4e4d6'; this.style.boxShadow = '0 1px 3px rgba(0,0,0,0.05)'; };
        ta.value = txt;
        const wrap = document.getElementById('modalDiaryWrap');
        const existing = document.getElementById('modalDiaryText');
        if (existing) wrap.replaceChild(ta, existing);
        saveModalBtn.style.display = 'inline-block';
        editModalBtn.style.display = 'none';
        // Re-render media list with delete buttons
        renderMediaList(currentMediaData, true);
      }

      editModalBtn.onclick = enterEditMode;
      
      // Mood emoji selection
      document.querySelectorAll('.mood-emoji-btn').forEach(btn => {
        btn.addEventListener('click', () => {
          document.querySelectorAll('.mood-emoji-btn').forEach(b => b.classList.remove('active'));
          btn.classList.add('active');
          currentMoodTag = btn.dataset.mood;
        });
      });
      
      // File upload handler
      const fileInput = document.getElementById('modalFileInput');
      const addAttachmentBtn = document.getElementById('addAttachmentBtn');
      
      addAttachmentBtn.addEventListener('click', () => {
        fileInput.click();
      });
      
      fileInput.addEventListener('change', async (e) => {
        const files = Array.from(e.target.files);
        if (files.length === 0) return;
        
        for (const file of files) {
          await uploadMediaToDiary(file, currentModalDate, lastDiaryId);
        }
        
        // Refresh media list
        try {
          const res = await fetch(`api/get_daily_log.php?date=${currentModalDate}`);
          const data = await res.json();
          currentMediaData = data.media || [];
          renderMediaList(currentMediaData, true);
          loadMonthMoods();
        } catch(err) {
          console.error('Error refreshing media:', err);
        }
        
        fileInput.value = '';
      });
      
      async function uploadMediaToDiary(file, date, diaryId) {
        const formData = new FormData();
        formData.append('file', file);
        formData.append('date', date);
        if (diaryId) formData.append('diary_id', diaryId);
        
        try {
          const res = await fetch('api/upload_media.php', {
            method: 'POST',
            body: formData
          });
          const result = await res.json();
          if (result.ok && result.id) {
            lastDiaryId = result.diary_id || diaryId;
            return result;
          } else {
            throw new Error(result.error || 'Upload failed');
          }
        } catch(err) {
          alert('Error uploading file: ' + err.message);
          throw err;
        }
      }

      async function handleSave() {
        const ta = document.getElementById('modalDiaryTextarea');
        if (!ta) return;
        const content = ta.value.trim();
        const diaryEl = document.getElementById('modalDiaryText');
        const diaryId = diaryEl ? (diaryEl.dataset.diaryId || '') : '';
        
        try {
          // Save diary content
          const res = await fetch('api/save_diary.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `content=${encodeURIComponent(content)}&date=${date}`
          });
          const r = await res.json();
          if (!r.ok) throw new Error(r.error || 'Save failed');
          
          lastDiaryId = r.id || diaryId;
          
          // Save mood tag if selected
          if (currentMoodTag) {
            try {
              const moodRes = await fetch('api/save_mood_tags.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `date=${date}&tags[]=${encodeURIComponent(currentMoodTag)}`
              });
              const moodResult = await moodRes.json();
              if (!moodResult.ok) {
                console.error('Error saving mood tag:', moodResult.error);
              }
            } catch(e) {
              console.error('Error saving mood tag:', e);
            }
          }
          
          // Reload mood tags from API to ensure we have the latest data
          let savedMoodTag = currentMoodTag;
          try {
            const moodRes = await fetch(`api/get_daily_log.php?date=${date}`);
            if (moodRes.ok) {
              const moodData = await moodRes.json();
              if (moodData.tags && moodData.tags.length > 0) {
                savedMoodTag = moodData.tags[0]; // tags[0] is already the tag_name string
                savedMoodTag = normalizeMoodName(savedMoodTag) || savedMoodTag;
                currentMoodTag = savedMoodTag;
              } else if (currentMoodTag) {
                // Keep the current selection if API doesn't return tags yet
                savedMoodTag = currentMoodTag;
              } else {
                savedMoodTag = null;
              }
            }
          } catch(e) {
            console.error('Error reloading mood tags:', e);
            // Keep current selection if reload fails
            savedMoodTag = currentMoodTag;
          }
          
          // Update mood display with latest data
          const moodEmoji = {
            'Happy': '😊', 'Calm': '🙂', 'Sad': '😢', 
            'Stressed': '😰', 'Neutral': '😐', 'Tired': '😴'
          };
          const moodDisplayEl = document.getElementById('modalMoodDisplay');
          if (moodDisplayEl) {
            moodDisplayEl.textContent = savedMoodTag ? (moodEmoji[savedMoodTag] || '😊') : '—';
          }

          // Persist a canonical mood record so other views (daily-log, home) see the update
          try {
            const moodPayload = {
              date: date,
              face_emotion: savedMoodTag || null,
              face_confidence: 0,
              audio_emotion: savedMoodTag || null,
              audio_score: 0,
              combined_score: 75,
              meta: {
                diary_id: lastDiaryId || r.id || '',
                selected_mood: savedMoodTag || null,
                saved_at: new Date().toISOString()
              }
            };
            const saveMoodRes = await fetch('api/save_mood.php', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify(moodPayload)
            });
            const saveMoodData = await saveMoodRes.json();
            if (!saveMoodData.ok) console.warn('save_mood warning:', saveMoodData.error);
          } catch (e) {
            console.error('Error saving mood record:', e);
          }

          // Notify other open views so they can refresh their data
          try {
            const latestRes = await fetch(`api/get_daily_log.php?date=${date}`);
            const latestData = latestRes.ok ? await latestRes.json() : {};
            window.dispatchEvent(new CustomEvent('dayUpdated', { detail: { date: date, data: latestData } }));
            // also set localStorage to notify other tabs
            try { localStorage.setItem('dayUpdated', JSON.stringify({ date: date, ts: Date.now() })); } catch (e) { /* ignore */ }
          } catch (e) {
            console.error('Error dispatching dayUpdated event:', e);
          }
          
          // Also refresh the calendar to show updated mood emoji
          loadMonthMoods();
          
          // replace textarea with updated div
          const newDiv = document.createElement('div');
          newDiv.id = 'modalDiaryText';
          newDiv.style.whiteSpace = 'pre-wrap';
          newDiv.style.background = '#f6f8f6';
          newDiv.style.padding = '12px';
          newDiv.style.borderRadius = '8px';
          newDiv.style.border = '1px solid #eef2ec';
          newDiv.textContent = content;
          newDiv.dataset.diaryId = r.id || '';
          const wrap = document.getElementById('modalDiaryWrap');
          wrap.replaceChild(newDiv, document.getElementById('modalDiaryTextarea'));
          
          // Hide edit mode UI
          saveModalBtn.style.display = 'none';
          editModalBtn.style.display = 'inline-block';
          isEditMode = false;
          document.getElementById('modalMoodSelector').style.display = 'none';
          document.getElementById('modalMoodDisplay').style.display = 'block';
          document.getElementById('addAttachmentBtn').style.display = 'none';
          if (modalEditBadge) modalEditBadge.style.display = 'none';
          if (modalStickyFooter) modalStickyFooter.style.display = 'none';
          
          // Re-render media list without delete buttons
          renderMediaList(currentMediaData, false);
          // refresh month moods (in case diary indicator changed)
          // Use setTimeout to ensure API has processed the save
          setTimeout(() => {
            loadMonthMoods();
          }, 300);
          showToast('Saved changes');
        } catch (err) {
          alert('Error saving diary: ' + err.message);
        }
      }

      saveModalBtn.onclick = handleSave;
      saveModalFooter.onclick = handleSave;

      // Cancel edit handler
      cancelModalEdit.onclick = async () => {
        if (!currentModalDate) return;
        try {
          const res = await fetch(`api/get_daily_log.php?date=${currentModalDate}`);
          const data = await res.json();
          await showDiaryModal(currentModalDate, data);
        } catch (err) {
          console.error('Error cancelling edit:', err);
          hideDiaryModal();
        }
      };

      diaryModal.style.display = 'flex';
    }
    // Wire quick action buttons
    document.getElementById('openToday')?.addEventListener('click', () => {
      const today = formatDate(new Date());
      window.location.href = `daily-log.php?date=${today}`;
    });
    document.getElementById('openCalm')?.addEventListener('click', () => {
      window.location.href = 'calming-tools.php';
    });

    // Respond to cross-tab day updates
    window.addEventListener('storage', async (ev) => {
      try {
        if (ev.key !== 'dayUpdated') return;
        const payload = ev.newValue ? JSON.parse(ev.newValue) : null;
        if (!payload || !payload.date) return;
        const d = payload.date;
        loadMonthMoods();
        if (currentModalDate && currentModalDate === d) {
          try {
            const res = await fetch(`api/get_daily_log.php?date=${currentModalDate}`);
            const data = res.ok ? await res.json() : null;
            if (data) await showDiaryModal(currentModalDate, data);
          } catch (e) { console.error('Error refreshing modal after storage dayUpdated', e); }
        }
      } catch (e) { console.error('storage handler error', e); }
    });

    // Respond to same-tab updates via CustomEvent('dayUpdated')
    window.addEventListener('dayUpdated', async (ev) => {
      try {
        const d = ev.detail && ev.detail.date ? ev.detail.date : null;
        if (!d) return;
        console.log('calendar: received dayUpdated for', d);
        loadMonthMoods();
        if (currentModalDate && currentModalDate === d) {
          try {
            const res = await fetch(`api/get_daily_log.php?date=${currentModalDate}`);
            const data = res.ok ? await res.json() : null;
            if (data) await showDiaryModal(currentModalDate, data);
          } catch (e) { console.error('Error refreshing modal after dayUpdated', e); }
        }
      } catch (e) { console.error('dayUpdated handler error (calendar)', e); }
    });
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


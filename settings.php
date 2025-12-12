<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header('Location: /login.php');
  exit;
}

require_once __DIR__ . '/config/db.php';
$username = 'User';
try {
  $pdo = getPDO();
  $stmt = $pdo->prepare('SELECT username FROM users WHERE id = :id LIMIT 1');
  $stmt->execute([':id' => $_SESSION['user_id']]);
  $row = $stmt->fetch();
  if ($row) {
    $username = $row['username'];
  }
} catch (Exception $e) {
  // Use default
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Settings</title>
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


    *{box-sizing:border-box;margin:0;padding:0;}
    body{font-family:Inter,system-ui,-apple-system,Segoe UI,sans-serif;background:var(--bg);color:#1f2a1f;display:flex;min-height:100vh;}

    /* Sidebar (unified) */
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

    .grid-2{display:grid;grid-template-columns:repeat(auto-fit,minmax(440px,1fr));gap:16px;margin-top:12px;}
    .card{background:var(--card);border-radius:18px;box-shadow:0 6px 18px rgba(0,0,0,0.06);border:1px solid var(--border);padding:14px 16px;}
    .card-title{font-weight:700;font-size:16px;margin-bottom:10px;color:#1f2a1f;}

    .row{display:flex;align-items:center;justify-content:space-between;padding:12px;border-radius:14px;border:1px solid var(--border);background:#f9fbf9;margin-bottom:8px;gap:12px;}
    .row .left{display:flex;align-items:center;gap:10px;}
    .icon-pill{width:34px;height:34px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:16px;background:#eef3ef;color:#4c6b50;}
    .label-strong{font-weight:700;color:#1f2a1f;}
    .label-sub{font-size:13px;color:var(--muted);}

    .toggle{width:46px;height:24px;border-radius:24px;background:#dfe6e1;position:relative;cursor:pointer;flex-shrink:0;}
    .toggle::after{content:'';position:absolute;top:3px;left:3px;width:18px;height:18px;border-radius:50%;background:#fff;transition:all .2s ease;}
    .toggle.on{background:var(--accent);}
    .toggle.on::after{left:25px;}

    .theme-tabs{display:flex;gap:12px;margin-top:10px;}
    .theme-pill{flex:1;border:1px solid var(--border);border-radius:12px;padding:12px;display:flex;flex-direction:column;gap:6px;align-items:center;cursor:pointer;background:#fff;transition:all .2s;}
    .theme-pill.active{background:var(--accent);color:#fff;box-shadow:0 8px 18px rgba(0,0,0,0.1);border-color:transparent;}
    .theme-pill .big{font-size:20px;}
    .theme-pill .tlabel{font-weight:700;}

    .about-row{display:flex;align-items:center;gap:10px;padding:12px;border-radius:14px;border:1px solid var(--border);background:#f9fbf9;margin-top:8px;}

    .privacy{margin-top:14px;border-radius:18px;border:1px solid var(--border);background:#f3f8f3;padding:14px;}
    .privacy strong{display:block;margin-bottom:6px;color:#1f2a1f;}
    .privacy p{color:#4b5563;font-size:14px;line-height:1.6;}

    @media(max-width:1024px){
      .content{margin-left:0;padding:20px;}
      .sidebar{position:relative;width:100%;flex-direction:row;flex-wrap:wrap;gap:10px;align-items:center;border-right:none;border-bottom:1px solid var(--border);}
      .nav{flex-direction:row;flex-wrap:wrap;}
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
      <a class="nav-item active" href="settings.php">
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
      <div class="page-title">Settings</div>
      <div class="page-sub">Manage your preferences and permissions</div>
    </div>

    <div class="grid-2">
      <div class="card">
        <div class="card-title">Permissions</div>
        <div class="row">
          <div class="left">
            <div class="icon-pill">📷</div>
            <div>
              <div class="label-strong">Camera Access</div>
              <div class="label-sub">Enable facial emotion detection</div>
            </div>
          </div>
          <div class="toggle on"></div>
        </div>
        <div class="row">
          <div class="left">
            <div class="icon-pill">🎙️</div>
            <div>
              <div class="label-strong">Microphone Access</div>
              <div class="label-sub">Enable audio mood analysis</div>
            </div>
          </div>
          <div class="toggle on"></div>
        </div>
      </div>

      <div class="card">
        <div class="card-title">Data Management</div>
        <div class="row">
          <div class="left">
            <div class="icon-pill" style="background:#e9f3ff;color:#2d6cb4;">🗂️</div>
            <div>
              <div class="label-strong">Local Data Backup</div>
              <div class="label-sub">Export your mood data</div>
            </div>
          </div>
          <div style="font-size:16px;color:#9ca3af;">›</div>
        </div>
        <div class="row">
          <div class="left">
            <div class="icon-pill" style="background:#e9f5ee;color:#2c8155;">⬇️</div>
            <div>
              <div class="label-strong">Download Data</div>
              <div class="label-sub">Get a copy of all your entries</div>
            </div>
          </div>
          <div style="font-size:16px;color:#9ca3af;">›</div>
        </div>
      </div>
    </div>

    <div class="grid-2">
      <div class="card">
        <div class="card-title">About</div>
        <div class="about-row">
          <div class="icon-pill" style="background:#f2f0ff;color:#6b5ca5;">ℹ️</div>
          <div>
            <div class="label-strong">About Mood Tracker</div>
            <div class="label-sub">Version 1.0.0</div>
          </div>
          <div style="margin-left:auto;font-size:16px;color:#9ca3af;">›</div>
        </div>
      </div>
    </div>

    <div class="privacy">
      <strong>Privacy First</strong>
      <p>All your mood data is stored locally on your device. Camera and microphone data is processed in real-time and never saved or shared. You have full control over your information.</p>
    </div>
  </main>

  <script>
    // Permission toggles
    const permissionToggles = {
      camera: document.querySelectorAll('.toggle')[0],
      microphone: document.querySelectorAll('.toggle')[1]
    };

    // Load settings from localStorage
    function loadSettings() {
      // Permissions
      const cameraEnabled = localStorage.getItem('cameraEnabled') !== 'false';
      const micEnabled = localStorage.getItem('micEnabled') !== 'false';

      updateToggle(permissionToggles.camera, cameraEnabled);
      updateToggle(permissionToggles.microphone, micEnabled);
    }

    function updateToggle(toggle, isOn) {
      if (isOn) {
        toggle.classList.add('on');
      } else {
        toggle.classList.remove('on');
      }
    }

    // Permission toggle handlers
    permissionToggles.camera?.addEventListener('click', function() {
      const isOn = !this.classList.contains('on');
      updateToggle(this, isOn);
      localStorage.setItem('cameraEnabled', isOn);
    });

    permissionToggles.microphone?.addEventListener('click', function() {
      const isOn = !this.classList.contains('on');
      updateToggle(this, isOn);
      localStorage.setItem('micEnabled', isOn);
    });

    // Data export - Local Data Backup
    const dataManagementCard = Array.from(document.querySelectorAll('.card')).find(card => 
      card.querySelector('.card-title')?.textContent === 'Data Management'
    );
    if (dataManagementCard) {
      const backupRow = dataManagementCard.querySelectorAll('.row')[0];
      if (backupRow) {
        backupRow.addEventListener('click', async function() {
          try {
            const res = await fetch('api/get_month_moods.php?year=' + new Date().getFullYear() + '&month=' + (new Date().getMonth() + 1));
            const data = await res.json();
            const csv = 'Date,Mood Score,Face Emotion,Audio Emotion\n' + 
              data.moods.map(m => `${m.date},${m.combined_score},${m.face_emotion || '—'},${m.audio_emotion || '—'}`).join('\n');
            
            // Show in modal instead of downloading
            const modal = document.createElement('div');
            modal.style.cssText = 'position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);display:flex;align-items:center;justify-content:center;z-index:9999';
            modal.innerHTML = `
              <div style="background:#fff;border-radius:16px;padding:24px;max-width:600px;width:90%;max-height:70vh;overflow-y:auto;">
                <h3 style="margin-bottom:16px;font-size:18px;font-weight:700;">Mood Data (CSV)</h3>
                <textarea readonly style="width:100%;height:300px;padding:12px;border:1px solid #ddd;border-radius:8px;font-family:monospace;font-size:12px;">${csv}</textarea>
                <div style="margin-top:16px;display:flex;gap:10px;">
                  <button style="flex:1;padding:10px;background:#7c9d85;color:#fff;border:none;border-radius:8px;cursor:pointer;font-weight:600;">Close</button>
                  <button style="flex:1;padding:10px;background:#6a8b74;color:#fff;border:none;border-radius:8px;cursor:pointer;font-weight:600;">Copy to Clipboard</button>
                </div>
              </div>
            `;
            document.body.appendChild(modal);
            
            // Add event listeners after modal is added
            const textarea = modal.querySelector('textarea');
            modal.querySelectorAll('button')[0].addEventListener('click', () => modal.remove());
            modal.querySelectorAll('button')[1].addEventListener('click', () => {
              navigator.clipboard.writeText(textarea.value).then(() => alert('Copied to clipboard!'));
            });
          } catch (e) {
            alert('Could not export data');
          }
        });
      }
      
      // Download data
      const downloadRow = dataManagementCard.querySelectorAll('.row')[1];
      if (downloadRow) {
        downloadRow.addEventListener('click', async function() {
          try {
            const res = await fetch('api/get_month_moods.php?year=' + new Date().getFullYear() + '&month=' + (new Date().getMonth() + 1));
            const data = await res.json();
            const json = JSON.stringify(data, null, 2);
            
            // Show in modal instead of downloading
            const modal = document.createElement('div');
            modal.style.cssText = 'position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);display:flex;align-items:center;justify-content:center;z-index:9999';
            modal.innerHTML = `
              <div style="background:#fff;border-radius:16px;padding:24px;max-width:600px;width:90%;max-height:70vh;overflow-y:auto;">
                <h3 style="margin-bottom:16px;font-size:18px;font-weight:700;">Mood Data (JSON)</h3>
                <textarea readonly style="width:100%;height:300px;padding:12px;border:1px solid #ddd;border-radius:8px;font-family:monospace;font-size:12px;">${json}</textarea>
                <div style="margin-top:16px;display:flex;gap:10px;">
                  <button style="flex:1;padding:10px;background:#7c9d85;color:#fff;border:none;border-radius:8px;cursor:pointer;font-weight:600;">Close</button>
                  <button style="flex:1;padding:10px;background:#6a8b74;color:#fff;border:none;border-radius:8px;cursor:pointer;font-weight:600;">Copy to Clipboard</button>
                </div>
              </div>
            `;
            document.body.appendChild(modal);
            
            // Add event listeners after modal is added
            const textarea = modal.querySelector('textarea');
            modal.querySelectorAll('button')[0].addEventListener('click', () => modal.remove());
            modal.querySelectorAll('button')[1].addEventListener('click', () => {
              navigator.clipboard.writeText(textarea.value).then(() => alert('Copied to clipboard!'));
            });
          } catch (e) {
            alert('Could not download data');
          }
        });
      }
    }

    async function updateAuthSection() {
      try {
        const response = await fetch('./api/auth_status.php', { credentials: 'include' });
        const data = await response.json();
        const userInfo = document.getElementById('userInfo');
        const authPrompt = document.getElementById('authPrompt');
        
        if (data.logged_in && data.user) {
          document.getElementById('username').textContent = data.user.username;
          userInfo.style.display = 'flex';
          authPrompt.style.display = 'none';
        } else {
          userInfo.style.display = 'none';
          authPrompt.style.display = 'flex';
        }
      } catch (error) {
        console.error('Error checking auth status:', error);
        // Fallback: if you can see settings page, you're logged in
        document.getElementById('userInfo').style.display = 'flex';
        document.getElementById('authPrompt').style.display = 'none';
      }
    }

    // Apply settings on page load
    loadSettings();
    updateAuthSection();
  </script>
</body>
</html>


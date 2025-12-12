<?php
session_start();
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
  <title>Emotion Flashcards</title>
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

    .cards-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;}
    .flash-card{background:var(--card);border-radius:18px;box-shadow:0 6px 18px rgba(0,0,0,0.06);border:1px solid var(--border);padding:16px;display:flex;flex-direction:column;gap:10px;cursor:pointer;transition:all .2s;}
    .flash-card:hover{transform:translateY(-2px);box-shadow:0 10px 22px rgba(0,0,0,0.08);}
    .fc-icon{width:56px;height:56px;border-radius:14px;background:#f8fbf8;display:flex;align-items:center;justify-content:center;font-size:28px;margin:0 auto;}
    .fc-title{text-align:center;font-weight:700;font-size:16px;color:#1f2a1f;}
    .fc-sub{text-align:center;color:#6b7280;font-size:13px;}

    /* Modal */
    .overlay{position:fixed;inset:0;background:rgba(26,37,31,0.45);backdrop-filter:blur(2px);opacity:0;pointer-events:none;transition:opacity .2s ease;z-index:90;}
    .overlay.show{opacity:1;pointer-events:auto;}
    .modal{position:fixed;inset:0;display:flex;align-items:center;justify-content:center;opacity:0;pointer-events:none;transition:opacity .2s ease;z-index:91;}
    .modal.show{opacity:1;pointer-events:auto;}
    .dialog{background:#fff;border-radius:18px;box-shadow:0 24px 60px rgba(0,0,0,0.18);max-width:820px;width:92%;max-height:90vh;overflow:auto;padding:22px;position:relative;}
    .modal-close{position:absolute;top:12px;right:12px;background:#f2f4f3;border:none;border-radius:999px;width:36px;height:36px;display:flex;align-items:center;justify-content:center;font-size:18px;color:#6b7280;cursor:pointer;}
    .modal-title{font-size:24px;font-weight:800;text-align:center;margin-top:8px;color:#1f2a1f;}
    .modal-sub{font-size:14px;color:#6b7280;text-align:center;margin-bottom:16px;}
    .section-title{font-weight:800;font-size:16px;margin:18px 0 8px;color:#1f2a1f;}
    .list{padding-left:18px;color:#374151;line-height:1.7;font-size:14px;}
    .chip{display:inline-flex;align-items:center;gap:8px;padding:10px 12px;border-radius:12px;border:1px solid #e8efe8;background:#f8fbf8;font-weight:700;font-size:14px;color:#1f2a1f;margin-bottom:10px;}
    .step{display:flex;align-items:center;gap:10px;background:#fffbf2;border:1px solid #f5e7c2;border-radius:14px;padding:10px 12px;font-size:14px;color:#374151;margin-bottom:10px;}
    .step .num{width:26px;height:26px;border-radius:50%;background:#f7d774;color:#5c4a06;font-weight:800;display:flex;align-items:center;justify-content:center;font-size:13px;}

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
      <a class="nav-item active" href="flashcard.php">
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
      <div class="page-title">Emotion Flashcards</div>
      <div class="page-sub">Learn about different emotions and how to manage them</div>
    </div>

    <div class="cards-grid" id="cards"></div>
  </main>

  <div class="overlay" id="overlay"></div>
  <div class="modal" id="modal">
    <div class="dialog">
      <button class="modal-close" id="closeModal">×</button>
      <div class="fc-icon" id="modalIcon" style="margin-top:8px;"></div>
      <div class="modal-title" id="modalTitle"></div>
      <div class="modal-sub" id="modalSub"></div>

      <div class="section-title">About This Emotion</div>
      <div class="modal-sub" id="modalAbout" style="text-align:left;margin-bottom:12px;"></div>

      <div class="section-title">Common Triggers</div>
      <ul class="list" id="modalTriggers"></ul>

      <div class="section-title">How to Manage</div>
      <div id="modalManage"></div>
    </div>
  </div>

  <script>
    const overlay = document.getElementById('overlay');
    const modal = document.getElementById('modal');
    const closeModalBtn = document.getElementById('closeModal');
    const cardsContainer = document.getElementById('cards');
    const data = [
      { title:'Happy', icon:'😊', sub:'Feeling joyful and content', about:'Happiness is a positive emotional state characterized by feelings of joy, satisfaction, contentment, and fulfillment.', triggers:['Spending time with loved ones','Achieving goals','Positive surprises','Beautiful weather'], manage:['Savor the moment','Share your joy with others','Journal about what made you happy','Take photos to remember']},
      { title:'Sad', icon:'😢', sub:'Feeling down or unhappy', about:'Sadness is a natural response to loss, disappointment, or hardship. It can help you process and reflect.', triggers:['Loss or grief','Feeling lonely','Unmet expectations','Difficult news'], manage:['Talk to someone you trust','Allow yourself to feel it','Do something small that comforts you','Get fresh air or a short walk']},
      { title:'Stressed', icon:'😰', sub:'Feeling overwhelmed or pressured', about:'Stress is the body’s response to pressure. Short-term stress can motivate, but prolonged stress can be draining.', triggers:['Workload or deadlines','Too many commitments','Uncertainty about outcomes','Lack of rest'], manage:['Take short breaks','Prioritize one task at a time','Practice deep breathing','Set gentle boundaries']},
      { title:'Calm', icon:'😌', sub:'Feeling peaceful and relaxed', about:'Calm is a state of mental and emotional ease. It supports clear thinking and recovery.', triggers:['Quiet environments','Mindful breathing','Nature time','Finishing important tasks'], manage:['Maintain simple routines','Protect quiet time','Gentle stretches','Light background sounds']},
      { title:'Anxious', icon:'😟', sub:'Feeling worried or nervous', about:'Anxiety is a feeling of fear or unease. It can appear when anticipating uncertain outcomes.', triggers:['Upcoming events','Social situations','Health concerns','Past difficult experiences'], manage:['Name what you feel','5-4-3-2-1 grounding','Slow belly breaths','Break tasks into steps']},
      { title:'Angry', icon:'😠', sub:'Feeling frustrated or mad', about:'Anger signals that boundaries or needs may be violated. It can motivate change when managed safely.', triggers:['Feeling disrespected','Unfair situations','Feeling ignored','Unexpected obstacles'], manage:['Pause and breathe out longer','Take space before responding','Move your body (walk/stretch)','Write what you’d like to say first']},
      { title:'Excited', icon:'🤩', sub:'Feeling energized and enthusiastic', about:'Excitement is high energy toward something positive. It fuels motivation and creativity.', triggers:['Anticipating good events','Achievements','New experiences','Supportive feedback'], manage:['Channel energy into planning','Share the news with a friend','Capture ideas quickly','Celebrate small wins']},
      { title:'Tired', icon:'😴', sub:'Feeling exhausted or drained', about:'Tiredness is a cue that the body or mind needs rest and replenishment.', triggers:['Poor sleep','Overworking','Long screen time','Emotional load'], manage:['Prioritize sleep tonight','Short movement break','Hydrate and light snack','Step away from screens briefly']},
      { title:'Grateful', icon:'🙏', sub:'Feeling thankful and appreciative', about:'Gratitude is recognizing the good in your life, which can boost mood and resilience.', triggers:['Acts of kindness','Support from others','Moments of beauty','Personal progress'], manage:['List 3 things you’re grateful for','Thank someone directly','Notice small comforts','Capture a gratitude photo']},
      { title:'Confused', icon:'🤔', sub:'Feeling uncertain or unclear', about:'Confusion happens when information or feelings are unclear. It invites curiosity and clarification.', triggers:['Mixed messages','New situations','Too much info at once','Conflicting priorities'], manage:['Write questions you have','Ask for clarification','Break info into chunks','Give yourself time to think']},
      { title:'Proud', icon:'😎', sub:'Feeling accomplished and confident', about:'Pride is satisfaction from achievements or values lived out. It can reinforce positive behaviors.', triggers:['Finishing a challenge','Recognition from others','Sticking to values','Helping someone'], manage:['Share your progress','Note what worked well','Set a next gentle goal','Celebrate with a small treat']},
      { title:'Lonely', icon:'😔', sub:'Feeling isolated or disconnected', about:'Loneliness is the gap between desired and actual connection. It can motivate reaching out.', triggers:['Lack of social time','Transitions or moves','Feeling misunderstood','Being physically alone'], manage:['Message or call one person','Join a shared-interest space','Go where people are (cafe/park)','Be kind to yourself today']}
    ];

    function openModal(card){
      overlay.classList.add('show');
      modal.classList.add('show');
      document.getElementById('modalIcon').textContent = card.icon;
      document.getElementById('modalTitle').textContent = card.title;
      document.getElementById('modalSub').textContent = card.sub;
      document.getElementById('modalAbout').textContent = card.about;
      const trigEl = document.getElementById('modalTriggers');
      trigEl.innerHTML = card.triggers.map(t=>`<li>${t}</li>`).join('');
      const manageEl = document.getElementById('modalManage');
      manageEl.innerHTML = card.manage.map((m,i)=>`<div class="step"><div class="num">${i+1}</div><div>${m}</div></div>`).join('');
    }
    function closeModal(){
      overlay.classList.remove('show');
      modal.classList.remove('show');
    }
    overlay.addEventListener('click',closeModal);
    closeModalBtn.addEventListener('click',closeModal);

    // render cards
    data.forEach(card=>{
      const el = document.createElement('div');
      el.className='flash-card';
      el.innerHTML = `
        <div class="fc-icon">${card.icon}</div>
        <div class="fc-title">${card.title}</div>
        <div class="fc-sub">${card.sub}</div>
      `;
      el.addEventListener('click',()=>openModal(card));
      cardsContainer.appendChild(el);
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


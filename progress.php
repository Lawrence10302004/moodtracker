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
  <title>Mood Progress</title>
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
      --green:#739d73;
      --yellow:#f4cd59;
      --orange:#ec8f6b;
      --blue:#7aa3c8;
      --purple:#a08bc8;
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

    .cards-row{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:14px;margin-top:14px;}
    .stat-card{border-radius:18px;padding:16px;color:#fff;box-shadow:0 6px 18px rgba(0,0,0,0.08);}
    .stat-title{font-size:14px;font-weight:700;opacity:0.9;margin-bottom:6px;}
    .stat-value{font-size:36px;font-weight:800;line-height:1;}
    .stat-sub{font-size:13px;margin-top:6px;opacity:0.9;}

    .grid-2{display:grid;grid-template-columns:1.15fr 1fr;gap:18px;margin-top:18px;}
    .card{background:var(--card);border-radius:18px;box-shadow:0 6px 18px rgba(0,0,0,0.06);border:1px solid var(--border);padding:16px;}
    .card-title{font-weight:700;font-size:16px;margin-bottom:10px;color:#1f2a1f;}
    .chart-placeholder{height:260px;border-radius:14px;background:#f9fbf9;border:1px dashed #dce6de;display:flex;align-items:center;justify-content:center;color:#6b7280;font-size:14px;}

    canvas{border-radius:14px;}
    .chart-container{position:relative;height:260px;margin-bottom:8px;}

    .pill-legend{display:flex;align-items:center;gap:6px;font-size:12px;color:#6b7280;margin-top:8px;}
    .dot{width:10px;height:10px;border-radius:50%;}

    .progress-bar{height:28px;border-radius:14px;overflow:hidden;display:flex;}
    .bar-good{background:linear-gradient(90deg,#f4cd59,#7c9d85);flex:1;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;color:#1f2a1f;}
    .bar-bad{background:linear-gradient(90deg,#c98aa4,#7aa3c8);flex:1;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;color:#1f2a1f;}

    .insight-row{display:flex;flex-direction:column;gap:12px;margin-top:8px;}
    .insight{display:flex;align-items:flex-start;gap:10px;padding:10px;border-radius:12px;background:#f9fbf9;border:1px solid #e8efe8;}
    .insight .icon{font-size:18px;}
    .insight strong{display:block;color:#1f2a1f;margin-bottom:2px;}
    .insight span{color:#6b7280;font-size:13px;line-height:1.5;}

    @media(max-width:1100px){ .grid-2{grid-template-columns:1fr;} }
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
      <a class="nav-item active" href="progress.php">
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
      <div class="page-title">Mood Progress</div>
      <div class="page-sub">Track your emotional patterns and insights</div>
    </div>

    <div class="cards-row">
      <div class="stat-card" style="background:linear-gradient(135deg,#81a38a,#6f9078);">
        <div class="stat-title">Average Mood</div>
        <div class="stat-value" id="avgMood">—</div>
        <div class="stat-sub">This week</div>
      </div>
      <div class="stat-card" style="background:linear-gradient(135deg,#f4cd59,#d3a324);">
        <div class="stat-title">Good Days</div>
        <div class="stat-value" id="goodDaysPercent">—</div>
        <div class="stat-sub">This month</div>
      </div>
      <div class="stat-card" style="background:linear-gradient(135deg,#7aa3c8,#5d87ad);">
        <div class="stat-title">Total Entries</div>
        <div class="stat-value" id="totalEntries">—</div>
        <div class="stat-sub">December</div>
      </div>
      <div class="stat-card" style="background:linear-gradient(135deg,#ec8f6b,#d16a47);">
        <div class="stat-title">Streak</div>
        <div class="stat-value" id="streak">—</div>
        <div class="stat-sub">Days logging</div>
      </div>
    </div>

    <div class="grid-2" style="margin-top:18px;">
      <div class="card">
        <div class="card-title">Weekly Mood Trends</div>
        <div class="chart-container"><canvas id="weeklyChart"></canvas></div>
        <div class="pill-legend"><div class="dot" style="background:var(--accent);"></div> Mood score</div>
      </div>
      <div class="card">
        <div class="card-title">Emotion Frequency (December)</div>
        <div class="chart-container"><canvas id="emotionChart"></canvas></div>
        <div class="pill-legend"><div class="dot" style="background:var(--accent);"></div> Frequency</div>
      </div>
    </div>

    <div class="grid-2" style="margin-top:18px;">
      <div class="card">
        <div class="card-title">Good vs Difficult Days</div>
        <div class="progress-bar" style="margin:10px 0 6px;">
          <div class="bar-good" id="goodDaysBar">—</div>
          <div class="bar-bad" id="difficultDaysBar">—</div>
        </div>
        <div class="pill-legend"><div class="dot" style="background:#f4cd59;"></div><span id="goodDaysLabel">0 good days (0%)</span> <span style="margin-left:12px;"></span><div class="dot" style="background:#7aa3c8;"></div><span id="difficultDaysLabel">0 difficult days (0%)</span></div>
      </div>
      <div class="card">
        <div class="card-title">Pattern Insights</div>
        <div class="insight-row" id="insightsContainer">
          <div class="insight"><div class="icon">⏳</div><div><strong>Loading insights...</strong><span>Analyzing your mood patterns</span></div></div>
        </div>
      </div>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
  <script>
    let weeklyChart, emotionChart;

    async function loadProgressData() {
      try {
        const res = await fetch('api/get_month_moods.php?year=' + new Date().getFullYear() + '&month=' + String(new Date().getMonth() + 1).padStart(2, '0'));
        if (res.status === 401) return;
        const data = await res.json();
        
        if (!data.moods || data.moods.length === 0) {
          document.getElementById('insightsContainer').innerHTML = '<div class="insight"><div class="icon">📊</div><div><strong>No Data Yet</strong><span>Start logging your moods to see insights here!</span></div></div>';
          return;
        }

        // Calculate stats
        const moods = data.moods;
        const scores = moods.map(m => m.combined_score || 50).filter(s => s > 0);
        const avgScore = scores.length > 0 ? Math.round(scores.reduce((a, b) => a + b, 0) / scores.length) : 0;
        const goodDays = moods.filter(m => (m.combined_score || 0) >= 60).length;
        const difficultDays = moods.filter(m => (m.combined_score || 0) < 40).length;
        const totalDays = moods.length;
        const goodPercent = totalDays > 0 ? Math.round((goodDays / totalDays) * 100) : 0;
        
        // Calculate streak
        const today = new Date();
        let streak = 0;
        for (let i = 0; i < 365; i++) {
          const checkDate = new Date(today);
          checkDate.setDate(checkDate.getDate() - i);
          const dateStr = checkDate.toISOString().slice(0, 10);
          if (moods.find(m => m.date === dateStr)) {
            streak++;
          } else {
            break;
          }
        }

        // Update stat cards
        document.getElementById('avgMood').textContent = avgScore;
        document.getElementById('goodDaysPercent').textContent = goodPercent + '%';
        document.getElementById('totalEntries').textContent = totalDays;
        document.getElementById('streak').textContent = streak;

        // Update progress bar
        const barGood = document.getElementById('goodDaysBar');
        const barBad = document.getElementById('difficultDaysBar');
        barGood.textContent = goodDays;
        barBad.textContent = difficultDays;
        barGood.style.flex = goodDays > 0 ? goodDays : 0.1;
        barBad.style.flex = difficultDays > 0 ? difficultDays : 0.1;
        document.getElementById('goodDaysLabel').textContent = goodDays + ' good days (' + goodPercent + '%)';
        document.getElementById('difficultDaysLabel').textContent = difficultDays + ' difficult days (' + (100 - goodPercent) + '%)';

        // Build weekly chart data (last 7 days)
        const last7Days = [];
        const labels = [];
        const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        for (let i = 6; i >= 0; i--) {
          const date = new Date();
          date.setDate(date.getDate() - i);
          last7Days.push(date);
          // Get the actual day name for this date
          labels.push(dayNames[date.getDay()]);
        }
        
        const weeklyData = last7Days.map(d => {
          const dateStr = d.toISOString().slice(0, 10);
          const mood = moods.find(m => m.date === dateStr);
          return mood ? (mood.combined_score || 50) : null;
        });

        // Build emotion frequency data
        const emotionCounts = {};
        moods.forEach(m => {
          const emotion = m.face_emotion || 'Unknown';
          emotionCounts[emotion] = (emotionCounts[emotion] || 0) + 1;
        });

        // Render weekly chart
        const weeklyCtx = document.getElementById('weeklyChart').getContext('2d');
        if (weeklyChart) weeklyChart.destroy();
        weeklyChart = new Chart(weeklyCtx, {
          type: 'line',
          data: {
            labels: labels,
            datasets: [{
              label: 'Mood Score',
              data: weeklyData,
              borderColor: '#7c9d85',
              backgroundColor: 'rgba(124, 157, 133, 0.1)',
              borderWidth: 2,
              fill: true,
              tension: 0.4,
              pointRadius: 5,
              pointBackgroundColor: '#7c9d85',
              pointHoverRadius: 7,
              pointHoverBackgroundColor: '#6a8b74'
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: { display: false }
            },
            scales: {
              y: {
                min: 0,
                max: 100,
                ticks: { color: '#6b7280', font: { size: 12 } },
                grid: { color: '#e8efe8' }
              },
              x: {
                ticks: { color: '#6b7280', font: { size: 12 } },
                grid: { display: false }
              }
            }
          }
        });

        // Render emotion frequency chart
        const emotionCtx = document.getElementById('emotionChart').getContext('2d');
        if (emotionChart) emotionChart.destroy();
        emotionChart = new Chart(emotionCtx, {
          type: 'bar',
          data: {
            labels: Object.keys(emotionCounts),
            datasets: [{
              label: 'Frequency',
              data: Object.values(emotionCounts),
              backgroundColor: ['#7c9d85', '#f4cd59', '#ec8f6b', '#7aa3c8', '#a08bc8'],
              borderRadius: 8,
              borderSkipped: false
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: { display: false }
            },
            scales: {
              y: {
                beginAtZero: true,
                ticks: { color: '#6b7280', font: { size: 12 } },
                grid: { color: '#e8efe8' }
              },
              x: {
                ticks: { color: '#6b7280', font: { size: 12 } },
                grid: { display: false }
              }
            }
          }
        });

        // Generate insights
        const insights = [];
        
        // Weekly pattern
        const firstHalf = weeklyData.slice(0, 3).filter(d => d !== null);
        const secondHalf = weeklyData.slice(4, 7).filter(d => d !== null);
        // Generate dynamic insights based on actual patterns
        
        // 1. Weekly trend analysis
        const firstAvg = firstHalf.length > 0 ? firstHalf.reduce((a, b) => a + b, 0) / firstHalf.length : 0;
        const secondAvg = secondHalf.length > 0 ? secondHalf.reduce((a, b) => a + b, 0) / secondHalf.length : 0;
        const trendDiff = Math.abs(secondAvg - firstAvg);
        
        if (secondAvg > firstAvg + 10) {
          insights.push({
            icon: '📈',
            title: 'Upward Trend',
            text: 'Your mood is improving! You\'re ' + Math.round(trendDiff) + ' points happier later in the week.'
          });
        } else if (firstAvg > secondAvg + 10) {
          insights.push({
            icon: '📉',
            title: 'Mid-Week Slump',
            text: 'Your mood dips mid-week by ' + Math.round(trendDiff) + ' points. Plan self-care activities for Wednesday-Thursday.'
          });
        } else {
          insights.push({
            icon: '⚖️',
            title: 'Mood Stability',
            text: 'Your mood stays consistent throughout the week - a sign of emotional balance!'
          });
        }

        // 2. Emotion dominance insight
        const sortedEmotions = Object.entries(emotionCounts).sort((a, b) => b[1] - a[1]);
        const topEmotion = sortedEmotions[0]?.[0] || 'Happy';
        const topCount = sortedEmotions[0]?.[1] || 0;
        const emotionPercent = Math.round((topCount / moods.length) * 100);
        
        if (emotionPercent >= 40) {
          insights.push({
            icon: '😊',
            title: 'Dominant Emotion',
            text: topEmotion + ' dominates your mood (' + emotionPercent + '%). This is your baseline emotional state.'
          });
        } else if (sortedEmotions.length >= 3) {
          insights.push({
            icon: '🌈',
            title: 'Emotional Range',
            text: 'You experience a diverse mix of emotions this month. That emotional flexibility is healthy!'
          });
        } else {
          insights.push({
            icon: '😊',
            title: 'Primary Emotion',
            text: topEmotion + ' is your most frequent emotion - ' + emotionPercent + '% of your entries.'
          });
        }

        // 3. Stress and challenge analysis
        const stressDays = moods.filter(m => (m.audio_emotion || '').toLowerCase().includes('stress') || (m.face_emotion || '').toLowerCase().includes('sad')).length;
        const sadDays = moods.filter(m => (m.face_emotion || '').toLowerCase().includes('sad')).length;
        
        if (stressDays === 0 && sadDays === 0) {
          insights.push({
            icon: '✨',
            title: 'Mental Health Check',
            text: 'Great news! No stress or sadness detected. Your emotional wellbeing looks strong.'
          });
        } else if (stressDays <= 1) {
          insights.push({
            icon: '💪',
            title: 'Resilience',
            text: 'You handled challenges well with only ' + stressDays + ' difficult day(s) out of ' + moods.length + '. Nice resilience!'
          });
        } else {
          insights.push({
            icon: '⚠️',
            title: 'Stress Alert',
            text: 'You had ' + stressDays + ' stressful days. Use calming tools more frequently this week.'
          });
        }

        // 4. Consistency and tracking behavior
        if (moods.length >= 25) {
          insights.push({
            icon: '📊',
            title: 'Excellent Tracking',
            text: 'You logged ' + moods.length + ' entries this month! This data gives great insight into your patterns.'
          });
        } else if (moods.length >= 15) {
          insights.push({
            icon: '📝',
            title: 'Good Consistency',
            text: 'You logged ' + moods.length + ' times. Keep tracking daily for the most accurate insights!'
          });
        } else {
          insights.push({
            icon: '📌',
            title: 'Build Your Data',
            text: 'You have ' + moods.length + ' entries so far. More data will reveal clearer patterns.'
          });
        }

        // 5. Streak achievement
        if (streak >= 7) {
          insights.push({
            icon: '🔥',
            title: 'On Fire!',
            text: 'You\'ve logged ' + streak + ' consecutive days! Keep this momentum going.'
          });
        } else if (streak >= 3) {
          insights.push({
            icon: '✅',
            title: 'Building Habit',
            text: 'You\'re on a ' + streak + ' day streak. A few more days will make this a solid habit!'
          });
        } else if (moods.length > 0) {
          insights.push({
            icon: '🎯',
            title: 'Tracking Start',
            text: 'Every entry matters. Start your tracking streak today!'
          });
        }

        // Render insights
        const insightsHtml = insights.map(i => 
          `<div class="insight"><div class="icon">${i.icon}</div><div><strong>${i.title}</strong><span>${i.text}</span></div></div>`
        ).join('');
        document.getElementById('insightsContainer').innerHTML = insightsHtml;

      } catch (e) {
        console.error('Error loading progress data:', e);
      }
    }

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
    loadProgressData();
  </script>
</body>
</html>


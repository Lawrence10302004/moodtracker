<?php
// Redirect to home if already logged in
if (session_status() === PHP_SESSION_NONE) session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: home.php');
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Sign Up — Mood Tracker</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --bg: #f6f8f6;
      --card: #ffffff;
      --accent: #7c9d85;
      --accent-strong: #6a8b74;
      --muted: #6b7280;
      --shadow: 0 8px 24px rgba(0,0,0,0.08);
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
      max-width: 400px;
      padding: 24px;
    }
    .card {
      background: var(--card);
      border-radius: 16px;
      padding: 32px;
      box-shadow: var(--shadow);
      border: 1px solid #e8efe8;
    }
    .brand {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 28px;
      justify-content: center;
    }
    .brand-icon {
      width: 42px;
      height: 42px;
      border-radius: 12px;
      background: var(--accent);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 22px;
      color: #fff;
      font-weight: 700;
    }
    .brand-text {
      text-align: center;
    }
    .brand-title {
      font-weight: 700;
      font-size: 18px;
      color: #1f2a1f;
    }
    .brand-sub {
      font-size: 13px;
      color: #54625a;
    }
    h1 {
      font-size: 24px;
      font-weight: 700;
      margin-bottom: 8px;
      color: #1f2a1f;
      text-align: center;
    }
    .subtitle {
      font-size: 14px;
      color: var(--muted);
      text-align: center;
      margin-bottom: 24px;
    }
    .form-group {
      margin-bottom: 18px;
    }
    label {
      display: block;
      font-weight: 600;
      font-size: 14px;
      color: #1f2a1f;
      margin-bottom: 8px;
    }
    input {
      width: 100%;
      padding: 10px 14px;
      border: 1px solid #e8efe8;
      border-radius: 10px;
      font-size: 14px;
      font-family: Inter, sans-serif;
      color: #1f2a1f;
      transition: all 0.2s ease;
    }
    input:focus {
      outline: none;
      border-color: var(--accent);
      box-shadow: 0 0 0 3px rgba(124,157,133,0.1);
    }
    .error {
      background: #fef2f2;
      border: 1px solid #fecaca;
      color: #991b1b;
      padding: 12px;
      border-radius: 8px;
      font-size: 13px;
      margin-bottom: 20px;
    }
    button {
      width: 100%;
      padding: 12px 16px;
      background: var(--accent);
      color: #fff;
      border: none;
      border-radius: 10px;
      font-weight: 600;
      font-size: 15px;
      cursor: pointer;
      transition: all 0.2s ease;
      margin-top: 8px;
    }
    button:hover {
      background: var(--accent-strong);
      box-shadow: 0 6px 14px rgba(106,139,116,0.16);
    }
    .footer {
      text-align: center;
      margin-top: 20px;
      font-size: 14px;
      color: var(--muted);
    }
    .footer a {
      color: var(--accent);
      text-decoration: none;
      font-weight: 600;
    }
    .footer a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="card">
      <div class="brand">
        <div class="brand-icon">
          <svg class="brand-icon-svg" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" fill="white"/>
        </svg>
        </div>
      </div>
      <div class="brand-text" style="margin-bottom: 28px;">
        <div class="brand-title">Mood Tracker</div>
        <div class="brand-sub">Track your wellbeing</div>
      </div>

      <h1>Create account</h1>
      <p class="subtitle">Start tracking your mood today</p>

      <?php if (isset($_GET['error'])): ?>
        <div class="error">
          <?php 
            $errors = [
              '1' => 'Username must be 3-100 characters and password at least 6 characters',
              'exists' => 'Username already exists',
              'server' => 'Server error. Please try again later'
            ];
            $key = htmlspecialchars($_GET['error']);
            echo $errors[$key] ?? 'An error occurred';
          ?>
        </div>
      <?php endif ?>

      <form method="post" action="./api/register.php">
        <div class="form-group">
          <label for="username">Username</label>
          <input id="username" name="username" type="text" required minlength="3" maxlength="100" autofocus>
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input id="password" name="password" type="password" required minlength="6">
        </div>
        <button type="submit">Create account</button>
      </form>

      <div class="footer">
        Already have an account? <a href="login.php">Log in</a>
      </div>
    </div>
  </div>
</body>
</html>

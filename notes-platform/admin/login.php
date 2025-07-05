<?php
session_start();
if(isset($_SESSION['admin'])) header("Location: dashboard.php");
if($_SERVER['REQUEST_METHOD']=='POST'){
  if($_POST['username']=='admin' && $_POST['password']=='Admin@12345'){
    $_SESSION['admin'] = true;
    header("Location: dashboard.php"); exit;
  }
  $error = "Invalid credentials. Please try again.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Admin Login</title>
  <link rel="stylesheet" href="css/admin_style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    .container {
      max-width: 500px;
      margin: 50px auto;
      padding: 30px;
      background: white;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .login-header {
      text-align: center;
      margin-bottom: 30px;
    }
    
    .login-header h1 {
      color: #2c3e50;
      margin-bottom: 10px;
    }
    
    .login-header i {
      font-size: 2.5em;
      color: #3498db;
    }
    
    .error {
      color: #e74c3c;
      background: #fde8e8;
      padding: 10px 15px;
      border-radius: 4px;
      margin-bottom: 20px;
      text-align: center;
    }
    
    .form-group {
      margin-bottom: 20px;
    }
    
    .form-group label {
      display: block;
      margin-bottom: 8px;
      color: #2c3e50;
      font-weight: 500;
    }
    
    .form-group input {
      width: 100%;
      padding: 12px 15px;
      border: 1px solid #ddd;
      border-radius: 4px;
      font-size: 1em;
      transition: border-color 0.3s;
    }
    
    .form-group input:focus {
      border-color: #3498db;
      outline: none;
    }
    
    .login-btn {
      width: 100%;
      padding: 12px;
      background: #3498db;
      color: white;
      border: none;
      border-radius: 4px;
      font-size: 1em;
      cursor: pointer;
      transition: background 0.3s;
    }
    
    .login-btn:hover {
      background: #2980b9;
    }
    
    .admin-footer {
      text-align: center;
      padding: 20px;
      background: #2c3e50;
      color: white;
      margin-top: 30px;
      border-radius: 0 0 8px 8px;
    }
    
    .social-icons {
      margin-top: 10px;
    }
    
    .social-icons a {
      color: white;
      margin: 0 10px;
      font-size: 1.2em;
      transition: color 0.3s;
    }
    
    .social-icons a:hover {
      color: #3498db;
    }
  </style>
</head>
<body>

<header>
  <div class="header-content">
    <h1><i class="fas fa-user-shield"></i> Admin Portal</h1>
  </div>
</header>

<div class="container">
  <div class="login-header">
    <i class="fas fa-lock"></i>
    <h1>Admin Login</h1>
  </div>
  
  <?php if(isset($error)): ?>
    <div class="error">
      <p><?= $error ?></p>
    </div>
  <?php endif; ?>
  
  <form method="POST">
    <div class="form-group">
      <label for="username">Username</label>
      <input type="text" id="username" name="username" placeholder="Enter your username" required>
    </div>
    
    <div class="form-group">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" placeholder="Enter your password" required>
    </div>
    
    <button type="submit" class="login-btn">
      <i class="fas fa-sign-in-alt"></i> Login
    </button>
  </form>
</div>

<footer class="admin-footer">
  <p>© <?= date('Y'); ?> My Notes Platform. All rights reserved.</p>
  <div class="social-icons">
    <a href="https://facebook.com" target="_blank" rel="noopener noreferrer">
      <i class="fab fa-facebook-f"></i>
    </a>
    <a href="https://twitter.com" target="_blank" rel="noopener noreferrer">
      <i class="fab fa-twitter"></i>
    </a>
    <a href="https://instagram.com" target="_blank" rel="noopener noreferrer">
      <i class="fab fa-instagram"></i>
    </a>
  </div>
</footer>

</body>
</html>
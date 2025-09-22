<?php
session_start();
require 'db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username']);
  $password = trim($_POST['password']);
  $confirm  = trim($_POST['confirm']);

  if ($password !== $confirm) {
    $error = "Passwords do not match!";
  } else {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $hash);

    if ($stmt->execute()) {
      header("Location: login.php?registered=1");
      exit;
    } else {
      $error = "Username already taken!";
    }
  }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #667eea, #764ba2);
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
    }
    .register-container {
      background: #fff;
      border-radius: 15px;
      box-shadow: 0px 8px 20px rgba(0,0,0,0.2);
      width: 100%;
      max-width: 400px;
      padding: 30px;
    }
    .register-container h3 {
      text-align: center;
      margin-bottom: 20px;
      color: #667eea;
    }
    .form-control {
      border-radius: 10px;
      padding: 12px;
    }
    .btn-primary {
      background: #667eea;
      border: none;
      border-radius: 10px;
      padding: 12px;
      font-weight: bold;
      width: 100%;
    }
    .btn-primary:hover {
      background: #5563c1;
    }
    .text-center a {
      color: #667eea;
      text-decoration: none;
    }
    .text-center a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="register-container">
    <h3>Create Account</h3>

    <?php if ($error): ?>
      <div class="alert alert-danger text-center"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="post">
      <div class="mb-3">
        <input type="text" name="username" class="form-control" placeholder="Username" required>
      </div>
      <div class="mb-3">
        <input type="password" name="password" class="form-control" placeholder="Password" required>
      </div>
      <div class="mb-3">
        <input type="password" name="confirm" class="form-control" placeholder="Confirm Password" required>
      </div>
      <button type="submit" class="btn btn-primary">Register</button>
    </form>

    <p class="text-center mt-3">Already have an account? <a href="login.php">Login</a></p>
  </div>
</body>
</html>

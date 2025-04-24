<?php
session_start();
include('message.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>

  <link rel="stylesheet" href="./assets/css/maicons.css">
  <link rel="stylesheet" href="./assets/css/bootstrap.css">
  <link rel="stylesheet" href="./assets/css/theme.css">
  <link rel="stylesheet" href="./assets/css/login.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css"/>

  <style>
    .role-buttons {
      display: flex;
      justify-content: center;
      gap: 15px;
      margin-bottom: 20px;
    }
    .role-button {
      border: 2px solid rgb(0, 217, 165);
      background-color: white;
      color:rgb(156, 212, 72);
      padding: 10px 20px;
      border-radius: 22px;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    .role-button.active,
    .role-button:hover {
      background-color:rgb(0, 217, 165);
      color: white;
    }
    input[name="role"] {
      display: none;
    }
  </style>
</head>
<body>

<header>
  <img src="./assets/img/logo.png" class="logo">
  <nav class="navbar navbar-expand-lg navbar-light shadow-sm">
    <div class="container">
      <div class="collapse navbar-collapse" id="navbarSupport">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
          <li class="nav-item"><a class="nav-link" href="vaccines.php">Vaccine Information</a></li>
          <li class="nav-item"><a class="nav-link" href="blog.php">Events</a></li>
          <li class="nav-item"><a class="btn btn-primary ml-lg-3" href="register.php">Register</a></li>
        </ul>
      </div> 
    </div> 
  </nav>
</header>

<div class="page-section">
  <div class="container">
    <h1 class="text-center" style="color:rgb(0, 217, 165)">Login Here</h1>

    <?php if (isset($_SESSION['message'])): ?>
      <div class="alert alert-warning text-center"><?= $_SESSION['message']; unset($_SESSION['message']); ?></div>
    <?php endif; ?>

    <form action="logincode.php" method="POST" class="main-form">

      <!-- Role buttons -->
      <div class="role-buttons">
        <button type="button" class="role-button" onclick="selectRole(1)">Admin</button>
        <button type="button" class="role-button" onclick="selectRole(2)">Vaccinator</button>
        <button type="button" class="role-button" onclick="selectRole(0)">User</button>
      </div>
      <input type="hidden" name="role" id="selectedRole" value="">

      <!-- Email and Password fields -->
      <div class="row mt-4">
        <div class="col-12 py-2">
          <input type="email" name="email" class="form-control" placeholder="Enter Email" required>
        </div>
        <div class="col-12 py-2">
          <input type="password" name="password" class="form-control" placeholder="Enter Password" required>
        </div>
      </div>

      <button type="submit" name="login_btn" class="btn btn-primary mt-3">Login</button>
    </form>
  </div>
</div>

<script>
  function selectRole(role) {
    document.getElementById('selectedRole').value = role;
    const buttons = document.querySelectorAll('.role-button');
    buttons.forEach(btn => btn.classList.remove('active'));
    buttons[role === 1 ? 0 : role === 2 ? 1 : 2].classList.add('active');
  }
</script>

<script src="./assets/js/jquery-3.5.1.min.js"></script>
<script src="./assets/js/bootstrap.bundle.min.js"></script>
<script src="./assets/vendor/owl-carousel/js/owl.carousel.min.js"></script>
<script src="./assets/vendor/wow/wow.min.js"></script>
<script src="./assets/js/theme.js"></script>
</body>
</html>

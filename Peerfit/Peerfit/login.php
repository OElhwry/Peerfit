<!DOCTYPE html>
<html>
<head>
  <title>Member Login</title>
  <link rel="stylesheet" type="text/css" href="Login.css">
</head>
<body>

<?php
    if (isset($_GET['signup']) && $_GET['signup'] === 'success') {
      echo '<div class="success-message">Sign up was successful!</div>';
      echo '<script>
              setTimeout(function() {
                window.location.href = "login.php";
              }, 5000); // Redirect after 5 seconds
            </script>';

    
    }
  ?>


  <div class="login-container">
    <div class="left-section">
        <img src="Images/PCIcon.jpg" alt="PC Icon" class="pc-icon">
    </div>
    <div class="right-section">
        <img src="Images/PeerFitLogo.jpg" alt="Brand Logo" class="brand-logo">
        <h1>Member Login</h1>
        <form id="loginForm" action="process_login.php" method="post">
          <input type="email" id="email" name="email" placeholder="johnsmith@gmail.com" required>
          <input type="password" id="password" name="password" placeholder="Password" required>
          <?php
// login.php

if (isset($_GET['error'])) {
    if ($_GET['error'] === 'invalid_password') {
        echo '<div class="error-message">Invalid password. Please try again.</div>';
    } elseif ($_GET['error'] === 'user_not_found') {
        echo '<div class="error-message">Incorrect email or password</div>';
    }
}
?>
          <button type="submit">Login</button>
        </form>
        <p>Forgot <a href="signup.php">Username / Password?</a></p>
      <a href="signup.php">
        <div class="signup-button">
            <a href="signup.php" class="create-account-link">Create your Account →</a>
          </div>
      </a>
    </div>
  </div>
</body>
</html>

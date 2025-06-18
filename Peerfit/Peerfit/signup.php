<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="signup.css">
  <title>Sign Up - PeerFit</title>
</head>
<body>
  <div class="container">
    <div class="signup-box">
      <h1>Hello!</h1>
      <h2>Please sign up to continue</h2>
      <form action="process_signup.php" method="post" onsubmit="return validatePassword() && validateForm()">
        <label for="fullname">Full Name</label><br>
        <input type="text" id="fullname" name="fullname" placeholder="John Smith" required><br>
        <label for="email">Email Address</label><br>
        <input type="email" id="email" name="email" placeholder="johnsmith@gmail.com" required><br>
        <label for="address">Address</label><br>
        <input type="text" id="address" name="address" placeholder="Enter your address" required><br>
        <div class="form-section">
        <label for="gender">Gender:</label>
        <div class="gender-options">
          <div class="gender-option">
            <input type="radio" id="male" name="gender" value="Male" required>
            <label for="male">Male</label>
          </div>
          <div class="gender-option">
            <input type="radio" id="female" name="gender" value="Female" required>
            <label for="female">Female</label>
          </div>
        </div>
        </div>

        <label for="password">Password</label><br>
        <input type="password" id="password" name="password" placeholder="Password" required><br>
        <label for="confirmPassword">Confirm Password</label><br>
        <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm Password" required><br>      
        <span id="passwordError" style="color: red; display: none;">Passwords don't match.</span><br><br>
        <input type="submit" value="Sign Up" class="btn">
      </form>
      <p>I'm already a member! <a href="login.php">Sign In</a></p>
    </div>
  </div>

  <div class="sidebar">
    <div class="logo">
      <img src="Images/PeerFitLogo.jpg" alt="PeerFit Logo">
    </div>
    <div class="peerfit-text">
      PeerFit
    </div>
    <div class="already-have-account">
      Already have an account?
    </div>
    <a href="login.php">
      <button class="signin-button">Sign In</button>
    </a>
  </div>

  <script>
  function validatePassword() {
    var password = document.getElementById("password").value;
    var confirmPassword = document.getElementById("confirmPassword").value;
    var passwordError = document.getElementById("passwordError");

    if (password !== confirmPassword) {
      passwordError.innerHTML = "Passwords don't match.";
      passwordError.style.display = "block";
      return false;  // Return false to prevent further processing
    } else {
      passwordError.style.display = "none";
      return true;   // Return true to allow form submission
    }
  }

  // Initialize Google Places Autocomplete
  function initAutocomplete() {
    var input = document.getElementById('address');
    var options = {
      types: ['geocode']
    };

    var autocomplete = new google.maps.places.Autocomplete(input, options);
  }

  function validateForm() {
  const interests = document.querySelectorAll('input[name="interests[]"]:checked');

  // if (interests.length === 0) {
  //   alert("Please select at least one interest.");
  //   return false;
  // }

  // Additional form validation if needed

  return true;
}
  </script>

  <!-- Include the Google Places Autocomplete API script -->
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA45ZFlxM0vD53MyLNF05UN8aa88tsiHpk&libraries=places&callback=initAutocomplete" async defer></script>
</body>
</html>

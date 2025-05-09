<!DOCTYPE html>
<!DOCTYPE html>
<html lang="en">

<head>
  <link rel="stylesheet" href="assets/css/forgot_pass_style.css" />
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Forgot Password</title>
</head>

<body>
  <main>
    <div class="login-container">
      <div class="back-button-container">
        <a href="index.html" class="back-button">Back</a>
      </div>
      <h1>Forgot Password</h1>
      <div class="welcome-container">
        <h2>Reset Your Password</h2>
        <p class="welcome">
          Please enter your username and security question.
        </p>
      </div>
      <form action="dashboard.html" method="post">
        <div class="input-container">
          <div class="security-question-container">
            <label for="security-question">Security Question:</label>
            <select id="security-question" name="security-question">
              <option value="pet">What is the name of your first pet?</option>
              <option value="school">
                What is the name of your elementary school?
              </option>
              <option value="city">In what city were you born?</option>
            </select>
            <label for="security-answer">Answer:</label>
            <input
              type="text"
              id="security-answer"
              name="security-answer"
              required />
          </div>
          <label for="newPassword">New Password:</label>
          <input
            type="password"
            id="newPassword"
            name="newPassword"
            required />
        </div>
        <button type="submit">confirm</button>
        <footer>
          <p>&copy; 2023 Archcube. All rights reserved.</p>
          <p>Privacy Policy | Terms of Service</p>
        </footer>
      </form>
    </div>
  </main>
  <div>
    <picture>
      <source srcset="assets/images/archcube_logo.png" type="image/png" />
      <img
        src="assets/images/archcube_logo.png"
        alt="archcube_logo"
        class="logo" />
    </picture>
  </div>
</body>

</html>
<?php
include_once "db_connect.php";

$msg = "";
$msgType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email   = trim($_POST["email"] ?? "");
    $newPass = trim($_POST["new_pass"] ?? "");
    $confirm = trim($_POST["confirm"] ?? "");

    if (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[A-Za-z]{2,}$/", $email)) {

        $msg = "Please enter a valid email address.";
        $msgType = "error";

    } elseif (strlen($newPass) < 6) {

        $msg = "Password must be at least 6 characters.";
        $msgType = "error";

    } elseif ($newPass !== $confirm) {

        $msg = "Passwords do not match.";
        $msgType = "error";

    } else {

        $safe_email = mysqli_real_escape_string($conn, $email);
        $safe_pass  = mysqli_real_escape_string($conn, $newPass);

        // CUSTOMER
        $userRes = mysqli_query($conn,
            "SELECT customer_id FROM customers WHERE email='$safe_email' LIMIT 1"
        );

        if ($userRes && mysqli_num_rows($userRes) > 0) {

            mysqli_query($conn,
                "UPDATE customers SET password='$safe_pass' WHERE email='$safe_email'"
            );

            $msg = "Password updated successfully! <a href='login.php'>Sign in</a>";
            $msgType = "success";

        } else {

            // ADMIN
            $adminRes = mysqli_query($conn,
                "SELECT admin_id FROM admin WHERE email='$safe_email' LIMIT 1"
            );

            if ($adminRes && mysqli_num_rows($adminRes) > 0) {

                mysqli_query($conn,
                    "UPDATE admin SET password='$safe_pass' WHERE email='$safe_email'"
                );

                $msg = "Password updated! <a href='loginAdmin.php'>Sign in as Admin</a>";
                $msgType = "success";

            } else {

                $msg = "No account found with this email.";
                $msgType = "error";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reset Password - Brew &amp; Bean</title>
<style>
:root{
  --brown:#4A2C1D;
  --brown-2:#6A3F28;
  --gold:#C8A96E;
  --bg:#EFE6DA;
  --panel:#F8F1E7;
  --line:#E2D2BE;
  --text:#2E2420;
  --muted:#6B5A50;
}
*{box-sizing:border-box;margin:0;padding:0}
body{
  font-family:Arial,sans-serif;
  min-height:100vh;
  display:flex;
  background:var(--bg);
}

/* LEFT HERO */
.hero{
  flex:1;
  background:linear-gradient(160deg,#2E1A0E 0%,var(--brown) 50%,var(--brown-2) 100%);
  display:flex;flex-direction:column;
  align-items:center;justify-content:center;
  padding:60px 48px;
  position:relative;overflow:hidden;
}
.hero::before{
  content:'';position:absolute;inset:0;
  background:url('images/BEAN.jpg') center/cover no-repeat;
  opacity:.18;
}
.hero-content{position:relative;z-index:1;text-align:center;color:#fff;}
.hero-logo{
  width:90px;height:90px;border-radius:50%;
  overflow:hidden;border:3px solid rgba(255,255,255,0.4);
  background:#fff;margin:0 auto 22px;
  box-shadow:0 8px 28px rgba(0,0,0,0.3);
}
.hero-logo img{width:100%;height:100%;object-fit:cover;display:block;}
.hero h1{font-size:34px;font-weight:800;margin-bottom:10px;}
.hero p{font-size:15px;opacity:.8;line-height:1.7;max-width:300px;}

.info-badge{
  margin-top:36px;
  background:rgba(200,169,110,0.15);
  border:1.5px solid rgba(200,169,110,0.4);
  border-radius:14px;
  padding:18px 24px;
  display:flex;flex-direction:column;gap:10px;
  width:100%;max-width:300px;
}
.info-badge-item{
  display:flex;align-items:center;gap:12px;
  color:#fff;font-size:13px;
}
.info-badge-item span:first-child{font-size:18px;flex-shrink:0;}

/* RIGHT FORM */
.form-side{
  width:480px;flex-shrink:0;
  display:flex;align-items:center;justify-content:center;
  padding:40px 48px;background:#fff;
}
.form-wrap{width:100%;max-width:360px;}

.form-header{margin-bottom:28px;}
.form-header h2{font-size:26px;font-weight:800;color:var(--brown);margin-bottom:6px;}
.form-header p{font-size:14px;color:var(--muted);line-height:1.5;}

.reset-tag{
  display:inline-flex;align-items:center;gap:6px;
  background:#FFF3E0;border:1px solid #FFCC80;
  color:#8A4500;border-radius:999px;
  padding:4px 12px;font-size:12px;font-weight:700;
  margin-bottom:16px;
}

.message-box{
  border-radius:12px;padding:12px 16px;
  display:flex;align-items:center;gap:10px;
  font-size:13px;margin-bottom:20px;
}
.message-box.error{
  background:#FFF0F0;border:1px solid #f5a0a0;
  color:#c0392b;animation:shake .3s;
}
.message-box.success{
  background:#E9F8EC;border:1px solid #a0d8af;
  color:#1E6B3A;
}
.message-box a{color:inherit;font-weight:700;}

@keyframes shake{
  0%,100%{transform:translateX(0)}
  25%{transform:translateX(-6px)}
  75%{transform:translateX(6px)}
}

.field{margin-bottom:18px;}
.field label{display:block;font-size:13px;font-weight:700;color:var(--text);margin-bottom:7px;}
.input-wrap{position:relative;}
.input-ico{
  position:absolute;left:14px;top:50%;
  transform:translateY(-50%);
  font-size:15px;pointer-events:none;
}
.field input{
  width:100%;padding:13px 42px 13px 42px;
  border-radius:12px;border:1.5px solid var(--line);
  background:#fafafa;font-size:14px;color:var(--text);
  outline:none;transition:.2s;
}
.field input:focus{
  border-color:var(--brown);background:#fff;
  box-shadow:0 0 0 3px rgba(74,44,29,0.1);
}
.toggle-pw{
  position:absolute;right:14px;top:50%;
  transform:translateY(-50%);
  background:none;border:none;color:var(--muted);
  font-size:14px;cursor:pointer;padding:0;
}

/* PASSWORD STRENGTH */
.strength{
  width:100%;height:6px;
  background:#ddd;border-radius:999px;
  margin-top:8px;overflow:hidden;
}
.strength-bar{height:100%;width:0%;transition:.3s;}

.match{font-size:12px;margin-top:7px;}

.btn-reset{
  width:100%;padding:14px;border-radius:999px;border:none;
  background:var(--brown);color:#fff;
  font-weight:800;font-size:15px;
  cursor:pointer;transition:.2s;margin-top:4px;
  display:flex;align-items:center;justify-content:center;gap:8px;
}
.btn-reset:hover{background:var(--brown-2);transform:translateY(-1px);}

.back-link{
  display:flex;align-items:center;justify-content:center;gap:6px;
  margin-top:24px;font-size:13px;color:var(--muted);
  text-decoration:none;
}
.back-link:hover{color:var(--brown);}

@media(max-width:860px){
  .hero{display:none;}
  .form-side{width:100%;padding:40px 28px;}
}
@media(max-width:420px){
  .form-side{padding:30px 20px;}
}
</style>
</head>
<body>

<!-- HERO -->
<div class="hero">
  <div class="hero-content">
    <div class="hero-logo">
      <img src="images/Brew&Bean3.jpg" alt="Brew & Bean">
    </div>
    <h1>Brew &amp; Bean</h1>
    <p>Reset your password and get back to your coffee experience.</p>

    <div class="info-badge">
      <div class="info-badge-item">
        <span>&#128274;</span>
        <span>Enter your registered email</span>
      </div>
      <div class="info-badge-item">
        <span>&#9989;</span>
        <span>Create a new secure password</span>
      </div>
      <div class="info-badge-item">
        <span>&#9749;</span>
        <span>Sign back in and enjoy your coffee</span>
      </div>
    </div>
  </div>
</div>

<!-- FORM -->
<div class="form-side">
  <div class="form-wrap">

    <div class="reset-tag">&#128273; Reset Password</div>

    <div class="form-header">
      <h2>Forgot Password</h2>
      <p>Enter your email and create a new password.</p>
    </div>

    <?php if ($msg): ?>
    <div class="message-box <?php echo $msgType; ?>">
      <span><?php echo $msgType === 'success' ? '&#9989;' : '&#9888;'; ?></span>
      <span><?php echo $msg; ?></span>
    </div>
    <?php endif; ?>

    <form method="POST" action="">

      <!-- EMAIL -->
      <div class="field">
        <label for="email">Email Address</label>
        <div class="input-wrap">
          <span class="input-ico">&#9993;</span>
          <input type="email" id="email" name="email"
                 placeholder="your@email.com"
                 required autocomplete="email">
        </div>
      </div>

      <!-- NEW PASSWORD -->
      <div class="field">
        <label for="newPass">New Password</label>
        <div class="input-wrap">
          <span class="input-ico">&#128274;</span>
          <input type="password" id="newPass" name="new_pass"
                 placeholder="Min. 6 characters"
                 required>
          <button type="button" class="toggle-pw" onclick="toggleNew()">&#128065;</button>
        </div>
        <div class="strength"><div class="strength-bar" id="strengthBar"></div></div>
      </div>

      <!-- CONFIRM PASSWORD -->
      <div class="field">
        <label for="confirmPass">Confirm Password</label>
        <div class="input-wrap">
          <span class="input-ico">&#128274;</span>
          <input type="password" id="confirmPass" name="confirm"
                 placeholder="Repeat new password"
                 required>
          <button type="button" class="toggle-pw" onclick="toggleConfirm()">&#128065;</button>
        </div>
        <div class="match" id="matchText"></div>
      </div>

      <button type="submit" class="btn-reset">
        Update Password &#8594;
      </button>

    </form>

    <a class="back-link" href="welcome.php">&#8592; Back to sign in</a>

  </div>
</div>

<script>
function toggleNew(){
  const i = document.getElementById('newPass');
  i.type = i.type === 'password' ? 'text' : 'password';
}
function toggleConfirm(){
  const i = document.getElementById('confirmPass');
  i.type = i.type === 'password' ? 'text' : 'password';
}

const pass = document.getElementById('newPass');
const confirmPass = document.getElementById('confirmPass');
const bar = document.getElementById('strengthBar');
const matchText = document.getElementById('matchText');

pass.addEventListener('input', () => {
  const val = pass.value;
  let strength = 0;
  if(val.length >= 6) strength++;
  if(/[A-Z]/.test(val)) strength++;
  if(/[0-9]/.test(val)) strength++;
  if(/[^A-Za-z0-9]/.test(val)) strength++;

  const colors = ['','red','orange','#d4b000','green'];
  const widths = ['0%','25%','50%','75%','100%'];
  bar.style.width  = widths[strength] || '0%';
  bar.style.background = colors[strength] || '';
});

confirmPass.addEventListener('input', () => {
  if(confirmPass.value === pass.value && confirmPass.value !== ''){
    matchText.innerHTML = '&#10003; Passwords match';
    matchText.style.color = 'green';
  } else if(confirmPass.value === '') {
    matchText.innerHTML = '';
  } else {
    matchText.innerHTML = '&#10007; Passwords do not match';
    matchText.style.color = 'red';
  }
});
</script>
<script src="accessibility.js"></script>

</body>
</html>

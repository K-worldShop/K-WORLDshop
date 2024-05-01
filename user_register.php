<?php
include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}

$message = [];

if (isset($_POST['submit'])) {
    // Sanitize input
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
    $pass = sha1($_POST['pass']); // Hash the password
    $cpass = sha1($_POST['cpass']); // Confirm hashed password
    $number = filter_var($_POST['number'], FILTER_SANITIZE_STRING);
    $address_line_01 = filter_var($_POST['address_line_01'], FILTER_SANITIZE_STRING);
    $address_line_02 = filter_var($_POST['address_line_02'], FILTER_SANITIZE_STRING);
    $city = filter_var($_POST['city'], FILTER_SANITIZE_STRING);
    $state = filter_var($_POST['state'], FILTER_SANITIZE_STRING);
    $country = filter_var($_POST['country'], FILTER_SANITIZE_STRING);

    try {
        // Check if email already exists
        $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
        $select_user->execute([$email]);

        if ($select_user->rowCount() > 0) {
            $message[] = 'Email already exists!';
        } else {
            if ($pass != $cpass) {
                $message[] = 'Confirm password does not match!';
            } else {
                // Insert user data into `users` table
                
                $insert_user = $conn->prepare("INSERT INTO `users` (name, email, password, number, address_line_01, address_line_02, city, state, country) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $insert_user->execute([$name, $email, $pass, $number, $address_line_01, $address_line_02, $city, $state, $country]);
                $message[] = 'Registered successfully, please log in!';
            }
        }
    } catch (PDOException $e) {
        // Handle PDO errors and report them gracefully
        $message[] = 'An error occurred: ' . $e->getMessage();
    }
}
?>





    <!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>register</title>
    
    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">

    </head>
    <body>
    
    <?php include 'components/user_header.php'; ?>

    <section class="form-container">

    <form action="" method="post">
    <h3>register now</h3>
    <input type="text" name="name" required placeholder="enter your username" maxlength="20" class="box">
    <input type="email" name="email" required placeholder="enter your email" maxlength="50" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
    <input type="password" name="pass" required placeholder="enter your password" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
    <input type="password" name="cpass" required placeholder="confirm your password" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
    <input type="text" name="number" required placeholder="enter your number" maxlength="15" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
    <input type="text" name="address_line_01" required placeholder="enter your address line 01" maxlength="100" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
    <input type="text" name="address_line_02" required placeholder="enter your address line 02" maxlength="100" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
    <input type="text" name="city" required placeholder="enter your city" maxlength="100" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
    <input type="text" name="state" required placeholder="enter your state" maxlength="100" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
    <input type="text" name="country" required placeholder="enter your country" maxlength="100" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
    <input type="submit" value="register now" class="btn" name="submit">
    <p>already have an account?</p>
    <a href="user_login.php" class="option-btn">login now</a>
    </form>



    </section>













    <?php include 'components/footer.php'; ?>

    <script src="js/script.js"></script>

    </body>
    </html>
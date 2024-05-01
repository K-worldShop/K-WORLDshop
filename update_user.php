<?php
include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
}

// Fetch user's profile details
$select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
$select_profile->execute([$user_id]);
$fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);

if(isset($_POST['submit'])){
   $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
   $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
   $number = filter_var($_POST['number'], FILTER_SANITIZE_STRING);
   $address_line_01 = filter_var($_POST['address_line_01'], FILTER_SANITIZE_STRING);
   $address_line_02 = filter_var($_POST['address_line_02'], FILTER_SANITIZE_STRING);
   $city = filter_var($_POST['city'], FILTER_SANITIZE_STRING);
   $state = filter_var($_POST['state'], FILTER_SANITIZE_STRING);
   $country = filter_var($_POST['country'], FILTER_SANITIZE_STRING);

   // Update profile details
   $update_profile = $conn->prepare("UPDATE `users` SET name = ?, email = ?, address_line_01 = ?, address_line_02 = ?, city = ?, state = ?, country = ?, number = ? WHERE id = ?");
   $update_profile->execute([$name, $email, $address_line_01, $address_line_02, $city, $state, $country, $number, $user_id]);

         $message[] = 'password updated successfully!';
      }else{
         $message[] = 'please enter a new password!';
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
      <h3>update now</h3>
      <input type="hidden" name="prev_pass" value="<?= $fetch_profile["password"]; ?>">
      <input type="text" name="name" required placeholder="enter your username" maxlength="20"  class="box" value="<?= $fetch_profile["name"]; ?>">
      <input type="email" name="email" required placeholder="enter your email" maxlength="50"  class="box" oninput="this.value = this.value.replace(/\s/g, '')" value="<?= $fetch_profile["email"]; ?>">
      <input type="password" name="old_pass" placeholder="enter your old password" maxlength="20"  class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="new_pass" placeholder="enter your new password" maxlength="20"  class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="cpass" placeholder="confirm your new password" maxlength="20"  class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="text" name="number" required placeholder="update your number" maxlength="15" class="box" oninput="this.value = this.value.replace(/\s/g, '')" value="<?= $fetch_profile["number"]; ?>">
      <input type="text" name="address line 01" required placeholder="update your address " maxlength="100" class="box" oninput="this.value = this.value.replace(/\s/g, '')" value="<?= $fetch_profile["address_line_01"]; ?>">
      <input type="text" name="address line 02" required placeholder="update your address" maxlength="100" class="box" oninput="this.value = this.value.replace(/\s/g, '')" value="<?= $fetch_profile["address_line_02"]; ?>">
      <input type="text" name="city" required placeholder="update your city" maxlength="100" class="box" oninput="this.value = this.value.replace(/\s/g, '')" value="<?= $fetch_profile["city"]; ?>">
      <input type="text" name="state" required placeholder="update your state" maxlength="100" class="box" oninput="this.value = this.value.replace(/\s/g, '')" value="<?= $fetch_profile["state"]; ?>">
      <input type="text" name="country" required placeholder="update your country" maxlength="100" class="box" oninput="this.value = this.value.replace(/\s/g, '')" value="<?= $fetch_profile["country"]; ?>">
      <input type="submit" value="update now" class="btn" name="submit">
   </form>

</section>













<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>
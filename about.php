<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>about</title>

   <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />
   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="about">

   <div class="row">

      <div class="image">
         <img src="images/tcu.png" alt="">
      </div>
      <div class="image">
         <img src="images/cict.png" alt="">
      </div>

      <div class="content">c
         <h3>K-WORLD SHOP</h3>
         <p>At K-WORLD SHOP, we're passionate about bringing the best of K-pop music to fans around the world. We understand the excitement and love that fans have for their favorite K-pop groups, which is why we've curated a selection of albums that celebrate the diversity, talent, and artistry of the K-pop industry.</p>
         <h3>OUR MISSION</h3>
         <p>We're on a mission to make it easy for K-pop enthusiasts to find and purchase their favorite albums. Whether you're a seasoned collector or a newcomer to the genre, we're here to provide a seamless shopping experience and deliver authentic, high-quality albums right to your doorstep.</p>
         <h3>OUR TEAM</h3>
         <p>Behind K-WORLD SHOP is a team of dedicated K-pop enthusiasts who are committed to providing excellent customer service and creating a community where fans can connect and share their love for K-pop music.

Thank you for choosing [Your Shop Name] as your go-to destination for K-pop albums. Let's spread the joy of music together!</p>
         <a href="contact.php" class="btn">contact us</a>
      </div>

   </div>

</section>



   </div>

</section>









<?php include 'components/footer.php'; ?>

<script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>

<script src="js/script.js"></script>

<script>

var swiper = new Swiper(".reviews-slider", {
   loop:true,
   spaceBetween: 20,
   pagination: {
      el: ".swiper-pagination",
      clickable:true,
   },
   breakpoints: {
      0: {
        slidesPerView:1,
      },
      768: {
        slidesPerView: 2,
      },
      991: {
        slidesPerView: 3,
      },
   },
});

</script>

</body>
</html>

<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
   header('location:user_login.php');
};

if(isset($_POST['delete'])){
   $cart_id = $_POST['cart_id'];
   $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE id = ?");
   $delete_cart_item->execute([$cart_id]);
}

if(isset($_GET['delete_all'])){
   $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
   $delete_cart_item->execute([$user_id]);
   header('location:cart.php');
}

if(isset($_POST['update_qty'])){
   $cart_id = $_POST['cart_id'];
   $qty = $_POST['qty'];
   $qty = filter_var($qty, FILTER_SANITIZE_STRING);
   $update_qty = $conn->prepare("UPDATE `cart` SET quantity = ? WHERE id = ?");
   $update_qty->execute([$qty, $cart_id]);
   $message[] = 'cart quantity updated';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>shopping cart</title>
   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="products shopping-cart">

   <h3 class="heading">shopping cart</h3>

   <div class="box-container">

   <?php
      $grand_total = 0;
      $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
      $select_cart->execute([$user_id]);
      if($select_cart->rowCount() > 0){
         while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){
   ?>
   <form action="" method="post" class="box">
    <input type="hidden" name="cart_id" value="<?= $fetch_cart['id']; ?>">
    <a href="quick_view.php?pid=<?= $fetch_cart['pid']; ?>" class="fas fa-eye"></a>
    <img src="uploaded_img/<?= $fetch_cart['image']; ?>" alt="">
    <div class="name"><?= $fetch_cart['name']; ?></div>
    <div class="flex">
        <div class="price">₱<?= $fetch_cart['price']; ?>/-</div>
        <input type="number" name="qty" class="qty" min="1" max="99" onkeypress="if(this.value.length == 2) return false;" value="<?= $fetch_cart['quantity']; ?>">
        <button type="submit" class="fas fa-edit" name="update_qty"></button>
    </div>
    <div class="sub-total"> sub total : <span>₱<?= $sub_total = ($fetch_cart['price'] * $fetch_cart['quantity']); ?>/-</span> </div>
    <input type="submit" value="delete item" onclick="return confirm('delete this from cart?');" class="delete-btn" name="delete">
    
    <!-- Additional Checkbox Section -->
<div class="additional-checkbox-container">
    <input type="checkbox" name="additional_selected_items[]" id="additional_selected_item_<?= $fetch_cart['id']; ?>" value="<?= $fetch_cart['id']; ?>" class="additional-checkbox">
    <label class="additional-checkmark" for="additional_selected_item_<?= $fetch_cart['id']; ?>">Select for Checkout</label>
</div>

</form>

   <?php
   $grand_total += $sub_total;
      }
   }else{
      echo '<p class="empty">your cart is empty</p>';
   }
   ?>
   </div>

   <div class="cart-total">
      <p>grand total : <span>₱<?= $grand_total; ?>/-</span></p>
      <a href="shop.php" class="option-btn">continue shopping</a>
      <a href="cart.php?delete_all" class="delete-btn <?= ($grand_total > 1)?'':'disabled'; ?>" onclick="return confirm('delete all from cart?');">delete all item</a>
      <!-- Modified Proceed to Checkout Button -->
      <button class="btn proceed-to-checkout <?= ($grand_total > 1) ? '' : 'disabled'; ?>">Proceed to Checkout</button>
   </div>

</section>

<?php include 'components/footer.php'; ?>

<script>
        document.addEventListener('DOMContentLoaded', () => {
            const checkoutButton = document.querySelector('.proceed-to-checkout');
            checkoutButton.addEventListener('click', () => {
                const selectedItems = document.querySelectorAll('.additional-checkbox:checked');
                const selectedItemIdArray = Array.from(selectedItems).map(item => item.value);
                if (selectedItemIdArray.length > 0) {
                    const selectedItemsQueryString = selectedItemIdArray.join(',');
                    const url = `checkout.php?selected_items=${selectedItemsQueryString}`;
                    window.location.href = url;
                } else {
                    alert('Please select at least one item to proceed to checkout.');
                }
            });
        });
    </script>



</body>
</html>

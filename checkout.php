<?php
require_once('TCPDF-main/tcpdf.php');  // Adjust this path if needed

include 'components/connect.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: user_login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$select_user = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
$select_user->execute([$user_id]);
$user_details = $select_user->fetch(PDO::FETCH_ASSOC);

if (isset($_POST['order'])) {
    ob_clean();

    $name = $_POST['name'];
    $number = $_POST['number'];
    $email = $_POST['email'];
    $method = $_POST['method'];
    $flat = $_POST['flat'];
    $street = $_POST['street'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $country = $_POST['country'];
    $pin_code = $_POST['pin_code'];
    $total_products = $_POST['total_products'];
    $total_price = $_POST['total_price'];

    $insert_order = $conn->prepare("INSERT INTO `orders` (user_id, name, number, email, method, address, total_products, total_price) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $insert_order->execute([$user_id, $name, $number, $email, $method, "$flat, $street, $city, $state, $country, $pin_code", $total_products, $total_price]);

    $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
    $delete_cart->execute([$user_id]);

    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetCreator('Your Website');
    $pdf->SetAuthor('Your Name');
    $pdf->SetTitle('Order Receipt');
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
    $pdf->AddPage();

    $receipt_content = '
        <h1>Order Receipt</h1>
        <p><strong>Name:</strong> ' . $name . '</p>
        <p><strong>Email:</strong> ' . $email . '</p>
        <p><strong>Address:</strong> ' . $flat . ', ' . $street . ', ' . $city . ', ' . $state . ', ' . $country . ', ' . $pin_code . '</p>
        <p><strong>Payment Method:</strong> ' . $method . '</p>
        <p><strong>Total Products:</strong> ' . $total_products . '</p>
        <p><strong>Total Price:</strong> P' . $total_price . '</p>
    ';

    $pdf->writeHTML($receipt_content, true, false, true, false, '');

    $pdf->Output('receipt.pdf', 'D');

    function redirectToOrderPage() {
      header('Location: orders.php');
      exit();
  }
  
}

// Function to reset user details
function resetUserDetails($conn, $user_id) {
   $update_user = $conn->prepare("UPDATE `users` SET name = '', number = '', email = '', address_line_01 = '', address_line_02 = '', city = '', state = '', country = '', pin_code = '' WHERE id = ?");
   $update_user->execute([$user_id]);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Checkout</title>
   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="checkout-orders">

   <form action="" method="POST">

   <h3>Your Orders</h3>

   <div class="display-orders">
    <?php
    $grand_total = 0;
    $selected_item_ids = isset($_GET['selected_items']) ? explode(',', $_GET['selected_items']) : array();
    if (!empty($selected_item_ids)) {
        $placeholders = rtrim(str_repeat('?,', count($selected_item_ids)), ',');
        $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ? AND id IN ($placeholders)");
        $params = array_merge([$user_id], $selected_item_ids);
        $select_cart->execute($params);
        if ($select_cart->rowCount() > 0) {
            while ($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)) {
                $cart_items[] = $fetch_cart['name'] . ' (' . $fetch_cart['price'] . ' x ' . $fetch_cart['quantity'] . ') - ';
                $total_products = implode($cart_items);
                $grand_total += ($fetch_cart['price'] * $fetch_cart['quantity']);
                ?>
                <p><?= $fetch_cart['name']; ?> <span>(<?= '$' . $fetch_cart['price'] . '/- x ' . $fetch_cart['quantity']; ?>)</span></p>
                <?php
            }
        } else {
            echo '<p class="empty">No items selected!</p>';
        }
    } else {
        echo '<p class="empty">No items selected!</p>';
    }
    ?>
    <input type="hidden" name="total_products" value="<?= $total_products; ?>">
    <input type="hidden" name="total_price" value="<?= $grand_total; ?>" value="">
    <div class="grand-total"> Total Amount: <span>$<?= $grand_total; ?>/-</span></div>
</div>

      <h3>Place Your Orders</h3>

      <div class="flex">
         <div class="inputBox">
            <span>Your Name :</span>
            <input type="text" name="name" placeholder="Enter your name" class="box" maxlength="20" value="<?= isset($user_details['name']) ? $user_details['name'] : '' ?>" required>
         </div>
         <div class="inputBox">
            <span>Your Number :</span>
            <input type="number" name="number" placeholder="Enter your number" class="box" min="0" max="9999999999" onkeypress="if(this.value.length == 10) return false;" value="<?= isset($user_details['number']) ? $user_details['number'] : '' ?>" required>
         </div>
         <div class="inputBox">
            <span>Your Email :</span>
            <input type="email" name="email" placeholder="Enter your email" class="box" maxlength="50" value="<?= isset($user_details['email']) ? $user_details['email'] : '' ?>" required>
         </div>
         <div class="inputBox">
            <span>Payment Method :</span>
            <select name="method" class="box" required>
               <option value="cash on delivery">Cash on Delivery</option>
               <option value="credit card">Credit Card</option>
               <option value="gcash">Gcash</option>
               <option value="paypal">PayPal</option>
            </select>
         </div>
         <!-- Prefill address details -->
         <div class="inputBox">
            <span>Address Line 01 :</span>
            <input type="text" name="flat" placeholder="Flat number" class="box" maxlength="50" value="<?= isset($user_details['address_line_01']) ? $user_details['address_line_01'] : '' ?>" required>
         </div>
         <div class="inputBox">
            <span>Address Line 02 :</span>
            <input type="text" name="street" placeholder="Street name" class="box" maxlength="50" value="<?= isset($user_details['address_line_02']) ? $user_details['address_line_02'] : '' ?>" required>
         </div>
         <div class="inputBox">
            <span>City :</span>
            <input type="text" name="city" placeholder="City" class="box" maxlength="50" value="<?= isset($user_details['city']) ? $user_details['city'] : '' ?>" required>
         </div>
         <div class="inputBox">
            <span>State :</span>
            <input type="text" name="state" placeholder="State" class="box" maxlength="50" value="<?= isset($user_details['state']) ? $user_details['state'] : '' ?>" required>
         </div>
         <div class="inputBox">
            <span>Country :</span>
            <input type="text" name="country" placeholder="Country" class="box" maxlength="50" value="<?= isset($user_details['country']) ? $user_details['country'] : '' ?>" required>
         </div>
         <div class="inputBox">
            <span>Zip Code :</span>
            <input type="number" name="pin_code" placeholder="Pin Code" min="0" max="999999" onkeypress="if(this.value.length == 6) return false;" class="box" value="<?= isset($user_details['pin_code']) ? $user_details['pin_code'] : '' ?>" required>
         </div>
      </div>
      <input type="submit" name="order" class="btn  <?= ($grand_total > 1) ? '' : 'disabled'; ?>" value="Place Order">
      <form id="placeOrderForm" action="" method="POST">
      <button type="button" class="btn" onclick="window.location.href='orders.php';">View Orders</button>
     

   </form>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const placeOrderForm = document.querySelector('#placeOrderForm');

        placeOrderForm.addEventListener('submit', function (event) {
            event.preventDefault(); // Prevent default form submission

            // Fetch form data
            const formData = new FormData(this);

            // Send a POST request to submit the order
            fetch('checkout.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    // Redirect to the orders page on successful order placement
                    window.location.href = 'orders.php';
                    // Reset the form after successful order placement
                    placeOrderForm.reset();
                    // Reset user details fields
                    resetUserDetails();
                } else {
                    throw new Error('Failed to place order.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to place order. Please try again.');
            });
        });
      });
        
</script>



<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>

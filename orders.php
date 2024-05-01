<?php
session_start();
include 'components/connect.php';

// Ensure the user is logged in; if not, redirect to the login page
if (!isset($_SESSION['user_id'])) {
    header('Location: user_login.php');
    exit();
}

$user_id = $_SESSION['user_id']; // Retrieve user ID from the session

// Fetch user orders with an optional search term for product names
$search_term = '';
if (isset($_POST['search'])) {
    $search_term = $_POST['search'];
}

$query = "SELECT * FROM `orders` WHERE user_id = ?"; // Query to get orders
$params = [$user_id];

if ($search_term) {
    $query .= " AND total_products LIKE ?";
    $params[] = "%" . $search_term . "%"; // Add search term to the query
}

$select_orders = $conn->prepare($query); // Prepare and execute the query
$select_orders->execute($params);
$user_orders = $select_orders->fetchAll(PDO::FETCH_ASSOC); // Get all matching orders

if (isset($_POST['cancel_order'])) {
   $order_id = $_POST['order_id'];
   $cancel_order = $conn->prepare("DELETE FROM `orders` WHERE id = ?");
   $cancel_order->execute([$order_id]);
   echo "Order cancelled successfully!";
}


if (isset($_POST['submit'])) {
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING); // Sanitize input
    $msg = filter_var($_POST['msg'], FILTER_SANITIZE_STRING);  // Correct key for message
    $order_id = filter_var($_POST['order_id'], FILTER_SANITIZE_STRING); // Correct order_id key
    $status = filter_var($_POST['status'], FILTER_SANITIZE_STRING); // Get status from POST data
    
    try {
        if (empty($user_id) || empty($order_id) || empty($name) || empty($status) || empty($msg)) {
         throw new Exception("All fields are required.");
      }

        $insert_message = $conn->prepare("INSERT INTO `messages` (user_id, order_id, name, status, message) VALUES (?, ?, ?, ?, ?)");
        $insert_message->execute([$user_id, $order_id, $name, $status, $msg]); // Execute the prepared statement
        
        if ($insert_message->rowCount() > 0) {
            echo 'Message sent successfully!';
        } else {
            echo 'Failed to send message.';
        }

    } catch (PDOException $pdoEx) {
        echo 'Database Error: ' . $pdoEx->getMessage();
    } catch (Exception $ex) {
        echo 'Error: ' . $ex->getMessage();
    }
}

// Fetch the correct order_id, you can set it through a POST or GET parameter
$order_id = isset($_GET['order_id']) ? filter_var($_GET['order_id'], FILTER_SANITIZE_STRING) : null;

// Initialize the `messages` array
$messages = [];

// Fetch messages for the given `order_id`
if ($order_id) {
    $select_messages = $conn->prepare("SELECT * FROM `messages` WHERE order_id = ?");
    $select_messages->execute([$order_id]); // Fetch messages for this order

    if ($select_messages->rowCount() > 0) {
        $messages = $select_messages->fetchAll(PDO::FETCH_ASSOC); // Get all matching messages
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Orders</title>

   <!-- Font Awesome CDN -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"> 
   <!-- Custom CSS -->
   <link rel="stylesheet" href="css/style.css">
   <!-- Additional CSS for Modal -->
   <style>
      .modal { /* Modal styling */
         display: none;
         position: fixed;
         top: 50%; 
         left: 50%; 
         transform: translate(-50%, -50%);
         background: white;
         padding: 20px;
         border-radius: 10px;
         z-index: 1000;
      }

      .modal-overlay {
         display: none;
         position: fixed;
         top: 0;
         left: 0;
         width: 100%;
         height: 100%;
         background: rgba(0, 0, 0, 0.5);
         z-index: 500;
      }

      .modal-button {
         background-color: #3498db;
         color: white;
         border-radius: 5px;
         border: none;
         padding: 8px 12px;
         cursor: pointer;
         margin-left: 10px;
      }

      .heading {
         display: flex;
         justify-content: space-between;
         align-items: center;
         margin-bottom: 20px;
      }

      .search-form {
         display: inline-flex;
         align-items: center;
      }

      .search-box {
         padding: 8px;
         border: 1px solid #ccc;
         border-radius: 5px;
      }

      .search-btn {
         background-color: #f39c12;
         color: white;
         border: none;
         padding: 8px 12px;
         border-radius: 5px;
         cursor: pointer;
         margin-left: 10px;
      }

      .box-container {
         display: flex;
         flex-wrap: wrap;
         justify-content: center;
         gap: 20px;
      }

      .order-card {
         background: #f7f7f7;
         border: 1px solid #ddd;
         border-radius: 10px;
         padding: 40px;
         width: 300px;
         text-align: left;
      }

      .order-details {
         font-size: 16px;
         line-height: 1.5;
      }

      .cancel-form {
         text-align: center;
         margin-top: 20px;
      }

      .cancel-btn {
         background-color: #e74c3c;
         color: white;
         border: none;
         padding: 8px 12px;
         border-radius: 5px;
         cursor: pointer;
      }
   </style>
</head>
<body>
   <?php include 'components/user_header.php'; ?> <!-- Include user header -->

   <section class="orders"> <!-- Orders section -->
      <div class="heading">
         <h1>Your Orders</h1> 

         <?php if (isset($_SESSION['user_id'])): ?> 
            <form action="" method="post" class="search-form"> 
               <input type="text" name="search" placeholder="Search orders..." class="search-box" value="<?= htmlspecialchars($search_term); ?>">
               <button type="submit" class="search-btn">Search</button>

               <button type="button" class="modal-button" onclick="openModal()">Admin Message</button> 
            </form>
         <?php endif; ?>

      </div>

      <div class="box-container"> <!-- Container for order cards -->
         <?php if (empty($user_orders)): ?>
             <p class="empty">You have not placed any orders yet.</p> <!-- Display if no orders -->
         <?php else: ?>
             <?php foreach ($user_orders as $order): ?> 
               
               <div class="order-card"> <!-- Display order details -->
                  <div class="order-details">
                     <p><strong>Placed on:</strong> <?= $order['placed_on']; ?></p>
                     <p><strong>Name:</strong> <?= $order['name']; ?></p>
                     <p><strong>Email:</strong> <?= $order['email']; ?></p>
                     <p><strong>Address:</strong> <?= $order['address']; ?></p>
                     <p><strong>Payment Method:</strong> <?= $order['method']; ?></p>
                     <p><strong>Total Products:</strong> <?= $order['total_products']; ?></p>
                     <p><strong>Total Price:</strong> ₱<?= $order['total_price']; ?></p>
                     <p><strong>Payment Status:</strong> 
                        <span style="color: <?= ($order['payment_status'] === 'pending') ? 'red' : 'green'; ?>">
                           <?= $order['payment_status']; ?>
                        </span>
                     </p>
                  </div>

                  <form action="" method="post" class="cancel-form">
                     <input type="hidden" name="order_id" value="<?= $order['id']; ?>"> 
                     <button type="submit" name="cancel_order" class="cancel-btn" onclick="return confirm('Cancel this order?');">Cancel Order</button> 
                  </form>
               </div> <!-- End order-card -->
            <?php endforeach; ?>
         <?php endif; ?>
      </div> <!-- End box-container -->
   </section> 

<!-- The modal for admin messages -->
<?php if (isset($_SESSION['user_id'])): ?> 
<div class="modal-overlay" id="modal-overlay">
    <div class="modal" id="modal">
        <span class="modal-close" onclick="closeModal()">✖</span> <!-- Close button -->
        <h2>Admin Messages</h2>

        <!-- Loop through all fetched messages for this order -->
        <?php if (!empty($messages)): ?>
            <?php foreach ($messages as $msg): ?>
            <div class="message">
                <p><strong>Name:</strong> <?= $msg['name']; ?></p>
                <p><strong>Status:</strong> <?= $msg['status']; ?></p>
                <p><strong>Message:</strong> <?= $msg['message']; ?></p>
                <hr> <!-- Divider between messages -->   
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No messages for this order.</p> <!-- Display when no messages -->
        <?php endif; ?>
    </div>
</div>
<?php endif; ?> <!-- End check for logged-in user -->

   <!-- JavaScript for modal functionality -->
   <script>
        // Ensure proper modal functionality
        function closeModal() {
            document.getElementById("modal").style.display = "none";
            document.getElementById("modal-overlay").style.display = "none";
        }

        function openModal() {
            document.getElementById("modal").style.display = "block";
            document.getElementById("modal-overlay").style.display = "block";
        }
        
    function cancelOrder(event, orderId) {
        event.preventDefault(); // Prevent default form submission behavior

        // Fetch the form data
        const formData = new FormData(document.getElementById(`cancelForm${orderId}`));

        // Send a POST request to cancel the order
        fetch('orders.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (response.ok) {
                alert('Order cancelled successfully!');
                // Hide the canceled order card
                const orderCard = document.getElementById(`orderCard${orderId}`);
                orderCard.style.display = 'none';
            } else {
                throw new Error('Failed to cancel order.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to cancel order. Please try again.');
        });
    }


    </script>

   <script src="js/script.js"></script> <!-- Load external script -->

</body>
</html>

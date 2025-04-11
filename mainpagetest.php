<?php
session_start();


$servername = "localhost";
$username = "root";
$password = "password";
$dbname = "UsTechComputers";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch data from the Inventory table
$sqlFetchInventory = "SELECT * FROM Inventory";
$result = $conn->query($sqlFetchInventory);


$sqlLastModified = "SELECT * FROM Inventory ORDER BY LastModified DESC LIMIT 5";
$resultLastModified = $conn->query($sqlLastModified);

// Check if the user is authenticated
if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated']) {
    header('Location: login.php');
    exit;
}

$totalProductsQuery = "SELECT COUNT(*) as total FROM Inventory"; 
$totalProductsResult = mysqli_query($conn, $totalProductsQuery);
$totalProductsRow = mysqli_fetch_assoc($totalProductsResult);
$totalProductsCount = $totalProductsRow['total'];

// Get count of out-of-stock products
$outOfStockQuery = "SELECT COUNT(*) as outOfStock FROM Inventory WHERE Quantity = 0";  
$outOfStockResult = mysqli_query($conn, $outOfStockQuery);
$outOfStockRow = mysqli_fetch_assoc($outOfStockResult);
$outOfStockCount = $outOfStockRow['outOfStock'];

// Get count of low-in-stock products (below 10)
$lowInStockQuery = "SELECT COUNT(*) as lowInStock FROM Inventory WHERE Quantity > 0 AND Quantity < 10"; 
$lowInStockResult = mysqli_query($conn, $lowInStockQuery);
$lowInStockRow = mysqli_fetch_assoc($lowInStockResult);
$lowInStockCount = $lowInStockRow['lowInStock'];

$sqlFetchLowStock = "SELECT * FROM Inventory WHERE Quantity > 0 AND Quantity < 10 ORDER BY Quantity ASC";
$sqlFetchOutOfStock = "SELECT * FROM Inventory WHERE Quantity = 0";

$resultLowStock = mysqli_query($conn, $sqlFetchLowStock);
$resultOutOfStock = mysqli_query($conn, $sqlFetchOutOfStock);



?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="shortcut icon" type="x-icon" href="main logo.png">
  <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
  <link rel="manifest" href="/site.webmanifest">
  <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
  <meta name="msapplication-TileColor" content="#da532c">
  <meta name="theme-color" content="#ffffff">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" integrity="sha384-rwqpQJebfE8M3Ps2bV8E5kUwSdbfPfOKYm1PYaIlu6+5nJtUqeGxhjFVoJQw9Avn" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel = "stylesheet" href = "STYLE.css">
    <title>UsTech Inventory</title>
</head>
<body>
<header>
    <nav>
      <div class="nav-left">
        <div id="sidebar-toggle" class="color">&#9776;</div>
        <a href="#">
          <i class="fa fa-bell" style="font-size: x-large; margin-left: 10px;"></i>
        </a>
        <a href ="mainpage.php"><img src="main logo.png" class="logo" id="authImage"></a>
        <div id="authResult"></div>
      </div>
      <div class="nav-middle navbar">
        <a href="mainpage.php" class="dash highlight">Dashboard</a>
        <a href="inventory.php" class="stock">Inventory</a>
        <a href="report.php" class="report">Report</a>
      </div>
      <div class="nav-right">
        <div id="search-bar" class="search">
        </div>
      </div>

    </nav>
  </header>

  <div id="sidebar">
    <div style="background-image: url('ImageLocation');background-size: cover;">
      <img src="profile.svg" class="profile" style="height:150px;">
    </div>
    <p style="color:white;">Employee Name</p>
    
    <a href="#">Settings</a>

    <form method="post" action="logout.php">
        <button type="submit" class="logout"><u>Logout</u></button>
    </form>
  </div>
  <div class="main-content">
  <div class="welcome-heading">
    <h1>Welcome, Admin!</h1>
  </div>
  <div class="grid-container">
    <div class="nav-bar">
    </div>
    <div class="grid-item large-widget">
    <h1 class="typo-head">TOTAL PRODUCTS</h1>
    <p class="typo-para"><?php echo $totalProductsCount; ?> PRODUCTS</p>
</div>

<div class="grid-item">
    <h1 class="typo-head">PRODUCTS OUT OF STOCK</h1>
    <p class="typo-para"><?php echo $outOfStockCount; ?> PRODUCTS</p>
</div>

<div class="grid-item">
    <h1 class="typo-head">PRODUCTS LOW IN STOCK</h1>
    <p class="typo-para"><?php echo $lowInStockCount; ?> PRODUCTS</p>
</div>






<div class="grid-item large-widget1">
    <div class="gridwidg5">
        <div class="headerwidg5">
            <h1 class="typo-para-large">Low and Out of Stock</h1>
        </div>
        <div class="table__body">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Quantity</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    
                  // Display out of stock items
                    while ($row = mysqli_fetch_assoc($resultOutOfStock)) {
                        echo "<tr>";
                        echo "<td>" . $row['ProductID'] . "</td>";
                        echo "<td>" . $row['Name'] . "</td>";
                        echo "<td>" . $row['Quantity'] . "</td>";
                        echo "<td>Out of Stock</td>";
                        echo "</tr>";
                    }
                    // Display low stock items
                    while ($row = mysqli_fetch_assoc($resultLowStock)) {
                        echo "<tr>";
                        echo "<td>" . $row['ProductID'] . "</td>";
                        echo "<td>" . $row['Name'] . "</td>";
                        echo "<td>" . $row['Quantity'] . "</td>";
                        echo "<td>Low Stock</td>";
                        echo "</tr>";
                    }

                    
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>




    <div class="grid-item large-widget">
      <h1 class="typo-para"> Last 5 Modified Records</h1>
      <div class="table__body">
        <?php

try {
    if ($resultLastModified === false) {
        throw new Exception("Query failed: " . $conn->error);
    }
    if ($resultLastModified->num_rows > 0) {
        echo "<table class='recently-updated-table'>
            <thead>
                <tr>
                    <th>Product ID</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Last Modified</th>
                </tr>
            </thead>
            <tbody>";
    
        while ($row = $resultLastModified->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["ProductID"] . "</td>";
            echo "<td>" . $row["Name"] . "</td>";
            echo "<td>" . $row["Quantity"] . "</td>";
            echo "<td>" . $row["LastModified"] . "</td>";
            echo "</tr>";
        }
    
        echo "</tbody></table>";
    } else {
        echo "No records found.";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

?>
        </table>
      </div>
    </div>

    <?php
$sql = "SELECT category, COUNT(*) as count FROM Inventory GROUP BY category";
$result = mysqli_query($conn, $sql);

    if ($result) {
    $categoryCounts = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $category = $row['category'];
        $count = $row['count'];

        $categoryCounts[$category] = $count;
    }


    echo '<div class="grid-item special-item"><a href="#" class="grid11">
            <h1 class="text11">' . $categoryCounts['Core'] . '<h2 class="text12">Core Components</h2></h1>
            <img class="image11" src="pic1.svg"> 
          </a></div>';

    echo '<div class="grid-item special-item"><a href="#" class="grid11">
            <h1 class="text11">' . $categoryCounts['Storage'] . '<h2 class="text12">Storage Devices</h2></h1>
            <img class="image11" src="pic2.svg"> 
          </a></div>';

    echo '<div class="grid-item special-item"><a href="#" class="grid11">
            <h1 class="text11">' . $categoryCounts['Network'] . '<h2 class="text12">Networking</h2></h1>
            <img class="image11" src="pic3.svg"> 
          </a></div>';

    echo '<div class="grid-item special-item"><a href="#" class="grid11">
            <h1 class="text11">' . $categoryCounts['Peripherals'] . '<h2 class="text12">Peripherals</h2></h1>
            <img class="image11" src="pic4.svg"> 
          </a></div>';

} else {
    echo 'Error: ' . mysqli_error($conn);
}

?>


  </div>
</div>

<script>
  const sidebarToggle = document.getElementById('sidebar-toggle');
  const sidebar = document.getElementById('sidebar');
  const mainContent = document.getElementById('main-content');

  sidebarToggle.addEventListener('click', () => {
    if (sidebar.style.left === '0px') {
      sidebar.style.left = '-250px';
      mainContent.style.marginLeft = '0';
    } else {
      sidebar.style.left = '0';
      mainContent.style.marginLeft = '250px';
    }
  });


  document.addEventListener("DOMContentLoaded", function () {
  const authImage = document.getElementById("authImage");
  const authResult = document.getElementById("authResult");

  authImage.addEventListener("auxclick", function (event) {
    // Check if the middle mouse button is clicked (event.button === 1)
    if (event.button === 1) {
      event.preventDefault();
      authenticateUser();
    }
  });

  function authenticateUser() {
    // Send a request to the server to authenticate the user
    const xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
      if (xhr.readyState === 4) {
        if (xhr.status === 200) {
          const response = JSON.parse(xhr.responseText);
          authResult.textContent = response.message;
        } else {
          authResult.textContent = 'Error during authentication.';
        }
      }
    };

    // Send a POST request to authenticate.php without providing a password
    xhr.open("POST", "login.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send();

  }
});






</script>

</body>
</html>
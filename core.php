<?php
session_start();

//Check if the user is authenticated
if (!isset($_SESSION['check']) || !$_SESSION['check']) {
    header('Location: login.php');
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "UsTechComputers";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch data from the Inventory table
$sqlFetchInventory = "SELECT * FROM Inventory WHERE category LIKE '%Core%'";
$result = $conn->query($sqlFetchInventory);



// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
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
    <link rel="stylesheet" href="Inventory.css">
    <title>Core</title>
     
    
</head>
<body>
  

    <!-- Navbar -->
    <header>
        <nav>
            <div class="nav-left">
                <div id="sidebar-toggle" class="color">&#9776;</div>
                <a href="#">
                    <i class="fa fa-bell" style="font-size: x-large; margin-left: 10px;"></i>
                </a>
                <a href="mainpage.php"><img src="main logo.png" class="logo"></a>
            </div>
            <div class="nav-middle navbar">
                <a href="mainpage.php" class="dash">Dashboard</a>
                <a href="inventory.php" class="stock highlight">Inventory</a>
                <a href="report.php" class="report ">Report</a>
            </div>
            <div class="nav-right">
                <div id="search-bar" class="search"></div>
            </div>
        </nav>
    </header>
    
    <!-- change -->
    <!-- sidebar -->
    <div id="sidebar">
        <div style="background-image: url('ImageLocation');background-size: cover;">
            <img src="profile.svg" class="profile" style="height:150px;">
        </div>
        <p style="color:white;">Employee Name</p>

        <div class="nav-sidebar">
            <a href="mainpage.php" class="dash">Dashboard</a>
            <a href="inventory.php" class="stock">Inventory</a>
            <a href="report.php" class="report">Report</a>
        </div>


        <form method="post" action="logout.php">
            <button type="submit" class="logout"><u>Logout</u></button>
        </form>
    </div>

    <div class="main-content">

            <!--table-->
            <div class="grid-item right-grid" style="margin: 7mm; margin-top: 30mm;">
                <section class="main-table">
                    <div class="table__body">
                    <table class="table-body" border="0">
                        <thead>
                            <tr>
                                <th>Product ID </th>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Brand</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Restock Date</th>
                                <th>Stock Status</th>
                            </tr>
                        </thead>
                        <tbody>
        <?php
        if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["ProductID"] . "</td>";
            echo "<td>" . $row["Name"] . "</td>";
            echo "<td>" . $row["Category"] . "</td>";
            echo "<td>" . $row["BrandName"] . "</td>";
            echo "<td>" . $row["Quantity"] . "</td>";
            echo "<td>" . $row["Price"] . "</td>";
            echo "<td>" . $row["RestockDate"] . "</td>";
            echo "<td>" . $row["Status"] . "</td>";
            echo "</tr>";
        }
        
        } else {
            echo "<tr><td colspan='8'>No records found</td></tr>";
        }
        ?>
         </tbody>
    </table>
                    
                    </div>
                </section>
            </div>

        </div>
    </div>
    </div>
    </div>








    <script>
    function toggleForms() {
            var addQuantityForm = document.getElementById('addQuantityForm');
            var addItemForm = document.getElementById('addItemForm');

            if (addQuantityForm.style.display === 'block') {
                addQuantityForm.style.display = 'none';
                addItemForm.style.display = 'block';
            } else {
                addQuantityForm.style.display = 'block';
                addItemForm.style.display = 'none';
            }
        }



        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        const navMiddle = document.querySelector('.nav-middle');

        sidebarToggle.addEventListener('click', () => {
            if (sidebar.style.left === '0px') {
                sidebar.style.left = '-250px';
                mainContent.style.marginLeft = '0';
                navMiddle.style.display = 'flex';
            } else {
                sidebar.style.left = '0';
                mainContent.style.marginLeft = '250px';
                navMiddle.style.display = 'none';
            }
        });



    

    
  </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-rqMy0lWj52xnt3SH8YL5nLrj6f8aGfZrFUxuFfW/dO6Gu9PvZDR9otKHG7REKk3l" crossorigin="anonymous"></script>
    
</body>
</html>


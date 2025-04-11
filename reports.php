<?php
session_start();
// Connect to your database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "UsTechComputers";
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['check']) || !$_SESSION['check']) {
    header('Location: login.php');
    exit;
}

// Fetch data from the Inventory table
$sqlFetchInventory = "SELECT ProductID, Name, Category, BrandName, Quantity, Price FROM Inventory;";
$result = $conn->query($sqlFetchInventory);

// Generate CSV file
if (isset($_GET['export']) && $_GET['export'] == 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="database_export.csv"');

    $output = fopen('php://output', 'w');

    // Output column headings
    fputcsv($output, array('Product ID', 'Product Name', 'Category', 'Brand', 'Quantity', 'Price'));

    // Output data from rows
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }

    fclose($output);
    $conn->close();
    exit;
}


?>





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
    <link rel="stylesheet" href="report.css">
    <title>Report</title>



    <style>
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-20px);
            }
            60% {
                transform: translateY(-10px);
            }
        }

        .marquee {
            
            padding:50px;
            white-space: nowrap;
            overflow: hidden;
            box-sizing: border-box;
            animation: bounce 4s infinite;
            justify-content: center;
            position: relative;
        }
    </style>

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
                <a href="inventory.php" class="stock">Inventory</a>
                <a href="report.php" class="report highlight">Report</a>
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

    <div class="message-container">
    <div class="excel-heading">
        <h1>DOWNLOAD THE REPORT IN EXCEL:</h1>
        <div>
        <button class="marquee excel" onclick="exportToExcel()">CLICK ME!!!!</button>
    </div>
    </div>




    <script>

    function exportToExcel() {
            window.location.href = 'report.php?export=csv';
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
</body>
</html>
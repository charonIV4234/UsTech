<?php
session_start();

//Check if the user is authenticated
if (!isset($_SESSION['check']) || !$_SESSION['check']) {
    header('Location: login.php');
    exit;
}

if (isset($_SESSION['user']) && $_SESSION['user']) {
    header('Location: stocks.php');
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
$searchInput = isset($_GET['search']) ? $_GET['search'] : '';
$sqlFetchInventory = "SELECT * FROM Inventory";

// Add a WHERE clause for filtering based on the search input
if (!empty($searchInput)) {
    $sqlFetchInventory .= " WHERE 
        Name LIKE '%$searchInput%' OR
        Category LIKE '%$searchInput%' OR
        BrandName LIKE '%$searchInput%' OR
        Status LIKE '%$searchInput%'";
}

$result = $conn->query($sqlFetchInventory);

if ($result === false) {
    die("Query failed: " . $conn->error);
}

$sqlLastModified = "SELECT * FROM Inventory ORDER BY LastModified DESC LIMIT 5";
$resultLastModified = $conn->query($sqlLastModified);




//start
// Handle form submission to add a new item or update existing
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addQuantity']) && is_numeric($_POST['addQuantity'])) {
        // Handle adding quantity to an existing item
        $productId = $_POST['productId'];
        $addQuantity = $_POST['addQuantity'];
    
        // Fetch old quantity before the update
        $sqlFetchOldQuantity = "SELECT Quantity FROM Inventory WHERE ProductID = $productId";
        $resultOldQuantity = $conn->query($sqlFetchOldQuantity);
    
        if ($resultOldQuantity && $rowOldQuantity = $resultOldQuantity->fetch_assoc()) {
            $oldQuantity = $rowOldQuantity['Quantity'];
    
            // Perform the update in the database
            $sqlUpdateQuantity = "UPDATE Inventory SET Quantity = Quantity + $addQuantity, LastModified = CURRENT_TIMESTAMP WHERE ProductID = $productId";
    
            if ($conn->query($sqlUpdateQuantity) === TRUE) {
                // Fetch new quantity after the update
                $sqlFetchNewQuantity = "SELECT Quantity FROM Inventory WHERE ProductID = $productId";
                $resultNewQuantity = $conn->query($sqlFetchNewQuantity);
    
                if ($resultNewQuantity && $rowNewQuantity = $resultNewQuantity->fetch_assoc()) {
                    $newQuantity = $rowNewQuantity['Quantity'];
    
                    // Log the quantity update in the QuantityUpdates table
                    $sqlInsertQuantityUpdate = "INSERT INTO Updates (ProductID, UpdatedColumn, OldValue, NewValue)
                                                VALUES ($productId, 'Quantity', '$oldQuantity', '$newQuantity')";
                
    
                    if ($conn->query($sqlInsertQuantityUpdate) !== TRUE) {
                        $conn->error;
                    } 
                    else {
                        header("Refresh:0");
                    }
                } 
            }
            else { $conn->error;
                
                }

            }

       exit; }

    // Check if it's an edit form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editProductId'])) {
      $editProductId = $_POST['editProductId'];
    $editName = $_POST['editName'];
    $editCategory = $_POST['editCategory'];
    $editBrandName = $_POST['editBrandName'];
    $editPrice = $_POST['editPrice'];
    $editRestockDate = $_POST['editRestockDate'];
    $editStatus = $_POST['editStatus'];

    $sqlFetchOldValues = "SELECT Name, Category, BrandName, Price, RestockDate FROM Inventory WHERE ProductID = $editProductId";
    $resultOldValues = $conn->query($sqlFetchOldValues);

    if ($resultOldValues && $rowOldValues = $resultOldValues->fetch_assoc()) {
        $oldName = $rowOldValues['Name'];
        $oldCategory = $rowOldValues['Category'];
        $oldBrandName = $rowOldValues['BrandName'];
        $oldPrice = $rowOldValues['Price'];
        $oldRestockDate = $rowOldValues['RestockDate'];

    // Perform the update in the database
    $sqlUpdateItem = "UPDATE Inventory SET 
        Name = '$editName', 
        Category = '$editCategory', 
        BrandName = '$editBrandName', 
        Price = $editPrice, 
        RestockDate = '$editRestockDate', 
        Status = '$editStatus',
        LastModified = CURRENT_TIMESTAMP
        WHERE ProductID = $editProductId";



if ($conn->query($sqlUpdateItem) === TRUE) {
    // Fetch new values after the update
    $sqlFetchNewValues = "SELECT Name, Category, BrandName, Price, RestockDate FROM Inventory WHERE ProductID = $editProductId";
    $resultNewValues = $conn->query($sqlFetchNewValues);

    if ($resultNewValues && $rowNewValues = $resultNewValues->fetch_assoc()) {
        $newName = $rowNewValues['Name'];
        $newCategory = $rowNewValues['Category'];
        $newBrandName = $rowNewValues['BrandName'];
        $newPrice = $rowNewValues['Price'];
        $newRestockDate = $rowNewValues['RestockDate'];

        // Compare old and new values
        $changes = array();

        if ($oldName != $newName) {
            $changes[] = array('Column' => 'Name', 'OldValue' => $oldName, 'NewValue' => $newName);
        }

        if ($oldCategory != $newCategory) {
            $changes[] = array('Column' => 'Category', 'OldValue' => $oldCategory, 'NewValue' => $newCategory);
        }

        if ($oldBrandName != $newBrandName) {
            $changes[] = array('Column' => 'BrandName', 'OldValue' => $oldBrandName, 'NewValue' => $newBrandName);
        }

        if ($oldPrice != $newPrice) {
            $changes[] = array('Column' => 'Price', 'OldValue' => $oldPrice, 'NewValue' => $newPrice);
        }

        if ($oldRestockDate != $newRestockDate) {
            $changes[] = array('Column' => 'RestockDate', 'OldValue' => $oldRestockDate, 'NewValue' => $newRestockDate);
        }

        // Log the changes in the Updates table
        if (!empty($changes)) {
            foreach ($changes as $change) {
                $column = $change['Column'];
                $oldValue = $change['OldValue'];
                $newValue = $change['NewValue'];

                $sqlInsertUpdate = "INSERT INTO Updates (ProductID, UpdatedColumn, OldValue, NewValue)
                                    VALUES ($editProductId, '$column', '$oldValue', '$newValue')";

                if ($conn->query($sqlInsertUpdate) !== TRUE) {
                    $conn->error;
                }
            }

        } else {
        }
    } else {
        $conn->error;
    }
} else {
    $conn->error;
}
} else {
    $conn->error;
}
}


    else {
        // Handle form submission to add a new item
        $newName = $_POST['newName'];
        $newCategory = $_POST['newCategory'];
        $newBrandName = $_POST['newBrandName'];
        $newQuantity = $_POST['newQuantity'];
        $newPrice = $_POST['newPrice'];
        $newRestockDate = $_POST['newRestockDate'];
        $newStatus = $_POST['newStatus'];

        if (!empty($newName) && !empty($newCategory) && !empty($newBrandName) && is_numeric($newQuantity) && is_numeric($newPrice) && !empty($newRestockDate) && !empty($newStatus)) {
            // Check if the item already exists
            $sqlCheckItem = "SELECT * FROM Inventory WHERE Name = '$newName' AND BrandName = '$newBrandName' AND Price = $newPrice AND Category = '$newCategory'";
            $resultCheckItem = $conn->query($sqlCheckItem);

            if ($resultCheckItem->num_rows > 0) {
                // Item already exists, update the quantity, restock date, and stock status
                $row = $resultCheckItem->fetch_assoc();
                $existingQuantity = $row['Quantity'];
                $existingRestockDate = $row['RestockDate'];
                $existingStatus = $row['Status'];

                $updatedQuantity = $existingQuantity + $newQuantity;
                $updatedRestockDate = $newRestockDate;
                $updatedStatus = $newStatus;

                $sqlUpdateItem = "UPDATE Inventory SET Quantity = $updatedQuantity, RestockDate = '$updatedRestockDate', Status = '$updatedStatus' WHERE Name = '$newName' AND BrandName = '$newBrandName' AND Price = $newPrice AND Category = '$newCategory'";

                if ($conn->query($sqlUpdateItem) === TRUE) {
                    echo "Item updated successfully";
                } else {
                    echo "Error updating item: " . $conn->error;
                }
            } else {
                // Item does not exist, insert a new record
                $sqlInsert = "INSERT INTO Inventory (Name, Category, BrandName, Quantity, Price, RestockDate, Status) VALUES ('$newName', '$newCategory', '$newBrandName', $newQuantity, $newPrice, '$newRestockDate', '$newStatus')";

                if ($conn->query($sqlInsert) === TRUE) {
                    echo "New item added successfully";
                } else {
                    echo "Error adding new item: " . $conn->error;
                }
            }

            header("Refresh:0");
        } else {
            echo "Invalid input for adding or updating an item";
        }
    }
}


//end



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
    <title>Inventory</title>
     
    <script>
        function editItem(productId, name, category, brandName, quantity, price, restockDate, status) {
            // Set values in the modal
            document.getElementById('editProductId').value = productId;
            document.getElementById('editName').value = name;
            document.getElementById('editCategory').value = category;
            document.getElementById('editBrandName').value = brandName;
            document.getElementById('editPrice').value = price;
            document.getElementById('editStatus').value = status;

            // Show the modal
            document.getElementById('modal').style.display = 'flex';
        }

        function closeEditForm() {
            // Close the modal
            document.getElementById('modal').style.display = 'none';
        }

        
    </script>
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


    <!-- search portion -->
    <div class="main-content">
        <div class="welcome-heading">
        <h1>Inventory System</h1>
        </div>
        <div class="nav-right" style="align-items: center;align-content: center;margin: 10mm;margin-bottom: 1mm;justify-content: center;">
        <div class="search">
        <form method="get" action="">
                <input type="text" id="search-input1" name="search" placeholder="Search..." value="<?php echo htmlspecialchars($searchInput); ?>">
                <button type="submit" class="button2"><i class="fa fa-search"></i></button>
            </form>

        </div>
        </div>


        <!-- add item --> 
        <div class="grid-container">
            <div class="grid-item left-grid">
                <div class="section">
                    <h2>Add New Item</h2>
                    <form method="post" action="">
                        <label>Name: </label>
                        <input type="text" name="newName" required><br>
                        <label>Category: </label>
                        <select name="newCategory" required>
                            <option value="Core">CORE COMPONENTS</option>
                            <option value="Storage">STORAGE DEVICES</option>
                            <option value="Network">NETWORKING</option>
                            <option value="Peripherals">PERIPHERALS</option>
                        </select><br>
                        <label>Brand Name: </label>
                        <input type="text" name="newBrandName" required><br>
                        <label>Quantity: </label>
                        <input type="number" name="newQuantity" required><br>
                        <label>Price: </label>
                        <input type="number" step="0.01" name="newPrice" required><br>
                        <label>Restock Date (YYYY-MM-DD): </label>
                        <input type="text" name="newRestockDate" value="<?php echo date("Y-m-d") ?>" required><br>
                        <label>Status: </label>
                        <select name="newStatus" required>
                            <option value="In Stock">In Stock</option>
                            <option value="Out of Stock">Out of Stock</option>
                        </select><br>
                        <button type="submit">Add Item</button>
                    </form>
                    <hr>

                    <!-- add quantity on existing -->
                    <div id="addQuantityForm">
                        <h2>Add Quantity to Item</h2>
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <label>Product ID: </label>
                            <input type="number" name="productId" required><br>
                            <label>Add Quantity: </label>
                            <input type="number" name="addQuantity" required><br>
                            <button type="submit">Add Quantity</button>
                        </form>
                    </div>
                </div>
            </div>


            <!--table-->
            <div class="grid-item right-grid">
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
                                <th>Action</th>
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

            echo "<td><button onclick=\"editItem('{$row["ProductID"]}', '{$row["Name"]}', '{$row["Category"]}', '{$row["BrandName"]}', '{$row["Price"]}', '{$row["RestockDate"]}', '{$row["Status"]}');\">Edit</button></td>";
            echo "</tr>";
        }
        
        } else {
            echo "<tr><td colspan='9'>No records found</td></tr>";
        }
        ?>
         </tbody>
    </table>
                    
                    </div>
                </section>
            </div>

        </div>

        <!-- Edit popup -->
        <div id="modal" class="modal">
            <div class="modal-content">
                <span class="close-btn-modal" onclick="closeEditForm()">&times;</span>
                <h2>Edit Item</h2>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <input type="hidden" name="editProductId" id="editProductId">
                    <label>Name: </label>
                    <input type="text" name="editName" id="editName" required><br>
                    <label>Category: </label>
                    <select name="editCategory" id="editCategory" required>
                    <option value="Core">CORE COMPONENTS</option>
                    <option value="Storage">STORAGE DEVICES</option>
                    <option value="Network">NETWORKING</option>
                    <option value="Peripherals">PERIPHERALS</option>
                    </select><br>
                    <label>Brand Name: </label>
                    <input type="text" name="editBrandName" id="editBrandName" required><br>
                    <label>Price: </label>
                    <input type="number" step="0.01" name="editPrice" id="editPrice" required><br>
                    <label>Restock Date (YYYY-MM-DD): </label>
                    <input type="text" name="editRestockDate" id="editRestockDate" value="<?php echo date("Y-m-d")?>" required><br>
                    <label>Status: </label>
                    <select name="editStatus" id="editStatus">
                    <option value="In Stock">In Stock</option>
                    <option value="Out of Stock">Out of Stock</option>
                    </select><br>
                    <button type="submit">Save Changes</button>
                    <button type="button" onclick="closeEditForm()">Cancel</button>
                </form>
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



    
    //last edit here

    
  </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-rqMy0lWj52xnt3SH8YL5nLrj6f8aGfZrFUxuFfW/dO6Gu9PvZDR9otKHG7REKk3l" crossorigin="anonymous"></script>
    
</body>
</html>


<?php
// Provides the database login
require_once "../misc/dbconfig.php";

// Provides functions for viewing item information
require_once "../Models/category-db.php";
require_once "../Models/location-db.php";
require_once "../Models/view-db.php";

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700">
    <link rel="stylesheet" href="../style.css">
    <title>View/Print Inventory</title>
    <script>
        function printData() {
            let divToPrint = document.getElementById("printTable");
            newWin = window.open("");
            newWin.document.write(divToPrint.outerHTML);
            newWin.print();
            newWin.close();
        }
    </script>
</head>

<body>
    <?php
    // check if user id exists
    if (!isset($_COOKIE['userID'])) {
        echo "Please login first!";
        die;
    }

    $user_id = $_COOKIE['userID'];
    ?>

    <div class="head-body">
        <div class="nav-container">
            <nav class="navbar">
                <h1 class="navbar-logo">Tots and Blocks IMS</h1>
                <div class="menu-toggle">
                    <span class="bar"></span>
                    <span class="bar"></span>
                    <span class="bar"></span>
                </div>
                <ul class="nav-menu">
                    <li><a href="./login.php" class="nav-links">Home</a></li>
                    <li><a href="./stock_actions.php" class="nav-links">Manage Stock</a></li>
                    <li><a href="./item_actions.php" class="nav-links">Manage Items</a></li>
                    <li><a href="./view_actions.php" class="nav-links">View Inventory</a></li>
                    <li><a href="./logout.php" class="nav-links nav-links-btn">Logout</a></li>
                </ul>
            </nav>
        </div>
    </div>
    <div class="login-body">
        <div class="center view-tab">
            <h2 class="panel-title">View/Print Inventory</h2>
            <form name="search_items" method="POST">
                <p>
                    <label for="category"> Category:
                        <select name="item_category" id="category" required>
                            <option value="*">ALL</option>
                            <?php
                            get_item_categories();
                            ?>
                        </select>
                    </label>
                    <label for="location">Location:
                        <select name="item_location" id="location" required>
                            <option value="*">ALL</option>
                            <?php
                            get_locations();
                            ?>
                        </select>
                    </label>
                </p>
                <p>
                    <label>
                        Quantity is
                        <select name="comparison_op" required>
                            <option value="">Select one</option>
                            <option value="=">equal to</option>
                            <option value="<">less than</option>
                            <option value="<=">less than or equal to</option>
                            <option value=">">greater than</option>
                            <option value=">=">greater than or equal to</option>
                        </select>
                        <input type="number" name="quantity-filter" min="0" max="1000" default="0" required>
                    </label>
                </p>

                <p>
                    Quantity Sort:<br>
                    <label for="quantity-nosort">
                        <input type="radio" name="quantity-sort" id="quantity-nosort" value="q-none" checked required>
                        None
                    </label>
                    <label for="quantity-ascsort">
                        <input type="radio" name="quantity-sort" id="quantity-ascsort" value="q-asc">
                        Ascending
                    </label>
                    <label for="quantity-descsort">
                        <input type="radio" name="quantity-sort" id="quantity-descsort" value="q-desc">
                        Descending
                    </label>
                </p>
                <p>
                    Item Name Sort:<br>
                    <label for="name-nosort">
                        <input type="radio" name="name-sort" id="name-nosort" value="n-none" checked required>
                        None
                    </label>
                    <label for="name-ascsort">
                        <input type="radio" name="name-sort" id="name-ascsort" value="n-asc">
                        Ascending
                    </label>
                    <label for="name-descsort">
                        <input type="radio" name="name-sort" id="name-descsort" value="n-desc">
                        Descending
                    </label>
                </p>
                <p><input class="submit-btn" type="submit" value="Search" name="btnSearch"></p>
                <?php
                if (isset($_POST['btnSearch'])) {
                    $item_category = $_POST['item_category'];
                    $item_location = $_POST['item_location'];
                    $quantity_comparison = $_POST['comparison_op'];
                    $quantity_number = $_POST['quantity-filter'];
                    $quantity_sort = $_POST['quantity-sort'];
                    $name_sort = $_POST['name-sort'];
                    // echo "category: $itemCategory <br>";
                    // echo "location: $itemLocation <br>";
                    // echo "comparison: $quantityComparison <br>";
                    // echo "number: $quantityNumber <br>";
                    // successful is a table with at least 1 result is returned
                    $successful = display_inventory($item_category, $item_location, $quantity_comparison, $quantity_number, $quantity_sort, $name_sort);
                    if ($successful)
                        echo "<br><br><button class='submit-btn' onclick=printData()>Print Table</button>";
                }
                ?>
            </form>
        </div>

    </div>
</body>

</html>
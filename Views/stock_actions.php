<?php
// Provides the database login
require_once "../misc/dbconfig.php";

// Provides functions for manipulating stock
require_once "../Models/quantity-db.php";
require_once "../Models/location-db.php";

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Stock</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700">
    <link rel="stylesheet" type="text/css" href="../style.css" />
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
        <div class="left">
            <h2 class="panel-title">Insert New item Stock Information</h2>
            <form name="insert_item_stockinfo" method="POST">
                <p>Item Name:
                    <select name="item_insert_code" required>
                        <option value="">Select Item</option>
                        <?php get_item_names_incomplete(); ?>
                    </select>
                </p>
                <p> Location:
                    <select name="item_insert_location" required>
                        <option value="">Select Location</option>
                        <?php get_locations(); ?>
                    </select>
                </p>
                <p>Quantity:
                    <input type="number" name="item_insert_quantity" min="1" max="99" required>
                </p>
                <p><input class="submit-btn" type="submit" value="Insert Item Info" name="btnSubmitInsert"></p>
                <?php
                if (isset($_POST['btnSubmitInsert'])) {
                    $item_code = $_POST['item_insert_code'];
                    $item_location = $_POST['item_insert_location'];
                    $item_quantity = $_POST['item_insert_quantity'];
                    if (item_stock_info_exists($item_code, $item_location)) {
                        echo "There is already a record with same Item Code + Location";
                    } else {
                        insert_item_stock($item_code, $item_location, $item_quantity, $user_id);
                    }
                }
                ?>
            </form>
        </div>
        <div class="center">
            <h2 class="panel-title">Update Item Quantity</h2>
            <form name="update-itemquantity" method="POST">
                <p>Item Name:
                    <select name="item_update_code" required>
                        <option value="">Select Item</option>
                        <?php get_item_names_for_update_delete(); ?>
                    </select>
                </p>
                <p> Location:
                    <select name="item_update_location" required>
                        <option value="">Select Location</option>
                        <?php get_locations(); ?>
                    </select>
                </p>
                <p>Quantity:
                    <input type="number" name="item_update_quantity" min="1" max="99" required>
                </p>
                <p>
                    <input type="radio" id="add" value="add" name="update_action" required>
                    <label for="add">Add</label>
                    <input type="radio" id="delete" value="del" name="update_action">
                    <label for="delete">Delete</label>
                </p>
                <p><input class="submit-btn" type="submit" value="Update Item Info" name="btnSubmitUpdate"></p>
                <?php
                if (isset($_POST['btnSubmitUpdate'])) {
                    $item_code = $_POST['item_update_code'];
                    $item_location = $_POST['item_update_location'];
                    $item_quantity = $_POST['item_update_quantity'];
                    $action = $_POST['update_action'];
                    if (!item_stock_info_exists($item_code, $item_location)) {
                        echo "There is no record with the specified item name and location";
                    } else {
                        // echo "Item Info to Update <br>";
                        // echo "Item Code: $itemCode <br>";
                        // echo "Item Location: $itemLocation <br>";
                        // echo "Item Quantity: $itemQuantity <br>";
                        // echo "Will $action <br>";
                        update_item_stock($item_code, $item_location, $item_quantity, $action);
                    }
                }
                ?>
            </form>
        </div>
        <div class="right">
            <h2 class="panel-title">Delete Item Stock Information</h2>
            <form name="delete-itemstockinfo" method="POST">
                <p>Item Name:
                    <select name="item_delete_code" required>
                        <option value="">Select Item</option>
                        <?php get_item_names_for_update_delete() ?>
                    </select>
                </p>
                <p>Location:
                    <select name="item_delete_location" required>
                        <option value="">Select Location</option>
                        <?php get_locations(); ?>
                    </select>
                </p>
                <p><input class="submit-btn" type="submit" value="Delete Item Info" name="btnSubmitDelete"></p>
                <?php
                if (isset($_POST['btnSubmitDelete'])) {
                    $item_code = $_POST['item_delete_code'];
                    $item_location = $_POST['item_delete_location'];
                    if (!item_stock_info_exists($item_code, $item_location)) {
                        echo "There is no record with the specified item name and location";
                    } else {
                        delete_item_stock($item_code, $item_location);
                    }
                }
                ?>
            </form>
        </div>
    </div>
    <hr>
    <?php display_quantity_table(); ?>
</body>

</html>
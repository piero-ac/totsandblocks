<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Stock</title>
    <link rel="stylesheet" type="text/css" href="style.css?<?php echo time(); ?>" />
</head>
<body>
    <?php
        // check if user id exists
        if(!isset($_COOKIE['userID'])) { 
            echo "Please login first!"; 
            die;
        }
        $user_id = $_COOKIE['userID'];
        require('dbfunctions.php');
        
    ?>
    <a href='login.php'>Back to Homepage</a>
    <h1>Manage Stock</h1>
    <hr>
    <main id="stock-main" class="side-by-side" >
        <div class="insert-initialstock-ctn">
            <h2>Insert New item Stock Information</h2>
            <form name="insert_item_stockinfo" method="POST">
                <p>Item Name:
                    <select name="item_insert_code" required>
                        <option value="">Select Item</option>
                        <?php getItemNamesWithoutCompleteStockInfo(); ?>
                    </select>
                </p>
                <p> Location:
                    <select name="item_insert_location" required>
                        <option value="">Select Location</option>
                        <?php getLocations(); ?>
                    </select>
                </p>
                <p>Quantity:
                    <input type="number" name="item_insert_quantity" min="1" max="99" required>
                </p>
                <p><input type="submit" value="Insert Item Info" name="btnSubmitInsert"></p>
                <?php
                    if(isset($_POST['btnSubmitInsert'])){
                        $itemCode = $_POST['item_insert_code'];
                        $itemLocation = $_POST['item_insert_location'];
                        $itemQuantity = $_POST['item_insert_quantity'];
                        if(checkIfComboExistsQuantityTable($itemCode, $itemLocation)){
                            echo "There is already a record with same Item Code + Location";
                        } else {
                           insertItemStock($itemCode, $itemLocation, $itemQuantity, $user_id); 
                        }
                        
                    }
                ?>

            </form>
        </div>
        <div class="update-itemquantity-ctn">
            <h2>Update Item Quantity</h2>
            <form name="update-itemquantity" method="POST">
            <p>Item Name:
                <select name="item_update_code" required>
                    <option value="">Select Item</option>
                    <?php getItemNamesFromQuantityTable(); ?>
                </select>
            </p>
            <p> Location:
                <select name="item_update_location" required>
                    <option value="">Select Location</option>
                    <?php getLocations(); ?>
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
            <p><input type="submit" value="Update Item Info" name="btnSubmitUpdate"></p>
            <?php
                if(isset($_POST['btnSubmitUpdate'])){
                    $itemCode = $_POST['item_update_code'];
                    $itemLocation = $_POST['item_update_location'];
                    $itemQuantity = $_POST['item_update_quantity'];
                    $action = $_POST['update_action'];
                    if(!checkIfComboExistsQuantityTable($itemCode, $itemLocation)){
                        echo "There is no record with the specified item name and location";
                    } else {
                        // echo "Item Info to Update <br>";
                        // echo "Item Code: $itemCode <br>";
                        // echo "Item Location: $itemLocation <br>";
                        // echo "Item Quantity: $itemQuantity <br>";
                        // echo "Will $action <br>";
                        updateItemStock($itemCode, $itemLocation, $itemQuantity, $action); 
                    }
                    
                }
            ?>

            </form>
            
        </div>
        <div class="delete-itemstockinfo-ctn">
            <h2>Delete Item Stock Information</h2>
            <form name="delete-itemstockinfo" method="POST">
            <p>Item Name:
                <select name="item_delete_code" required>
                    <option value="">Select Item</option>
                    <?php getItemNamesFromQuantityTable(); ?>
                </select>
            </p>
            <p>Location:
                <select name="item_delete_location" required>
                    <option value="">Select Location</option>
                    <?php getLocations(); ?>
                </select> 
            </p>
            <p><input type="submit" value="Delete Item Info" name="btnSubmitDelete"></p>
            <?php
                if(isset($_POST['btnSubmitDelete'])){
                    $itemCode = $_POST['item_delete_code'];
                    $itemLocation = $_POST['item_delete_location'];
                    if(!checkIfComboExistsQuantityTable($itemCode, $itemLocation)){
                        echo "There is no record with the specified item name and location";
                    } else {
                        deleteItemStock($itemCode, $itemLocation);
                    }
                    
                }
            ?>
            </form>
        </div>
    </main>
    <hr>
    <?php displayQuantityTable(); ?>
</body>
</html>
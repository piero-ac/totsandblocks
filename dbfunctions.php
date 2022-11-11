<?php
    require('dbconfig.php');

    # Get Items' Names That Users Can Select From
    function getItemNames(){
        global $con;
        //get the item names from items table
        $items_sql = "select itemCode, itemName from totsandblocks.Item";
        $items_results = mysqli_query($con, $items_sql);

        if($items_results){
            while($items_row = mysqli_fetch_array($items_results)){
                $itemCode = $items_row['itemCode'];
                $itemName = $items_row['itemName'];
                echo "<option value='$itemCode'>$itemName</option>";
            }
            mysqli_free_result($items_results);
        } else {
            echo "Something is wrong with SQL: " . mysqli_error($con);
        }
    }

    # Get Categories That Users Can Select From
    function getCategories() {
        global $con;

        $category_sql = "select categoryID, categoryName from totsandblocks.Category";
        $category_results = mysqli_query($con, $category_sql);

        if($category_results){
            while($category_row = mysqli_fetch_array($category_results)){
                $categoryID = $category_row['categoryID'];
                $categoryName = $category_row['categoryName'];
                echo "<option value='$categoryID'>$categoryName</option>";
            }
            mysqli_free_result($category_results);
        } else {
            echo "Something is wrong with SQL: " . mysqli_error($con);
        }
    }

    # Get Existing Item Codes
    function getItemCodes($table){
        global $con;

        if($table == 'Item'){
            $codes_sql = "select itemCode from totsandblocks.Item";
        } else  if ($table == 'Quantity'){
            $codes_sql = "select itemCode from totsandblocks.Quantity";
        }

        $codes_result = mysqli_query($con, $codes_sql);
        $codes_array = array();

        if($codes_result){
            $num_codes = mysqli_num_rows($codes_result);
            if($num_codes == 0){
                echo "No codes were returned.";
            } else {
                while($code_row = mysqli_fetch_array($codes_result)){ 
                    $code = $code_row['itemCode'];
                    array_push($codes_array, $code);
                }
                mysqli_free_result($codes_result);
            }
        } else {
            echo "Something wrong with checking codes SQL: " . mysqli_error($con);
        }

        return $codes_array;

    }

    # Insert Item to Items Table
    function insertItem($itemCode, $itemName, $itemCategory, $itemComment, $user_id){
        global $con;

        // check if item code is not duplicated or amount entered is not valid input
        if(duplicateCode($itemCode)){
            echo "<p style='color:red'>Did not insert item</p>";
        } else {
            $insert_sql = "insert into totsandblocks.Item values ('$itemCode', '$itemName', '$itemCategory', '$itemComment', '$user_id')";
            $insert_result = mysqli_query($con, $insert_sql);
            if($insert_result){
                echo "<br>Item Code: ($itemCode) has been inserted successfully.";
            } else {
                echo "Something is wrong with insertion SQL: " . mysqli_error($con);
            }
        }
    }

    function updateItem($itemCode, $newItemName, $newItemCategory, $newItemComment){
        global $con;

        // get the current information of the item
        $current_item_info_sql = "select * from totsandblocks.Item where itemCode = '$itemCode'";
        $current_results = mysqli_query($con, $current_item_info_sql);

        if($current_results){
            $num_rows = mysqli_num_rows($current_results);
            if($num_rows == 0){
                echo "Did not get item's information";
            } else if ($num_rows > 1){
                echo "Returned more than one item's information";
            } else {
                $item_row = mysqli_fetch_array($current_results);
                $currentItemName = $item_row['itemName'];
                $currentItemCategory = $item_row['itemCategory'];
                $currentItemComment = $item_row['itemDescription'];

                // check if inputs are empty and if they're equal to the current item info
                if(!emptyInputs($newItemName) && strcmp($currentItemName, $newItemName) != 0){
                    $update_item_name = "update totsandblocks.Item set itemName = '$newItemName' where itemCode = '$itemCode'";
                    $update_result = mysqli_query($con, $update_item_name);
                    if($update_result){
                        echo "<br>Updated Item Name.";
                    } else {
                        echo "Something is wrong with updating item name SQL: " . mysqli_error($con);
                    }
                } else {
                    echo "<br>Did not update item name.";
                }

                if(!emptyInputs($newItemCategory) && strcmp($currentItemCategory, $newItemCategory) != 0){
                    $update_item_category = "update totsandblocks.Item set itemCategory = '$newItemCategory' where itemCode = '$itemCode'"; 
                    $update_result = mysqli_query($con, $update_item_category);
                    if($update_result){
                        echo "<br>Updated Item Category.";
                    } else {
                        echo "Something is wrong with updating item category SQL: " . mysqli_error($con);
                    }
                } else {
                    echo "<br>Did not update item category.";
                }

                if(!emptyInputs($newItemComment) && strcmp($currentItemComment, $newItemComment) != 0){
                    $update_item_comment = "update totsandblocks.Item set itemDescription = '$newItemComment' where itemCode = '$itemCode'";
                    $update_result = mysqli_query($con, $update_item_comment);
                    if($update_result){
                        echo "<br>Updated Item Description.";
                    } else {
                        echo "Something is wrong with updating item description SQL: " . mysqli_error($con);
                    }
                } else {
                    echo "<br>Did not update item description.";
                }

            }
        } else {
            echo "Something wrong with getting current item's info SQL: " . mysqli_error($con);
        }
    }

    function deleteItem($itemCode){
        global $con;

        // check if $itemCode is in quantity
        if(empty($itemCode)){
            echo "<p style='color:blue'> Please select an item</p>";
            return;
        }
        if(itemCodeReferenced($itemCode)){
            echo "<p style='color:red'>Did not delete item</p>";
        } else {
            $delete_sql = "delete from totsandblocks.Item where itemCode = '$itemCode'";
            $delete_sql = mysqli_query($con, $delete_sql);
            if($delete_sql){
                echo "<br>Item Code: ($itemCode) has been deleted successfully.";
            } else {
                echo "Something is wrong with deletion SQL: " . mysqli_error($con);
            }
        }
        
    }

    # Display Items Table in Page
    function displayItems(){
        global $con; 

        $items_sql = "select i.*, c.categoryName as cName, u.fName as fName from totsandblocks.Item i, totsandblocks.Users u, totsandblocks.Category c";
        $items_sql = $items_sql . " where c.categoryID = i.itemCategory and u.userID = i.addedBy";

        $items_results = mysqli_query($con, $items_sql);
        if($items_results){
            $num_items = mysqli_num_rows($items_results);
            if($num_items == 0){
                echo "No items in Item table.";
            } else {
                echo "<table border=1>";
                echo "<tbody>";
                echo "<tr><th>Item Code</th><th>Item Name</th><th>Category</th><th>Comment</th><th>Added By</th><tr>";

                while($items_row = mysqli_fetch_array($items_results)){
                    $itemCode = $items_row['itemCode'];
                    $itemName = $items_row['itemName'];
                    $itemCategory = $items_row['cName'];
                    $itemDesc = $items_row['itemDescription'];
                    $addedBy = $items_row['fName'];

                    echo "<tr><td>$itemCode</td><td>$itemName</td><td>$itemCategory</td><td>$itemDesc</td><td>$addedBy</td></tr>";
                }

                echo "</tbody>";
                echo "</table>";
            }
            mysqli_free_result($items_results);
        } else {
            echo "Something is wrong with SQL: " . mysqli_error($con);
        }
    }

    function duplicateCode($itemCode){
        $codes_array = getItemCodes("Item");
        if(in_array($itemCode, $codes_array)){
            echo "<p style='color:red'>Error: Attempting to insert duplicate code.</p>";
            return true;
        }
        return false;
    }

    function emptyInputs($input){
        // check if entered input is empty
        $inputLen = strlen(trim($input));
        if($inputLen == 0 || empty($input)){
            return true;
        }
        return false;
    }

    function itemCodeReferenced($itemCode){
        $codes_array = getItemCodes("Quantity");
        if(in_array($itemCode, $codes_array)){
            echo "<p style='color:red'>Error: Attempting to delete item that is still referenced in Quantity Table.</p>";
            return true;
        }
        return false;

    }

    function viewInventory($itemCategory, $itemLocation){
        global $con;

        $view_sql = "";
        if($itemCategory == "*" && $itemLocation == "*"){
            $view_sql = viewEntireInventory();
        } else {
            if($itemCategory == "*" && $itemLocation != "*"){
                $view_sql = viewInventoryAllCategoriesSpecificLocation($itemLocation);
            } else if ($itemCategory != "*" && $itemLocation == "*"){
                $view_sql = viewInventorySpecificCategoryAllLocations($itemCategory);
            }  else {
                $view_sql = viewInventorySpecifiedCategoryAndLocation($itemCategory, $itemLocation);
            }
        }
    
        $view_result = mysqli_query($con, $view_sql);
        if($view_result) {
            $num_items = mysqli_num_rows($view_result);
        if($num_items == 0){
            echo "No items in Item table.";
        } else {
            echo "<table border=1 cellpadding=3 id='printTable'>";
            echo "<tbody>";
            echo "<tr><th>Item Code</th><th>Item Name</th><th>Category</th><th>Quantity</th><th>Location</th><tr>";
    
            while($view_row = mysqli_fetch_array($view_result)){
                $itemCode = $view_row['itemCode'];
                $itemName = $view_row['itemName'];
                $itemCategory = $view_row['cName'];
                $itemQuantity = $view_row['quantity'];
                $location = $view_row['locationName'];
    
                echo "<tr><td>$itemCode</td><td>$itemName</td><td>$itemCategory</td><td>$itemQuantity</td><td>$location</td></tr>";
            }
    
            echo "</tbody>";
            echo "</table>";
        }
        mysqli_free_result($view_result);
        } else {
            echo "Something is wrong with view SQL: " . mysqli_error($con);
        }
    
    }
    
    function viewInventorySpecifiedCategoryAndLocation($itemCategory, $itemLocation){
        $view_sql = "select i.itemCode, i.itemName, c.categoryName as cName, q.quantity, l.locationName\n"
        . "from totsandblocks.Item i , totsandblocks.Category c, totsandblocks.Quantity q, totsandblocks.Location l\n"
        . "where c.categoryID = i.itemCategory and i.itemCode = q.itemCode and q.locationID = l.locationID and c.categoryID = $itemCategory and q.locationID = $itemLocation";
    
        return $view_sql;
    }
    
    function viewInventorySpecificCategoryAllLocations($itemCategory){
        $view_sql = "select i.itemCode, i.itemName, c.categoryName as cName, q.quantity, l.locationName\n"
        . "from totsandblocks.Item i , totsandblocks.Category c, totsandblocks.Quantity q, totsandblocks.Location l\n"
        . "where c.categoryID = i.itemCategory and i.itemCode = q.itemCode and q.locationID = l.locationID and c.categoryID = $itemCategory";
    
        return $view_sql;
    }
    
    function viewInventoryAllCategoriesSpecificLocation($itemLocation){
        $view_sql = "select i.itemCode, i.itemName, c.categoryName as cName, q.quantity, l.locationName\n"
        . "from totsandblocks.Item i , totsandblocks.Category c, totsandblocks.Quantity q, totsandblocks.Location l\n"
        . "where c.categoryID = i.itemCategory and i.itemCode = q.itemCode and q.locationID = l.locationID and q.locationID = $itemLocation";
    
        return $view_sql;
    }
    
    function viewEntireInventory(){
        $view_sql = "select i.itemCode, i.itemName, c.categoryName as cName, q.quantity, l.locationName\n"
        . "from totsandblocks.Item i , totsandblocks.Category c, totsandblocks.Quantity q, totsandblocks.Location l\n"
        . "where c.categoryID = i.itemCategory and i.itemCode = q.itemCode and q.locationID = l.locationID";
    
        return $view_sql;
    
    }

    function getLocations(){
        global $con;

        $location_sql = "select locationID, locationName from totsandblocks.Location";
        $location_results = mysqli_query($con, $location_sql);
    
        if($location_results){
            while($location_row = mysqli_fetch_array($location_results)){
                $locationID = $location_row['locationID'];
                $locationName = $location_row['locationName'];
                echo "<option value='$locationID'>$locationName</option>";
            }
        } else {
            echo "Something is wrong with location SQL: " . mysqli_error($con);
        }
    }

    function getItemNamesWithoutCompleteStockInfo(){
        global $con;

        // get the item names for items that don't have quantity stock information for both locations
        // this include items that:
        // 1. have no stock information whatsoever
        // 2. have stock information for only one location
        // This means exclude items that have two records in the quantity table
        $items_sql = "select itemCode, itemName from totsandblocks.Item "
                    . " where itemCode not in (select itemCode from totsandblocks.Quantity "
                    . " group by itemCode having count(*) = 2)";
        
        $items_results = mysqli_query($con, $items_sql);

        if($items_results){
            while($items_row = mysqli_fetch_array($items_results)){
                $itemCode = $items_row['itemCode'];
                $itemName = $items_row['itemName'];
                echo "<option value='$itemCode'>$itemName</option>";
            }
            mysqli_free_result($items_results);
        } else {
            echo "Something is wrong with SQL: " . mysqli_error($con);
        } 
    }

    # Insert Item Information to Quantity Table
    function insertItemStock($itemCode, $itemLocation, $itemQuantity, $user_id) {
        global $con;

        if (!is_numeric($itemQuantity)){
            echo "Quantity is not a number.";
        } else {
            // echo "Item Code: $itemCode <br>";
            // echo "Item Quantity: $itemQuantity <br>";
            // echo "Item Location: $itemLocation <br>";
            // echo "User ID: $user_id";
            $insert_sql = "insert into totsandblocks.Quantity (itemCode, quantity, locationID, addedBy) values ('$itemCode', $itemQuantity, $itemLocation, '$user_id')";
            $insert_result = mysqli_query($con, $insert_sql);
            if($insert_result){
                echo "<br>Quantity information for Item Code: ($itemCode) has been inserted successfully.";
            } else {
                echo "Something is wrong with insertion to quantity table SQL: " . mysqli_error($con);
            }
        }
        
    }

    # Check if a record with same ItemCode + ItemLocation exists
    function checkIfComboExistsQuantityTable($itemCode, $itemLocation){
        global $con;

        $check_sql = "select * from totsandblocks.Quantity where itemCode = '$itemCode' and locationID = '$itemLocation'";
        $check_results = mysqli_query($con, $check_sql);

        if($check_results){
            $num_rows = mysqli_num_rows($check_results);
            if($num_rows == 1) return true;
            else return false;
            mysqli_free_result($check_results);
        } else {
            echo "Something is wrong with checking SQL: " . mysqli_error($con);
        } 

    }

    # Display Quantity Table
    function displayQuantityTable(){
        global $con;

        $view_sql = viewEntireInventory();
        $view_result = mysqli_query($con, $view_sql);
        if($view_result) {
            $num_items = mysqli_num_rows($view_result);
        if($num_items == 0){
            echo "No items in Item table.";
        } else {
            echo "<table border=1 cellpadding=3>";
            echo "<tbody>";
            echo "<tr><th>Item Code</th><th>Item Name</th><th>Category</th><th>Quantity</th><th>Location</th><tr>";
    
            while($view_row = mysqli_fetch_array($view_result)){
                $itemCode = $view_row['itemCode'];
                $itemName = $view_row['itemName'];
                $itemCategory = $view_row['cName'];
                $itemQuantity = $view_row['quantity'];
                $location = $view_row['locationName'];
    
                echo "<tr><td>$itemCode</td><td>$itemName</td><td>$itemCategory</td><td>$itemQuantity</td><td>$location</td></tr>";
            }
    
            echo "</tbody>";
            echo "</table>";
        }
        mysqli_free_result($view_result);
        } else {
            echo "Something is wrong with view SQL: " . mysqli_error($con);
        }

    }

    function getItemNamesFromQuantityTable(){
        global $con;

        $items_sql = "select DISTINCT i.itemCode, i.itemName " 
                    . " from totsandblocks.Item i, totsandblocks.Quantity q "
                    . " where i.itemCode = q.itemCode";
        $items_results = mysqli_query($con, $items_sql);

        if($items_results){
            while($items_row = mysqli_fetch_array($items_results)){
                $itemCode = $items_row['itemCode'];
                $itemName = $items_row['itemName'];
                echo "<option value='$itemCode'>$itemName</option>";
            }
            mysqli_free_result($items_results);
        } else {
            echo "Something is wrong with SQL: " . mysqli_error($con);
        } 
        
    }

    # Update Quantity of Matching Record[itemCode, itemLocation]
    function updateItemStock($itemCode, $itemLocation, $itemQuantity, $action){
        global $con;
        $itemQuantity = (int)$itemQuantity;
        $currentQuantity = getCurrentQuantity($itemCode, $itemLocation);
        $new_quantity = 0;

        //if action is delete and quantityToDelete is <= existing quantity
        if($action == 'del'){
            if(greaterThanCurrentQuantity($itemCode, $itemLocation, $itemQuantity)){
                echo "Existing quantity is less than quantity to delete. <br>";
                return;
            } 
            $new_quantity = $currentQuantity - $itemQuantity;
        } else if ($action == 'add'){
            $new_quantity = $currentQuantity + $itemQuantity;
        }
            
        $update_sql = "update totsandblocks.Quantity set quantity = '$new_quantity' where itemCode = '$itemCode' and locationID = '$itemLocation'";
        $update_result = mysqli_query($con, $update_sql);
        if($update_result){
            echo "<br>Updated Item Quantity.";
        } else {
            echo "Something is wrong with updating item quantity SQL: " . mysqli_error($con);
        }

    }

    function getCurrentQuantity($itemCode, $itemLocation){
        global $con;

        $sql = "select quantity from totsandblocks.Quantity where itemCode = '$itemCode' and locationID = '$itemLocation'";
        $sql_result= mysqli_query($con, $sql);

        if($sql_result){
            $currentQuantity = mysqli_fetch_array($sql_result)['quantity'];
            return (int)$currentQuantity;
        } else {
            echo "Something is wrong with getting current quantity SQL: " . mysqli_error($con);
            return -1;
        }
    }
    function greaterThanCurrentQuantity($itemCode, $itemLocation, $quantityToDelete){
        $currentQuantity = getCurrentQuantity($itemCode, $itemLocation);
        # check if quantity to delete is greater than current quantity
        if($quantityToDelete > $currentQuantity){
            return true; # we cannot delete from existing quantity
        } else {
            return false; # we can delete from existing quantity
        }
        
    } 
    
    function deleteItemStock($itemCode, $itemLocation){
        global $con;

        $sql = "delete from totsandblocks.Quantity where itemCode = '$itemCode' and locationID = '$itemLocation'";
        $sql_result= mysqli_query($con, $sql);

        if($sql_result){
            echo "Successfully deleted record with Item Code: $itemCode and Location ID: $itemLocation";
        } else {
            echo "Something is wrong with delete item stock SQL: " . mysqli_error($con);
        }
    }

?>
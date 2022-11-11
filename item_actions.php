<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Items</title>
    <link rel="stylesheet" href="style.css">
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
    <h1>Manage Items</h1>
    <hr>
    <main id="items-main">
        <div class="add-items-ctn">
            <h2>Add Item</h2>
             <!-- ADD ITEMS FORM -->
            <form name='add_item' method='POST' >
                <p>Item Code:
                    <input type="text" name="item_code" required>
                </p>
                <p>Item Name: 
                    <input type="text" name="item_name" required>
                </p>
                <p>Item Category:
                    <select name="item_category">
                        <?php
                            getCategories();
                        ?>
                    </select>
                </p>
                <p>Comment (Optional):</p>
                <p><textarea name="item_comment" cols="30" rows="10"></textarea></p>
                <p><input type="submit" value="Add Item"></p>
                <?php
                    if(isset($_POST['item_code'], $_POST['item_name'], $_POST['item_category'])){
                        $itemCode = $_POST['item_code'];
                        $itemName = $_POST['item_name'];
                        $itemCategory = $_POST['item_category'];
                        $itemComment = $_POST['item_comment'];

                        insertItem($itemCode, $itemName, $itemCategory, $itemComment, $user_id);
                    } 
                ?>
            </form>
        </div>
        <div class="update-items-ctn">
            <h2>Update Item</h2>
            <form name='update_item' method='POST'>
                <p>Item Name:
                    <select name="item_update" id="" required>
                        <option value=""></option>
                        <?php
                            getItemNames();
                        ?>
                    </select>
                </p>
                <p>New Item Name:
                    <input type="text" name="new_item_name">
                </p>
                <p>New Category:
                    <select name="new_item_category">
                        <option value="">Do not update</option>
                        <?php
                            getCategories();
                        ?>
                    </select>
                </p>
                <p>New Comment:</p>
                <p><textarea name="new_item_comment" cols="30" rows="10"></textarea></p>
                <p><input type="submit" value="Update Item" name="btnSubmitUpdate"></p>
                <?php
                    if(isset($_POST['btnSubmitUpdate'])){
                        $itemCodeToUpdate = $_POST['item_update'];
                        $newItemName = $_POST['new_item_name'];
                        $newItemCategory = $_POST['new_item_category'];
                        $newItemComment = $_POST['new_item_comment'];

                        if(!empty($itemCodeToUpdate))
                            updateItem($itemCodeToUpdate, $newItemName, $newItemCategory, $newItemComment);
                        else 
                            echo "Please select an item first.";

                        // $newItemName = (empty($_POST['new_item_name']) || strlen(trim($_POST['new_item_name']))== 0) ? "Do not update item name" : $_POST['new_item_name'];
                        // $newItemCategory = (empty($_POST['new_item_category'])) ? "Do not update category" : $_POST['new_item_category'];
                        // $newItemAvgCost = (empty($_POST['new_item_avgcost']) || strlen(trim($_POST['new_item_avgcost']))== 0) ? "Do not update cost" : $_POST['new_item_avgcost'];
                        // $newItemDesc = (empty($_POST['new_item_description']) || strlen(trim($_POST['new_item_description']))== 0) ? "Do not update desc" : $_POST['new_item_description'];

                        // echo "New Item Name: $newItemName <br>";
                        // echo "New Item Category: $newItemCategory <br>";
                        // echo "New Item Avg Cost: $newItemAvgCost <br>";
                        // echo "New Item Desc: $newItemDesc <br>";
                    }
                    
                ?>
            </form>
        </div>
        <div class="delete-items-ctn">
            <h2>Delete Item</h2>
            <!-- DELETE ITEMS FORM -->
            <form name='delete_item' method='POST'>
                <p>Item Name:
                    <select name="item_delete" id="">
                        <option value=""></option>
                        <?php
                            getItemNames();
                        ?>
                    </select>
                </p>
                <p><input type="submit" value="Delete Item" name="btnSubmitDelete"></p>
                <?php 
                    if(isset($_POST['btnSubmitDelete'])){
                        $itemCodeToDelete = $_POST['item_delete'];
                        deleteItem($itemCodeToDelete);  
                    }
                    
                ?>
                </p>
            </form>
        </div>
    </main>
    <hr>
    <?php
        displayItems();
    ?>
    <?php

    // function emptyInputs($input){
    //     // check if entered input is empty
    //     $inputLen = strlen(trim($input));
    //     if($inputLen == 0 || empty($input)){
    //         return true;
    //     }
    //     return false;
    // }
    // function updateItem($con, $itemCode, $newItemName, $newItemCategory, $newItemComment){

    //     // get the current information of the item
    //     $current_item_info_sql = "select * from totsandblocks.Item where itemCode = '$itemCode'";
    //     $current_results = mysqli_query($con, $current_item_info_sql);

    //     if($current_results){
    //         $num_rows = mysqli_num_rows($current_results);
    //         if($num_rows == 0){
    //             echo "Did not get item's information";
    //         } else if ($num_rows > 1){
    //             echo "Returned more than one item's information";
    //         } else {
    //             $item_row = mysqli_fetch_array($current_results);
    //             $currentItemName = $item_row['itemName'];
    //             $currentItemCategory = $item_row['itemCategory'];
    //             $currentItemComment = $item_row['itemDescription'];

    //             // check if inputs are empty and if they're equal to the current item info
    //             if(!emptyInputs($newItemName) && strcmp($currentItemName, $newItemName) != 0){
    //                 $update_item_name = "update totsandblocks.Item set itemName = '$newItemName' where itemCode = '$itemCode'";
    //                 $update_result = mysqli_query($con, $update_item_name);
    //                 if($update_result){
    //                     echo "<br>Updated Item Name.";
    //                 } else {
    //                     echo "Something is wrong with updating item name SQL: " . mysqli_error($con);
    //                 }
    //             } else {
    //                 echo "<br>Did not update item name.";
    //             }

    //             if(!emptyInputs($newItemCategory) && strcmp($currentItemCategory, $newItemCategory) != 0){
    //                 $update_item_category = "update totsandblocks.Item set itemCategory = '$newItemCategory' where itemCode = '$itemCode'"; 
    //                 $update_result = mysqli_query($con, $update_item_category);
    //                 if($update_result){
    //                     echo "<br>Updated Item Category.";
    //                 } else {
    //                     echo "Something is wrong with updating item category SQL: " . mysqli_error($con);
    //                 }
    //             } else {
    //                 echo "<br>Did not update item category.";
    //             }

    //             if(!emptyInputs($newItemComment) && strcmp($currentItemComment, $newItemComment) != 0){
    //                 $update_item_comment = "update totsandblocks.Item set itemDescription = '$newItemComment' where itemCode = '$itemCode'";
    //                 $update_result = mysqli_query($con, $update_item_comment);
    //                 if($update_result){
    //                     echo "<br>Updated Item Description.";
    //                 } else {
    //                     echo "Something is wrong with updating item description SQL: " . mysqli_error($con);
    //                 }
    //             } else {
    //                 echo "<br>Did not update item description.";
    //             }

    //         }
    //     } else {
    //         echo "Something wrong with getting current item's info SQL: " . mysqli_error($con);
    //     }
    // }

    // function displayItems($con){
    //     $items_sql = "select i.*, c.categoryName as cName, u.fName as fName from totsandblocks.Item i, totsandblocks.Users u, totsandblocks.Category c";
    //     $items_sql = $items_sql . " where c.categoryID = i.itemCategory and u.userID = i.addedBy";

    //     $items_results = mysqli_query($con, $items_sql);
    //     if($items_results){
    //         $num_items = mysqli_num_rows($items_results);
    //         if($num_items == 0){
    //             echo "No items in Item table.";
    //         } else {
    //             echo "<table border=1>";
    //             echo "<tbody>";
    //             echo "<tr><th>Item Code</th><th>Item Name</th><th>Category</th><th>Comment</th><th>Added By</th><tr>";

    //             while($items_row = mysqli_fetch_array($items_results)){
    //                 $itemCode = $items_row['itemCode'];
    //                 $itemName = $items_row['itemName'];
    //                 $itemCategory = $items_row['cName'];
    //                 $itemDesc = $items_row['itemDescription'];
    //                 $addedBy = $items_row['fName'];

    //                 echo "<tr><td>$itemCode</td><td>$itemName</td><td>$itemCategory</td><td>$itemDesc</td><td>$addedBy</td></tr>";
    //             }

    //             echo "</tbody>";
    //             echo "</table>";
    //         }
    //         mysqli_free_result($items_results);
    //     } else {
    //         echo "Something is wrong with SQL: " . mysqli_error($con);
    //     }
    // }

    // function getCategories($con){
    //     $category_sql = "select categoryID, categoryName from totsandblocks.Category";
    //     $category_results = mysqli_query($con, $category_sql);

    //     if($category_results){
    //         while($category_row = mysqli_fetch_array($category_results)){
    //             $categoryID = $category_row['categoryID'];
    //             $categoryName = $category_row['categoryName'];
    //             echo "<option value='$categoryID'>$categoryName</option>";
    //         }
    //         mysqli_free_result($category_results);
    //     } else {
    //         echo "Something is wrong with SQL: " . mysqli_error($con);
    //     }
    // }

    // function getItemNames($con){
    //     //get the item names from items table
    //     $items_sql = "select itemCode, itemName from totsandblocks.Item";
    //     $items_results = mysqli_query($con, $items_sql);

    //     if($items_results){
    //         while($items_row = mysqli_fetch_array($items_results)){
    //             $itemCode = $items_row['itemCode'];
    //             $itemName = $items_row['itemName'];
    //             echo "<option value='$itemCode'>$itemName</option>";
    //         }
    //         mysqli_free_result($items_results);
    //     } else {
    //         echo "Something is wrong with SQL: " . mysqli_error($con);
    //     }
    // }

    // function insertItem($con, $itemCode, $itemName, $itemCategory, $itemComment, $user_id){
    //     // check if item code is not duplicated or amount entered is not valid input
    //     if(duplicateCode($con, $itemCode)){
    //         echo "<p style='color:red'>Did not insert item</p>";
    //     } else {
    //         $insert_sql = "insert into totsandblocks.Item values ('$itemCode', '$itemName', '$itemCategory', '$itemComment', '$user_id')";
    //         $insert_result = mysqli_query($con, $insert_sql);
    //         if($insert_result){
    //             echo "<br>Item Code: ($itemCode) has been inserted successfully.";
    //         } else {
    //             echo "Something is wrong with insertion SQL: " . mysqli_error($con);
    //         }
    //     }

    // }

    // function deleteItem($con, $itemCode){
    //     // check if $itemCode is in quantity
    //     if(empty($itemCode)){
    //         echo "<p style='color:blue'> Please select an item</p>";
    //         return;
    //     }
    //     if(itemCodeReferenced($con, $itemCode)){
    //         echo "<p style='color:red'>Did not delete item</p>";
    //     } else {
    //         $delete_sql = "delete from totsandblocks.Item where itemCode = '$itemCode'";
    //         $delete_sql = mysqli_query($con, $delete_sql);
    //         if($delete_sql){
    //             echo "<br>Item Code: ($itemCode) has been deleted successfully.";
    //         } else {
    //             echo "Something is wrong with deletion SQL: " . mysqli_error($con);
    //         }
    //     }
        
    // }

    // function itemCodeReferenced($con, $itemCode){
    //     $codes_array = getItemCodes($con, "Quantity");
    //     if(in_array($itemCode, $codes_array)){
    //         echo "<p style='color:red'>Error: Attempting to delete item that is still referenced in Quantity Table.</p>";
    //         return true;
    //     }
    //     return false;

    // }

    // function duplicateCode($con, $itemCode){
    //     $codes_array = getItemCodes($con, "Item");
    //     if(in_array($itemCode, $codes_array)){
    //         echo "<p style='color:red'>Error: Attempting to insert duplicate code.</p>";
    //         return true;
    //     }
    //     return false;
    // }
    
    // function getItemCodes($con, $table){
    //     if($table == 'Item'){
    //         $codes_sql = "select itemCode from totsandblocks.Item";
    //     } else  if ($table == 'Quantity'){
    //         $codes_sql = "select itemCode from totsandblocks.Quantity";
    //     }

    //     $codes_result = mysqli_query($con, $codes_sql);
    //     $codes_array = array();

    //     if($codes_result){
    //         $num_codes = mysqli_num_rows($codes_result);
    //         if($num_codes == 0){
    //             echo "No codes were returned.";
    //         } else {
    //             while($code_row = mysqli_fetch_array($codes_result)){ 
    //                 $code = $code_row['itemCode'];
    //                 array_push($codes_array, $code);
    //             }
    //             mysqli_free_result($codes_result);
    //         }
    //     } else {
    //         echo "Something wrong with checking codes SQL: " . mysqli_error($con);
    //     }

    //     return $codes_array;

    // }
    // mysqli_close($con);
    ?>
</body>
</html>
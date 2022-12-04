<?php

// Provides the database login
require_once "../misc/dbconfig.php";

// Provides the necessary functions for quantity-db.php
require_once "../Models/quantity-db.php";

/**
 * Get items names from the Item Table
 * 
 * Function will be used to generate the options
 * for the dropdown menu in the Manage Items Forms
 *
 * @global mixed $con
 * @return boolean
 */
function get_item_names()
{
    global $con;

    $items_sql = "select itemCode, itemName from totsandblocks.Item";
    $items_results = mysqli_query($con, $items_sql);

    if ($items_results) {
        while ($items_row = mysqli_fetch_array($items_results)) {
            $item_code = $items_row['itemCode'];
            $item_name = $items_row['itemName'];
            echo "<option value='$item_code'>$item_name</option>";
        }
        mysqli_free_result($items_results);
        return true;
    } else {
        echo "Something is wrong with SQL: " . mysqli_error($con);
        return false;
    }
}

/**
 * Get items codes from the Item Table
 * 
 * Function will be used to return an array 
 * of the codes in the Item Table.
 *
 * @global mixed $con
 * @return array $codes_array
 */
function get_item_codes_item_db()
{
    global $con;
    $codes_array = array();

    $codes_sql = "select itemCode from totsandblocks.Item";
    $codes_result = mysqli_query($con, $codes_sql);

    if ($codes_result) {
        $num_codes = mysqli_num_rows($codes_result);
        if ($num_codes == 0) {
            echo "No codes were returned.";
        } else {
            while ($code_row = mysqli_fetch_array($codes_result)) {
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

/**
 * Insert Item Info to Item Table
 * 
 * Function will be used to insert the item information provided 
 * by the user in the Manage Items - Insert New Item Info Form
 *
 * @global mixed $con
 * @param string $item_code Inserted item's unique code
 * @param string $item_name Inserted item's identifying name
 * @param string $item_category Inserted item's category
 * @param string $item_comment Inserted item's comment (description)
 * @param string $user_id The ID of the user that inserts the item
 * @return boolean
 */
function insert_item_info(string $item_code, string $item_name, string $item_category, string $item_comment, string $user_id)
{
    global $con;

    // check if item code is not duplicated or amount entered is not valid input
    if (is_duplicate_code($item_code)) {
        echo "<p style='color:red'>Did not insert item. Duplicate item code.</p>";
        return false;
    }

    $insert_sql = "insert into totsandblocks.Item values ('$item_code', '$item_name', '$item_category', '$item_comment', '$user_id')";
    $insert_result = mysqli_query($con, $insert_sql);
    if ($insert_result) {
        echo "<br>Item Code: ($item_code) has been inserted successfully.";
        return true;
    } else {
        echo "Something is wrong with insertion SQL: " . mysqli_error($con);
        return false;
    }
}

/**
 * Update Item Info in Item Table
 * 
 * Function will be used to update the item information of an item
 * that exists in the Item Table. New values are provided 
 * by the user in the Manage Items - Update Item Info Form.
 * Does a comparison of the current item's information with 
 * the new information to determine if it should update the value.
 *
 * @global mixed $con
 * @param string $item_code Item Code for item whose information will be updated
 * @param string $new_item_name New name for chosen item (optional)
 * @param string $new_item_category New category for chosen item (optional)
 * @param string $new_item_comment New comment (description) for chosen item (optional)
 * @return boolean
 */
function update_item_info(string $item_code, string $new_item_name, string $new_item_category, string $new_item_comment)
{
    global $con;

    // get the current information of the item
    $current_item_info_sql = "select * from totsandblocks.Item where itemCode = '$item_code'";
    $current_results = mysqli_query($con, $current_item_info_sql);

    if ($current_results) {
        $num_rows = mysqli_num_rows($current_results);
        if ($num_rows == 0) {
            echo "Did not get item's information";
        } else if ($num_rows > 1) {
            echo "Returned more than one item's information";
        } else {
            $item_row = mysqli_fetch_array($current_results);
            $current_item_name = $item_row['itemName'];
            $current_item_category = $item_row['itemCategory'];
            $current_item_comment = $item_row['itemDescription'];

            // Update item name function
            update_item_name($item_code, $current_item_name, $new_item_name);

            // Update item category function
            update_item_category($item_code, $current_item_category, $new_item_category);

            // Update item comment function
            update_item_comment($item_code, $current_item_comment, $new_item_comment);
            return true;
        }
    } else {
        echo "Something wrong with getting current item's info SQL: " . mysqli_error($con);
        return false;
    }
}


/**
 * Update Item Name
 * 
 * Function will be used to update the item name of the passed in
 * item code. Function will check if the input is empty (meaning do not update)
 * and check if the current item name is the same as the updated item name (meaning do not update).
 * If both of these are not true, then the item name will be updated.
 *
 * @global mixed $con
 * @param string $item_code Item Code for item whose information will be updated
 * @param string $current_item_name Current item name for the item code
 * @param string $new_item_name New name for chosen item (optional)
 * @return void
 */
function update_item_name(string $item_code, string $current_item_name, string $new_item_name)
{
    global $con;

    if (!is_empty_input($new_item_name) && strcmp($current_item_name, $new_item_name) != 0) {
        $update_item_name_sql = "update totsandblocks.Item set itemName = '$new_item_name' where itemCode = '$item_code'";
        $update_result = mysqli_query($con, $update_item_name_sql);
        if ($update_result) {
            echo "<br>Updated Item Name.";
        } else {
            echo "Something is wrong with updating item name SQL: " . mysqli_error($con);
        }
    } else {
        echo "<br>Did not update item name.";
    }
}

/**
 * Update Item Category
 * 
 * Function will be used to update the item category of the passed in
 * item code. Function will check if the input is empty (meaning do not update)
 * and check if the current item category is the same as the updated item category (meaning do not update).
 * If both of these are not true, then the item category will be updated.
 *
 * @global mixed $con
 * @param string $item_code Item Code for item whose information will be updated
 * @param string $current_item_category Current item category for the item code
 * @param string $new_item_category New category for chosen item (optional)
 * @return void
 */
function update_item_category(string $item_code, string $current_item_category, string $new_item_category)
{
    global $con;

    if (!is_empty_input($new_item_category) && strcmp($current_item_category, $new_item_category) != 0) {
        $update_item_category_sql = "update totsandblocks.Item set itemCategory = '$new_item_category' where itemCode = '$item_code'";
        $update_result = mysqli_query($con, $update_item_category_sql);
        if ($update_result) {
            echo "<br>Updated Item Category.";
        } else {
            echo "Something is wrong with updating item category SQL: " . mysqli_error($con);
        }
    } else {
        echo "<br>Did not update item category.";
    }
}

/**
 * Update Item Comment
 * 
 * Function will be used to update the item comment of the passed in
 * item code. Function will check if the input is empty (meaning do not update)
 * and check if the current item comment is the same as the updated item comment (meaning do not update).
 * If both of these are not true, then the item comment will be updated.
 *
 * @global mixed $con
 * @param string $item_code Item Code for item whose information will be updated
 * @param string $current_item_comment Current item comment for the item code
 * @param string $new_item_comment New comment for chosen item (optional)
 * @return void
 */
function update_item_comment(string $item_code, string $current_item_comment, string $new_item_comment)
{
    global $con;

    if (!is_empty_input($new_item_comment) && strcmp($current_item_comment, $new_item_comment) != 0) {
        $update_item_comment_sql = "update totsandblocks.Item set itemDescription = '$new_item_comment' where itemCode = '$item_code'";
        $update_result = mysqli_query($con, $update_item_comment_sql);
        if ($update_result) {
            echo "<br>Updated Item Description.";
        } else {
            echo "Something is wrong with updating item description SQL: " . mysqli_error($con);
        }
    } else {
        echo "<br>Did not update item description.";
    }
}

/**
 * Delete Item Info from Item Table
 * 
 * Function will be used to delete the item information of an item
 * that exists in the Item Table. The item code and name is selected
 * by the user in the Manage Items - Delete Item Info Form.
 * Checks if item_code has child records in the Quantity table. If so,
 * then the user can't delete the item and needs to delete the
 * child records first.
 *
 * @global mixed $con
 * @param string $item_code Item Code for item whose information will be deleted
 * @return boolean
 */
function delete_item_info(string $item_code)
{
    global $con;

    // Check if $item_code exists in Quantity table
    if (item_code_exist($item_code)) {
        echo "<p style='color:red'>Did not delete item. Item still has stock records.</p>";
        return false;
    }

    $delete_sql = "delete from totsandblocks.Item where itemCode = '$item_code'";
    $delete_sql = mysqli_query($con, $delete_sql);
    if ($delete_sql) {
        echo "<br>Item Code: ($item_code) has been deleted successfully.";
        return true;
    } else {
        echo "Something is wrong with deletion SQL: " . mysqli_error($con);
        return false;
    }
}

/**
 * Display Items Table in Page
 * 
 * Function will be used to display the Item Table in
 * the Manage Items Page.
 *
 * @global mixed $con
 * @return boolean
 */
function display_item_table()
{
    global $con;

    $items_sql = "select i.*, c.categoryName as cName, u.fName as fName from totsandblocks.Item i, totsandblocks.Users u, totsandblocks.Category c";
    $items_sql = $items_sql . " where c.categoryID = i.itemCategory and u.userID = i.addedBy";
    $items_results = mysqli_query($con, $items_sql);

    if ($items_results) {
        $num_items = mysqli_num_rows($items_results);
        if ($num_items == 0) {
            echo "No items in Item table.";
        } else {
            echo "<table border=1 class='content-table'>";
            echo "<thead><tr class='header'><th>Item Code</th><th>Item Name</th><th>Category</th><th>Comment</th><th>Added By</th><tr></thead>";
            echo "<tbody>";

            while ($items_row = mysqli_fetch_array($items_results)) {
                $item_code = $items_row['itemCode'];
                $item_name = $items_row['itemName'];
                $item_category = $items_row['cName'];
                $item_desc = $items_row['itemDescription'];
                $added_by = $items_row['fName'];

                echo "<tr><td>$item_code</td><td>$item_name</td><td>$item_category</td><td>$item_desc</td><td>$added_by</td></tr>";
            }

            echo "</tbody>";
            echo "</table>";
        }
        mysqli_free_result($items_results);
        return true;
    } else {
        echo "Something is wrong with SQL: " . mysqli_error($con);
        return false;
    }
}

/**
 * Check If Inserting Duplicate Item Code to Item Table
 * 
 * Function will check the item code of the item that is to be
 * inserted to the Item table for duplicate values. If so,
 * the insertion is halted. 
 *
 * @global mixed $con
 * @param string $item_code Item code to be checked if duplicate
 * @return boolean
 */
function is_duplicate_code(string $item_code)
{
    $codes_array = get_item_codes_item_db();
    if (in_array($item_code, $codes_array)) {
        echo "<p style='color:red'>Error: Attempting to insert duplicate code.</p>";
        return true;
    }
    return false;
}

/**
 * Check If Item Information is Empty (for update function)
 * 
 * Function will check if the input passed is empty. If so,
 * the item value will not be updated in its attribute.
 *
 * @global mixed $con
 * @param string $input Input to check if empty
 * @return boolean
 */
function is_empty_input(string $input)
{
    // check if entered input is empty
    $inputLen = strlen(trim($input));
    if ($inputLen == 0 || empty($input)) {
        return true;
    }
    return false;
}

/**
 * Check If Item Code Exists in Quantity Table (for delete function)
 * 
 * Function will check if the item code of the item trying to be deleted
 * still has stock records in the Quantity table. If so, then item 
 * cannot be deleted.
 *
 * @global mixed $con
 * @param string $item_code Item code to check if exists in Quantity Table
 * @return boolean
 */
function item_code_exist(string $item_code)
{
    $codes_array = get_item_codes_quantity_db();
    if (in_array($item_code, $codes_array)) {
        echo "<p style='color:red'>Error: Attempting to delete item that is still referenced in Quantity Table.</p>";
        return true;
    }
    return false;
}

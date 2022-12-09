<?php
// Provides the database login
require_once "../misc/dbconfig.php";

// Provides the functions necessary from view-db.php
require_once "../Models/view-db.php";

/**
 * Get items codes from the Quantity Table
 * 
 * Function will be used to return an array of
 * the item codes from the Quantity Table.
 *
 * @global mixed $con
 * @return array $codes_array
 */
function get_item_codes_quantity_db()
{
    global $con;
    $codes_array = array();

    $codes_sql =  "select itemCode from totsandblocks.Quantity";
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
 * Insert Item Stock Information to Quantity Table
 * 
 * Function will be used to insert the initial item stock
 * information for an item. Check if quantity is numeric value,
 * before insertion.
 *
 * @global mixed $con
 * @param string $item_code Item code for item without stock info
 * @param string $item_location Location of the stock info for the item
 * @param string $item_quantity Initial quantity of the item
 * @param string $user_id The ID of the user inserting stock information
 * @return boolean
 */
function insert_item_stock(string $item_code, string $item_location, string $item_quantity, string $user_id)
{
    global $con;

    // cleanse inputs
    $item_code = mysqli_real_escape_string($con, $item_code);
    $item_location = mysqli_real_escape_string($con, $item_location);
    $item_quantity = mysqli_real_escape_string($con, $item_quantity);

    if (!is_numeric($item_quantity)) {
        echo "Quantity is not a number. Did not insert.";
        return false;
    }

    $insert_sql = "insert into totsandblocks.Quantity (itemCode, quantity, locationID, addedBy) values ('$item_code', $item_quantity, $item_location, '$user_id')";
    $insert_result = mysqli_query($con, $insert_sql);

    if ($insert_result) {
        echo "<br>Quantity information for Item Code: ($item_code) has been inserted successfully.";
        return true;
    } else {
        echo "Something is wrong with insertion to quantity table SQL: " . mysqli_error($con);
        return false;
    }
}

/**
 * Check if Item Stock Information Exists for Location
 * 
 * Function will be used to check if a record with the 
 * Item Code + Item Location pair exists in the Quantity table.
 *
 * @global mixed $con
 * @param string $item_code Item code to check.
 * @param string $item_location Item location to check.
 * @return boolean true if pair exists, false otherwise.
 */
function item_stock_info_exists(string $item_code, string $item_location)
{
    global $con;

    // cleanse inputs
    $item_code = mysqli_real_escape_string($con, $item_code);
    $item_location = mysqli_real_escape_string($con, $item_location);

    $check_sql = "select * from totsandblocks.Quantity where itemCode = '$item_code' and locationID = '$item_location'";
    $check_results = mysqli_query($con, $check_sql);

    if ($check_results) {
        $num_rows = mysqli_num_rows($check_results);
        if ($num_rows == 1)
            return true;
        else
            return false;
        mysqli_free_result($check_results);
    } else {
        echo "Something is wrong with checking SQL: " . mysqli_error($con);
        return false;
    }
}

/**
 * Update the Stock Information for the Item Based on the Location
 * 
 * Function will be used to update the stock information
 * of the item based on the selected Item Code and Item Locaton. 
 * Quantity will be added to or deleted from based on select action.
 * If delete, the quantity will be checked if there is enough to delete from.
 *
 * @global mixed $con
 * @param string $item_code Item code of item to update.
 * @param string $item_location Item location of item to update.
 * @param string $item_quantity Quantity to add or delete.
 * @param string $action Will either be 'del' or 'add'.
 * @return boolean true if quantity was able to be updated, false otherwise.
 */
function update_item_stock(string $item_code, string $item_location, string $item_quantity, string $action)
{
    global $con;

    // cleanse inputs
    $item_code = mysqli_real_escape_string($con, $item_code);
    $item_location = mysqli_real_escape_string($con, $item_location);
    $item_quantity = mysqli_real_escape_string($con, $item_quantity);

    $item_quantity = (int)$item_quantity;
    $currentQuantity = get_current_quantity($item_code, $item_location);
    if ($currentQuantity == -1) {
        return false;
    }
    $new_quantity = 0;

    //if action is delete and quantityToDelete is <= existing quantity
    if ($action == 'del') {
        if (greater_than_current_quantity($item_code, $item_location, $item_quantity)) {
            echo "Existing quantity is less than quantity to delete. <br>";
            return false;
        }
        $new_quantity = $currentQuantity - $item_quantity;
    } else if ($action == 'add') {
        $new_quantity = $currentQuantity + $item_quantity;
    }

    $update_sql = "update totsandblocks.Quantity set quantity = '$new_quantity' where itemCode = '$item_code' and locationID = '$item_location'";
    $update_result = mysqli_query($con, $update_sql);
    if ($update_result) {
        echo "<br>Updated Item Quantity.";
        return true;
    } else {
        echo "Something is wrong with updating item quantity SQL: " . mysqli_error($con);
        return false;
    }
}

/**
 * Get the current quantity for the item
 * 
 * Function will be used to obtain the current quantity 
 * for the item code and item location pair.
 *
 * @global mixed $con
 * @param string $item_code Item code of item to check for quantity.
 * @param string $item_location Item location of item to check for quantity.
 * @return mixed integer value if quantity was able to be obtained, -1 otherwise
 */
function get_current_quantity(string $item_code, string $item_location)
{
    global $con;

    // cleanse inputs
    $item_code = mysqli_real_escape_string($con, $item_code);
    $item_location = mysqli_real_escape_string($con, $item_location);

    $sql = "select quantity from totsandblocks.Quantity where itemCode = '$item_code' and locationID = '$item_location'";
    $sql_result = mysqli_query($con, $sql);

    if ($sql_result) {
        $currentQuantity = mysqli_fetch_array($sql_result)['quantity'];
        return (int)$currentQuantity;
    } else {
        echo "Something is wrong with getting current quantity SQL: " . mysqli_error($con);
        return -1;
    }
}

/**
 * Check if quantity to delete is greater than current quantity
 * 
 * Function will be used to check if quantity
 * to delete is greater than current quantity
 *
 * @global mixed $con
 * @param string $item_code Item code of item to check for quantity.
 * @param string $item_location Item location of item to check for quantity.
 * @param string $quantity_to_delete Quantity that needs to be deleted.
 * @return boolean true if greater than or equal to, false otherwise.
 */
function greater_than_current_quantity(string $item_code, string $item_location, string $quantity_to_delete)
{
    $current_quantity = get_current_quantity($item_code, $item_location);
    return $quantity_to_delete >= $current_quantity;
}

/**
 * Delete the stock information for the Item Code + Item Location Pair
 * 
 * Function will be used to delete the stock information
 * for the Item Code + Item Location pair.
 *
 * @global mixed $con
 * @param string $item_code Item code of item to delete.
 * @param string $item_location Item location of item to delete.
 * @return boolean true if delete successfull, false otherwise.
 */
function delete_item_stock(string $item_code, string $item_location)
{
    global $con;

    // cleanse inputs
    $item_code = mysqli_real_escape_string($con, $item_code);
    $item_location = mysqli_real_escape_string($con, $item_location);

    $sql = "delete from totsandblocks.Quantity where itemCode = '$item_code' and locationID = '$item_location'";
    $sql_result = mysqli_query($con, $sql);

    if ($sql_result) {
        echo "Successfully deleted record with Item Code: $item_code and Location ID: $item_location";
        return true;
    } else {
        echo "Something is wrong with delete item stock SQL: " . mysqli_error($con);
        return false;
    }
}

/**
 * Display Quantity Table in Page
 * 
 * Function will be used to display the Quantity Table in
 * the Manage Stock Page.
 *
 * @global mixed $con
 * @return boolean
 */
function display_quantity_table()
{
    global $con;

    $view_sql = get_inventory_query("*", "*");
    $view_result = mysqli_query($con, $view_sql);
    if ($view_result) {
        $num_items = mysqli_num_rows($view_result);
        if ($num_items == 0) {
            echo "No items in Item table.";
        } else {
            echo "<table border=1 class='content-table'>";
            echo "<thead><tr class='header'><th>Item Code</th><th>Item Name</th><th>Category</th><th>Quantity</th><th>Location</th><tr></thead>";
            echo "<tbody>";

            while ($view_row = mysqli_fetch_array($view_result)) {
                $item_code = $view_row['itemCode'];
                $item_name = $view_row['itemName'];
                $item_category = $view_row['cName'];
                $item_quantity = $view_row['quantity'];
                $location = $view_row['locationName'];

                echo "<tr><td>$item_code</td><td>$item_name</td><td>$item_category</td><td>$item_quantity</td><td>$location</td></tr>";
            }

            echo "</tbody>";
            echo "</table>";
        }
        mysqli_free_result($view_result);
    } else {
        echo "Something is wrong with view SQL: " . mysqli_error($con);
    }
}

/**
 * Get items codes and names from Quantity Table with incomplete info
 * 
 * Function will be used to generate the options
 * for the dropdown menu in the Manage Stock Page - Add Item Stock forms.
 * The options will be items that don't have stock information 
 * for both locations. 
 *
 * @global mixed $con
 * @return boolean true if options were obtained. false otherwise.
 */
function get_item_names_incomplete()
{
    global $con;

    $items_sql = "select itemCode, itemName from totsandblocks.Item "
        . " where itemCode not in (select itemCode from totsandblocks.Quantity "
        . " group by itemCode having count(*) = 2)";
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
 * Get items codes and names from Quantity Table
 * 
 * Function will be used to generate the options
 * for the dropdown menu in the Manage Stock Page - Update & Delete
 * Item Stock forms. The options will be items have at least stock information
 * for at least one location.
 *
 * @global mixed $con
 * @return boolean true if options were obtained. false otherwise.
 */
function get_item_names_for_update_delete()
{
    global $con;

    $items_sql = "select DISTINCT i.itemCode, i.itemName "
        . " from totsandblocks.Item i, totsandblocks.Quantity q "
        . " where i.itemCode = q.itemCode";
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

<?php
require('dbconfig.php');

# Get Items' Names That Users Can Select From
function get_item_names(string $limiter = "all")
{
    global $con;
    $items_sql = "";
    if (strcmp($limiter, "all") == 0) { # For View Page Select Element
        $items_sql = "select itemCode, itemName from totsandblocks.Item";
    } else if (strcmp($limiter, "incomplete") == 0) { # For Stock Page - Add Item Stock Select Element
        $items_sql = "select itemCode, itemName from totsandblocks.Item "
            . " where itemCode not in (select itemCode from totsandblocks.Quantity "
            . " group by itemCode having count(*) = 2)";
    } else if (strcmp($limiter, "quantity" == 0)) { # For Stock Page - Update & Delete Item Stock Select Elements
        $items_sql = "select DISTINCT i.itemCode, i.itemName "
            . " from totsandblocks.Item i, totsandblocks.Quantity q "
            . " where i.itemCode = q.itemCode";
    }

    $items_results = mysqli_query($con, $items_sql);

    if ($items_results) {
        while ($items_row = mysqli_fetch_array($items_results)) {
            $item_code = $items_row['itemCode'];
            $item_name = $items_row['itemName'];
            echo "<option value='$item_code'>$item_name</option>";
        }
        mysqli_free_result($items_results);
    } else {
        echo "Something is wrong with SQL: " . mysqli_error($con);
    }
}

# Get Categories That Users Can Select From
function get_item_categories()
{
    global $con;

    $category_sql = "select categoryID, categoryName from totsandblocks.Category";
    $category_results = mysqli_query($con, $category_sql);

    if ($category_results) {
        while ($category_row = mysqli_fetch_array($category_results)) {
            $category_id = $category_row['categoryID'];
            $category_name = $category_row['categoryName'];
            echo "<option value='$category_id'>$category_name</option>";
        }
        mysqli_free_result($category_results);
    } else {
        echo "Something is wrong with SQL: " . mysqli_error($con);
    }
}

# Get Existing Item Codes
function get_item_codes(string $table)
{
    global $con;

    if ($table == 'Item') {
        $codes_sql = "select itemCode from totsandblocks.Item";
    } else  if ($table == 'Quantity') {
        $codes_sql = "select itemCode from totsandblocks.Quantity";
    }

    $codes_result = mysqli_query($con, $codes_sql);
    $codes_array = array();

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

# Insert Item Info to Item Table
function insert_item_info(string $item_code, string $item_name, string $item_category, string $item_comment, string $user_id)
{
    global $con;

    // check if item code is not duplicated or amount entered is not valid input
    if (is_duplicate_code($item_code)) {
        echo "<p style='color:red'>Did not insert item</p>";
    } else {
        $insert_sql = "insert into totsandblocks.Item values ('$item_code', '$item_name', '$item_category', '$item_comment', '$user_id')";
        $insert_result = mysqli_query($con, $insert_sql);
        if ($insert_result) {
            echo "<br>Item Code: ($item_code) has been inserted successfully.";
        } else {
            echo "Something is wrong with insertion SQL: " . mysqli_error($con);
        }
    }
}

# Update Item Info in Item Table
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

            // check if inputs are empty and if they're equal to the current item info
            if (!is_empty_input($new_item_name) && strcmp($current_item_name, $new_item_name) != 0) {
                $update_item_name = "update totsandblocks.Item set itemName = '$new_item_name' where itemCode = '$item_code'";
                $update_result = mysqli_query($con, $update_item_name);
                if ($update_result) {
                    echo "<br>Updated Item Name.";
                } else {
                    echo "Something is wrong with updating item name SQL: " . mysqli_error($con);
                }
            } else {
                echo "<br>Did not update item name.";
            }

            if (!is_empty_input($new_item_category) && strcmp($current_item_category, $new_item_category) != 0) {
                $update_item_category = "update totsandblocks.Item set itemCategory = '$new_item_category' where itemCode = '$item_code'";
                $update_result = mysqli_query($con, $update_item_category);
                if ($update_result) {
                    echo "<br>Updated Item Category.";
                } else {
                    echo "Something is wrong with updating item category SQL: " . mysqli_error($con);
                }
            } else {
                echo "<br>Did not update item category.";
            }

            if (!is_empty_input($new_item_comment) && strcmp($current_item_comment, $new_item_comment) != 0) {
                $update_item_comment = "update totsandblocks.Item set itemDescription = '$new_item_comment' where itemCode = '$item_code'";
                $update_result = mysqli_query($con, $update_item_comment);
                if ($update_result) {
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

# Delete Item Info from Item Table
function delete_item_info(string $item_code)
{
    global $con;

    // check if $item_code is in quantity
    if (empty($item_code)) {
        echo "<p style='color:blue'> Please select an item</p>";
        return;
    }
    if (item_code_exist($item_code)) {
        echo "<p style='color:red'>Did not delete item</p>";
    } else {
        $delete_sql = "delete from totsandblocks.Item where itemCode = '$item_code'";
        $delete_sql = mysqli_query($con, $delete_sql);
        if ($delete_sql) {
            echo "<br>Item Code: ($item_code) has been deleted successfully.";
        } else {
            echo "Something is wrong with deletion SQL: " . mysqli_error($con);
        }
    }
}

# Display Items Table in Page
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
            echo "<table border=1>";
            echo "<tbody>";
            echo "<tr><th>Item Code</th><th>Item Name</th><th>Category</th><th>Comment</th><th>Added By</th><tr>";

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
    } else {
        echo "Something is wrong with SQL: " . mysqli_error($con);
    }
}

function is_duplicate_code(string $item_code)
{
    $codes_array = get_item_codes("Item");
    if (in_array($item_code, $codes_array)) {
        echo "<p style='color:red'>Error: Attempting to insert duplicate code.</p>";
        return true;
    }
    return false;
}

function is_empty_input(string $input)
{
    // check if entered input is empty
    $inputLen = strlen(trim($input));
    if ($inputLen == 0 || empty($input)) {
        return true;
    }
    return false;
}

function item_code_exist(string $item_code)
{
    $codes_array = get_item_codes("Quantity");
    if (in_array($item_code, $codes_array)) {
        echo "<p style='color:red'>Error: Attempting to delete item that is still referenced in Quantity Table.</p>";
        return true;
    }
    return false;
}

function display_inventory(string $item_category, string $item_location, string $quantity_comparison, string $quantity_number, string $qty_sort, string $name_sort)
{
    global $con;

    $view_sql = get_inventory_query($item_category, $item_location);

    // Add having clause for quantiy filtering
    $view_sql = $view_sql . " having quantity $quantity_comparison $quantity_number";


    // Add additional criteria to SQL statement
    if (strcmp($qty_sort, "q-none") != 0 || strcmp($name_sort, "n-none") != 0) {
        $view_sql = $view_sql . " order by ";

        // Add correct quantity sort, and check if we need a comma for next criteria
        if (strcmp($qty_sort, "q-none") != 0) {
            if (strcmp($qty_sort, "q-asc")) {
                $view_sql = $view_sql . " quantity asc, ";
            } else {
                $view_sql = $view_sql . " quantity desc, ";
            }
        }

        // Add correct itemName sort
        if (strcmp($name_sort, "n-none") != 0) {
            if (strcmp($name_sort, "n-asc") == 0) {
                $view_sql = $view_sql . " itemName asc ";
            } else {
                $view_sql = $view_sql . " itemName desc";
            }
        }

        // trim any trailing commas
        $view_sql = rtrim(trim($view_sql), ",");
    }

    //echo "$view_sql <br>";

    $view_result = mysqli_query($con, $view_sql);
    if ($view_result) {
        $num_items = mysqli_num_rows($view_result);
        if ($num_items == 0) {
            echo "No items match search criteria.";
            return false;
        } else {
            echo "<table border=1 cellpadding=3 id='printTable'>";
            echo "<tbody>";
            echo "<tr><th>Item Code</th><th>Item Name</th><th>Category</th><th>Quantity</th><th>Location</th><tr>";

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
            return true;
        }
        mysqli_free_result($view_result);
    } else {
        echo "Something is wrong with view SQL: " . mysqli_error($con);
        return false;
    }
}

function get_inventory_query(string $item_category, string $item_location)
{
    $view_sql = "select i.itemCode, i.itemName, c.categoryName as cName, q.quantity, l.locationName\n"
        . "from totsandblocks.Item i , totsandblocks.Category c, totsandblocks.Quantity q, totsandblocks.Location l\n"
        . "where c.categoryID = i.itemCategory and i.itemCode = q.itemCode and q.locationID = l.locationID";


    if ($item_category == "*" && $item_location == "*") { # Get All Inventory Information
        return $view_sql;
    } else {
        if ($item_category == "*" && $item_location != "*") { # Get Items of All Categories with Specified Location
            $view_sql .= " and q.locationID = $item_location";
        } else if ($item_category != "*" && $item_location == "*") { # Get Items of Both Locations with Specifed Category
            $view_sql .= " and c.categoryID = $item_category";
        } else { # Get Items that match Specified Location and Category
            $view_sql .= "and c.categoryID = $item_category and q.locationID = $item_location";
        }
        return $view_sql;
    }
}

function get_locations()
{
    global $con;

    $location_sql = "select locationID, locationName from totsandblocks.Location";
    $location_results = mysqli_query($con, $location_sql);

    if ($location_results) {
        while ($location_row = mysqli_fetch_array($location_results)) {
            $locationID = $location_row['locationID'];
            $locationName = $location_row['locationName'];
            echo "<option value='$locationID'>$locationName</option>";
        }
    } else {
        echo "Something is wrong with location SQL: " . mysqli_error($con);
    }
}

# Insert Item Information to Quantity Table
function insert_item_stock(string $item_code, string $item_location, string $item_quantity, string $user_id)
{
    global $con;

    if (!is_numeric($item_quantity)) {
        echo "Quantity is not a number.";
    } else {
        // echo "Item Code: $item_code <br>";
        // echo "Item Quantity: $item_quantity <br>";
        // echo "Item Location: $item_location <br>";
        // echo "User ID: $user_id";
        $insert_sql = "insert into totsandblocks.Quantity (itemCode, quantity, locationID, addedBy) values ('$item_code', $item_quantity, $item_location, '$user_id')";
        $insert_result = mysqli_query($con, $insert_sql);
        if ($insert_result) {
            echo "<br>Quantity information for Item Code: ($item_code) has been inserted successfully.";
        } else {
            echo "Something is wrong with insertion to quantity table SQL: " . mysqli_error($con);
        }
    }
}

# Check if a record with same ItemCode + ItemLocation exists
function item_stock_info_exists(string $item_code, string $item_location)
{
    global $con;

    $check_sql = "select * from totsandblocks.Quantity where itemCode = '$item_code' and locationID = '$item_location'";
    $check_results = mysqli_query($con, $check_sql);

    if ($check_results) {
        $num_rows = mysqli_num_rows($check_results);
        if ($num_rows == 1) return true;
        else return false;
        mysqli_free_result($check_results);
    } else {
        echo "Something is wrong with checking SQL: " . mysqli_error($con);
    }
}

# Display Quantity Table
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
            echo "<table border=1 cellpadding=3>";
            echo "<tbody>";
            echo "<tr><th>Item Code</th><th>Item Name</th><th>Category</th><th>Quantity</th><th>Location</th><tr>";

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

# Update Quantity of Matching Record[itemCode, itemLocation]
function update_item_stock(string $item_code, string $item_location, string $item_quantity, string $action)
{
    global $con;
    $item_quantity = (int)$item_quantity;
    $currentQuantity = get_current_quantity($item_code, $item_location);
    $new_quantity = 0;

    //if action is delete and quantityToDelete is <= existing quantity
    if ($action == 'del') {
        if (greater_than_current_quantity($item_code, $item_location, $item_quantity)) {
            echo "Existing quantity is less than quantity to delete. <br>";
            return;
        }
        $new_quantity = $currentQuantity - $item_quantity;
    } else if ($action == 'add') {
        $new_quantity = $currentQuantity + $item_quantity;
    }

    $update_sql = "update totsandblocks.Quantity set quantity = '$new_quantity' where itemCode = '$item_code' and locationID = '$item_location'";
    $update_result = mysqli_query($con, $update_sql);
    if ($update_result) {
        echo "<br>Updated Item Quantity.";
    } else {
        echo "Something is wrong with updating item quantity SQL: " . mysqli_error($con);
    }
}

function get_current_quantity(string $item_code, string $item_location)
{
    global $con;

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
function greater_than_current_quantity(string $item_code, string $item_location, string $quantityToDelete)
{
    $currentQuantity = get_current_quantity($item_code, $item_location);
    # check if quantity to delete is greater than current quantity
    if ($quantityToDelete > $currentQuantity) {
        return true; # we cannot delete from existing quantity
    } else {
        return false; # we can delete from existing quantity
    }
}

function delete_item_stock(string $item_code, string $item_location)
{
    global $con;

    $sql = "delete from totsandblocks.Quantity where itemCode = '$item_code' and locationID = '$item_location'";
    $sql_result = mysqli_query($con, $sql);

    if ($sql_result) {
        echo "Successfully deleted record with Item Code: $item_code and Location ID: $item_location";
    } else {
        echo "Something is wrong with delete item stock SQL: " . mysqli_error($con);
    }
}

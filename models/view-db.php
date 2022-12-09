<?php
// Provides the database login
require_once "../misc/dbconfig.php";

/**
 * Get Inventory Query
 * 
 * Function used to generate the approriate
 * query for viewing the inventory based on item
 * category and item location
 *
 * @global mixed $con
 * @param string $item_category Item Category to search for
 * @param string $item_location Item Location to search For
 * @return string SQL query for generating inventory view
 */
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
            $view_sql .= " and c.categoryID = $item_category and q.locationID = $item_location";
        }
        return $view_sql;
    }
}

/**
 * Display Inventory 
 * 
 * Function used to generate the table (report)
 * based on the criteria entered by the user.
 *
 * @global mixed $con
 * @param string $item_category Item Category to search for.
 * @param string $item_location Item Location to search for.
 * @param string $quantity_comparison Comparison operator for quantity.
 * @param string $quantity_number Quantity to compare.
 * @param string $qty_sort Sort by quantity. Ascending or Descending. (optional)
 * @param string $name_sort Sort by name. Ascending or Descending. (optional)
 * @return string SQL query for generating inventory view
 */
function display_inventory(string $item_category, string $item_location, string $quantity_comparison, string $quantity_number, string $qty_sort, string $name_sort)
{
    global $con;

    // cleanse inputs
    $item_category = mysqli_real_escape_string($con,  $item_category);
    $item_location = mysqli_real_escape_string($con, $item_location);
    $quantity_number = mysqli_real_escape_string($con, $quantity_number);

    $view_sql = get_inventory_query($item_category, $item_location);

    // Add having clause for quantiy filtering
    $view_sql = $view_sql . " having quantity $quantity_comparison $quantity_number";
    // echo "$view_sql <br>";


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

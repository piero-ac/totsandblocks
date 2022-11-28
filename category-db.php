<?php

require("dbconfig.php");

/**
 * Get categories for items
 * 
 * Function will be used to generate the options
 * for the dropdown menu for selecting the category
 * of an item. Used in multiple forms.
 *
 * @global mixed $con
 * @return boolean true if options were obtained. false otherwise.
 */
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
        return true;
    } else {
        echo "Something is wrong with SQL: " . mysqli_error($con);
        return false;
    }
}

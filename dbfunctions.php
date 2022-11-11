<?php
    require('dbconfig.php');

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
?>
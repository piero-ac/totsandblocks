<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View/Print Inventory</title>
    <script>
        function printData()
        {
            let divToPrint = document.getElementById("printTable");
            newWin = window.open("");
            newWin.document.write(divToPrint.outerHTML);
            newWin.print();
            newWin.close();
        }
    </script>
</head>
<body>
    <?php
        // check if user id exists
        if(!isset($_COOKIE['userID'])) { 
            echo "Please login first!"; 
            die;
        }
        
        // file containing db login
        include "dbconfig.php";

        // connection to database
        $con = mysqli_connect($host, $username, $password, $dbname) 
        or die("<br>Cannot connect to DB:$dbname on $host, error: " . mysqli_connect_errno());

        $user_id = $_COOKIE['userID'];
    ?>

    <a href='login.php'>Back to Homepage</a>
    <h1>View/Print Inventory</h1>
    <hr>
    <main id="view-main">
        <div class="search-inventory-ctn">
            <form name="search_items" method="POST">
                <label for="category"> Category:
                    <select name="item_category" id="category" required>
                        <option value="*">ALL</option>
                        <?php
                            getCategories($con);
                        ?>
                    </select>
                </label>
                <label for="location">Location:
                    <select name="item_location" id="location" required>
                        <option value="*">ALL</option>
                        <?php
                            getLocations($con);
                        ?>
                    </select>
                </label>
                <p><input type="submit" value="Search" name="btnSearch"></p>
                <?php
                    if(isset($_POST['btnSearch'])){
                        $itemCategory = $_POST['item_category'];
                        $itemLocation = $_POST['item_location'];
                        echo "category: $itemCategory <br>";
                        echo "location: $itemLocation <br>";
                        viewInventory($con, $itemCategory, $itemLocation);
                        echo "<br><br><button onclick=printData()>Print Table</button>";
                    }
                ?>
            </form>
        </div>
    </main>


<?php

function viewInventory($con, $itemCategory, $itemLocation){
    $view_sql = "";
    if($itemCategory == "*" && $itemLocation == "*"){
        $view_sql = viewEntireInventory($con);
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
function getLocations($con){
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

function getCategories($con){
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
        echo "Something is wrong with categories SQL: " . mysqli_error($con);
    }
}
mysqli_close($con);
?>
</body>
</html>
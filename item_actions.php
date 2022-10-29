<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Items</title>
</head>
<body>
<?php
    // check if user id exists
    if(!isset($_COOKIE['userID'])) { 
        echo "Please login first!"; 
        die;
    }
    
    include "dbconfig.php";

    // connection to database
    $con = mysqli_connect($host, $username, $password, $dbname) 
      or die("<br>Cannot connect to DB:$dbname on $host, error: " . mysqli_connect_errno());

    $user_id = $_COOKIE['userID'];
    echo "<h1>Manage Items</h1>";
    echo "<hr>";

    displayItems($con);

    echo "<h2>Add Item</h2>";
    echo "<form name='add_item' method='POST'>";
?>
<p>
    Item Code:
    <input type="text" name="item_code" required>
</p>
<p>
    Item Name: 
    <input type="text" name="item_name" required>
</p>
<p>
Item Category:
<select name="item_location_search">
<option value="*">ALL</option>
<?php
//get the item names from items table
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
?>
</select>
</p>
<p>
    Average Cost ($):
    <input type="text" name="item_avgcost" required>
</p>
<p>Description (Optional):</p>
<p><textarea name="item_description" cols="30" rows="10"></textarea></p>
<p><input type="submit" value="Add Item"></p>
</form>

<hr>
<h2>Delete Item</h2>
<form name='delete_item' method='POST'>
    <p>
        Item Name:
        <select name="item_delete" id="">
            <option value=""></option>
            <?php
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
            ?>
        </select>
    </p>
    <p>
        <input type="submit" value="Check References">
    </p>
</form>

<p>
References:
<?php 
    if(isset($_POST['item_delete']) && !empty($_POST['item_delete'])){
        $name = $_POST['item_delete'];
        echo "Selected $name";
    }
?>
</p>
<?php
function displayItems($con){
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
            echo "<tr><th>Item Code</th><th>Item Name</th><th>Category</th><th>Avg. Cost</th><th>Added By</th><tr>";

            while($items_row = mysqli_fetch_array($items_results)){
                $itemCode = $items_row['itemCode'];
                $itemName = $items_row['itemName'];
                $itemCategory = $items_row['cName'];
                $itemAvgCost = $items_row['itemAvgCost'];
                $addedBy = $items_row['fName'];
                echo "<tr><td>$itemCode</td><td>$itemName</td><td>$itemCategory</td><td>$$itemAvgCost</td><td>$addedBy</td></tr>";
            }

            echo "</tbody>";
            echo "</table>";
        }
        mysqli_free_result($items_results);
    } else {
        echo "Something is wrong with SQL: " . mysqli_error($con);
    }
}
mysqli_close($con);
?>
</body>
</html>
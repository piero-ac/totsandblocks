<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Stock</title>
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
    echo "<h1>Manage Stock</h1>";
    echo "<hr>";

    echo "<h2>Search Stock Information</h2>";
    echo "<form name='search_stock' action='POST'>";
?>
<p> 
Item Name: 
<select name="item_name_search" id="">
    <option value="*">ALL</option>
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
</p>
Item Location: 
<select name="item_location_search">
    <option value="*">ALL</option>
    <?php
    //get the item names from items table
    $locations_sql = "select locationID, locationName from totsandblocks.Location";
    $locations_results = mysqli_query($con, $locations_sql);

    if($locations_results){
        while($location_row = mysqli_fetch_array($locations_results)){
            $locationID = $location_row['locationID'];
            $locationName = $location_row['locationName'];
            echo "<option value='$locationID'>$locationName</option>";
        }
        mysqli_free_result($locations_results);
    } else {
        echo "Something is wrong with SQL: " . mysqli_error($con);
    }
    ?>
</select>
</p>
<input type="submit" value="Search Stock">
</form>
<hr>
<?php
mysqli_close($con);
?>
</body>
</html>
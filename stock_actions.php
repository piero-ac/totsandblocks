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
?>
    <a href='login.php'>Back to Homepage</a>
    <h1>Manage Stock</h1>
    <hr>
    <main id="stock-main">
        <div class="insert-initialstock-ctn">
            <h2>Insert New item Stock Information</h2>
            <form action="">
                <p>Item Name:
                    <select name="item_insert_name" required>
                        <option value="">Select Item</option>
                    </select>
                </p>
                <p> Location:
                    <select name="item_insert_location" required>
                        <option value="">Select Location</option>
                    </select>
                </p>
                <p>Quantity:
                    <input type="text" name="item_insert_quantity" required>
                </p>
                <p><input type="submit" value="Insert Item" name="btnSubmitInsert"></p>
            </form>
        </div>
        <div class="update-itemquantity-ctn">
            <form action="">
            <p>Item Name:
                <select name="item_update_name" required>
                    <option value="">Select Item</option>
                </select>
            </p>
            <p> Location:
                <select name="item_update_location" required>
                    <option value="">Select Location</option>
                </select>
            </p>
            <p>Quantity:
                <input type="text" name="item_update_quantity" required>
            </p>
            <p>
                <input type="radio" id="add" value="add_quantity" name="update_quantity">
                <label for="add">Add</label>
                <input type="radio" id="delete" value="del_quantity" name="update_quantity">
                <label for="delete">Add</label>
            </p>
            <p><input type="submit" value="Insert Item" name="btnSubmitUpdate"></p>
            </form>
        </div>
    </main>
<?php
mysqli_close($con);
?>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<?php
    include "dbconfig.php";

    // connection to database
    $con = mysqli_connect($host, $username, $password, $dbname) 
      or die("<br>Cannot connect to DB:$dbname on $host, error: " . mysqli_connect_errno());
    
    // Get the user's username and password
    // Check if they have entered username and password
    if(isset($_POST['username']))
        $user = mysqli_real_escape_string($con, $_POST['username']);
    else
        die("Please enter username first!");
    
    if(isset($_POST['password']))
        $pass = mysqli_real_escape_string($con, $_POST['password']);
    else
        die("Please enter password first!");

    // SQL statement to check user login info
    $users_sql = "select * from totsandblocks.Users where username = '$user'"; // do not compare passwords
    $users_results = mysqli_query($con, $users_sql);

    if($users_results){ // check if result is false (something wrong with query)
        $num_rows = mysqli_num_rows($users_results);

        if($num_rows == 0) 
            echo "<h1>Login $user doesn't exist in the database</h1>"; // 1.2
        else if ($num_rows > 1) 
            echo "<h1>More than one user with login $user</h1>";
        else {
            $user_row = mysqli_fetch_array($users_results);
            $user_password = $user_row['password'];
            if($user_password == $pass){

                ##### Logout Function #####
                echo "<a href='logout.php'>User Logout</a><br>";

                ##### Get user id #####
                $user_id = $user_row['userID'];

                #####  Set cookie for user #####
                setcookie('userID', $user_id, time()+3600);

                ##### Obtain user info #####
                $fname = $user_row['fName'];
                $lname = $user_row['lName'];
                $position = $user_row['position'];

                ##### Display user info #####
                echo "<h2>Welcome $fname $lname</h2>"; 
                echo "<p>Position: $position </p>";
                echo "<hr />";  

            } else {
                echo "<h1>Login $user exists, but password does not match</h1>"; // 1.3
            }
        }

    } else {
        echo "Something is wrong with SQL: " . mysqli_error($con);
    }
?>
<main id="actions">
    <section id="stock-actions">
        <div>
            <h2>Check Stock</h2> 
            <p>Click below to Search for Item Stock</p> 
        </div>
        <div>
            <h2>Update Stock</h2> 
            <p>Click below to Update Quantity of Item Stock</p>
        </div>
        <div>
            <h2>Add Stock</h2>
            <p>Click below to Add Stock</p>
        </div>
    </section>
    <section id="items-actions">
        <div>
            <h2>Add Items</h2>
            <p>Click below to Add New Items</p>
        </div>
        <div>
            <h2>Delete Items</h2>
            <p>Click below to Delete Old/Unused Items</p>
        </div>
    </section>
</main>
<?php
 mysqli_close($con);
?>
</body>
</html>




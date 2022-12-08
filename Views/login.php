<?php
// Provides the database login
require_once "../misc/dbconfig.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700">
    <link rel="stylesheet" href="../style.css">
    <title>Tots and Blocks IMS</title>
</head>

<body>
    <?php
    // connection to database
    $con = mysqli_connect($host, $username, $password, $dbname)
        or die("<br>Cannot connect to DB:$dbname on $host, error: " . mysqli_connect_errno());


    // check if user is already logged in
    if (isset($_COOKIE['userID'])) {
        $user_id = $_COOKIE['userID'];

        #####  Set cookie for user #####
        setcookie('userID', $user_id, time() + 3600);

        // find out the user's username and password
        $user_info_sql = "select * from totsandblocks.Users where userID=$user_id";
        $user_result = mysqli_query($con, $user_info_sql);
        $user_row = mysqli_fetch_array($user_result);
        $name = $user_row['fName'] . " " . $user_row['lName'];
        $position = $user_row['position'];

        // display homepage
        displayHomepage($name);
    } else {
        // Get the user's username and password
        // Check if they have entered username and password
        if (isset($_POST['username']))
            $user = mysqli_real_escape_string($con, $_POST['username']);
        else
            die("Please enter username first!");

        if (isset($_POST['password']))
            $pass = mysqli_real_escape_string($con, $_POST['password']);
        else
            die("Please enter password first!");

        // SQL statement to check user login info
        $users_sql = "select * from totsandblocks.Users where username = '$user'"; // do not compare passwords
        $users_results = mysqli_query($con, $users_sql);

        if ($users_results) { // check if result is false (something wrong with query)
            $num_rows = mysqli_num_rows($users_results);

            if ($num_rows == 0)
                echo "<h1>Login $user doesn't exist in the database</h1>"; // 1.2
            else if ($num_rows > 1)
                echo "<h1>More than one user with login $user</h1>";
            else {
                $user_row = mysqli_fetch_array($users_results);
                $user_password = $user_row['password'];
                if ($user_password == $pass) {

                    ##### Get user id #####
                    $user_id = $user_row['userID'];

                    #####  Set cookie for user #####
                    setcookie('userID', $user_id, time() + 3600);

                    ##### Obtain user info #####
                    $fname = $user_row['fName'];
                    $lname = $user_row['lName'];
                    $name = $fname . " " . $lname;
                    $position = $user_row['position'];

                    ##### Display user info #####
                    displayHomepage($name);
                } else {
                    echo "<h1>Login $user exists, but password does not match</h1>"; // 1.3
                }
            }
        } else {
            echo "Something is wrong with SQL: " . mysqli_error($con);
        }
    }
    ?>
    <?php
    function displayHomepage($name)
    {
        echo <<<HTML
        <div class="head-body">
            <div class="nav-container">
                <nav class="navbar">
                    <h1 class="navbar-logo">Tots and Blocks IMS</h1>
                    <div class="menu-toggle">
                        <span class="bar"></span>
                        <span class="bar"></span>
                        <span class="bar"></span>
                    </div>
                    <ul class="nav-menu">
                        <li><a href="./login.php" class="nav-links">Home</a></li>
                        <li><a href="./stock_actions.php" class="nav-links">Manage Stock</a></li>
                        <li><a href="./item_actions.php" class="nav-links">Manage Items</a></li>
                        <li><a href="./view_actions.php" class="nav-links">View Inventory</a></li>
                        <li><a href="./logout.php" class="nav-links nav-links-btn">Logout</a></li>
                    </ul>
                </nav>
            </div>
        </div>
        <div class="login-body">
            <div class="left">
                <h2 class="panel-title">Manage Stock</h2>
                <p class="panel-disc">Insert, Update, Delete Stock Information</p>
                <form action="./stock_actions.php" method="POST">
                    <input type="hidden" name="customer_name" value="$name">
                    <input class="submit-btn" type="submit" value="Manage Stock">
                </form>
            </div>
            <div class="center">
                <h2 class="panel-title">Manage Items</h2>
                <p class="panel-disc">Insert, Update, Delete Item Information</p>
                <p class="extra">*Items need to be added before inserting it's stock information*</p>
                <form action="./item_actions.php" method="POST">
                    <input type="hidden" name="customer_name" value="$name">
                    <input class="submit-btn" type="submit" value="Manage Items">
                </form>
            </div>
            <div class="right">
                <h2 class="panel-title">View Inventory Information</h2>
                <p class="panel-disc">View Inventory & Print Reports</p>
                <form action="./view_actions.php">
                    <input type="hidden" name="customer_name" value="$name">
                    <input class="submit-btn" type="submit" value="View Inventory">
                </form>
            </div>
        </div>
    HTML;
    }
    mysqli_close($con);
    ?>
</body>

</html>
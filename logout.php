<?php
if(!isset($_COOKIE['userID'])) { 
    echo "Cookie userid does not exist!"; 
    die;
}
else {
    echo "<p>You successfully logged out.</p>";
    unset($_COOKIE['userID']); // remove a cookie
    setcookie('userID', '', time() - 3600); //expire the cookie immediately
    # unset($_COOKIE['name']); // remove a cookie
   #  setcookie('name', '', time() - 3600); //expire the cookie immediately

    echo "<br><br>";
    echo "<a href='index.html'>Back to homepage</a>";

}
?>

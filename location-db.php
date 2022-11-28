<?php

require("dbconfig.php");

/**
 * Get locations for items
 * 
 * Function will be used to generate the options
 * for the dropdown menu for selecting the location
 * of an item. Used in multiple forms.
 *
 * @global mixed $con
 * @return boolean true if options were obtained. false otherwise.
 */
function get_locations()
{
    global $con;

    $location_sql = "select locationID, locationName from totsandblocks.Location";
    $location_results = mysqli_query($con, $location_sql);

    if ($location_results) {
        while ($location_row = mysqli_fetch_array($location_results)) {
            $locationID = $location_row['locationID'];
            $locationName = $location_row['locationName'];
            echo "<option value='$locationID'>$locationName</option>";
        }
        mysqli_free_result($location_results);
        return true;
    } else {
        echo "Something is wrong with location SQL: " . mysqli_error($con);
        return false;
    }
}

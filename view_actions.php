<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View/Print Inventory</title>
    <script>
        function printData() {
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
    if (!isset($_COOKIE['userID'])) {
        echo "Please login first!";
        die;
    }

    $user_id = $_COOKIE['userID'];
    require('dbfunctions.php');
    ?>

    <a href='login.php'>Back to Homepage</a>
    <h1>View/Print Inventory*</h1>
    <hr>
    <main id="view-main">
        <div class="search-inventory-ctn">
            <form name="search_items" method="POST">
                <label for="category"> Category:
                    <select name="item_category" id="category" required>
                        <option value="*">ALL</option>
                        <?php
                        getCategories();
                        ?>
                    </select>
                </label>
                <label for="location">Location:
                    <select name="item_location" id="location" required>
                        <option value="*">ALL</option>
                        <?php
                        getLocations();
                        ?>
                    </select>
                </label>
                <br>
                <label>
                    Quantity is
                    <select name="comparison_op" required>
                        <option value="">Select one</option>
                        <option value="=">equal to</option>
                        <option value="<">less than</option>
                        <option value="<=">less than or equal to</option>
                        <option value=">">greater than</option>
                        <option value=">=">greater than or equal to</option>
                    </select>
                    <input type="number" name="quantity-filter" min="0" max="1000" required>
                </label>

                <p><input type="submit" value="Search" name="btnSearch"></p>
                <?php
                if (isset($_POST['btnSearch'])) {
                    $itemCategory = $_POST['item_category'];
                    $itemLocation = $_POST['item_location'];
                    $quantityComparison = $_POST['comparison_op'];
                    $quantityNumber = $_POST['quantity-filter'];
                    // echo "category: $itemCategory <br>";
                    // echo "location: $itemLocation <br>";
                    // echo "comparison: $quantityComparison <br>";
                    // echo "number: $quantityNumber <br>";
                    $successful = viewInventory($itemCategory, $itemLocation, $quantityComparison, $quantityNumber);
                    if ($successful)
                        echo "<br><br><button onclick=printData()>Print Table</button>";
                }
                ?>
            </form>
        </div>
    </main>
</body>

</html>
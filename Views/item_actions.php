<?php
// Provides the database login
require_once "../misc/dbconfig.php";

// Provides functions for manipulating item info
require_once "../Models/item-db.php";
require_once "../Models/category-db.php";

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Items</title>
    <link rel="stylesheet" type="text/css" href="../style.css">
</head>

<body>
    <?php
    // check if user id exists
    if (!isset($_COOKIE['userID'])) {
        echo "Please login first!";
        die;
    }
    $user_id = $_COOKIE['userID'];

    ?>
    <a href='login.php'>Back to Homepage</a>
    <h1>Manage Items*</h1>
    <hr>
    <main id="items-main" class="side-by-side">
        <div class="add-items-ctn">
            <h2>Add Item</h2>
            <!-- ADD ITEMS FORM -->
            <form name='add_item' method='POST'>
                <p>Item Code:
                    <input type="text" name="item_code" required>
                </p>
                <p>Item Name:
                    <input type="text" name="item_name" required>
                </p>
                <p>Item Category:
                    <select name="item_category">
                        <?php
                        get_item_categories();
                        ?>
                    </select>
                </p>
                <p>Comment (Optional):</p>
                <p><textarea name="item_comment" cols="30" rows="10"></textarea></p>
                <p><input type="submit" value="Add Item"></p>
                <?php
                if (isset($_POST['item_code'], $_POST['item_name'], $_POST['item_category'])) {
                    $item_code = $_POST['item_code'];
                    $item_name = $_POST['item_name'];
                    $item_category = $_POST['item_category'];
                    $item_comment = $_POST['item_comment'];

                    insert_item_info($item_code, $item_name, $item_category, $item_comment, $user_id);
                }
                ?>
            </form>
        </div>
        <div class="update-items-ctn">
            <h2>Update Item</h2>
            <form name='update_item' method='POST'>
                <p>Item Name:
                    <select name="item_update" id="" required>
                        <option value=""></option>
                        <?php
                        get_item_names();
                        ?>
                    </select>
                </p>
                <p>New Item Name:
                    <input type="text" name="new_item_name">
                </p>
                <p>New Category:
                    <select name="new_item_category">
                        <option value="">Do not update</option>
                        <?php
                        get_item_categories();
                        ?>
                    </select>
                </p>
                <p>New Comment:</p>
                <p><textarea name="new_item_comment" cols="30" rows="10"></textarea></p>
                <p><input type="submit" value="Update Item" name="btnSubmitUpdate"></p>
                <?php
                if (isset($_POST['btnSubmitUpdate'])) {
                    $item_code_to_update = $_POST['item_update'];
                    $new_item_name = $_POST['new_item_name'];
                    $new_item_category = $_POST['new_item_category'];
                    $new_item_comment = $_POST['new_item_comment'];

                    if (!empty($item_code_to_update))
                        update_item_info($item_code_to_update, $new_item_name, $new_item_category,  $new_item_comment);
                    else
                        echo "Please select an item first.";
                }

                ?>
            </form>
        </div>
        <div class="delete-items-ctn">
            <h2>Delete Item</h2>
            <!-- DELETE ITEMS FORM -->
            <form name='delete_item' method='POST'>
                <p>Item Name:
                    <select name="item_delete" id="">
                        <option value=""></option>
                        <?php
                        get_item_names();
                        ?>
                    </select>
                </p>
                <p><input type="submit" value="Delete Item" name="btnSubmitDelete"></p>
                <?php
                if (isset($_POST['btnSubmitDelete'])) {
                    $item_code_to_delete = $_POST['item_delete'];
                    delete_item_info($item_code_to_delete);
                }

                ?>
                </p>
            </form>
        </div>
    </main>
    <section id="display-item-sect" style="border: 1px solid black;">
        <div id="display-item-div">
            <?php display_item_table(); ?>
        </div>

    </section>

</body>

</html>
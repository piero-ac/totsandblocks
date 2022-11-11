<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Items</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php
        // check if user id exists
        if(!isset($_COOKIE['userID'])) { 
            echo "Please login first!"; 
            die;
        }
        $user_id = $_COOKIE['userID'];
        require('dbfunctions.php');

    ?>
    <a href='login.php'>Back to Homepage</a>
    <h1>Manage Items*</h1>
    <hr>
    <main id="items-main">
        <div class="add-items-ctn">
            <h2>Add Item</h2>
             <!-- ADD ITEMS FORM -->
            <form name='add_item' method='POST' >
                <p>Item Code:
                    <input type="text" name="item_code" required>
                </p>
                <p>Item Name: 
                    <input type="text" name="item_name" required>
                </p>
                <p>Item Category:
                    <select name="item_category">
                        <?php
                            getCategories();
                        ?>
                    </select>
                </p>
                <p>Comment (Optional):</p>
                <p><textarea name="item_comment" cols="30" rows="10"></textarea></p>
                <p><input type="submit" value="Add Item"></p>
                <?php
                    if(isset($_POST['item_code'], $_POST['item_name'], $_POST['item_category'])){
                        $itemCode = $_POST['item_code'];
                        $itemName = $_POST['item_name'];
                        $itemCategory = $_POST['item_category'];
                        $itemComment = $_POST['item_comment'];

                        insertItem($itemCode, $itemName, $itemCategory, $itemComment, $user_id);
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
                            getItemNames();
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
                            getCategories();
                        ?>
                    </select>
                </p>
                <p>New Comment:</p>
                <p><textarea name="new_item_comment" cols="30" rows="10"></textarea></p>
                <p><input type="submit" value="Update Item" name="btnSubmitUpdate"></p>
                <?php
                    if(isset($_POST['btnSubmitUpdate'])){
                        $itemCodeToUpdate = $_POST['item_update'];
                        $newItemName = $_POST['new_item_name'];
                        $newItemCategory = $_POST['new_item_category'];
                        $newItemComment = $_POST['new_item_comment'];

                        if(!empty($itemCodeToUpdate))
                            updateItem($itemCodeToUpdate, $newItemName, $newItemCategory, $newItemComment);
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
                            getItemNames();
                        ?>
                    </select>
                </p>
                <p><input type="submit" value="Delete Item" name="btnSubmitDelete"></p>
                <?php 
                    if(isset($_POST['btnSubmitDelete'])){
                        $itemCodeToDelete = $_POST['item_delete'];
                        deleteItem($itemCodeToDelete);  
                    }
                    
                ?>
                </p>
            </form>
        </div>
    </main>
    <hr>
    <?php
        displayItems();
    ?>
</body>
</html>
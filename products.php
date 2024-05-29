<?php
session_start();
include "functions.php";
$database = new database();

validateLogin();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty(cleanInput($_POST, "dashboard"))) {
        safeRedirect("dashboard");
    }

    $id = cleanInput($_POST, "id");
    $name = cleanInput($_POST, "name");
    $price = cleanInput($_POST, "price");
    $amount = cleanInput($_POST, "amount");
    if (!empty($name) && !empty($price) && !empty($amount)) {
        switch (false) {
            case empty(cleanInput($_POST, "add")): {
                $date = date("Y-m-d");
                $database->query("INSERT INTO products (name, price, amount, addedAt) VALUES ('$name', '$price', '$amount', '$date')");
                break;
            }
            case empty(cleanInput($_POST, "delete")): {
                $database->query("DELETE FROM products WHERE id='$id'");
                break;
            }
            case empty(cleanInput($_POST, "update")): {
                $database->query("UPDATE products SET name='$name', price='$price', amount='$amount' WHERE id='$id'");
                break;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="style.css">
        <title>Products</title>
    </head>
    <body>
        <?= printHeader("<form method='POST'>
                    <input class='button' type='submit' name='dashboard' value='Dashboard' />
                </form>");
        ?>

        <div class="page items-grid">
            <form class="item card card-dark-shadow" style="padding: 1rem" method="POST">
                <div class="card-image"></div>
                <label for="name-new">Name:</label>
                <input type="text" id="name-new" name="name" placeholder="Lemon" required />
                <label for="price-new">Price:</label>
                <input type="number" id="price-new" name="price" placeholder="1" step="0.01" required />
                <label for="amount-new">Amount:</label>
                <input type="number" id="amount-new" name="amount" placeholder="1" required />
                <input type="submit" class="button" name="add" value="Add" />
            </form>
            <?php
            $products = $database->query("SELECT * FROM products");
            while($row = $products->fetch_assoc()) {
            ?><form class="item card card-dark-shadow" id="<?= $row["id"] ?>" method="POST">
                <img class="card-image" loading="lazy" src="Assets/products/<?= $row["name"] ?>.webp" alt="<?= $row["name"] ?>" />
                <div style="padding: 1rem" class="container container-col">
                    <input type="hidden" name="id" value="<?= $row["id"] ?>" />
                    <label for="name-<?= $row["id"] ?>">Name:</label>
                    <input type="text" id="name-<?= $row["id"] ?>" name="name" value="<?= $row["name"] ?>" required />
                    <label for="price-<?= $row["id"] ?>">Price:</label>
                    <input type="number" id="price-<?= $row["id"] ?>" name="price" value="<?= $row["price"] ?>" required />
                    <label for="amount-<?= $row["id"] ?>">Amount:</label>
                    <input type="number" id="amount-<?= $row["id"] ?>" name="amount" value="<?= $row["amount"] ?>" required />
                    <input type="submit" class="button button-red" name="delete" value="Delete" />
                    <input type="submit" class="button" name="update" value="Update" />
                </div>
            </form>
            <?php } ?>
        </div>

<?php include "footer.php" ?>
    </body>
</html>

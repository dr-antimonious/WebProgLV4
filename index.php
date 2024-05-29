<?php
session_start();
include "functions.php";
$database = new database();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = cleanInput($_POST, "id");
    $amount = cleanInput($_POST, "amount");
    switch (false) {
        case empty(cleanInput($_POST, "add")): {
            $query = "SELECT * FROM products WHERE id='$id' AND amount >= $amount";
            if (empty($id) || empty($amount) || !$database->query($query)->num_rows) {
                $_SESSION["modalMessage"] = colored("We don't have this many products in stock!", "red");
            }
            $cart = $_SESSION["cart"] ?? array();
            $cart[$id] = ($cart[$id] ?? 0) + (int) $amount;
            $_SESSION["cart"] = $cart;
            $_SESSION["modalMessage"] = colored("Added to cart.", "green");
            break;
        }
        case empty(cleanInput($_POST, "cart")): {
            if (empty($_SESSION["cart"])) {
                $_SESSION["modalMessage"] = colored("Your cart is still empty!", "red");
                break;
            }
            safeRedirect("cart");
            break;
        }
    }
}

$sort = array(
    "none" => array(
       "text" => "Sort by...",
       "query" => ""
    ),
    "cheap" => array(
        "text" => "Price (cheapest first)",
        "query" => " ORDER BY price"
    ),
    "expensive" => array(
        "text" => "Price (most expensive first)",
        "query" => " ORDER BY price DESC"
    ),
    "alpha" => array(
        "text" => "Alphabetically (A to Z)",
        "query" => " ORDER BY name"
    ),
    "beta" => array(
        "text" => "Alphabetically (Z to A)",
        "query" => " ORDER BY name DESC"
    )
);
$oneWeekAgo = "'" . date("Y-m-d", time() - (7 * 24 * 60 * 60)) . "'";

function printProducts($query): void
{
    global $database, $sort;
    $order = empty($_GET["sort"]) ? "" : $sort[$_GET["sort"]]["query"];
    $products = $database->query("SELECT * FROM products" . $query . $order);
    while($row = $products->fetch_assoc()) {
        $disabled = $row["amount"] > 0 ? "" : " disabled";
        echo "<form class='card card-dark-shadow' method='POST'>
                <img class='card-image' loading='lazy' src='Assets/products/$row[name].webp' alt='$row[name]' />
                <div style='padding: 1rem'>
                    <div class='card-title'>$row[name]</div>
                    <p>$$row[price]</p>
                    <div class='container container-col' style='padding: 0 1rem'>
                        <input type='hidden' name='id' value='$row[id]' required />
                        <label for='amount-$row[id]'>Amount:</label>
                        <input type='number' id='amount-$row[id]' name='amount' min='1' max='$row[amount]' value='1' required />
                        <input type='submit' class='button' name='add' value='Add to cart'$disabled />
                    </div>
                </div>
            </form>
            ";
    }
    echo "\n";
}

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="style.css">
        <title>Web store</title>
    </head>
    <body>
        <?php
        if (empty($_SESSION["cart"])) echo printHeader();
        else {
            $cartAmount = 0;
            foreach ($_SESSION["cart"] as $amount) $cartAmount += $amount;
            echo printHeader("<form method='POST'>
            <button class='button' type='submit' name='cart' value='cart'>Cart <span class='cart-badge'>$cartAmount</span></button>
        </form>");
        }
        ?>

        <div class="page container container-col splash-bg">
            <h1 style="font-size: 10em; color: white">Welcome</h1>
            <h2 style="font-size: 4em; color: white">to the fruit market</h2>
        </div>

        <div class="container container-row container-center" style="margin: 2rem">
            <label for="sort">Sort by: </label>
            <div style="margin: 1em 1em"></div>
            <select name="sort" id="sort" onchange="sort(this)">
                <?php
                foreach ($sort as $key => $value) {
                    $selected = !empty($_GET["sort"]) && $_GET["sort"] === $key ? " selected" : "";
                ?><option value="<?= $key ?>"<?= $selected ?>><?= $value["text"] ?></option>
                <?php } ?>
            </select>
        </div>

        <?php if ($database->query("SELECT * FROM products WHERE addedAt > $oneWeekAgo")->num_rows) { ?>
        <div class="page">
            <h1>New in stock!</h1>
            <div class="items-grid">
                <?php printProducts(" WHERE addedAt > $oneWeekAgo") ?>
            </div>
        </div>
        <div class="hr"></div>
        <div class="page">
            <h1>Other products:</h1>
            <div class="items-grid">
                <?php printProducts(" WHERE addedAt <= $oneWeekAgo") ?>
            </div>
        </div>
        <?php } else { ?>
        <div class="page items-grid">
            <?php printProducts("") ?>
        </div>
        <?php } ?>

<?php include "modal.php" ?>

<?php include "footer.php" ?>
        <script src="script.js"></script>
    </body>
</html>

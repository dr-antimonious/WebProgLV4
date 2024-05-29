<?php
session_start();
include "functions.php";
$database = new database();

if (empty($_SESSION["cart"])) safeRedirect("index");
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = cleanInput($_POST, "id");
    $amount = cleanInput($_POST, "amount");
    $firstName = cleanInput($_POST, "lastName");
    $lastName = cleanInput($_POST, "lastName");
    $telephone = cleanInput($_POST, "telephone");
    $address = cleanInput($_POST, "address");
    switch (false) {
        case empty(cleanInput($_POST, "store")): {
            safeRedirect("index");
            break;
        }
        case empty(cleanInput($_POST, "remove")): {
            if (empty($id)) break;
            unset($_SESSION["cart"][$id]);
            break;
        }
        case empty(cleanInput($_POST, "update")): {
            $query = "SELECT * FROM products WHERE id='$id' AND amount >= $amount";
            if (empty($id) || empty($amount) || !$database->query($query)->num_rows) {
                $_SESSION["modalMessage"] = colored("We don't have this many products in stock!", "red");
                break;
            }
            if ($amount == 0) unset($_SESSION["cart"][$id]);
            else $_SESSION["cart"][$id] = $amount;
            break;
        }
        case empty(cleanInput($_POST, "buy")): {
            if (empty($firstName) || empty($lastName) || empty($telephone) || empty($address)) break;
            $unavailable = array();
            $available = array();

            foreach ($_SESSION["cart"] as $id => $amount) {
                $item = $database->query("SELECT * FROM products WHERE id='$id'");
                if (!$item) continue;
                $item = $item->fetch_assoc();
                if (!$item) continue;

                if ($amount > $item["amount"]) {
                    $unavailable[] = "$item[name] x<span class='strike'>$amount</span> $item[amount]";
                    if ($item["amount"] == 0) unset($_SESSION["cart"][$id]);
                    else $_SESSION["cart"][$id] = $item["amount"];
                    continue;
                }
                $item["ordered"] = $amount;
                $available[] = $item;
            }

            if (!empty($unavailable)) {
                $text = "<ul>";
                foreach ($unavailable as $item) $text .= "\n<li>$item</li>";
                $text .= "\n</ul>";
                $_SESSION["modalMessage"] = colored("The following items are not available:", "red") . $text;
                break;
            }

            if (empty($available)) break;
            $uuid = uuid_v4();
            foreach ($available as $item) {
                echo $item["amount"] . "<br>";
                echo $item["ordered"] . "<br>";
                $newAmount = $item["amount"] - $item["ordered"];
                echo $newAmount . "<br>";
                $database->query("UPDATE products SET amount='$newAmount' WHERE id = '$item[id]'");
                $database->query("INSERT INTO items (name, amount, price, orderId) VALUES ('$item[name]', '$item[ordered]', '$item[price]', '$uuid')");
            }
            $date = date("Y-m-d H:i:s");
            $database->query("INSERT INTO orders (id, firstName, lastName, telephone, address, purchasedAt) VALUES
                  ('$uuid', '$firstName', '$lastName', '$telephone', '$address', '$date')");

            unset($_SESSION["cart"]);
            safeRedirect("receipt", "orderId=$uuid");
            break;
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
        <title>Cart</title>
    </head>
    <body>
        <?= printHeader("<form method='POST'>
                <input type='submit' class='button' name='store' value='Store'>
            </form>") ?>

        <div class="page items-grid">
            <?php
            $total = 0;
            foreach ($_SESSION["cart"] as $id => $amount) {
                $item = $database->query("SELECT * FROM products WHERE id = '$id'");
                if (!$item) continue;
                $item = $item->fetch_assoc();
                if (!$item) continue;
                $total += $item["price"] * $amount;
                ?><form class="item card card-dark-shadow" method="POST">
                <img class="card-image" loading="lazy" src="Assets/products/<?= $item["name"] ?>.webp" alt="<?= $item["name"] ?>" />
                <div style="padding: 1rem">
                    <div class="card-title"><?= $item["name"] ?></div>
                    <h3>$/piece: $<?= $item["price"] ?></h3>
                    <h3>Total: $<?= $item["price"] * $amount ?></h3>
                    <input type="hidden" name="id" value="<?= $id ?>" />
                    <div style="padding: 0.25rem 0"></div>
                    <label for="amount-<?= $id ?>">Amount:</label>
                    <input type="number" id="amount-<?= $id ?>" name="amount" value="<?= $amount ?>" required />
                    <input type="submit" class="button button-red" name="remove" value="Remove" />
                    <input type="submit" class="button" name="update" value="Update" />
                </div>
            </form>
            <?php } ?>
        </div>

        <form class="page container container-center" method="POST">
            <div class="container container-col" style="width: 30vw">
                <h1>Total: $<?= $total ?></h1>
                <div class="hr"></div>
                <label for="first-name">First name:</label>
                <input type="text" name="firstName" id="first-name" required />
                <label for="last-name">Last name:</label>
                <input type="text" name="lastName" id="last-name" required />
                <label for="telephone">Telephone:</label>
                <input type="tel" name="telephone" id="telephone" required />
                <label for="address">Address:</label>
                <input type="text" name="address" id="address" required />
                <div class="container container-center">
                    <button class="button" type="submit" name="buy" value="buy" style="width: 20vw">
                        Buy
                    </button>
                </div>
                <div class="hr"></div>
            </div>
        </form>

<?php include "modal.php" ?>

<?php include "footer.php" ?>
        <script src="script.js"></script>
    </body>
</html>

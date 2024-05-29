<?php
session_start();
include "functions.php";
$database = new database();

$order = array();
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $orderId = cleanInput($_GET, "orderId");
    $query = "SELECT * FROM orders WHERE id = '$orderId'";
    if (!empty($orderId)) $order = $database->query($query);
    if ($order) $order = $order->fetch_assoc();
    if (!$order) $order = array();
}

if (!$order) {
    $_SESSION["modalMessage"] = colored("Receipt does not exist.", "red");
    safeRedirect("index");
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="style.css">
        <title>Receipt</title>
    </head>
    <body>
    <?= printHeader("<form method='POST'>
                <input type='submit' class='button' name='store' value='Store'>
            </form>") ?>

    <div class="page items-grid">
        <?php
        $total = 0;
        $items = $database->query("SELECT * FROM items WHERE orderId = '$order[id]'");
        while($row = $items->fetch_assoc()) {
            $total += $row["price"] * $row["amount"];
            ?><div class="item card card-dark-shadow">
            <img class="card-image" loading="lazy" src="Assets/products/<?= $row["name"] ?>.webp" alt="<?= $row["name"] ?>" />
            <div style="padding: 1rem" class="container container-col">
                <h3>Name:</h3>
                <?= $row["name"] ?>
                <h3>Price:</h3>
                $<?= $row["price"] ?>
                <h3>Amount:</h3>
                <?= $row["amount"] ?>
                <h3>Total:</h3>
                $<?= $row["price"] * $row["amount"] ?>
            </div>
        </div>
        <?php } ?>
    </div>

    <div class="page container container-center">
        <div class="container container-col" style="width: 30vw">
            <h1>Total: $<?= $total ?></h1>
            <div class="hr"></div>
            <h3>Order ID:</h3>
            <?= $order["id"] ?>
            <h3>First name:</h3>
            <?= $order["firstName"] ?>
            <h3>Last name:</h3>
            <?= $order["lastName"] ?>
            <h3>Telephone:</h3>
            <?= $order["telephone"] ?>
            <h3>Address:</h3>
            <?= $order["address"] ?>
            <h3>Purchased at:</h3>
            <?= $order["purchasedAt"] ?>
            <div class="hr"></div>
        </div>
    </div>

<?php include "footer.php" ?>
    </body>
</html>


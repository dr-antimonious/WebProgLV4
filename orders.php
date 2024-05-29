<?php
session_start();
include "functions.php";
$database = new database();

validateLogin();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $receipt = cleanInput($_POST, "receipt");
    switch (false) {
        case empty($receipt): {
            safeRedirect("receipt", "orderId=$receipt");
            break;
        }
        case empty(cleanInput($_POST, "dashboard")): {
            safeRedirect("dashboard");
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
        <title>Orders</title>
    </head>
    <body>
    <?= printHeader("<form method='POST'>
                <input class='button' type='submit' name='dashboard' value='Dashboard' />
            </form>");
    ?>

    <div class="page">
        <table>
            <thead>
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Telephone</th>
                    <th>Address</th>
                    <th>Purchased at</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $orders = $database->query("SELECT * FROM orders");
                if ($orders->num_rows) {
                    while ($row = $orders->fetch_assoc()) {
                        ?>
                        <tr>
                            <td><?= $row["firstName"] ?></td>
                            <td><?= $row["lastName"] ?></td>
                            <td><?= $row["telephone"] ?></td>
                            <td><?= $row["address"] ?></td>
                            <td><?= $row["purchasedAt"] ?></td>
                            <td>
                                <form method="POST">
                                    <button class="button" type="submit" name="receipt" value="<?= $row["id"] ?>">
                                        Receipt
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php
                    }
                } else echo colored("No orders yet...", "red"); ?>
            </tbody>
        </table>
    </div>

<?php include "footer.php" ?>
    </body>
</html>

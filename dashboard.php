<?php
session_start();
include "functions.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    switch (false) {
        case empty($_POST["logout"]): {
            setcookie("email", "");
            setcookie("password", "");
            safeRedirect("admin");
            break;
        }
        case empty($_POST["home"]): {
            safeRedirect("index");
            break;
        }
        case empty($_POST["products"]): {
            safeRedirect("products");
            break;
        }
        case empty($_POST["orders"]): {
            safeRedirect("orders");
            break;
        }
    }
} else validateLogin();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="style.css">
        <title>Dashboard</title>
    </head>
    <body>
        <?= printHeader("<form method='POST'>
                <input class='button' type='submit' name='logout' value='Log out' />
            </form>");
        ?>

        <div class="page container container-col container-center">
            <form class="container container-col container-center" method="POST">
                <input class="button" type="submit" name="home" value="Home" />
                <input class="button" type="submit" name="products" value="Products" />
                <input class="button" type="submit" name="orders" value="Orders" />
            </form>
        </div>

<?php include "footer.php" ?>
    </body>
</html>

<?php
session_start();
include "functions.php";
$database = new database();

validateLogin();

$email = $emailError = $password = $passwordError = $loginError = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["email"])) $emailError = colored("Required", "red");
    else {
        $email = cleanInput($_POST, "email");
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailError = colored("Invalid email", "red");
            $email = "";
        }
    }
    if (empty($_POST["password"])) $passwordError = colored("Required", "red");
    else $password = cleanInput($_POST, "password");

    if (!empty($email) && !empty($password)) {
        $password = hash("sha256", $password);

        $return = $database->query("SELECT * FROM admins WHERE email = '$email'");
        if ($return->num_rows) {
            if ($return->fetch_array()["passwordHash"] === $password) {
                setcookie("email", $email, time() + 3600);
                setcookie("password", $password, time() + 3600);
                safeRedirect($_SESSION["redirect"] ?? "dashboard");
            }
            else $passwordError = colored("Invalid password", "red");
        }
        else $loginError = colored("No user with these credentials", "red");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="style.css">
        <title>Admin</title>
    </head>
    <body>
        <div class="page container container-col container-center">
            <form class="container container-col container-center" method="POST">
                <?= $emailError ?>
                <input type="email" name="email" placeholder="Email" required>
                <?= $passwordError ?>
                <input type="password" name="password" placeholder="Password" required>
                <button class="button" type="submit">Login</button>
                <?= $loginError ?>
            </form>
        </div>

<?php include "footer.php" ?>
    </body>
</html>

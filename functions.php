<?php
class database extends mysqli
{

    #[Override]
    public function __construct()
    {
        parent::__construct("localhost", "root", "");

        if ($this->connect_errno) die("Database connection failed: " . $this->connect_error);

        $this->query("CREATE DATABASE IF NOT EXISTS shop");
        $this->query("USE shop");
    }

    /**
     * @return true|mysqli_result For successful SELECT, SHOW, DESCRIBE or EXPLAIN queries, mysqli_query()
     * will return a mysqli_result object. For other successful queries mysqli_query() will return TRUE.
     */
    #[Override]
    public function query(string $query, int $result_mode = MYSQLI_STORE_RESULT): true|mysqli_result
    {
        $result = parent::query($query);
        if (!$result) die("Database connection failed: " . $this->connect_error);
        return $result;
    }

}

function cleanInput($array, $index): string {
    if (!isset($array[$index])) return "";
    return htmlspecialchars(stripslashes(trim($array[$index])));
}

function printHeader($element = ""): string
{
    return "<header>
            <img class='logo' src='Assets/Logo.png' alt='Logo' />" . ($element ? "
            " . $element : "") . "
        </header>";
}

function colored($text, $color): string {
    return "<div style='color: $color'>$text</div>";
}

function validateLogin(): void
{
    $isLoggedIn = false;
    if (!empty($_COOKIE["email"]) && !empty($_COOKIE["password"])) {
        $database = new database();
        $return = $database->query("SELECT * FROM admins WHERE email = '$_COOKIE[email]' AND passwordHash = '$_COOKIE[password]'");
        if ($return->num_rows) $isLoggedIn = true;
    }

    if ($isLoggedIn) {
        if (!isOnRoute("admin")) return;
        $redirect = $_SESSION["redirect"] ?? "dashboard";
        safeRedirect($redirect);
    }
    else {
        setcookie("email", "");
        setcookie("password", "");
        if (isOnRoute("admin")) return;
        $_SESSION["redirect"] = substr($_SERVER["PHP_SELF"], strrpos($_SERVER["PHP_SELF"], "/") + 1, -4);
        safeRedirect("admin");
    }
}

function isOnRoute($route): bool
{
    return str_ends_with($_SERVER["PHP_SELF"], "$route.php");
}

function safeRedirect($routeFile, $query = ""): void
{
    if (isOnRoute($routeFile)) return;
    header("Location: $routeFile.php" . (empty($query) ? "" : "?" . $query));
    exit();
}

function uuid_v4(): string
{
    $data = openssl_random_pseudo_bytes(16);

    // set version to 0100
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    // set bits 6-7 to 10
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

<?php
$lifetime=600;
session_start();
setcookie(session_name(),session_id(),time()+$lifetime);

if (!isset($_SESSION["logged-in"]) || $_SESSION["logged-in"] == false){
    echo "template/klantenportaal/login.html";
    exit();
}

include_once("config.php");

$bedrijf = $_SESSION["bedrijf"];

$link_accounts = new mysqli(host, username, password, database_accounts); 

$stmt = $link_accounts->prepare("SELECT logo FROM table WHERE bedrijf = ?");

$stmt->bind_param("s", $bedrijf);

$stmt->execute();
$result = $stmt->get_result();
$result = $result->fetch_all(MYSQLI_ASSOC);
$logo = $result[0]["logo"];

echo $logo; //. "<br><span class='brand-text font-weight-light'>" . $bedrijf . "</span>";
?>
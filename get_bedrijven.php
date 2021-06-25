<?php

$lifetime=600;
  session_start();
  setcookie(session_name(),session_id(),time()+$lifetime);

if (!isset($_SESSION["logged-in"]) || $_SESSION["logged-in"] == false){
    echo "template/klantenportaal/login.html";
    exit();
}

$bedrijven = "";

include_once("config.php");

$link_accounts = new mysqli(host, username, password, database_accounts); 

$result = $link_accounts->query("SELECT bedrijf FROM dummy")->fetch_all(MYSQLI_ASSOC);

for ($i = 0; $i < count($result); $i++){
     $bedrijven .= "<option>" . $result[$i]["bedrijf"] . "</option>"; 
}

echo $bedrijven;
?>

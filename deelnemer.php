<?php
header("Content-Type: application/json"); 
$data = json_decode(file_get_contents("php://input")); 
$lifetime=600;
  session_start();
  setcookie(session_name(),session_id(),time()+$lifetime);


if (!isset($_SESSION["logged-in"]) || $_SESSION["logged-in"] == false){
    header("Location: template/klantenportaal/login.html");
    exit();
}

include_once("config.php");

$link = new mysqli(host, username, password, database_accounts);

$voornaam = explode(" ", htmlspecialchars($data))[0]; 
$achternaam = explode(" ", htmlspecialchars($data))[1];

$stmt = $link->prepare("SELECT roepnaam, achternaam, geboortedatum, geboorteplaats, kandidaat_nummer_cbr FROM `" . $_SESSION["bedrijf"] . "` WHERE roepnaam = ? AND achternaam = ?");
$stmt->bind_param('ss', $voornaam, $achternaam);
$stmt->execute();
$result = $stmt->get_result();
$result = $result->fetch_all(MYSQLI_ASSOC);

if ($result){
    echo "<table><tbody><tr><th><span id='details-sluiten'>X</span>Roepnaam</th><td>" . $result[0]['roepnaam'] . "</td></tr><tr><th>Achternaam</th><td>" . $result[0]['achternaam'] . "</td></tr><tr><th>Geboortedatum</th><td>" . $result[0]['geboortedatum'] . "</td></tr><tr><th>Geboorteplaats</th><td>" . $result[0]['geboorteplaats'] . "</td></tr><tr><th>CBR nummer<button id='sluiten-knop'>Sluiten</button></th><td>" . $result[0]['kandidaat_nummer_cbr'] . "</td></tr></tbody></table>";
}
?>
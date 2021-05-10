<?php
$lifetime=600;
  session_start();
  setcookie(session_name(),session_id(),time()+$lifetime);
if (isset($_SESSION["logged-in"]) && $_SESSION["logged-in"] == true){
    echo "<a href='home.html' class='d-block'>" . $_SESSION["gebruiker"] . "</a>"; 
    exit();
}

else {
    echo "template/klantenportaal/login.html";
    exit();
}
?>
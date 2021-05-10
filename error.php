<?php
$lifetime=600;
  session_start();
  setcookie(session_name(),session_id(),time()+$lifetime);
if ($_SESSION["error-message"]){
    echo $_SESSION["error-message"];
    $_SESSION["error-message"] = "";
    exit();
}

else {
    echo "";
    exit();
}
?>
<?php
$lifetime=600;
session_start();
include_once("config.php");

$link = new mysqli(host, username, password, database_accounts); 
if(!mysqli_set_charset($link, 'utf8')){
    printf("Error loading character set utf8: %s\n", $link->error);
    printf("Current character set: %s\n", $link->character_set_name());
    die();
};   

if (!$link){
     die('Connect Error (' . $mysqli->connect_errno . ') '
            . $mysqli->connect_error);
   }

$gebruikersnaam; $wachtwoord;

if ($_SERVER["REQUEST_METHOD"] === "POST"){
    $gebruikersnaam = htmlspecialchars($_POST["gebruikersnaam"]); 
    
    if ($gebruikersnaam == ""){
        $_SESSION["error-message"] = "Vul a.u.b. een gebruikersnaam in.";
        header("Location: template/klantenportaal/login.html");
        exit();
    }
    
    $wachtwoord = htmlspecialchars($_POST['wachtwoord']);
    
    if ($wachtwoord == ""){
        $_SESSION["error-message"] = "Vul a.u.b. een wachtwoord in.";
        header("Location: template/klantenportaal/login.html");
        exit();
    }
    
    
        $stmt = $link->prepare("SELECT gebruiker, wachtwoord, bedrijf FROM adviesbureauRenB WHERE gebruiker = ? ");
        $stmt->bind_param("s", $gebruikersnaam);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);                  
        
    
        if (password_verify($wachtwoord, $result[0]['wachtwoord'])){
            $_SESSION["logged-in"] = true;
            $_SESSION["gebruiker"] = $gebruikersnaam;
            $_SESSION["bedrijf"] = $result[0]['bedrijf'];
            setcookie(session_name(),session_id(),time()+$lifetime);
            header("Location: template/klantenportaal/home.html");
            exit();
        }
    
        else {
            $_SESSION["error-message"] = "Uw gebruikersnaam of wachtwoord is incorrect. Ga deze na en probeer het opnieuw.";
            header("Location: template/klantenportaal/login.html");
            exit();
        }
    
    
}
 
    header("Location: template/klantenportaal/login.html");

?>
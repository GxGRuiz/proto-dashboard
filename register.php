<?php
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

$gebruikersnaam; $wachtwoord; $bevestig_wachtwoord; $email; $bedrijf;



if ($_SERVER["REQUEST_METHOD"] === "POST"){
    $gebruikersnaam = htmlspecialchars($_POST["gebruiker"]); 
    
    if ($gebruikersnaam == ""){
        $_SESSION["error-message"] = "Vul a.u.b. een gebruikersnaam in.";
        header("Location: template/klantenportaal/register.html");
        exit();
    }
    
    
    $email = htmlspecialchars($_POST["email"]); 
    
    if ($email == ""){
        $_SESSION["error-message"] = "Vul a.u.b. een email in.";
        header("Location: template/klantenportaal/register.html");
        exit();
    }
    
    
    $bedrijf = htmlspecialchars($_POST["bedrijf"]); 
    
    if ($bedrijf == ""){
        $_SESSION["error-message"] = "Vul a.u.b. een bedrijf in.";
        header("Location: template/klantenportaal/register.html");
        exit();
    }
    
    $wachtwoord = htmlspecialchars($_POST['wachtwoord']);
    
    if ($wachtwoord == ""){
        $_SESSION["error-message"] = "Vul a.u.b. een wachtwoord in.";
        header("Location: template/klantenportaal/register.html");
        exit();
    }
    
     $bevestig_wachtwoord = htmlspecialchars($_POST['wachtwoord-bevestiging']);
    
    if ($bevestig_wachtwoord == ""){
        $_SESSION["error-message"] = "Bevestig a.u.b uw wachtwoord";
        header("Location: template/klantenportaal/register.html");
        exit();
    }
    
    if ($bevestig_wachtwoord != $wachtwoord){
        $_SESSION["error-message"] = "Wachtwoorden komen niet overeen.";
        header("Location: template/klantenportaal/register.html");
        exit();
    }
    
        $stmt = $link->prepare("INSERT INTO dummy (gebruiker, wachtwoord, email, bedrijf) VALUES(?, ?, ?, ?)");
        $stmt->bind_param("ssss", $gebruikersnaam, password_hash($wachtwoord, PASSWORD_DEFAULT), $email, $bedrijf);             
        
    
        if ($stmt->execute()){
            $_SESSION["error-message"] = "Account aangemaakt.";
            header("Location: template/klantenportaal/login.html");
            exit();
        }
    
        else {
            $_SESSION["error-message"] = $stmt->error;
            header("Location: template/klantenportaal/register.html");
            exit();
        }
    
    
}
 
    header("Location: template/klantenportaal/register.html");

?>
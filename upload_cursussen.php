<?php

$lifetime=600;
  session_start();
  setcookie(session_name(),session_id(),time()+$lifetime);

if (!isset($_SESSION["logged-in"]) || $_SESSION["logged-in"] == false){
    header("Location: template/klantenportaal/login.html");
    exit();
}

include_once("config.php");


if ($_SERVER["REQUEST_METHOD"] === "POST"){
    
   $bedrijf = htmlspecialchars($_POST["bedrijf"]);
    
    if ($bedrijf == ""){
        $_SESSION["error-message"] = "Kies a.u.b een bedrijf.";
        header("Location: template/klantenportaal/upload.html");
        exit;
    }
    
   
   if (pathinfo($_FILES["medewerker-data"]["name"])["extension"] != "csv"){
        $_SESSION["error-message"] = "Kies a.u.b. een csv bestand met cursussen";
        header("Location: template/klantenportaal/upload.html");
        exit;   
    }
    $uploaddir = 'uploads/';
$uploadfile = $uploaddir . basename($_FILES['medewerker-data']['name']);

    
if (move_uploaded_file($_FILES['medewerker-data']['tmp_name'], $uploadfile)) {
    $test = fopen($uploadfile, 'r');

    $row = 1;
    $veld_namen = [];
    $link_cursus = new mysqli(host, username, password, database_cursus); 
    
    
    while (($content = fgetcsv($test)) !== FALSE) {
        $content = explode(";", $content[0]);
        $num = count($content);
        
        if ($row > 1){
            
            $nieuwe_rij = [];
            
            for ($c=0; $c < $num; $c++) {
                 echo $content[$c] . "<br />\n";
                 $nieuwe_rij[] = $content[$c];
                }
            
            $stmt = $link_cursus->prepare("SELECT * FROM " . $bedrijf . " WHERE deelnemer = ? AND cursus_code = ? AND cursus_naam = ?");
            $stmt->bind_param("sss", $naam, $cursus_code, $cursus_naam);
            
            $naam = $nieuwe_rij[0];
            $cursus_code = $nieuwe_rij[1];
            $cursus_naam = $nieuwe_rij[2];
            
            $stmt->execute();
            $result = $stmt->get_result();
            $result = $result->fetch_all(MYSQLI_ASSOC);
            
            if ($result){
            
                $stmt = $link_cursus->prepare("UPDATE " . $bedrijf . " SET behaald = ?, punten = ?, verloopdatum = ? WHERE deelnemer = ? AND cursus_code = ? AND cursus_naam = ?");
            $stmt->bind_param('iissss', $behaald, $punten, $verloopdatum, $naam, $cursus_code, $cursus_naam);
            
            $punten = $nieuwe_rij[3];
            $verloopdatum = date('Y-m-d', strtotime($nieuwe_rij[4]));
            
            if ($punten == 7){
                $behaald = 1;
            }
            
            else {
                $behaald = 0;
            }
            
            $stmt->execute();
                
               }
            
            else{
                
            $stmt = $link_cursus->prepare("INSERT INTO " . $bedrijf . "(deelnemer, cursus_code, cursus_naam, behaald, punten, verloopdatum) VALUES(?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('sssiis', $naam, $cursus_code, $cursus_naam, $behaald, $punten, $verloopdatum);
            
            $punten = $nieuwe_rij[3];
            $verloopdatum = date('Y-m-d', strtotime($nieuwe_rij[4]));
            
            if ($punten == 7){
                $behaald = 1;
            }
            
            else {
                $behaald = 0;
            }
            
            $stmt->execute();
            
            }
        }
        
        else {
            
              for ($c=0; $c < $num; $c++) {
                   $veld_namen[] = $content[$c];
                  }
            
            if ($veld_namen[0] === "naam" && $veld_namen[1] === "cursus id" && $veld_namen[2] === "cursus naam" && $veld_namen[3] === "punten" && $veld_namen[4] === "verloopdatum"){
                $good = "to go";
            }
            
            else {
                $_SESSION["error-message"] = "Veldnamen zijn onjuist, pas deze in de file aan.";
                header("Location: template/klantenportaal/upload.html");
                exit();
            }
            
        }
        
        
        $row++;
    }

    
} else {
    echo "Possible file upload attack!\n";
}


}
header("Location: template/klantenportaal/upload.html");
                exit();
?>
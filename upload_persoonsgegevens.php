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
        $_SESSION["error-message"] = "Kies a.u.b. een csv bestand met persoonsgegevens";
        header("Location: template/klantenportaal/upload.html");
        exit;   
    }
    
    $uploaddir = 'uploads/';
$uploadfile = $uploaddir . basename($_FILES['medewerker-data']['name']);

    
if (move_uploaded_file($_FILES['medewerker-data']['tmp_name'], $uploadfile)) {
    $test = fopen($uploadfile, 'r');

    $row = 1;
    $veld_namen = [];
    $link_accounts = new mysqli(host, username, password, database_accounts); 
    
    
    while (($content = fgetcsv($test)) !== FALSE) {
        $content = explode(";", $content[0]);
        $num = count($content);
        
        if ($row > 1){
            
            $nieuwe_rij = [];
            
            for ($c=0; $c < $num; $c++) {
                 echo $content[$c] . "<br />\n";
                 $nieuwe_rij[] = $content[$c];
                }
            
            $stmt = $link_accounts->prepare("SELECT * FROM " . $bedrijf . " WHERE roepnaam = ? AND achternaam = ? AND geboortedatum = ? AND geboorteplaats = ?");
            $stmt->bind_param("ssss", $roepnaam, $achternaam, $geboortedatum, $geboorteplaats);
            
            $roepnaam = $nieuwe_rij[0];
            $achternaam = $nieuwe_rij[1];
            $geboortedatum = date('Y-m-d', strtotime($nieuwe_rij[2]));
            $geboorteplaats = $nieuwe_rij[3];
            
            $stmt->execute();
            $result = $stmt->get_result();
            $result = $result->fetch_all(MYSQLI_ASSOC);
            
            if ($result){
            
                $stmt = $link_accounts->prepare("UPDATE " . $bedrijf . " SET kandidaat_nummer_cbr = ?, verloopdatum_code_95 = ?, verloopdatum_rijbewijs_c = ? WHERE roepnaam = ? AND achternaam = ? AND geboortedatum = ? AND geboorteplaats = ?");
            $stmt->bind_param('sssssss', $kandidaat_nummer_cbr, $verloopdatum_code_95, $verloopdatum_rijbewijs_c, $roepnaam, $achternaam, $geboortedatum, $geboorteplaats);
            
             $kandidaat_nummer_cbr = $nieuwe_rij[4];    
            $verloopdatum_code_95 = date('Y-m-d', strtotime($nieuwe_rij[5]));
            $verloopdatum_rijbewijs_c = date('Y-m-d', strtotime($nieuwe_rij[6]));
            
            $stmt->execute();
               }
            
            else{
                
            $stmt = $link_accounts->prepare("INSERT INTO " . $bedrijf . " (roepnaam, achternaam, geboortedatum, geboorteplaats, kandidaat_nummer_cbr, verloopdatum_code_95, verloopdatum_rijbewijs_c) VALUES(?, ?, ?, ?, ?, ?, ?)");  
            $stmt->bind_param('sssssss', $roepnaam, $achternaam, $geboortedatum, $geboorteplaats, $kandidaat_nummer_cbr, $verloopdatum_code_95, $verloopdatum_rijbewijs_c);
            
            $kandidaat_nummer_cbr = $nieuwe_rij[4];    
            $verloopdatum_code_95 = date('Y-m-d', strtotime($nieuwe_rij[5]));
            $verloopdatum_rijbewijs_c = date('Y-m-d', strtotime($nieuwe_rij[6]));
            
            $stmt->execute();
            }
        }
        
        else {
            
              for ($c=0; $c < $num; $c++) {
                   $veld_namen[] = $content[$c];
                  }
            
            if ($veld_namen[0] === "Roepnaam" && $veld_namen[1] === "Achternaam" && $veld_namen[2] === "Geboortedatum" && $veld_namen[3] === "Geboorteplaats" && $veld_namen[4] === "CBR nummer" && $veld_namen[5] === "Verloopdatum code 95" && $veld_namen[6] === "Verloopdatum rijbewijs c"){
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
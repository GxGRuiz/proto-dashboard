<?php
header("Content-Type: application/json"); 
$data = json_decode(file_get_contents("php://input")); 
$lifetime=600;
  session_start();
  setcookie(session_name(),session_id(),time()+$lifetime);


if (!isset($_SESSION["logged-in"]) || $_SESSION["logged-in"] == false){
    echo "template/klantenportaal/login.html";
    exit();
}

include_once("config.php");

$limit = htmlspecialchars($data[0]);

if ($limit == ""){
    $limit = 10;
}

$offset = (htmlspecialchars($data[1]) - 1) * $limit;

if ($offset == "" || $offset < 0){
    $offset = 0;
}

$overzicht = "<table><thead><tr><th>Medewerker</th><th>Cursus Code</th><th>Cursus Naam</th><th>Punten</th><th>Verloopdatum</th><th>Verloopdatum Code 95</th><th>Verloopdatum Rijbewijs C</th><th>Totaal</th></tr></thead><tbody>";

$link = new mysqli(host, username, password, database_cursus);

$link_persoongegevens = new mysqli(host, username, password, database_accounts);

$paginas = ceil(count($link->query("SELECT * FROM `" . $_SESSION['bedrijf'] . "` WHERE behaald = 1 ORDER BY deelnemer ASC")->fetch_all(MYSQLI_ASSOC)) / $limit);

$result = $link->query("SELECT deelnemer, cursus_code, cursus_naam, behaald, punten, verloopdatum FROM `" . $_SESSION['bedrijf'] . "` WHERE behaald = 1 ORDER BY deelnemer ASC LIMIT " . $limit . " OFFSET " . $offset)->fetch_all(MYSQLI_ASSOC);


if ($result){
    $rowspan_array = [];
    $totaal_array = [];
    
    for ($i = 0; $i < count($result); $i++){
         $rowspan = 0;
         $totaal = 0;
         $deelnemer = $result[$i]["deelnemer"];
         
         for ($j = $i; $j < count($result); $j++){
              if ($result[$j]["deelnemer"] == $deelnemer){
                  $rowspan++;
                  $totaal += $result[$j]["punten"];
              }
              else {
                  continue;
              }
         } 
         
        $i += $rowspan - 1;
        $rowspan_array[] = $rowspan;
        $totaal_array[] = $totaal;
    }
       
    for ($i = 0; $i < count($result); $i++){
        $rowspan = array_shift($rowspan_array);
        $totaal = array_shift($totaal_array);
        
        $hele_naam = explode(" ", $result[$i]['deelnemer']);
        
        $voornaam = $hele_naam[0];
        $achternaam = $hele_naam[1];
        
        for ($j = 2; $j < count($hele_naam); $j++){
            $achternaam .= " " . $hele_naam[$j];
        } 
        
        $verloopdata = $link_persoongegevens->query("SELECT verloopdatum_code_95, verloopdatum_rijbewijs_c FROM `" . $_SESSION['bedrijf'] . "` WHERE roepnaam = '" . $voornaam . "' AND achternaam = '" . $achternaam . "'")->fetch_all(MYSQLI_ASSOC);
        
        
        $overzicht .= "<tr><td name='" . $result[$i]["deelnemer"] . "' rowspan=" . $rowspan . ">" . $result[$i]["deelnemer"] . "</td>";
        
        
        if ($result[$i]["cursus_code"][0] === "W"){
           $overzicht .= "<td class='W-cursus'>" . $result[$i]["cursus_code"] . "</td>";
        }
        
        if ($result[$i]["cursus_code"][0] === "U"){
            $overzicht .= "<td class='U-cursus'>" . $result[$i]["cursus_code"] . "</td>";
        }
        
        
        $overzicht .= "<td>" . $result[$i]["cursus_naam"] . "</td><td>" . $result[$i]["punten"] . "</td><td>" . $result[$i]["verloopdatum"] . "</td><td rowspan='" . $rowspan . "'>" . $verloopdata[0]['verloopdatum_code_95'] . "</td><td rowspan='" . $rowspan . "'>" . $verloopdata[0]['verloopdatum_rijbewijs_c'] . "</td><td rowspan='" . $rowspan . "'>" . $totaal . "</td></tr>";
    
         
        
        for ($j = 1; $j < $rowspan; $j++){
             $overzicht .= "<tr>";
        
        
             if ($result[$i + $j]["cursus_code"][0] === "W"){
                 $overzicht .= "<td class='W-cursus'>" . $result[$i + $j]["cursus_code"] . "</td>";
             }
        
             if ($result[$i + $j]["cursus_code"][0] === "U"){
                 $overzicht .= "<td class='U-cursus'>" . $result[$i + $j]["cursus_code"] . "</td>";
             }
        
        
             $overzicht .= "<td>" . $result[$i + $j]["cursus_naam"] . "</td><td>" . $result[$i + $j]["punten"] . "</td><td>" . $result[$i + $j]["verloopdatum"] . "</td></tr>";
            
        }
        $i += $rowspan - 1;
    }
}

$overzicht .= "<tr><td></td><td></td><td></td><td></td><td></td><td></td><td id='praktijk'>Praktijk</td><td id='theorie'>Theorie</td></tr></tbody></table>
<div>
Pagina:
<select class='paginas' onchange='VeranderPagina();'>";

for ($i = 1; $i < $paginas+1; $i++){
     if ($i == htmlspecialchars($data[1])){
    
     $overzicht .= "<option selected>" . $i . "</option>";
     }
    
    else {
        $overzicht .= "<option>" . $i . "</option>";
    }
}

$overzicht .= "</select></div>";

echo $overzicht;
?>
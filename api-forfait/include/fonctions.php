<?php

// Cette fonction prend l'object au format tablulaire SQL 
// et retourne un objet dont la structure correspond au format
// devant être retourné par l'API. 
function ConversionHotelsEnObjet($hotels) {
    $hotelOBJ = new stdClass();
    $hotelOBJ->destination = $hotels["destination"];
    $hotelOBJ->ville_depart = $hotels["ville_depart"];

        $hotelOBJ->hotel = new stdClass();
        $hotelOBJ->hotel->nom = $hotels["nom"];
        $hotelOBJ->hotel->coordonnees = $hotels["coordonnees"];
        $hotelOBJ->hotel->nombre_etoiles = $hotels["nombre_etoiles"];
        $hotelOBJ->hotel->nombre_chambre = $hotels["nombre_chambre"];
        $hotelOBJ->hotel->caracteristiques = explode(";", $hotels["caracteristiques"]);

    $hotelOBJ->date_depart = $hotels["date_depart"];
    $hotelOBJ->date_retour = $hotels["date_retour"];
    $hotelOBJ->prix = $hotels["prix"];
    $hotelOBJ->rabais = $hotels["rabais"];
    $hotelOBJ->vedette = $hotels["vedette"];
       

    return $hotelOBJ;
}   

?>
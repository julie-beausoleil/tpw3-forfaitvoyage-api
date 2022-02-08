<?php
    include_once '../include/config.php';
    include_once '../include/fonctions.php'; 

    header('Content-Type: application/json'); //Indique que c'est une application JSON
    header('Access-Control-Allow-Origin: *'); //Permet à n'importe qui d'intéragir avec l'API

    // Établissement de la connexion
	$mysqli = new mysqli($host, $username, $password, $database); 
	if ($mysqli -> connect_errno) { // Affichage d'une erreur si la connexion échoue 
		echo 'Échec de connexion à la base de données MySQL: ' . $mysqli -> connect_error; 
		exit(); }
	
    $corpsJSON = file_get_contents('php://input'); 
    $data = json_decode($corpsJSON, TRUE);

switch($_SERVER['REQUEST_METHOD']) {


    // Gestion des demandes de type GET
    case 'GET': 
        if(isset($_GET['id'])) {
            //Gére GET une seul enregistrement
            if ($requete = $mysqli->prepare("SELECT * FROM forfaits WHERE id=?")) {
                $requete->bind_param("i", $_GET['id']);
                $requete->execute();

                $resultat_requete = $requete->get_result();
                $hotels = $resultat_requete->fetch_assoc();

                // Convesion de l'objet au format JSON désiré
		        $hotelObj = ConversionHotelsEnObjet($hotels);

                echo json_encode($hotelObj, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                $requete->close(); // Fermeture du traitement
            }
        } else {


            $requete = $mysqli->query("SELECT * FROM forfaits"); // Création d'une requête préparée
            $listehotelsObj = [];
    
            while ($hotels = $requete->fetch_assoc()) {
                // Conversion de l'objet au format JSON désiré
                
                $hotelObj = ConversionHotelsEnObjet($hotels);
    
                // Ajout du forfait à la liste
                array_push($listehotelsObj, $hotelObj);
            }
    
            echo json_encode($listehotelsObj, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $requete->close();  // Fermeture du traitement
        } 
        break;

    
   // Gestion des demandes de type POST
   case 'POST':  // GESTION DES DEMANDES DE TYPE POST
	$reponse = new stdClass();
	$reponse->message = "Ajout d'un forfait: ";
	
	$corpsJSON = file_get_contents('php://input');
	$data = json_decode($corpsJSON, TRUE); 

	$destination = $data['destination'];
    $ville_depart = $data['ville_depart'];
	
    $hotel_nom = $data['hotel']['nom'];
    $hotel_coordonnees = $data['hotel']['coordonnees'];
    $hotel_nombre_etoiles = $data['hotel']['nombre_etoiles'];
    $hotel_nombre_chambre = $data['hotel']['nombre_chambre'];
    $hotel_caracteristiques = $data['hotel']['caracteristiques'];
	
  
    $date_depart = $data['date_depart'];
	$date_retour = $data['date_retour'];
	$prix = $data['prix'];
	$rabais = $data['rabais'];	
	$vedette = $data['vedette'];	



	if(isset($destination) && isset($ville_depart) && isset($hotel_nom) && isset($hotel_coordonnees) && isset($hotel_nombre_etoiles) && isset($hotel_nombre_chambre)  && isset($hotel_caracteristiques) && isset($date_depart) && isset($date_retour) && isset($prix) && isset($rabais) && isset($vedette)) {
	  $hotel_caracteristiques_str = implode(';', $hotel_caracteristiques);

      if ($requete = $mysqli->prepare("INSERT INTO forfaits (destination, ville_depart, nom, coordonnees, nombre_etoiles, nombre_chambre, caracteristiques, date_depart, date_retour, prix, rabais, vedette) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);")) {      
		$requete->bind_param("ssssiisssiis", $destination, $ville_depart, $hotel_nom, $hotel_coordonnees, $hotel_nombre_etoiles, $hotel_nombre_chambre, $hotel_caracteristiques_str, $date_depart, $date_retour, $prix, $rabais, $vedette);

        if($requete->execute()) { 
          $reponse->message .= "Succès";  
        } else {
          $reponse->message .=  "Erreur dans l'exécution de la requête";  
        }

        $requete->close(); 
      } else  {
        $reponse->message .=  "Erreur dans la préparation de la requête";  
      } 
    } else {
		$reponse->message .=  "Erreur dans le corps de l'objet fourni";  
	}
	echo json_encode($reponse, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	
	break;
 
    // Gestion des demandes de type PUT (UPDATE)
 case 'PUT':
    
    
            $reponse = new stdClass(); 
            $reponse->message = "Édition du forfait: "; 

            $corpsJSON = file_get_contents('php://input'); 
            $data = json_decode($corpsJSON, TRUE); 
            
            if(isset($_GET['id'])) {
            
            $destination = $data['destination'];
            $ville_depart = $data['ville_depart'];
            
            $hotel_nom = $data['hotel']['nom'];
            $hotel_coordonnees = $data['hotel']['coordonnees'];
            $hotel_nombre_etoiles = $data['hotel']['nombre_etoiles'];
            $hotel_nombre_chambre = $data['hotel']['nombre_chambre'];
            $hotel_caracteristiques = $data['hotel']['caracteristiques'];
            
        
            $date_depart = $data['date_depart'];
            $date_retour = $data['date_retour'];
            $prix = $data['prix'];
            $rabais = $data['rabais'];	
            $vedette = $data['vedette'];
      

                     
                        if(
                               isset($destination) 
                            && isset($ville_depart) 
                            && isset($hotel_nom) 
                            && isset($hotel_coordonnees) 
                            && isset($hotel_nombre_etoiles) 
                            && isset($hotel_nombre_chambre) 
                            && isset($hotel_caracteristiques) 
                            && isset($date_depart) 
                            && isset($date_retour) 
                            && isset($prix) 
                            && isset($rabais) 
                            && isset($vedette)) { 
                            
                            $hotel_caracteristiques_str = implode(';', $hotel_caracteristiques);
                            
                            if ($requete = $mysqli->prepare("UPDATE forfaits SET destination=?, ville_depart=?, nom=?, coordonnees=?, nombre_etoiles=?, nombre_chambre=?, caracteristiques=?, date_depart=?, date_retour=?, prix=?, rabais=?, vedette=? WHERE id=?")) 
                            
                            {
                                $requete->bind_param("ssssiisssiisi", 
                                $destination, 
                                $ville_depart, 
                                $hotel_nom, 
                                $hotel_coordonnees, 
                                $hotel_nombre_etoiles, 
                                $hotel_nombre_chambre, 
                                $hotel_caracteristiques_str, 
                                $date_depart, 
                                $date_retour, 
                                $prix, 
                                $rabais, 
                                $vedette, 
                                $_GET['id']); 
                                if($requete->execute()) { 
                                    $reponse->message .= "Succès"; 
                                } else { 
                                    $reponse->message .= "Erreur dans l'exécution de la requête"; 
                                } $requete->close();
                            } else { 
                                $reponse->message .= "Erreur dans la préparation de la requête"; 
                            } 
                        } else { 
                            $reponse->message .= "Erreur dans le corps de l'objet fourni"; 
                        } 
                    } else { 
                        $reponse->message .= "Erreur dans les paramètres (aucun identifiant fourni)"; 
                    } 
                    echo json_encode($reponse, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); 
break;


        break;

    // Gestion des demandes de type DELETE
    
    case 'DELETE': 
        
            
            $reponse = new stdClass(); 
            $reponse->message = "Suppression du forfait: "; 

                    if(isset($_GET['id'])) { 
                        if ($requete = $mysqli->prepare("DELETE FROM forfaits WHERE id=?")) { 
                            $requete->bind_param("i", $_GET['id']); 
                            if($requete->execute()) { 
                                $reponse->message .= "Succès"; 
                            } else { 
                                $reponse->message .= "Erreur dans l'exécution de la requête"; 
                            } 
                            $requete->close(); 
                        } else { 
                            $reponse->message .= "Erreur dans la préparation de la requête"; 
                        } 
                    } else { 
                        $reponse->message .= "Erreur dans les paramètres (aucun identifiant fourni)"; 
                    } 
            echo json_encode($reponse, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            break;


      


break;
default: 
    $reponse = new stdClass(); 
    $reponse->message = "Opération non supportée ";
    echo json_encode($reponse, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

}

if(isset($_GET['id'])) {
    //Gére GET une seul enregistrement
    if ($requete = $mysqli->prepare("SELECT * FROM reservations_restaurant WHERE id=?")) {
        $requete->bind_param("i", $_GET['id']);
        $requete->execute();

        $resultat_requete = $requete->get_result();
        $resto = $resultat_requete->fetch_assoc();

        echo json_encode($resto, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $requete->close(); // Fermeture du traitement
    }
} else {

    $requete = $mysqli->query("SELECT * FROM reservations_restaurant"); // Création d'une requête préparée
    $donnees_tableau = $requete->fetch_all(MYSQLI_ASSOC);
    echo json_encode($donnees_tableau, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); 
    $requete->close(); // Fermeture du traitement
} 

//Fermeture de la connexion 
$mysqli->close(); 
?>
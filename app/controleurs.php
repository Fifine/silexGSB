<?php
require_once __DIR__.'/../modele/class.pdogsb.php';
use Symfony\Component\HttpFoundation\Request;
use Silex\Application;
use Symfony\Component\HttpFoundation\Response;

//********************************************Contr�leur connexion*****************//
class ConnexionControleur{

    public function __construct(){
        ob_start();             // d�marre le flux de sortie
        require_once __DIR__.'/../vues/v_entete.php';
    }
    public function accueil(){
        require_once __DIR__.'/../vues/v_connexion.php';
        require_once __DIR__.'/../vues/v_pied.php';
        $view = ob_get_clean(); // r�cup�re le contenu du flux et le vide
        return $view;     // retourne le flux 
    }
    /*public function verifierUser(Request $request, Application $app){
        session_start();
        $login = $request->get('login');
	$mdp = $request->get('mdp');
        $pdo = PdoGsb::getPdoGsb();
	$visiteur = $pdo->getInfosVisiteur($login,$mdp);
	if(!is_array( $visiteur)){
            $app['couteauSuisse']->ajouterErreur("Login ou mot de passe incorrect");
            require_once __DIR__.'/../vues/v_erreurs.php';
            require_once __DIR__.'/../vues/v_connexion.php';
            require_once __DIR__.'/../vues/v_pied.php';
            $view = ob_get_clean();
        }
	else{
            $id = $visiteur['id'];
            $nom =  $visiteur['nom'];
            $prenom = $visiteur['prenom'];
            $app['couteauSuisse']->connecter($id,$nom,$prenom);
            require_once __DIR__.'/../vues/v_sommaire.php';
            require_once __DIR__.'/../vues/v_pied.php';
            $view = ob_get_clean();
        }
        return $view;        
    }*/
    public function verifierUser(Request $request, Application $app){
    session_start();
    $login = $request->get('login');
    $mdp = $request->get('mdp');
    $pdo = PdoGsb::getPdoGsb();
    $comptable = $pdo->getInfosComptable($login,$mdp);
	if(!is_array( $comptable)){
            $app['couteauSuisse']->ajouterErreur("Login ou mot de passe incorrect");
            require_once __DIR__.'/../vues/v_erreurs.php';
            require_once __DIR__.'/../vues/v_connexion.php';
            require_once __DIR__.'/../vues/v_pied.php';
            $view = ob_get_clean();
        }
	else{
            $id = $comptable['id'];
            $nom =  $comptable['nom'];
            $prenom = $comptable['prenom'];
            $app['couteauSuisse']->connecter($id,$nom,$prenom);
            require_once __DIR__.'/../vues/v_sommaire.php';
            require_once __DIR__.'/../vues/v_pied.php';
            $view = ob_get_clean();
        }
        return $view;        
    }
    public function deconnecter(Application $app){
        $app['couteauSuisse']->deconnecter();
       return $app->redirect('../public/');
    }
}
//**************************************Contr�leur EtatFrais**********************

class EtatFraisControleur {
     private $idVisiteur;
     private $pdo;
     public function init(){
        $this->idVisiteur = $_SESSION['idVisiteur'];
        $this->pdo = PdoGsb::getPdoGsb();
        ob_start();             // d�marre le flux de sortie
        require_once __DIR__.'/../vues/v_entete.php';
        require_once __DIR__.'/../vues/v_sommaire.php';
        
    }
    public function selectionnerMois(Application $app){
        session_start();
        if($app['couteauSuisse']->estConnecte()){
            $this->init();
            $lesMois = $this->pdo->getLesMoisDisponibles($this->idVisiteur);
            // Afin de s�lectionner par d�faut le dernier mois dans la zone de liste
            // on demande toutes les cl�s, et on prend la premi�re,
            // les mois �tant tri�s d�croissants
            $lesCles = array_keys( $lesMois );
            $moisASelectionner = $lesCles[0];
            require_once __DIR__.'/../vues/v_listeMois.php';
             require_once __DIR__.'/../vues/v_pied.php';
            $view = ob_get_clean();
            return $view;
        }
        else{
            return Response::HTTP_NOT_FOUND;
        }
    }
    public function voirFrais(Request $request,Application $app){
        session_start();
        if($app['couteauSuisse']->estConnecte()){
            $this->init();
            $leMois = $request->get('lstMois');
            $this->pdo = PdoGsb::getPdoGsb();
            $lesMois = $this->pdo->getLesMoisDisponibles($this->idVisiteur);
            $moisASelectionner = $leMois;
            $lesFraisForfait= $this->pdo->getLesFraisForfait($this->idVisiteur,$leMois);
            $lesInfosFicheFrais = $this->pdo->getLesInfosFicheFrais($this->idVisiteur,$leMois);
            $numeroAnnee = substr( $leMois,0,4);
            $numeroMois = substr( $leMois,4,2);
            $libEtat = $lesInfosFicheFrais['libEtat'];
            $montantValide = $lesInfosFicheFrais['montantValide'];
            $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
            $dateModif =  $lesInfosFicheFrais['dateModif'];
            $dateModif =  $app['couteauSuisse']->dateAnglaisVersFrancais($dateModif);
            require_once __DIR__.'/../vues/v_listeMois.php';
            require_once __DIR__.'/../vues/v_etatFrais.php';
            require_once __DIR__.'/../vues/v_pied.php';
            $view = ob_get_clean();
            return $view;
         }
        else {
            $response = new Response();
            $response->setContent('Connexion n�cessaire');
            return $response;
        }
    } 
}
//************************************Controleur GererFicheFrais********************

Class GestionFicheFraisControleur{
    private $pdo;
    private $mois;
    private $idVisiteur;
    private $numAnnee;
    private $numMois;
    
    public function init(Application $app){
            $this->idVisiteur = $_SESSION['idVisiteur'];
            ob_start();
            require_once __DIR__.'/../vues/v_entete.php';
            require_once __DIR__.'/../vues/v_sommaire.php';
            $this->mois = $app['couteauSuisse']->getMois(date("d/m/Y"));
            $this->numAnnee =substr($this->mois,0,4);
            $this->numMois =substr( $this->mois,4,2);
            $this->pdo = PdoGsb::getPdoGsb();
        
     }
     
    public function saisirFrais(Application $app){
        session_start();
        if($app['couteauSuisse']->estConnecte()){
            $this->init($app);
            if($this->pdo->estPremierFraisMois($this->idVisiteur,$this->mois)){
                $this->pdo->creeNouvellesLignesFrais($this->idVisiteur,$this->mois);
            }
            $lesFraisForfait = $this->pdo->getLesFraisForfait($this->idVisiteur,$this->mois);
            $numMois = $this->numMois;
            $numAnnee = $this->numAnnee; 
            require_once __DIR__.'/../vues/v_listeFraisForfait.php';
            require_once __DIR__.'/../vues/v_pied.php';
            $view = ob_get_clean();
            return $view; 
        }
         else {
            $response = new Response();
            $response->setContent('Connexion n�cessaire');
            return $response;
        }
    }
    public function validerFrais(Request $request,Application $app){
        session_start();
        if($app['couteauSuisse']->estConnecte()){
            $this->init($app);
            $lesFrais = $request->get('lesFrais');
            if($app['couteauSuisse']->lesQteFraisValides($lesFrais)){
                $this->pdo->majFraisForfait($this->idVisiteur,$this->mois,$lesFrais);
            }
            else{
                $app['couteauSuisse']->ajouterErreur("Les valeurs des frais doivent �tre num�riques");
                require_once __DIR__.'/../vues/v_erreurs.php';
                require_once __DIR__.'/../vues/v_pied.php';
            }
            $lesFraisForfait= $this->pdo->getLesFraisForfait($this->idVisiteur,$this->mois);
            $numMois = $this->numMois;
            $numAnnee = $this->numAnnee; 
            require_once __DIR__.'/../vues/v_listeFraisForfait.php';
            require_once __DIR__.'/../vues/v_pied.php';
            $view = ob_get_clean();
            return $view; 
        }
         else {
            $response = new Response();
            $response->setContent('Connexion n�cessaire');
            return $response;
        }
        
    }
}

 //**************************************Contr�leur ValiderFicheFrais**********************

class ValiderFicheFraisControleur {

     private $pdo;
	 private $mois;
     private $idVisiteur;     
     private $numAnnee;
     private $numMois;

	    public function init(Application $app){
            $this->idVisiteur = $_SESSION['idVisiteur'];
            $this->mois = $app['couteauSuisse']->getMois(date("d/m/Y"));
            $this->numAnnee =substr($this->mois,0,4);
            $this->numMois =substr( $this->mois,4,2);
            $this->pdo = PdoGsb::getPdoGsb();
            ob_start();
            require_once __DIR__.'/../vues/v_entete.php';
            require_once __DIR__.'/../vues/v_sommaire.php';
		}
		public function selectionnerFiche(Application $app){
        session_start();
        if($app['couteauSuisse']->estConnecte()){
            $this->init($app);
            $lesNoms = $this->pdo->getListNoms();
            $lesMois = $this->pdo->getListMois();
            // Afin de s�lectionner par d�faut le dernier mois dans la zone de liste
            // on demande toutes les cl�s, et on prend la premi�re,
            // les mois �tant tri�s d�croissants
            $lesCles = array_keys( $lesMois );
            $moisASelectionner = $lesCles[0];
            require_once __DIR__.'/../vues/v_listeFiche.php';
            require_once __DIR__.'/../vues/v_pied.php';
            $view = ob_get_clean();
            return $view;
         }
        else{
            $response = new Response();
            $response->setContent('Connexion n�cessaire');
            return $response;
        }
	}
		
	//AFFICHAGE apr�s avoir selectionn� le nom et le mois dans les listes d�roulantes de la page v_listeFiche
		private function recupererInfoFrais(Request $request,Application $app)
		{
			//On r�cup les champs selectio par le formu
            $leMois = $request->get('lstMois');
            $infosVisiteur = $request->get('lstNoms');
            $this->pdo = PdoGsb::getPdoGsb();
			//On r�cup s�par�ment id et nomPre
			$infos = explode(",", $infosVisiteur);
			$idSelect = $infos[0];
			$nomPreSelec = $infos[1];		
            $moisASelectionner = $leMois;
			//stocker les variables dans des sessions
			$_SESSION["id"]= $idSelect;
			$_SESSION["nomPre"]= $nomPreSelec;
			$_SESSION["mois"]= $leMois;
			
		}	
		 public function validerFiche(Request $request,Application $app){
        session_start();
        if($app['couteauSuisse']->estConnecte()){
			 
            $this->init($app);
            $this->pdo = PdoGsb::getPdoGsb();
			$boutonListe = $request->get('boutonListe');
			$boutonEditer = $request->get('boutonEditer');
			//On r�cup�re la liste des noms et des mois pour le foreach de la vue
            $lesNoms = $this->pdo->getListNoms();
            $lesMois = $this->pdo->getListMois();
			//R�cup�rer les champs selectionn�s par le formulaire
			 if(isset($boutonListe))
			 {
				$this->recupererInfoFrais($request,$app);
			 }
			 //R�cup�rer les variables qui �taient stock�es dans les sessions
		    if(isset($boutonListe) || isset($boutonEditer))
		    {
				 $idSelect=$_SESSION["id"];
				 $nomPreSelec=$_SESSION["nomPre"];
				 $leMois=$_SESSION["mois"];
				 $nomASelectionner = $nomPreSelec;
				 $moisASelectionner = $leMois;
			}
			//V�rifier que la fiche existe et que son �tat est bien 'Clotur�'
            $etatCloture= $this->pdo->estFicheEtatCloture($idSelect,$leMois,'CL');
			//Si c'est le cas, on pr�pare les pr�requis pour l'affichage de la fiche de frais
			if($etatCloture)
			{
				//Requete pour avoir informations sur la fiche de frais
				$lesFraisForfait= $this->pdo->getLesFraisForfait($idSelect,$leMois);
				$lesInfosFicheFrais = $this->pdo->getLesInfosFicheFrais($idSelect,$leMois);
				//On r�cup et mets dans une variable l'ann�e et le mois s�par�ment
				$numeroAnnee = substr( $leMois,0,4);
				$numeroMois = substr( $leMois,4,2);
				//On mets dans des variables les informations de notre fiche de frais
				$libEtat = $lesInfosFicheFrais['libEtat'];
				$montantValide = $lesInfosFicheFrais['montantValide'];
				$nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
				$dateModif =  $lesInfosFicheFrais['dateModif'];
				$dateModif =  $app['couteauSuisse']->dateAnglaisVersFrancais($dateModif);
				//Si on a valider les informations concernant les frais forfaits, le syst�me v�rifie ces valeurs puis va mettre � jour la fiche dans la base de donn�es
				if(isset($boutonEditer))
				 {
					$lesFrais = $request->get('lesFrais');
					if($app['couteauSuisse']->lesQteFraisValides($lesFrais)){
						$this->pdo->majFraisForfait($idSelect,$leMois,$lesFrais);
						$this->pdo->majEtatFicheFrais($idSelect,$leMois,'VA');
					}
					else{
						$app['couteauSuisse']->ajouterErreur("Les valeurs des frais doivent �tre num�riques");
						require_once __DIR__.'/../vues/v_erreurs.php';
						require_once __DIR__.'/../vues/v_pied.php';
					}
			 }
			}
            require_once __DIR__.'/../vues/v_listeFiche.php';
			//On affiche le tableau des frais seulement si la fiche existe et que son �tat est 'Clotur�'
			if(isset ($etatCloture) && ($etatCloture))
			{
				require_once __DIR__.'/../vues/v_editerFraisForfait.php';
			}
			//Sinon on affiche un message d'erreur
			else
			{
				$app['couteauSuisse']->ajouterErreur("Pas de fiche de frais a valider pour ce visitieur pour ce mois.");
				require_once __DIR__.'/../vues/v_erreurs.php';
			}
            require_once __DIR__.'/../vues/v_pied.php';
            $view = ob_get_clean();
            return $view;
         }
        else {
            $response = new Response();
            $response->setContent('Connexion n�cessaire');
		return $response;}

     
		 }
}
?>


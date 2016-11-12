    <!-- Division pour le sommaire -->
    <div id="menuGauche">
     <div id="infosUtil">
       </div>  
        <ul id="menuList">
			<li >
				  Comptable :<br>
				<?php echo $_SESSION['prenom']."  ".$_SESSION['nom']  ?>
			</li>
           <li class="smenu">
              <a href="saisirFrais" title="Saisie fiche de frais ">Saisie fiche de frais</a>
           </li>
           <li class="smenu">
              <a href="selectionnerFiche" title="Valider fiche de frais ">Valider fiche de frais</a>
           </li>
           <li class="smenu">
              <a href="afficherEtat" title="Afficher etat">Afficher Etat</a>
           </li>
 	   <li class="smenu">
              <a href="deconnecter" title="Se déconnecter">Déconnexion</a>
           </li>
         </ul>
        
    </div>
    
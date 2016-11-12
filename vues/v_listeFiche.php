 <div id="contenu">
      <h2>Valider Fiche de Frais</h2>
      <h3>Nom et Mois à sélectionner : </h3>
	  <form action="validerFiche" method="POST">
      <div class="corpsForm">
      <p>
      <label for="lstNoms" accesskey="n">Noms : </label>
        <select id="lstNoms" name="lstNoms">
            <?php
			foreach ($lesNoms as $unNom)
			{
				$nomPre = $unNom['nomPre'];
				
				if($nomPre == $nomASelectionner){
				?>
				<option selected value="<?php echo implode(",",$unNom); ?>"><?php echo $unNom['nomPre'] ?> </option>
				<?php 
				}
				else{ 
				?>
				<option value="<?php echo implode(",",$unNom); ?>"><?php echo $unNom['nomPre'] ?> </option>
				<?php 
				}
			}
		   ?>
					
            
        </select>
      </p>
	  
      <p>
      <label for="lstMois" accesskey="n">Mois : </label>
        <select id="lstMois" name="lstMois">
            <?php
			foreach ($lesMois as $unMois)
			{
			    $mois = $unMois['mois'];
				$numAnnee =  $unMois['numAnnee'];
				$numMois =  $unMois['numMois'];
				if($mois == $moisASelectionner){
				?>
				<option selected value="<?php echo $mois ?>"><?php echo  $numMois."/".$numAnnee ?> </option>
				<?php 
				}
				else{ ?>
				<option value="<?php echo $mois ?>"><?php echo  $numMois."/".$numAnnee ?> </option>
				<?php 
				}
			
			}
           
		   ?>    
            
        </select>
      </p>
      </div>
      <div class="piedForm">
      <p>
        <input id="ok" name="boutonListe" type="submit" value="Valider" size="20" />
        <input id="annuler" type="reset" value="Effacer" size="20" />
      </p> 
      </div>
        
      </form>
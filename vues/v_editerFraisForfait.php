
<h3>Fiche de frais du mois <?php echo $numeroMois."-".$numeroAnnee." de ".$nomPreSelec ?> : 
    </h3>
    <div class="encadre">
    <p>
        Etat : <?php echo $libEtat?> depuis le <?php echo $dateModif?> <br> Montant validé : <?php echo $montantValide?>
     </p>
	 <form method="POST"  action="validerFiche">
            <div class="corpsForm">
               <fieldset>
                    <legend>Eléments forfaitisés</legend>
			<?php
				foreach ($lesFraisForfait as $unFrais)
				{
                                    $idFrais = $unFrais['idfrais'];
                                    $libelle = $unFrais['libelle'];
                                    $quantite = $unFrais['quantite'];
			?>
				<p>
                                    <label for="idFrais"><?php echo $libelle ?></label>
                                            <input type="text" id="idFrais" name="lesFrais[<?php echo $idFrais?>]" size="10" maxlength="5" value="<?php echo $quantite?>"
										<?php 
										
										if(isset($boutonEditer))
										{
											echo 'disabled';
										}
										?>
											>
                                </p>
			
			<?php
				}
			?>
			
              </fieldset>
            </div>
				<?php 
				if(!isset($boutonEditer))
				{
					?>
                <p>
                    <input id="ok" name="boutonEditer" type="submit" value="Valider" size="20" />
                    <input id="annuler" type="reset" value="Effacer" size="20" />
                </p> 
				<?php 
				}
				?>
        
      </form>
  
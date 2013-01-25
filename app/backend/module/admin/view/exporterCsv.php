<?php
if ($exportUser) :
   if (!empty($data) && !empty($fields)) :
      echo $fields . "\n";
      echo $data;
   else :
      ?>
      <h1>Exporter les données d'un étudiant</h1>
      <form method="post" action="">
         <fieldset>
            <legend>Veuillez choisir un étudiant</legend>
            <label>Étudiant :</label> <?php if (!empty($listeDesEtudiants)) : ?>
               <select name="idUtil">
                  <?php
                  foreach ($listeDesEtudiants as $etudiant) :
                     if ($idUtil === $etudiant['idUtil']) :
                        ?>
                        <option value="<?php echo $etudiant['idUtil']; ?>" selected><?php echo $etudiant['nom']; ?> <?php echo $etudiant['prenom']; ?> (<?php echo $etudiant['login']; ?>)</option>
                     <?php else : ?>
                        <option value="<?php echo $etudiant['idUtil']; ?>"><?php echo $etudiant['nom']; ?> <?php echo $etudiant['prenom']; ?> (<?php echo $etudiant['login']; ?>)</option>
                  <?php
                  endif;
               endforeach;
               ?>
               </select>
      <?php else : ?>
               Aucun
      <?php endif; ?>
         </fieldset>
         <fieldset>
            <legend>Informations à exporter</legend>
            <fieldset>
               <legend>Identité</legend>
               <label>Login</label> <input type="checkbox" name="login" /><br />
               <label>Nom</label> <input type="checkbox" name="nom" /><br />
               <label>Prénom</label> <input type="checkbox" name="prenom" />
            </fieldset>
            <fieldset>
               <legend>Informations principales</legend>
               <label>Promotion</label> <input type="checkbox" name="promotion" /><br />
               <label>Numéro d'étudiant</label> <input type="checkbox" name="numEtudiant" /><br />
               <label>Année de redoublement</label> <input type="checkbox" name="anneeRedouble" />
            </fieldset>
            <fieldset>
               <legend>Moyennes</legend>
               <label>Moyenne générale</label> <input type="checkbox" name="moyenneGenerale" /> <br />
               <label>Moyennes des matières</label> <input type="checkbox" name="moyenneMatieres" />
            </fieldset>
         </fieldset>
         <input type="submit" value="Exporter !" />
      </form>
      <p><a href="/admin/exporter">Retour à l'export d'informations</a></p>
   <?php endif;
elseif ($exportPromo) :
   ?>
   <h1>Exporter les données d'une promotion</h1>
   <ul>
      <li>Liste des élèves</li>
      <li>Liste des modules</li>
   </ul>
   <p><a href="/admin/exporter">Retour à l'export d'informations</a></p>
<?php else : ?>
   <h1>Exporter des informations</h1>
   <p>Que voulez-vous exporter ?</p>
   <ul>
      <li><a href="/admin/exporter/étudiant">Données d'un étudiant</a></li>
      <li><a href="/admin/exporter/promotion">Données d'une promotion</a></li>
   </ul>
<?php endif; ?>
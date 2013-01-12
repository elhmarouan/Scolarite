<?php if ($addMatiere) : ?>
   <h1>Ajouter une matière</h1>
   <form method="post" action="/admin/<?php echo $promo; ?>/<?php echo $module; ?>/matières/ajouter">
      <label>Nom de la matière :</label> <input type="text" name="libelle" placeholder="ex: Physique" /><br />
      <label>Coefficient :</label> <input type="number" name="coef" /><br />
      <label>Professeur responsable :</label> 
      <?php if (!empty($listeProfsResponsables)) : ?>
         <select name="idProf">
            <?php foreach ($listeProfsResponsables as $prof) : ?>
               <option value="<?php echo $prof['idProf']; ?>"><?php echo $prof['prenom']; ?> <?php echo $prof['nom']; ?> (<?php echo $prof['login']; ?>)</option>
            <?php endforeach; ?>
         </select>
      <?php else : ?>
         Aucun professeur disponible
      <?php endif; ?><br />
      <input type="submit" value="Ajouter !" />
   </form>
   <p><a href="/admin/<?php echo $promo; ?>/<?php echo $module; ?>/matières">Retour à la gestion du module</a></p>
<?php elseif ($editMatiere) : ?>
   <h1>Modifier une matière</h1>
   <form method="post" action="/admin/<?php echo $promo; ?>/<?php echo $module; ?>/<?php echo $matiere; ?>/modifier">
      <label>Nom de la matière :</label> <input type="text" name="libelle" value="<?php echo $matiere; ?>" /><br />
      <label>Coefficient :</label> <input type="number" name="coef" value="<?php echo $coef; ?>" /><br />
      <input type="submit" value="Modifier !" />
   </form>
   <p><a href="/admin/<?php echo $promo; ?>/<?php echo $module; ?>/<?php echo $matiere; ?>">Retour à la gestion de la matière</a></p>
<?php elseif ($addModule) : ?>
   <h1>Ajouter un module</h1>
   <form method="post" action="/admin/<?php echo $promo; ?>/modules/ajouter">
      <label>Nom du module :</label> <input type="text" name="libelle" placeholder="ex: Informatique" /><br />
      <input type="submit" value="Ajouter !" />
   </form>
<?php elseif ($editModule) : ?>
   <h1>Modifier un module</h1>
   <form method="post" action="/admin/<?php echo $promo; ?>/<?php echo $module; ?>/modifier">
      <label>Nom du module :</label> <input type="text" name="libelle" value="<?php echo $module; ?>" /><br />
      <input type="submit" value="Modifier !" />
   </form>
<?php elseif ($manageMatiere) : ?>
   <h1>Gestion de la matière <?php echo $matiere; ?></h1>
   <p><a href="/admin/<?php echo $promo; ?>/<?php echo $module; ?>/<?php echo $matiere; ?>/modifier" class="button orangeButton">Modifier cette matière</a> <a href="/admin/<?php echo $promo; ?>/<?php echo $module; ?>/<?php echo $matiere; ?>/supprimer" class="button redButton" onClick="return confirm('Êtes-vous sûr de vouloir supprimer cette matière ?');">Supprimer cette matière</a></p>
   <p><strong>Coefficient</strong> : <?php echo $coef; ?></p>
   <p><strong>Moyenne de la promotion</strong> :
      <?php if ($moyennePromo !== null) :
         echo $moyennePromo; ?> / 20
      <?php else: ?>
         Indisponible
   <?php endif; ?>
   </p>
   <h2>Liste des examens</h2>
   <table>
      <thead>
         <tr>
            <th>Libelle</th>
            <th>Type</th>
            <th>Date</th>
         </tr>
      </thead>
      <tbody>
      <?php
      if (!empty($listeDesExamens)) :
         foreach ($listeDesExamens as $examen) :
            ?>
         <tr>
            <td><?php echo $examen['libelle']; ?></td>
            <td></td>
            <td><?php echo $examen['date']; ?></td>
         </tr>
            <?php
         endforeach;
      else :
         ?>
            <tr>
               <td colspan="3">Aucun examen</td>
            </tr>
   <?php endif; ?>
   </tbody>
   </table>
   <p><a href="/admin/<?php echo $promo; ?>/<?php echo $module; ?>/matières">Retour à la gestion du module</a></p>
<?php elseif ($manageModule) : ?>
   <h1>Gestion du module <?php echo $module; ?></h1>
   <p><a href="/admin/<?php echo $promo; ?>/<?php echo $module; ?>/matières/ajouter" class="button greenButton">Ajouter un nouvelle matière</a> <a href="/admin/<?php echo $promo; ?>/<?php echo $module; ?>/modifier" class="button orangeButton">Modifier ce module</a> <a href="/admin/<?php echo $promo; ?>/<?php echo $module; ?>/supprimer" class="button redButton" onClick="return confirm('Êtes-vous sûr de vouloir supprimer ce module ?');">Supprimer ce module</a></p>
   <ul>
      <?php
      if (!empty($listeDesMatieres)) :
         foreach ($listeDesMatieres as $matiere) :
            ?>
            <li><a href="/admin/<?php echo $promo; ?>/<?php echo $module; ?>/<?php echo $matiere; ?>"><?php echo $matiere; ?></a></li>
            <?php
         endforeach;
      else :
         ?>
         <li>Aucune matière disponible</li>
   <?php endif; ?>
   </ul>
   <p><a href="/admin/<?php echo $promo; ?>/modules">Retour à la gestion des modules</a></p>
<?php else : ?>
   <h1>Gestion des modules <?php echo $prefixPromo . $promo; ?></h1>
   <p><a href="/admin/<?php echo $promo; ?>/modules/ajouter" class="button greenButton">Ajouter un nouveau module</a></p>
   <ul>
      <?php
      if (!empty($listeDesModules)) :
         foreach ($listeDesModules as $module) :
            ?>
            <li><a href="/admin/<?php echo $promo; ?>/<?php echo $module; ?>/matières"><?php echo $module; ?></a></li>
            <?php
         endforeach;
      else :
         ?>
         <li>Aucun module disponible</li>
   <?php endif; ?>
   </ul>
   <p><a href="/admin/<?php echo $promo; ?>">Retour à la gestion de la promotion</a></p>
<?php endif; ?>
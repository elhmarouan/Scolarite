<?php if ($addExam) : ?>
   <h1>Ajouter un examen</h1>
   <form method="post" action="/admin/<?php echo $promo; ?>/<?php echo $module; ?>/<?php echo $matiere; ?>/examen/ajouter">
      <label>Libellé :</label> <input type="text" name="libelle" /><br />
      <label>Date :</label> <input type="date" name="date" /> (au format JJ/MM/AAAA)<br />
      <label>Type :</label>
      <?php if (!empty($listeTypesExams)) : ?>
         <select name="idType">
            <?php foreach ($listeTypesExams as $typeExam) : ?>
               <option value="<?php echo $typeExam['idType']; ?>"><?php echo $typeExam['libelle']; ?> (coefficient <?php echo $typeExam['coef']; ?>)</option>
            <?php endforeach; ?>
         </select>
      <?php else : ?>
         Aucun type disponible
      <?php endif; ?><br />
      <input type="submit" value="Ajouter !" />
   </form>
   <p><a href="/admin/<?php echo $promo; ?>/<?php echo $module; ?>/<?php echo $matiere; ?>">Retour à la gestion de la matière</a></p>
<?php elseif ($editExam) : ?>
   <h1>Modifier un examen</h1>
   <form method="post" action="/admin/<?php echo $promo; ?>/<?php echo $module; ?>/<?php echo $matiere; ?>/<?php echo $examen['idExam']; ?>/modifier">
      <label>Libellé :</label> <input type="text" name="libelle" value="<?php echo $examen['libelle']; ?>" /><br />
      <label>Date :</label> <input type="date" name="date" value="<?php echo $examen['date']; ?>" /> (au format JJ/MM/AAAA)<br />
      <label>Type :</label>
      <?php if (!empty($listeTypesExams)) : ?>
         <select name="idType">
            <?php
            foreach ($listeTypesExams as $typeExam) :
               if ($examen['idType'] === $typeExam['idType']) :
                  ?>
                  <option value="<?php echo $typeExam['idType']; ?>" selected><?php echo $typeExam['libelle']; ?> (coefficient <?php echo $typeExam['coef']; ?>)</option>
               <?php else : ?>
                  <option value="<?php echo $typeExam['idType']; ?>"><?php echo $typeExam['libelle']; ?> (coefficient <?php echo $typeExam['coef']; ?>)</option>
               <?php
               endif;
            endforeach;
            ?>
         </select>
      <?php else : ?>
         Aucun type disponible
   <?php endif; ?><br />
      <input type="submit" value="Modifier !" />
   </form>
   <p><a href="/admin/<?php echo $promo; ?>/<?php echo $module; ?>/<?php echo $matiere; ?>">Retour à la gestion de la matière</a></p>
<?php elseif ($addMatiere) : ?>
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
      <label>Professeur responsable :</label> 
         <?php if (!empty($listeProfsResponsables)) : ?>
         <select name="idProf">
            <?php
            foreach ($listeProfsResponsables as $prof) :
               if ($prof['responsable']) :
                  ?>
                  <option value="<?php echo $prof['idProf']; ?>" selected><?php echo $prof['prenom']; ?> <?php echo $prof['nom']; ?> (<?php echo $prof['login']; ?>)</option>
               <?php else : ?>
                  <option value="<?php echo $prof['idProf']; ?>"><?php echo $prof['prenom']; ?> <?php echo $prof['nom']; ?> (<?php echo $prof['login']; ?>)</option>
               <?php
               endif;
            endforeach;
            ?>
         </select>
      <?php else : ?>
         Aucun professeur disponible
   <?php endif; ?><br />
      <input type="submit" value="Modifier !" />
   </form>
   <p><a href="/admin/<?php echo $promo; ?>/<?php echo $module; ?>/<?php echo $matiere; ?>">Retour à la gestion de la matière</a></p>
<?php elseif ($manageMatiere) : ?>
   <h1>Gestion de la matière <?php echo $matiere; ?></h1>
   <p><a href="/admin/<?php echo $promo; ?>/<?php echo $module; ?>/<?php echo $matiere; ?>/modifier" class="button orangeButton">Modifier cette matière</a> <a href="/admin/<?php echo $promo; ?>/<?php echo $module; ?>/<?php echo $matiere; ?>/supprimer" class="button redButton" onClick="return confirm('Êtes-vous sûr de vouloir supprimer cette matière ?');">Supprimer cette matière</a></p>
   <p><strong>Coefficient</strong> : <?php echo $coef; ?></p>
   <p><strong>Moyenne de la promotion</strong> :
      <?php
      if ($moyennePromo !== null) :
         echo $moyennePromo;
         ?> / 20
      <?php else: ?>
         Indisponible
   <?php endif; ?>
   </p>
   <h2>Liste des examens</h2>
   <p><a href="/admin/<?php echo $promo; ?>/<?php echo $module; ?>/<?php echo $matiere; ?>/examen/ajouter" class="button greenButton">Ajouter un examen</a></p>
   <table>
      <thead>
         <tr>
            <th>Libelle</th>
            <th>Type</th>
            <th>Date</th>
            <th>Actions</th>
         </tr>
      </thead>
      <tbody>
         <?php
         if (!empty($listeDesExamens)) :
            foreach ($listeDesExamens as $examen) :
               ?>
               <tr>
                  <td><?php echo $examen['libelle']; ?></td>
                  <td><?php echo $examen['type']; ?></td>
                  <td><?php echo $examen['date']; ?></td>
                  <td><a href="/admin/<?php echo $promo; ?>/<?php echo $module; ?>/<?php echo $matiere; ?>/<?php echo $examen['idExam']; ?>/modifier"><img src="/img/admin/exam_edit.png" alt="modifier" title="Modifier cet examen" /></a> <a href="/admin/<?php echo $promo; ?>/<?php echo $module; ?>/<?php echo $matiere; ?>/<?php echo $examen['idExam']; ?>/supprimer"><img src="/img/admin/exam_delete.png" alt="supprimer"  title="Supprimer cet examen" onClick="return confirm('Êtes-vous sûr de vouloir supprimer cet examen ?');" /></a></td>
               </tr>
               <?php
            endforeach;
         else :
            ?>
            <tr>
               <td colspan="4">Aucun examen</td>
            </tr>
   <?php endif; ?>
      </tbody>
   </table>
   <p><a href="/admin/<?php echo $promo; ?>/<?php echo $module; ?>/matières">Retour à la gestion du module</a></p>
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
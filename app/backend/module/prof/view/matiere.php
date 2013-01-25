<h1>Gestion de la matière <?php echo $matiere; ?></h1>
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
<p><strong>Professeur responsable</strong> : <?php echo $profResponsable['prenom']; ?> <?php echo $profResponsable['nom']; ?> (<?php echo $profResponsable['login']; ?>)</p>
<h2>Liste des examens</h2>
<table>
   <thead>
      <tr>
         <th>Libellé</th>
         <th>Type</th>
         <th>Date</th>
         <th>Moyenne de la promo</th>
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
               <td>
                  <?php
                  if (!empty($examen['moyennePromo'])) :
                     echo $examen['moyennePromo'];
                     ?> / 20
                  <?php else : ?>
                     Indisponible
                  <?php endif; ?>
               </td>
               <td><a href="/prof/<?php echo $promo; ?>/<?php echo $module; ?>/<?php echo $matiere; ?>/<?php echo $examen['idExam']; ?>"><img src="/img/prof/go_notes.png" alt="notes" title="Accéder aux notes" /></a></td>
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
<p><a href="/prof/<?php echo $promo; ?>/<?php echo $module; ?>/matières">Retour à la gestion du module</a></p>
<h1>Notes de l'examen « <?php echo $examen; ?> »</h1>
<?php if ($estResponsable) : ?>
<p><a href="/prof/<?php echo $promo; ?>/<?php echo $module; ?>/<?php echo $matiere; ?>/<?php echo $idExam; ?>/ajouter" class="button greenButton">Saisir une nouvelle note</a></p>
<?php endif; ?>
<table>
   <thead>
      <tr>
         <th>Login</th>
         <th>Nom</th>
         <th>Prénom</th>
         <th>Note</th>
         <th>Action</th>
      </tr>
   </thead>
   <tbody>
      <?php
      if (!empty($listeDesNotes)) :
         foreach ($listeDesNotes as $note) :
            ?>
            <tr>
               <td><?php echo $note['login']; ?></td>
               <td><?php echo $note['nom']; ?></td>
               <td><?php echo $note['prenom']; ?></td>
               <td>
                  <?php if (!empty($note['note'])) :
                     echo $note['note'];
                     ?>/20
                  <?php else : ?>
                     Absence justifiée
      <?php endif; ?>
               </td>
               <td>-</td>
            </tr>
         <?php
         endforeach;
      else :
         ?>
         <tr>
            <td colspan="5">Aucune note à afficher</td>
         </tr>
<?php
endif;
?>
   </tbody>
</table>

<p><a href="/prof/<?php echo $promo; ?>/<?php echo $module; ?>/<?php echo $matiere; ?>">Retour à la liste des examens</a></p>
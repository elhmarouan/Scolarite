<h1>Gestion de la matière <?php echo $matiere; ?></h1>
<h2>Liste des examens</h2>
<table>
   <thead>
      <tr>
         <th>Libellé</th>
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
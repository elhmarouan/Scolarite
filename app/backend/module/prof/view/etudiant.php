<h1>Liste des étudiants <?php echo $prefixPromo . $promo; ?></h1>
<table>
   <thead>
      <tr>
         <th>Login</th>
         <th>Nom</th>
         <th>Prénom</th>
      </tr>
   </thead>
   <tbody>
      <?php
      if (!empty($listeDesEtudiants)) :
         foreach ($listeDesEtudiants as $etudiant) :
            ?>
            <tr>
               <td><?php echo $etudiant['login']; ?></td>
               <td><?php echo $etudiant['nom']; ?></td>
               <td><?php echo $etudiant['prenom']; ?></td>
            </tr>
            <?php
         endforeach;
      else :
         ?>
         <tr>
            <td colspan="3">Aucun étudiant dans cette promotion</td>
         </tr>
      <?php endif; ?>
   </tbody>
</table>

<p><a href="/prof/<?php echo $promo; ?>">Retour à la gestion de la promotion</a></p>
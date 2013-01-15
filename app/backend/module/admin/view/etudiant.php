<h1>Liste des étudiants <?php echo $prefixPromo . $promo; ?></h1>
<p>Pour modifier ou supprimer un étudiant, veuillez cliquer <a href="/admin/utilisateurs">ici</a>.</p>
<table>
   <thead>
      <tr>
         <th>Login</th>
         <th>Nom</th>
         <th>Prénom</th>
         <th>Actions</th>
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
               <td>-</td>
            </tr>
            <?php
         endforeach;
      else :
         ?>
         <tr>
            <td colspan="5">Aucun étudiant</td>
         </tr>
      <?php endif; ?>
   </tbody>
</table>
<p><a href="/admin/<?php echo $promo ?>">Retour à la promotion</a></p>

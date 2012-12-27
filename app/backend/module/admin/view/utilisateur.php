<?php if ($addUser) : ?>
   <h1>Ajouter un utilisateur</h1>
<?php else : ?>
   <h1>Gestion des utilisateurs</h1>
   <p><a href="/admin/utilisateurs/ajouter">Ajouter un utilisateur</a></p>
   <table>
      <thead>
         <tr>
            <th>Login</th>
            <th>Nom</th>
            <th>Prénom</th>
         </tr>
      </thead>
      <tbody>
         <?php if (!empty($listeDesUtilisateurs)) :
            foreach ($listeDesUtilisateurs as $utilisateur) :
               ?>
               <tr>
                  <td><?php echo $utilisateur['login']; ?></td>
                  <td><?php echo $utilisateur['nom']; ?></td>
                  <td><?php echo $utilisateur['prenom']; ?></td>
               </tr>
            <?php endforeach;
         else :
            ?>
            <tr>
               <td colspan="3">Aucun utilisateur</td>
            </tr>
   <?php endif; ?>
      </tbody>
   </table>
<?php endif; ?>
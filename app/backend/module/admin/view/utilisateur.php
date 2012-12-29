<?php if ($addUser) : ?>
   <h1>Ajouter un utilisateur</h1>
   <p>Veuillez remplir les champs ci-dessous pour créer un nouvel utilisateur</p>
   <form method="post" action="/admin/utilisateurs/ajouter">
      <fieldset>
         <legend>Identité réelle</legend>
         Nom : <input type="text" name="nom" /><br />
         Prénom : <input type="text" name="prenom" /><br />
         Rôle :
         <?php if (!empty($listeDesRoles)) : ?>
            <select name="role">
               <?php foreach ($listeDesRoles as $role) : ?>
                  <option value="<?php echo $role['idRole']; ?>"><?php echo $role['libelle']; ?></option>
                  <?php endforeach; ?>
            </select>
               <?php
            else :
               ?>
            Aucun rôle disponible
         <?php
         endif;
         ?>
      </fieldset>
      <fieldset>
         <legend>Identité virtuelle</legend>
         Login : <input type="text" name="login" /><br />
         Mot de passe : <input type="password" name="password" /><br />
         Confirmer : <input type="password" name="passwordConfirm" />
      </fieldset>
      <input type="submit" value="Ajouter !" />
   </form>
<?php else : ?>
   <h1>Gestion des utilisateurs</h1>
   <p><a href="/admin/utilisateurs/ajouter">Ajouter un utilisateur</a></p>
   <table>
      <thead>
         <tr>
            <th>Login</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Rôle</th>
            <th>Actions</th>
         </tr>
      </thead>
      <tbody>
   <?php
   if (!empty($listeDesUtilisateurs)) :
      foreach ($listeDesUtilisateurs as $utilisateur) :
         ?>
               <tr>
                  <td><?php echo $utilisateur['login']; ?></td>
                  <td><?php echo $utilisateur['nom']; ?></td>
                  <td><?php echo $utilisateur['prenom']; ?></td>
                  <td></td>
                  <td>-</td>
               </tr>
            <?php
            endforeach;
         else :
            ?>
            <tr>
               <td colspan="5">Aucun utilisateur</td>
            </tr>
         <?php endif; ?>
      </tbody>
   </table>
   <p><a href="/admin/">Retour à l'accueil du panel d'administration</a></p>
<?php endif; ?>
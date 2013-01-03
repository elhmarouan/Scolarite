<?php if ($addUser) : ?>
   <h1>Ajouter un utilisateur</h1>
   <p>Veuillez remplir les champs ci-dessous pour créer un nouvel utilisateur :</p>
   <form method="post" action="/admin/utilisateurs/ajouter">
      <fieldset>
         <legend>Identité réelle</legend>
         <label>Nom :</label> <input type="text" name="nom" /><br />
         <label>Prénom :</label> <input type="text" name="prenom" /><br />
         <label>Rôle :</label>
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
         <label>Login :</label> <input type="text" name="login" /><br />
         <label>Mot de passe :</label> <input type="password" name="password" /><br />
         <label>Confirmer :</label> <input type="password" name="passwordConfirm" />
      </fieldset>
      <input type="submit" value="Ajouter !" />
   </form>
   <p><a href="/admin/utilisateurs">Retour à la liste des utilisateurs</a></p>
   <?php elseif ($editUser) : ?>
   <h1>Modifier un utilisateur</h1>
   <p>Veuillez remplir les champs ci-dessous pour créer un nouvel utilisateur</p>
   <form method="post" action="/admin/utilisateurs/<?php echo $utilisateur['idUtil']; ?>/modifier">
      <fieldset>
         <legend>Identité réelle</legend>
         <label>Nom :</label> <input type="text" name="nom" value="<?php echo $utilisateur['nom']; ?>" /><br />
         <label>Prénom :</label> <input type="text" name="prenom" value="<?php echo $utilisateur['prenom']; ?>" /><br />
         <label>Rôle :</label>
         <?php if (!empty($listeDesRoles)) : ?>
            <select name="role">
               <?php foreach ($listeDesRoles as $role) : ?>
                  <option value="<?php echo $role['idRole']; ?>"<?php if($role['idRole'] === $utilisateur['idRole']) : ?> selected<?php endif; ?>><?php echo $role['libelle']; ?></option>
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
         <label>Login :</label> <input type="text" name="login" value="<?php echo $utilisateur['login']; ?>" /> (s'il s'agit du vôtre, vous devrez vous reconnecter)<br />
         <label>Mot de passe :</label> <input type="password" name="password" /> (laissez vide pour ne pas changer)<br />
         <label>Confirmer :</label> <input type="password" name="passwordConfirm" />
      </fieldset>
      <input type="submit" value="Modifier !" />
   </form>
   <p><a href="/admin/utilisateurs">Retour à la liste des utilisateurs</a></p>
<?php else : ?>
   <h1>Gestion des utilisateurs</h1>
   <p><a href="/admin/utilisateurs/ajouter" class="button greenButton">Ajouter un utilisateur</a></p>
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
                  <td><?php echo $utilisateur['role']; ?></td>
                  <td><a href="/admin/utilisateurs/<?php echo $utilisateur['idUtil']; ?>/modifier"><img src="/img/admin/user_edit.png" alt="modifier" title="Modifier cet utilisateur" /></a> <a href="/admin/utilisateurs/<?php echo $utilisateur['idUtil']; ?>/supprimer"><img src="/img/admin/user_delete.png" alt="supprimer"  title="Supprimer cet utilisateur" onClick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');" /></a></td>
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
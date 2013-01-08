<?php if ($addStudent) : ?>
   <h1>Ajouter un étudiant</h1>
   <p>Veuillez sélectionner un étudiant à rajouter à la promotion : </p>
   <form method="post" action="/admin/<?php echo $promo; ?>/étudiants/ajouter">
      <label>Étudiant : </label>
      <?php if (!empty($listeDesEtudiants)) : ?>
         <select name="etudiant">
            <?php
            foreach ($listeDesEtudiants as $etudiant) :
               ?>
               <option value="<?php echo $etudiant['idUtil']; ?>"><?php echo $etudiant['prenom']; ?> <?php echo $etudiant['nom']; ?> (<?php echo $etudiant['login']; ?>)</option>
               <?php
            endforeach;
            ?>
         </select>
         <?php
      else :
         ?>
         Aucun étudiant
      <?php endif; ?>
      <br />
      <input type="submit" value="Ajouter !" />
   </form>
<?php else : ?>
   <h1>Gestion des étudiants <?php echo $prefixPromo . $promo; ?></h1>
   <p><a href="/admin/<?php echo $promo; ?>/étudiants/ajouter" class="button greenButton">Ajouter un étudiant</a></p>
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
   <?php endif; ?>
</table>
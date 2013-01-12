<?php if ($addExamType) : ?>
   <h1>Ajouter un type d'examen</h1>
   <form method="post" action="/admin/typesExams/ajouter">
      <label>Libellé :</label> <input type="text" name="libelle" /><br />
      <label>Coefficient :</label> <input type="text" name="coef" /><br />
      <input type="submit" value="Ajouter !" />
   </form>
   <p><a href="/admin/typesExams">Retour à la gestion des types d'examens</a></p>
<?php elseif ($editExamType) : ?>
   <h1>Modifier un type d'examen</h1>
   <form method="post" action="/admin/typesExams/<?php echo $idTypeExam; ?>/modifier">
      <label>Libellé :</label> <input type="text" name="libelle" value="<?php echo $typeExam['libelle']; ?>" /><br />
      <label>Coefficient :</label> <input type="text" name="coef" value="<?php echo $typeExam['coef']; ?>" /><br />
      <input type="submit" value="Modifier !" />
   </form>
   <p><a href="/admin/typesExams">Retour à la gestion des types d'examens</a></p>
<?php else : ?>
   <h1>Gestion des types d'examens</h1>
   <p><a href="/admin/typesExams/ajouter" class="button greenButton">Ajouter un type d'examen</a></p>
   <table>
      <thead>
         <tr>
            <th>Libelle</th>
            <th>Coefficient</th>
            <th>Actions</th>
         </tr>
      </thead>
      <tbody>
         <?php
         if (!empty($listeDesTypesExams)) :
            foreach ($listeDesTypesExams as $typeExam) :
               ?>
               <tr>
                  <td><?php echo $typeExam['libelle']; ?></td>
                  <td><?php echo $typeExam['coef']; ?></td>
                  <td><a href="/admin/typesExams/<?php echo $typeExam['idType']; ?>/modifier"><img src="/img/admin/type_exam_edit.png" alt="modifier" title="Modifier ce type d'examen" /></a> <a href="/admin/typesExams/<?php echo $typeExam['idType']; ?>/supprimer"><img src="/img/admin/type_exam_delete.png" alt="supprimer"  title="Supprimer ce type d'examen" onClick="return confirm('Êtes-vous sûr de vouloir supprimer ce type d\'examen ?');" /></a></td>
               </tr>
               <?php
            endforeach;
         else :
            ?>
            <tr>
               <td colspan="3">Aucun type d'examen</td>
            </tr>
         <?php endif; ?>
      </tbody>
   </table>
   <p><a href="/admin/">Retour à l'accueil du panel d'administration</a></p>
<?php endif; ?>
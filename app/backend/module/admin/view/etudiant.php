<?php if ($showProfil) : ?>
   <h1>Profil étudiant</h1>
   <p><strong>Nom</strong> : <?php echo $etudiant['nom']; ?></p>
   <p><strong>Prénom</strong> : <?php echo $etudiant['prenom']; ?></p>
   <p><strong>Promotion</strong> : <?php echo $etudiant['promo']; ?></p>
   <h2>Modules</h2>
   <ul>
      <?php
      if (!empty($etudiant['listeDesModules'])) :
         foreach ($etudiant['listeDesModules'] as $module) :
            ?>
            <li><a href="/admin/<?php echo $etudiant['promo']; ?>/<?php echo $module['libelle']; ?>/matières"><?php echo $module['libelle']; ?></a></li>
            <ul>
               <?php
               if (!empty($module['listeDesMatieres'])) :
                  foreach ($module['listeDesMatieres'] as $matiere) :
                     ?>
                     <li><a href="/admin/<?php echo $etudiant['promo']; ?>/<?php echo $module['libelle']; ?>/<?php echo $matiere['libelle']; ?>"><?php echo $matiere['libelle']; ?></a></li>
                     <ul>
                        <?php
                        if (!empty($matiere['listeDesExamens'])) :
                           foreach ($matiere['listeDesExamens'] as $examen) :
                              ?>
                              <li><?php echo $examen['libelle']; ?> (<strong>Note</strong> :
                                 <?php if (!empty($examen['note'])) :
                                    echo $examen['note']; ?>/20
                                 <?php else : ?>
                                    Absence justifiée
                              <?php endif; ?> ; <strong>Moyenne promo</strong> : <?php echo $examen['moyennePromo']; ?>/20)
                              </li>
                              <?php
                           endforeach;
                        else :
                           ?>
                           <li>Aucun examen</li>
                     <?php endif; ?>
                     </ul>
                     <?php
                  endforeach;
               else :
                  ?>
                  <li>Aucune matière</li>
            <?php endif; ?>
            </ul>
            <?php
         endforeach;
      else :
         ?>
         <li>Aucun module</li>
      <?php
      endif;
   else :
      ?>
   </ul>
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
                  <td><a href="/admin/étudiant/<?php echo $etudiant['idUtil']; ?>/profil"><img src="/img/admin/go_user.png" alt="Profil élève" title="Consulter le profil de cet élève" /></a></td>
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
<?php endif; ?>
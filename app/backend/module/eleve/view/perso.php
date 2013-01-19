<?php if ($voirMatiere) : ?>
   <h1><?php echo $matiere; ?></h1>
   <p><a href="/étudiant/promo/<?php echo $module; ?>/<?php echo $matiere; ?>" class="button blueButton">Résultats de ma promotion</a></p>
   <h2>Examens</h2>
   <table>
      <thead>
         <tr>
            <th>Intitulé</th>
            <th>Date</th>
            <th>Note</th>
            <th>Moyenne promotion</th>
         </tr>
      </thead>
      <tbody>
         <?php if (!empty($listeDesExamens)) :
            foreach ($listeDesExamens as $examen) :
               ?>
               <tr>
                  <td><?php echo $examen['libelle']; ?></td>
                  <td><?php echo $examen['date']; ?></td>
                  <td>
                     <?php if (!empty($examen['note'])) : 
                     echo $examen['note']; ?>/20
                     <?php else : ?>
                     Absence justifiée
                     <?php endif; ?>
                  </td>
                  <td><?php echo $examen['moyennePromo']; ?>/20</td>
               </tr>
               <?php
            endforeach;
         else :
            ?>
            <tr>
               <td colspan="4">Aucun examen</td>
            </tr>
         <?php
         endif;
         ?>
      </tbody>
   </table>
   <p><a href="/étudiant/perso/<?php echo $module; ?>">Retour au module <?php echo $module; ?></a></p>
<?php elseif ($voirModule) : ?>
   <h1><?php echo $module; ?></h1>
   <p><a href="/étudiant/promo/<?php echo $module; ?>" class="button blueButton">Résultats de ma promotion</a></p>
   <h2>Matières</h2>
   <ul>
      <?php
      if (!empty($listeDesMatieres)) :
         foreach ($listeDesMatieres as $matiere) :
            ?>
            <li><a href="/étudiant/perso/<?php echo $module; ?>/<?php echo $matiere['libelle']; ?>"><?php echo $matiere['libelle']; ?></a></li>
            <?php
         endforeach;
      else :
         ?>
         <li>Aucune matière</li>
      <?php
      endif;
      ?>
   </ul>
   <p><a href="/étudiant/perso">Retour à vos résultats</a></p>
   <?php else : ?>
   <h1>Vos résultats</h1>
   <p><strong>Moyenne générale</strong> :
      <?php
      if (!empty($moyenneGenerale)) :
         echo $moyenneGenerale;
         ?>/20
         <?php
      else :
         ?> Indisponible
      <?php endif; ?></p>
   <h2>Mes modules</h2>
   <p><a href="/étudiant/promo" class="button blueButton">Résultats de ma promotion</a></p>
   <ul>
      <?php
      if (!empty($listeDesModules)) :
         foreach ($listeDesModules as $module) :
            ?>
            <li>
               <a href="/étudiant/perso/<?php echo $module['libelle']; ?>"><?php echo $module['libelle']; ?></a> :
               <?php
               if (!empty($module['moyenne'])) :
                  echo $module['moyenne'];
                  ?>/20
               <?php else :
                  ?>
                  Moyenne indisponible
               <?php
               endif;
               ?>
            </li>
            <?php
         endforeach;
      else :
         ?>
         <li>Aucun module</li>
   <?php endif; ?>
   </ul>

   <p><a href="/étudiant/">Retour à l'accueil de votre espace personnel</a></p>
<?php endif; ?>
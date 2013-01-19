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
<ul>
   <?php
   if (!empty($listeDesModules)) :
      foreach ($listeDesModules as $module) :
         ?>
         <li>
            <?php echo $module['libelle']; ?> :
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
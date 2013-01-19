<h1>Résultats de votre promotion</h1>
<p><strong>Moyenne générale</strong> :
   <?php
   if (!empty($moyenneGenerale)) :
      echo $moyenneGenerale;
      ?>/20
      <?php
   else :
      ?> Indisponible
   <?php endif; ?></p>
<h2>Modules</h2>
<p><a href="/étudiant/perso" class="button blueButton">Mes résultats</a></p>
<ul>
   <?php
   if (!empty($listeDesModules)) :
      foreach ($listeDesModules as $module) :
         ?>
         <li>
            <a href="/étudiant/promo/<?php echo $module['libelle']; ?>"><?php echo $module['libelle']; ?></a> :
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
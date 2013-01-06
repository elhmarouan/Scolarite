<?php if ($manageMatieres) : ?>
   <h1>Matières du module <?php echo $module; ?></h1>
<?php else : ?>
   <h1>Liste des modules <?php echo $prefixPromo . $promo; ?></h1>
   <ul>
      <?php
      if (!empty($listeDesModules)) :
         foreach ($listeDesModules as $module) :
            ?>
            <li><a href="/prof/<?php echo $promo; ?>/<?php echo $module; ?>/matières"><?php echo $module; ?></a></li>
            <?php
         endforeach;
      else :
         ?>
         <li>Aucun module disponible</li>
      <?php endif; ?>
   </ul>
   <p><a href="/prof/<?php echo $promo; ?>">Retour à la gestion de la promotion</a></p>
<?php endif; ?>
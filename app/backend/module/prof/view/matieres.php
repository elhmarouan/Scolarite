<?php if ($manageExamens) : ?>
   <h1>Examens de la matière <?php echo $matiere; ?></h1>
<?php else : ?>
   <h1>Matières du module <?php echo $module; ?></h1>
   <ul>
      <?php
      if (!empty($MatiereDuModule)) :
         foreach ($MatiereDuModule as $matiere) :
            ?>
            <li><a href="/prof/<?php echo $promo; ?>/<?php echo $module; ?>/<?php echo $matiere; ?>/examens"><?php echo $matiere; ?></a></li>
            <?php
         endforeach;
         else : ?>
            <li>Aucune matière</li>
            <?php endif; ?>
   </ul>
   <?php endif; ?>
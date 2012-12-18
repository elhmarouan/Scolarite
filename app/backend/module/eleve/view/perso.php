<?php if ($viewNotes) : ?>
   <h2>Voir mes notes <?php echo $viewNotes; ?></h2>
   <a href="<?php echo $perso; ?>">Notes par matière</a>
<?php else : ?>
   <h2>Voir mes moyennes <?php echo $viewAverages; ?></h2>
   <a href="<?php echo $perso; ?>">Moyennes par matière</a>
<?php endif; ?>

   
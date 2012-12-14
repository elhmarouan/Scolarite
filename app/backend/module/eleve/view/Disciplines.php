<?php if ($viewNotes) : ?>
   <h2>Voir mes notes <?php echo $viewNotes; ?></h2>
   <a href="/notesm">Notes par matière</a>
<?php else : ?>
   <h2>Voir mes moyennes <?php echo $viewAverages; ?></h2>
   <a href="/moyennesm">Moyennes par matière</a>
<?php endif; ?>

   
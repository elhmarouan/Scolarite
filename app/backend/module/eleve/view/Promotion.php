<?php if ($viewPromoNotes) : ?>
   <h2>Consulter les notes de la promotion <?php echo $viewPromoNotes; ?></h2>
   <a href="/notesm">Notes de la promotion par matière</a>
<?php else : ?>
   <h2>Consulter les moyennes de la promotion <?php echo $viewPromoAverages; ?></h2>
   <a href="/moyennesm">Moyennes de la promotion par matière</a>
<?php endif; ?>

      
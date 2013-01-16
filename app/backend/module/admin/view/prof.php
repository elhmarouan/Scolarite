<h1>Profil professeur</h1>
<p><strong>Nom</strong> : <?php echo $prof['nom']; ?></p>
<p><strong>Prénom</strong> : <?php echo $prof['prenom']; ?></p>
<h2>Contact</h2>
<p><strong>Bureau</strong> : <?php echo $prof['numBureau']; ?></p>
<p><strong>Téléphone</strong> : (+33)<?php echo $prof['telBureau']; ?></p>
<h2>Responsabilités</h2>
<ul>
   <?php
   if (!empty($prof['responsabilites'])) :
      foreach ($prof['responsabilites'] as $responsabilite) :
         ?>
         <li><?php echo $responsabilite; ?></li>
         <?php
      endforeach;
   else :
      ?>
      <li>Ce professeur n'est en charge d'aucune matière</li>
   <?php endif; ?>
</ul>
<?php
if ($addNote) :

   if (!empty($data)) :
      echo '<?xml version="1.0" encoding="utf-8"?>'; ?>
      <root>
         <data>
            <td><?php echo $data['login']; ?></td>
            <td><?php echo $data['nom']; ?></td>
            <td><?php echo $data['prenom']; ?></td>
            <td><?php echo $data['note']; ?>/20</td>
            <td>-</td>
         </data>
         <erreurs></erreurs>
      </root>
   <?php else :
      echo '<?xml version="1.0" encoding="utf-8"?>'; ?>
      <root>
         <data></data>
         <erreurs>
            <?php foreach ($erreurs as $erreur) : ?>
               <erreur><?php echo $erreur; ?></erreur>
            <?php endforeach; ?>
         </erreurs>
      </root>
   <?php endif; ?>
<?php else : ?>
   <h1>Notes de l'examen « <?php echo $examen; ?> »</h1>
   <form method="post" action="" onSubmit="return save_note(this, '<?php echo $urlPage; ?>');">
      <table>
         <thead>
            <tr>
               <th>Login</th>
               <th>Nom</th>
               <th>Prénom</th>
               <th>Note</th>
               <th>Action</th>
            </tr>
         </thead>
         <tbody>
            <?php
            if (!empty($listeDesNotes)) :
               foreach ($listeDesNotes as $note) :
                  ?>
                  <tr>
                     <td><?php echo $note['login']; ?></td>
                     <td><?php echo $note['nom']; ?></td>
                     <td><?php echo $note['prenom']; ?></td>
                     <td>
                        <?php
                        if (!empty($note['note'])) :
                           echo $note['note'];
                           ?>/20
                        <?php else : ?>
                           Absence justifiée
                        <?php endif; ?>
                     </td>
                     <td><?php if ($estResponsable) : ?><a href="/prof/<?php echo $promo; ?>/<?php echo $module; ?>/<?php echo $matiere; ?>/<?php echo $idExam; ?>/<?php echo $note['numEtudiant']; ?>/modifier"><img src="/img/prof/note_edit.png" alt="Modifier la note" title="Modifier la note" /></a> <a href="/prof/<?php echo $promo; ?>/<?php echo $module; ?>/<?php echo $matiere; ?>/<?php echo $idExam; ?>/<?php echo $note['numEtudiant']; ?>/supprimer"><img src="/img/prof/note_delete.png" alt="Supprimer la note" title="Supprimer la note" /></a><?php else : ?>-<?php endif; ?></td>
                  </tr>
                  <?php
               endforeach;
            else :
               ?>
               <tr>
                  <td colspan="5">Aucune note à afficher</td>
               </tr>
            <?php
            endif;
            if ($estResponsable) :
               ?><tr>
                  <td colspan="5"><a onClick="saisir_note(this);">Saisir une nouvelle note</a></td>
               </tr>
            <?php endif; ?>
         </tbody>
      </table>
   </form>

   <p><a href="/prof/<?php echo $promo; ?>/<?php echo $module; ?>/<?php echo $matiere; ?>">Retour à la liste des examens</a></p>
<?php endif; ?>
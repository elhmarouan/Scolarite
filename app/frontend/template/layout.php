<!DOCTYPE html>
<html lang="fr">
   <head>
      <title><?php echo $windowTitle; ?></title>
      <meta charset="utf-8" />
      <base href="<?php echo $root; ?>" />
      <link rel="stylesheet" type="text/css" href="css/frontend.css" />
      <link href="http://fonts.googleapis.com/css?family=Rambla:700|Open+Sans" rel="stylesheet" type="text/css">
   </head>
   <body>
      <header>
         <a href="/"><img src="/img/scol.png" alt="scol.png"/></a>
      </header>
      <div id="connexionBlock">
         <?php if (!User::isOnline()) : ?>
            <form method="post" action="/connexion">
               <label>Login :</label> <input type="text" name="login" /><br />
               <label>Mot de passe :</label> <input type="password" name="password" />
               <input type="submit" value="Connexion" />
            </form>
         <?php else : ?>
            <ul>
               <?php
               if (User::isMemberOf('Administrateur')) :
                  ?>
                  <li><a href="/admin/">Mon espace personnel</a></li>
               <?php elseif (User::isMemberOf('Professeur')) :
                  ?>
                  <li><a href="/prof/">Mon espace personnel</a></li>
                  <?php
               else :
                  ?>
                  <li><a href="/eleve/">Mon espace personnel</a></li>
               <?php endif; ?>
               <li><a href="/déconnexion">Déconnexion</a></li>
            </ul>
         <?php
         endif;
         ?>
      </div>
      <div id="content">
         <div id="popup">
            <?php
            if (!empty($popupsList)) :
               foreach ($popupsList as $popup) :
                  switch ($popup['type']) :
                     case Popup::ERROR:
                        $class = 'popupErreur';
                        break;
                     case Popup::INFORMATION:
                        $class = 'popupInfo';
                        break;
                     case Popup::SUCCESS:
                        $class = 'popupSucces';
                        break;
                     case Popup::WARNING:
                        $class = 'popupAttention';
                        break;
                  endswitch;
                  ?>
                  <div class="<?php echo $class; ?>">
                  <?php echo $popup['message']; ?>
                  </div>
                  <?php
               endforeach;
            endif;
            ?>
         </div>
         <?php echo $content; ?>
      </div>
      <footer>
         <p>Projet réalisé par Soheil Dahmani, Céline Lepicard, Stanislas Michalak et Vincent Simon.</p>
         <p><a href="http://creativecommons.org/licenses/by-sa/3.0/deed.fr"><img src="http://i.creativecommons.org/l/by-sa/3.0/80x15.png" title="Cette oeuvre est sous licence creative commons BY-SA" alt="creative commons BY-SA" /></a></p>
      </footer>
   </body>
</html>
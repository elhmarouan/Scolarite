<!DOCTYPE html>
<html lang="fr">
   <head>
      <title><?php echo $windowTitle; ?></title>
      <meta charset="utf-8" />
      <base href="<?php echo $root; ?>" />
      <link rel="stylesheet" type="text/css" href="css/backend.css" />
      <link href="http://fonts.googleapis.com/css?family=Rambla:700|Open+Sans" rel="stylesheet" type="text/css">
   </head>
   <body>
      <nav>
         <ul>
            <li><a href="/deconnexion">Déconnexion</a></li>
         </ul>
      </nav>
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
   </body>
</html>
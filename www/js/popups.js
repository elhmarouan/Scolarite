/**
 * Interface javascript pour l'ajout de bulles d'information
 * 
 * @author Stanislas Michalak <stanislas.michalak@gmail.com>
 */

var popup = {
   INFORMATION: 1,
   ERROR: 2,
   SUCCESS: 3,
   WARNING: 4,
   
   add: function(message, type) {
      var newPopup = document.createElement('div');
      if (!type) {
         type = this.INFORMATION;
      }
      var id = document.getElementById('popup').childNodes.length + 1;
      newPopup.setAttribute('id', 'popup' + id);
      switch (type) {
         case this.ERROR:
            newPopup.setAttribute('class', 'popupErreur');
            break;
         case this.INFORMATION:
            newPopup.setAttribute('class', 'popupInfo');
            break;
         case this.SUCCESS:
            newPopup.setAttribute('class', 'popupSucces');
            break;
         case this.WARNING:
            newPopup.setAttribute('class', 'popupAttention');
            break;
      }
      newPopup.innerHTML = message;
      document.getElementById('popup').appendChild(newPopup);
      return newPopup.id;
   },
   delete: function(idPopup) {
      if (document.getElementById(idPopup)) {
         document.getElementById('popup').removeChild(document.getElementById(idPopup));
      }
   }
};
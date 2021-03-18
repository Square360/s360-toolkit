Drupal.behaviors.addParagraphModalId = {
  attach: function(context, settings) {
    const $ = jQuery;


    $('.paragraph--type--modal', context).once().each(function () {
      let modalId = $(this).attr('id');

      if (modalId) {
        $(this).append('<div><strong>Modal ID:</strong> ' + modalId + '</div>');
      }
    });
  }
};

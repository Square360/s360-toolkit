Drupal.behaviors.addParagraphModalId = {
  attach: function(context, settings) {
    const $ = jQuery;

    $('.paragraph--type--modal', context).once().each(function () {
      let modalId = $(this).attr('id');

      if (modalId) {
        $(this).append('<div class="paragraph__modal-trigger-id"><strong>Modal Trigger Id:</strong> ' + modalId + '</div>');
      }
    });
  }
};

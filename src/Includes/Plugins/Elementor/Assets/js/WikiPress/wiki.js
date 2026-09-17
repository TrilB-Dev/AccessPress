(( $ ) => {
  'use strict';

    $(document).on('click', '.accesspress-wiki-searchmodal .open-search', (event) => {
      $(event.currentTarget).closest('.accesspress-wiki-searchmodal').find('.overlay').show();
  });

  $(document).on('click', '.accesspress-wiki-searchmodal .close', (event) => {
    $(event.currentTarget).closest('.overlay').hide();
  });
})(jQuery);


// Require the stlye files inside the "src/scss" folder.
require('SRC_STYLES/s360-toolkit-custom-modal.theme.scss');

// Require all static images inside the "src/images" folder.
require.context('SRC_IMAGES', true, /\.(gif|png|jpe?g|svg)$/);

require('./partials/paragraph-modal');

/**
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
Joomla = window.Joomla || {};

((document, Joomla) => {
  'use strict';

  document.addEventListener('DOMContentLoaded', () => {
    if (Joomla.getOptions('js-category-edit')) {
      const options = Joomla.getOptions('js-category-edit');

      if (!options.elementId) {
        throw new Error('Element Id id required, Choices cannot be initiated for category on the fly.');
      }

      // eslint-disable-next-line no-undef,no-new
      new Choices(document.getElementById(options.elementId), {
        addItems: false,
        duplicateItems: options.duplicateItems ? options.duplicateItems : false,
        flip: options.flip ? options.flip : true,
        shouldSort: options.shouldSort ? options.shouldSort : false,
        searchEnabled: options.searchEnabled ? options.searchEnabled : true,
      });
    }
  });
})(document, Joomla);

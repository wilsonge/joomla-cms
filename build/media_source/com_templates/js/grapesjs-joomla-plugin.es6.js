/**
 * @copyright   (C) 2024 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
/* global grapesjs */
((grapesjs, Joomla) => {
  grapesjs.plugins.add('joomla-plugin', (editor, options) => {
    /** We use a custom model here so that we can override the rendering in the grapesjs editor */
    editor.DomComponents.addType('jcomponent', {
      isComponent: (el) => el.tagName === 'JDOC:INCLUDE' && el.attributes.type.value === 'component',

      // Model definition
      model: {
        // Default properties
        defaults: {
          tagName: 'jdoc:include',
          droppable: false,
          copyable: false,
          void: true,
          attributes: {
            type: 'component',
          },
        },
      },
      view: {
        tagName: 'div',
        onRender({ el }) {
          // TODO: We'd love to use a class rather than the styling below. But struggling to make it work
          el.classList.add('j-class');
          el.style.padding = '10px';
          el.style.margin = '20px';
          el.style.border = '2px solid red';
        },
      },
    });

    editor.BlockManager.add('joomla-component', {
      label: options.componentLabel || Joomla.Text._('COM_TEMPLATES_PAGEBUILDER_BLOCK_JOOMLA_COMPONENT'),
      category: options.componentCategory || Joomla.Text._('COM_TEMPLATES_PAGEBUILDER_JOOMLA_CATEGORY'),
      content: {
        type: 'jcomponent',
      },
      attributes: {
        class: 'fas fa-swatchbook',
      },
    });
  });
})(grapesjs, Joomla);

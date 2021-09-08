/* global grapesjs */
((grapesjs, Joomla) => {
  grapesjs.plugins.add('joomla-plugin', (editor, options) => {
    editor.setStyle({
      selectors: ['j-class'],
      style: {
        width: '100%',
        background: 'red',
        padding: '10px',
        margin: '20px',
      },
    });

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
          // Adds a class for our styling which we've added above
          el.classList.add('j-class');
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

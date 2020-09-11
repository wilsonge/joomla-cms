Joomla = window.Joomla || {};

grapesjs.plugins.add('joomla-plugin', (editor, options) => {
  editor.BlockManager.add('joomla-component', {
    label: options.componentLabel || Joomla.Text._('COM_TEMPLATES_PAGEBUILDER_BLOCK_JOOMLA_COMPONENT'),
    category: options.componentCategory || Joomla.Text._('COM_TEMPLATES_PAGEBUILDER_JOOMLA_CATEGORY'),
    content: {
      style: { padding: '10px' },
      content: '<jdoc:include type="component" />',
    },
    attributes: {
      class: 'fas fa-swatchbook',
    },
    droppable: false,
  });
});

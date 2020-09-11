Joomla = window.Joomla || {};

((Joomla, grapesjs) => {
  document.addEventListener('DOMContentLoaded', () => {
    const grapesJSParams = {};

    // TODO: On success we must update the CSRF token in the storageManager (onComplete?)
    grapesJSParams[Joomla.getOptions('csrf.token')] = 1;

    const editor = grapesjs.init({
      container: '#gjs',
      cssIcons: null,
      // Load our Joomla Custom blocks
      plugins: ['joomla-plugin'],
      storageManager: {
        type: 'remote',
        stepsBeforeSave: 1,
        urlStore: `${Joomla.getOptions('system.paths').rootFull}administrator/index.php?option=com_templates&task=file.save&file=${Joomla.getOptions('file_name')}&id=${Joomla.getOptions('extension_id')}`,
        urlLoad: `${Joomla.getOptions('system.paths').rootFull}administrator/index.php?option=com_templates&task=file.load&file=${Joomla.getOptions('file_name')}&id=${Joomla.getOptions('extension_id')}`,
        params: grapesJSParams,
      },
    });

    editor.BlockManager.add('text', {
      label: Joomla.Text._('COM_TEMPLATES_PAGEBUILDER_BLOCK_BASIC_TEXT'),
      category: Joomla.Text._('COM_TEMPLATES_PAGEBUILDER_BASIC_CATEGORY'),
      attributes: { class: 'fas fa-file-alt' },
      content: {
        type: 'text',
        content: 'Insert your text here',
        style: { padding: '10px' },
        activeOnRender: 1,
      },
    });
    editor.BlockManager.add('video', {
      label: Joomla.Text._('COM_TEMPLATES_PAGEBUILDER_BLOCK_BASIC_VIDEO'),
      category: Joomla.Text._('COM_TEMPLATES_PAGEBUILDER_BASIC_CATEGORY'),
      attributes: { class: 'fas fa-video' },
      content: {
        type: 'video',
        provider: 'yt',
        style: {
          height: '350px',
          width: '615px',
        },
      },
    });
  });
})(Joomla, grapesjs);

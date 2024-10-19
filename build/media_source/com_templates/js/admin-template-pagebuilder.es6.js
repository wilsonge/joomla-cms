/**
 * @copyright   (C) 2024 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

Joomla = window.Joomla || {};

/* global grapesjs */
((Joomla, grapesjs) => {
  document.addEventListener('DOMContentLoaded', () => {
    const editor = grapesjs.init({
      container: '#gjs',
      // Load our Joomla Custom blocks
      plugins: [
        'joomla-plugin',
        'grapesjs-plugin-header',
      ],
      pluginsOpts: {
        'grapesjs-plugin-header': {
          // TODO: Override all the setting labels
          category: Joomla.Text._('COM_TEMPLATES_PAGEBUILDER_BASIC_CATEGORY'),
        },
      },
      storageManager: {
        type: 'remote',
        stepsBeforeSave: 1,
        options: {
          remote: {
            urlStore: `${Joomla.getOptions('system.paths').rootFull}administrator/index.php?option=com_templates&task=file.save&file=${Joomla.getOptions('file_name')}&id=${Joomla.getOptions('extension_id')}`,
            urlLoad: `${Joomla.getOptions('system.paths').rootFull}administrator/index.php?option=com_templates&task=file.load&file=${Joomla.getOptions('file_name')}&id=${Joomla.getOptions('extension_id')}`,
            // TODO: On success we must update the CSRF token in the storageManager (onComplete?)
            headers: {
              'X-CSRF-Token': Joomla.getOptions('csrf.token', ''),
            },
          },
        },
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

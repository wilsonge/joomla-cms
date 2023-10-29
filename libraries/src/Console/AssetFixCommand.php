<?php

/**
 * Joomla! Content Management System
 *
 * @copyright  (C) 2019 Open Source Matters, Inc. <https://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\Console;

use Joomla\CMS\Table\Asset;
use Joomla\CMS\Table\Content;
use Joomla\CMS\Table\Table;
use Joomla\Console\Command\AbstractCommand;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\StyleInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

// phpusercs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Console command for adding a user
 *
 * @since  4.0.0
 */
class AssetFixCommand extends AbstractCommand
{
    use DatabaseAwareTrait;

    /**
     * Command constructor.
     *
     * @param   DatabaseInterface  $db  The database
     *
     * @since   4.2.0
     */
    public function __construct(DatabaseInterface $db)
    {
        parent::__construct();

        $this->setDatabase($db);
    }

    /**
     * Internal function to execute the command.
     *
     * @param   InputInterface   $input   The input to inject into the command.
     * @param   OutputInterface  $output  The output to inject into the command.
     *
     * @return  integer  The command exit code
     *
     * @since   4.0.0
     */
    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $ioStyle  = new SymfonyStyle($input, $output);
        $ioStyle->title('Asset Fix');

        /** @var Asset $asset */
        $asset = Table::getInstance('Asset');

        $rootAssetLoadResult = $asset->loadByName('root.1');
        $rootId              = false;

        if ($rootAssetLoadResult) {
            $rootId = (int) $asset->id;
        }

        if ($rootId && ($asset->level != 0 || $asset->parent_id != 0)) {
            $this->fixRoot($rootId);
        }

        // TODO: Is this doing anything??
        if (!$asset->id) {
            try {
                $rootId = $this->getAssetRootId();
            } catch (\UnexpectedValueException) {
                // Continue
            }

            $this->fixRoot($rootId);
        }

        if ($rootId === false) {
            $this->createRootAsset();
            $ioStyle->success('No valid root asset was detected so one has been created');
            $rootId = $this->getAssetRootId();
        }

        if (!$rootId) {
            $ioStyle->error('Failed to create a root asset');

            return Command::FAILURE;
        }

        $this->validateComponentAssets($ioStyle, $rootId);

        // Let's rebuild the categories tree
        Table::getInstance('Category')->rebuild();

        // Although we have rebuilt it may not have fully worked. Let's do some extra checks.
        $asset = Table::getInstance('Asset');

        $assetTree = $asset->getTree(1);

        // Get all the categories as objects
        $queryc = $this->getDatabase()->getQuery(true);
        $queryc->select($this->getDatabase()->quoteName(['id', 'asset_id', 'parent_id']));
        $queryc->from($this->getDatabase()->quoteName('#__categories'));
        $this->getDatabase()->setQuery($queryc);
        $categories = $this->getDatabase()->loadObjectList();

        // Create an array of just level 1 assets that look like the are extensions.
        $extensionAssets = [];

        foreach ($assetTree as $assetData) {
            // Now we will make a list of components based on the assets table not the extensions table.
            if (substr($assetData->name, 0, 4) === 'com_' && $assetData->level == 1) {
                $extensionAssets[$assetData->title] = $assetData->id;
            }
        }

        foreach ($assetTree as $assetData) {
            // Assume the root asset is valid.
            if ($assetData->name != 'root.1') {
                // There have been some reports of misnamed contact assets on very old sites. Handle this
                // gracefully.
                if (strpos($assetData->name, 'contact_details') != false) {
                    str_replace($assetData->name, 'contact_details', 'contact');
                }

                // Now handle categories with parent_id of 0 or 1
                if (strpos($assetData->name, 'category') != false) {
                    $catFixCount = 0;
                    $fixedCats   = [];
                    // Just assume that they are top level categories.
                    // We are also goingto fix parent_id of 1 since some people in the forums did this to temporarily
                    // fix a problem and also categories should never have a parent_id of 1.
                    if ($assetData->parent_id == 0 || $assetData->parent_id == 1) {
                        $catFixCount += 1;
                        $explodeAssetName     = explode('.', $assetData->name);
                        $assetData->parent_id = $extensionAssets[$explodeAssetName[0]];
                        $fixedCats[]          = $assetData->id;

                        $asset->load($assetData->id);
                        // For categories the ultimate parent is the extension
                        $asset->parent_id = $extensionAssets[$explodeAssetName[0]];
                        $asset->store();
                        $asset->reset();

                        if ($output->isVerbose()) {
                            $ioStyle->info("The assets for the following category was fixed:' . $assetData->name . ' You will want to
                                check the category manager to make sure any nesting you require is in place.");
                        }
                    }

                    if ($output->isVeryVerbose())
                    {
                        $ioStyle->info(sprintf("There were %d category issues fixed.", $catFixCount));
                    }
                }
            }
        }

        // Rebuild again as a final check to clear up any newly created inconsistencies.
        Table::getInstance('Category')->rebuild();
        $ioStyle->info("Categories asset checks finished.");

        $this->checkArticleAssets($ioStyle);

        // TODO: Workflows
        // TODO: Modules
        // TODO: Scheduler Tasks

        return Command::SUCCESS;
    }

    /**
     * Configure the command.
     *
     * @return  void
     *
     * @since   4.0.0
     */
    protected function configure(): void
    {
        $this->setName('core:asset-fix');
        $this->setDescription('Fix any detected site asset problems');
    }

    /**
     * Fix the root asset record
     *
     * @param   integer  $rootId  The primary key value for the root id, usually 1.
     *
     * @return  void
     *
     * @since   __DEPLOY_VERSION__
     */
    protected function fixRoot(int $rootId)
    {
        // Set up the proper nested values for root
        $queryr = $this->getDatabase()->getQuery(true);
        $queryr->update($this->getDatabase()->quoteName('#__assets'));
        $queryr->set($this->getDatabase()->quoteName('parent_id') . ' = 0 ')
            ->set($this->getDatabase()->quoteName('level') . ' =  0 ')
            ->set($this->getDatabase()->quoteName('lft') . ' = 1 ')
            ->set($this->getDatabase()->quoteName('name') . ' = ' . $this->getDatabase()->quote('root.' . $rootId));
        $queryr->where('id = ' . $rootId);
        $this->getDatabase()->setQuery($queryr);
        $this->getDatabase()->execute();
    }

    /**
     * Fix the asset record for extensions
     *
     * @param   Asset    $asset   The asset table object
     * @param   integer  $rootId  The primary key value for the root id, usually 1.
     *
     * @return  void
     *
     * @since   __DEPLOY_VERSION__
     */
    protected function fixExtensionAsset($asset, $rootId = 1)
    {
        // Set up the proper nested values for an extension
        $query = $this->getDatabase()->getQuery(true);
        $query->update($this->getDatabase()->quoteName('#__assets'));
        $query->set($this->getDatabase()->quoteName('parent_id') . ' =  ' . $rootId)
            ->set($this->getDatabase()->quoteName('level') . ' = 1 ');
        $query->where('name = ' . $this->getDatabase()->quote($asset->name));
        $this->getDatabase()->setQuery($query);
        $this->getDatabase()->execute();
    }

    /**
     * Fix the asset record for extensions
     *
     * @param   string   $extensionName  The asset table object
     * @param   integer  $rootId         The primary key value for the root id, usually 1.
     *
     * @return  bool
     *
     * @since   __DEPLOY_VERSION__
     */
    protected function createExtensionAsset($extensionName, $rootId = 1)
    {
        $assetTable = Table::getInstance('Asset');

        return $assetTable->save([
            'parent_id' => $rootId,
            'level'     => 1,
            'name'      => $extensionName,
            'title'     => $extensionName,
            'rule'      => json_encode([]),
        ]);
    }

    /**
     * Create the root asset record
     *
     * @return  void
     *
     * @since   __DEPLOY_VERSION__
     */
    protected function createRootAsset(): void
    {
        $parentId = 0;
        $level    = 0;
        $name     = 'root.1';
        $title    = 'Root Asset';
        $rule     = json_encode(
            [
                'core.login.site' => [
                    '6' => '1',
                    '2' => '1',
                ],
                'core.login.admin' => [
                    '6' => '1',
                ],
                'core.login.api' => [
                    '8' => '1',
                ],
                'core.login.offline' => [
                    '6' => '1',
                ],
                'core.admin' => [
                    '8' => '1',
                ],
                'core.manage' => [
                    '7' => '1',
                ],
                'core.create' => [
                    '6' => '1',
                    '3' => '1',
                ],
                'core.delete' => [
                    '6' => '1',
                ],
                'core.edit' => [
                    '6' => '1',
                    '4' => '1',
                ],
                'core.edit.state' => [
                    '6' => '1',
                    '5' => '1',
                ],
                'core.edit.own' => [
                    '6' => '1',
                    '3' => '1',
                ],
            ],
        );

        $query = $this->getDatabase()->getQuery(true);
        $query->insert($this->getDatabase()->quoteName('#__assets'))
            ->columns(['parent_id', 'level', 'name', 'title', 'rules']);

        $query->values(
            implode(
                ',',
                $query->bindArray(
                    [$parentId, $level, $name, $title, $rule],
                    [
                        ParameterType::INTEGER,
                        ParameterType::INTEGER,
                        ParameterType::STRING,
                        ParameterType::STRING,
                        ParameterType::STRING,
                    ]
                )
            )
        );
        $this->getDatabase()->setQuery($query);
        $this->getDatabase()->execute();
    }

    /**
     * Gets the ID of the root item in the tree
     *
     * @return  integer  The primary id of the root row
     *
     * @since   __DEPLOY_VERSION__
     * @throws  \UnexpectedValueException
     */
    protected function getAssetRootId(): mixed
    {
        $rootParentId = 0;

        // Test for a unique record with parent_id = 0
        $query = $this->getDatabase()->getQuery(true);
        $query->select($this->getDatabase()->quoteName('id'))
            ->from($this->getDatabase()->quoteName('#__assets'))
            ->where($this->getDatabase()->quote('parent_id') . ' = :parent_id')
            ->bind(':parent_id', $rootParentId, ParameterType::INTEGER);

        $result = $this->getDatabase()->setQuery($query)->loadColumn();

        if (\count($result) == 1) {
            return $result[0];
        }

        // Test for a unique record with lft = 0
        $query = $this->getDatabase()->getQuery(true);
        $query->select('id')
            ->from($this->getDatabase()->quoteName('#__assets'))
            ->where($this->getDatabase()->quote('lft') . ' = 0');

        $result = $this->getDatabase()->setQuery($query)->loadColumn();

        if (\count($result) == 1) {
            return $result[0];
        }

        // Test for a unique record alias = root
        $query = $this->getDatabase()->getQuery(true);
        $query->select($this->getDatabase()->quoteName('id'))
            ->from($this->getDatabase()->quoteName('#__assets'))
            ->where('name LIKE ' . $this->getDatabase()->quote('root%'));

        $result = $this->getDatabase()->setQuery($query)->loadColumn();

        if (\count($result) == 1) {
            return $result[0];
        }

        throw new \UnexpectedValueException(sprintf('%s::getRootId', static::class));
    }

    /**
     * Gets the ID of the root item in the tree
     *
     * @param   StyleInterface  $ioStyle  The renderer
     * @param   integer         $rootId   The Root ID - usually 1
     *
     * @return  void
     *
     * @throws  \UnexpectedValueException
     * @since   __DEPLOY_VERSION__
     */
    protected function validateComponentAssets(StyleInterface $ioStyle, int $rootId = 1): void {
        $type = 'component';
        $asset = Table::getInstance('Asset');

        // Now let's make sure that the components  make sense
        $query = $this->getDatabase()->getQuery(true);
        $query->select($this->getDatabase()->quoteName(['extension_id', 'name']))
            ->from($this->getDatabase()->quoteName('#__extensions'))
            ->where($this->getDatabase()->quoteName('type') . ' = :type')
            ->bind(':type', $type);
        $this->getDatabase()->setQuery($query);
        $components = $this->getDatabase()->loadObjectList();

        foreach ($components as $component) {
            $asset->reset();
            $assetLoadResult = $asset->loadByName($component->name);

            if ($assetLoadResult && ($asset->parent_id != $rootId || $asset->level != 1)) {
                $this->fixExtensionAsset($asset, $rootId);
                $ioStyle->info('This asset for this extension was fixed: ' . $component->name);
            } elseif (!$assetLoadResult) {
                $ioStyle->info(sprintf('Created an asset for extension: %s', $component->name));

                if (!$this->createExtensionAsset($component->name, $rootId)) {
                    $ioStyle->error(sprintf('Failed to create asset for extension: %s', $component->name));
                }
            }
        }
    }

    /**
     * Gets the ID of the root item in the tree
     *
     * @param   StyleInterface  $ioStyle  The renderer
     * @param   integer         $rootId   The Root ID - usually 1
     *
     * @return  void
     *
     * @throws  \UnexpectedValueException
     * @since   __DEPLOY_VERSION__
     */
    protected function checkArticleAssets(StyleInterface $ioStyle) {
        /** @var Content $contentTable */
        $contentTable = Table::getInstance('Content');

        /** @var Asset $assetTable */
        $assetTable = Table::getInstance('Asset');

        // Now we will start work on the articles
        $query = $this->getDatabase()->getQuery(true);
        $query->select($this->getDatabase()->quoteName(['id', 'asset_id']));
        $query->from($this->getDatabase()->quoteName('#__content'));
        $this->getDatabase()->setQuery($query);
        $articles = $this->getDatabase()->loadObjectList();

        foreach ($articles as $article) {
            $assetTable->id = 0;
            $assetTable->reset();

            // We're going to load the articles by asset name.
            if ($article->id > 0) {
                $assetTable->loadByName('com_content.article.' . (int)$article->id);
                $query = $this->getDatabase()->getQuery(true);
                $query->update($this->getDatabase()->quoteName('#__content'));
                $query->set($this->getDatabase()->quoteName('asset_id') . ' = ' . (int) $assetTable->id);
                $query->where('id = ' . (int)$article->id);
                $this->getDatabase()->setQuery($query);
                $this->getDatabase()->execute();
            }

            //  TableAssets can clean an empty value for asset_id but not a 0 value.
            if ($article->asset_id == 0) {
                $article->asset_id = '';
            }

            $contentTable->load($article->id);
            $contentTable->store();
        }

        $ioStyle->info('Article asset checks finished.');
    }
}

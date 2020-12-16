<?php
declare(strict_types=1);

namespace GenerationGapModelBaker\Command;

use Bake\Command\ModelCommand;
use Bake\Utility\TemplateRenderer;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;
use Cake\ORM\Table;

/**
 * ExtendedModel command.
 *
 * bin/cake bake extended_model [テーブル名]
 * これにより、Model/Baked/Table|Entity に bake でメンテ対象のクラスを出力
 * Model/Table|Entity に bake されたクラスを継承した、今まで通り機能実装するクラスを出力
 */
class ExtendedModelCommand extends ModelCommand
{
    /**
     * {@inheritDoc}
     */
    public function bake(string $name, Arguments $args, ConsoleIo $io): void
    {
        $table = $this->getTable($name, $args);
        $tableObject = $this->getTableObject($name, $table);
        $data = $this->getTableContext($tableObject, $table, $name, $args, $io);

        // bake で生成される Table と Entity
        $this->pathFragment = 'Model/Baked/';
        $this->bakeTable($tableObject, $data, $args, $io);
        $this->bakeEntity($tableObject, $data, $args, $io);

        // Baked を継承した、機能を追加する Table と Entity
        $this->pathFragment = 'Model/';
        $this->bakeExtendedTable($tableObject, $data, $args, $io);
        $this->bakeExtendedEntity($tableObject, $data, $args, $io);

        $this->bakeFixture($tableObject->getAlias(), $tableObject->getTable(), $args, $io);
        $this->bakeTest($tableObject->getAlias(), $args, $io);
    }

    /**
     * Bake a extended table class.
     *
     * @param \Cake\ORM\Table $model Model name or object
     * @param array $data An array to use to generate the Table
     */
    public function bakeExtendedTable(Table $model, array $data, Arguments $args, ConsoleIo $io): void
    {
        if ($args->getOption('no-table')) {
            return;
        }

        $namespace = Configure::read('App.namespace');
        $pluginPath = '';
        if ($this->plugin) {
            $namespace = $this->_pluginNamespace($this->plugin);
        }

        $name = $model->getAlias();
        $entity = $this->_entityName($model->getAlias());
        $data += [
            'plugin' => $this->plugin,
            'pluginPath' => $pluginPath,
            'namespace' => $namespace,
            'name' => $name,
            'entity' => $entity,
            'associations' => [],
            'primaryKey' => 'id',
            'displayField' => null,
            'table' => null,
            'validation' => [],
            'rulesChecker' => [],
            'behaviors' => [],
            'connection' => $this->connection,
        ];

        $renderer = new TemplateRenderer($this->theme);
        $renderer->set($data);
        $out = $renderer->generate('Model/extended_table');

        $path = $this->getPath($args);
        $filename = $path . 'Table' . DS . $name . 'Table.php';
        $io->out("\n" . sprintf('Baking entended table class for %s...', $name), 1, ConsoleIo::QUIET);

        // 継承されたファイルは上書きを防ぐため、存在する場合は自動でスキップさせる
        if ($this->_isFile($filename, $io)) {
            return;
        }
        $io->createFile($filename, $out, false);

        // Work around composer caching that classes/files do not exist.
        // Check for the file as it might not exist in tests.
        if (file_exists($filename)) {
            require_once $filename;
        }
        $this->getTableLocator()->clear();

        $emptyFile = $path . 'Table' . DS . '.gitkeep';
        $this->deleteEmptyFile($emptyFile, $io);
    }

    /**
     * Bake a extended entity class.
     *
     * @param \Cake\ORM\Table $model Model name or object
     * @param array $data An array to use to generate the Table
     */
    public function bakeExtendedEntity(Table $model, array $data, Arguments $args, ConsoleIo $io): void
    {
        if ($args->getOption('no-entity')) {
            return;
        }
        $name = $this->_entityName($model->getAlias());

        $namespace = Configure::read('App.namespace');
        $pluginPath = '';
        if ($this->plugin) {
            $namespace = $this->_pluginNamespace($this->plugin);
            $pluginPath = $this->plugin . '.';
        }

        $data += [
            'name' => $name,
            'namespace' => $namespace,
            'plugin' => $this->plugin,
            'pluginPath' => $pluginPath,
            'primaryKey' => [],
        ];

        $renderer = new TemplateRenderer($this->theme);
        $renderer->set($data);
        $out = $renderer->generate('Model/extended_entity');

        $path = $this->getPath($args);
        $filename = $path . 'Entity' . DS . $name . '.php';
        $io->out("\n" . sprintf('Baking entended entity class for %s...', $name), 1, ConsoleIo::QUIET);

        // 継承されたファイルは上書きを防ぐため、存在する場合は自動でスキップさせる
        if ($this->_isFile($filename, $io)) {
            return;
        }
        $io->createFile($filename, $out, $args->getOption('force'));

        $emptyFile = $path . 'Entity' . DS . '.gitkeep';
        $this->deleteEmptyFile($emptyFile, $io);
    }

    /**
     * Check file exists
     * @param string $path filepath
     * @return bool
     */
    protected function _isFile(string $path, ConsoleIo $io): bool
    {
        $path = str_replace(DS . DS, DS, $path);

        $fileExists = is_file($path);
        if ($fileExists) {
            $io->out("\n" . sprintf('<warning>File exists, skipping</warning> for %s', $path), 1, ConsoleIo::QUIET);
            return true;
        }

        return false;
    }
}

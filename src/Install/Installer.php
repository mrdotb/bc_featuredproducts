<?php
/**
*  @author    mrdotb <hello@mrdotb.com>
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

declare(strict_types=1);

namespace Bc_Featuredproducts\Install;

use Db;
use Exception;
use Module;
use Tools;

class Installer
{
    /**
     * Module's installation entry point.
     *
     * @param Module $module
     *
     * @return bool
     */
    public function install(Module $module): bool
    {
        if (!$this->registerHooks($module)) {
            return false;
        }

        $this->installDb($module);

        return true;
    }

    public function uninstall(Module $module): bool
    {
        $this->uninstallDb($module);

        return true;
    }

    /**
     * Register hooks for the module.
     *
     * @see https://devdocs.prestashop.com/1.7/modules/concepts/hooks/
     *
     * @param Module $module
     *
     * @return bool
     */
    private function registerHooks(Module $module): bool
    {
        $hooks = [];

        return (bool) $module->registerHook($hooks);
    }

    /**
     * @param Module $module
     */
    private function installDb(Module $module): void
    {
        $this->executeSqlFromFile($module->getLocalPath() . 'src/Install/install.sql');
    }

    private function uninstallDb(Module $module): void
    {
        $this->executeSqlFromFile($module->getLocalPath() . 'src/Install/uninstall.sql');
    }

    /**
     * @param string $path
     */
    private function executeSqlFromFile(string $path): void
    {
        $database = Db::getInstance();
        $sqlStatements = Tools::file_get_contents($path);
        $sqlStatements = str_replace(['_DB_PREFIX_', '_MYSQL_ENGINE_'], [_DB_PREFIX_, _MYSQL_ENGINE_], $sqlStatements);

        try {
            $database->execute($sqlStatements);
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }
}

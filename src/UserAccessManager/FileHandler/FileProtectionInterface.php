<?php
/**
 * FileProtectionInterface.php
 *
 * The FileProtectionInterface interface file.
 *
 * PHP versions 5
 *
 * @author    Alexander Schneider <alexanderschneider85@gmail.com>
 * @copyright 2008-2017 Alexander Schneider
 * @license   http://www.gnu.org/licenses/gpl-2.0.html  GNU General Public License, version 2
 * @version   SVN: $id$
 * @link      http://wordpress.org/extend/plugins/user-access-manager/
 */
namespace UserAccessManager\FileHandler;

use UserAccessManager\Config\MainConfig;
use UserAccessManager\Util\Util;
use UserAccessManager\Wrapper\Php;
use UserAccessManager\Wrapper\Wordpress;

/**
 * Interface FileProtectionInterface
 *
 * @package UserAccessManager\FileProtection
 */
interface FileProtectionInterface
{
    /**
     * FileProtectionInterface constructor.
     *
     * @param Php        $php
     * @param Wordpress  $wordpress
     * @param MainConfig $config
     * @param Util       $util
     */
    public function __construct(Php $php, Wordpress $wordpress, MainConfig $config, Util $util);

    /**
     * Creates the file protection.
     *
     * @param string $dir
     * @param string $objectType
     *
     * @return bool
     */
    public function create($dir, $objectType = null);

    /**
     * Deletes the file protection.
     *
     * @param string $dir
     *
     * @return bool
     */
    public function delete($dir);
}

<?php
/**
 * ConfigFactoryTest.php
 *
 * The ConfigFactoryTest unit test class file.
 *
 * PHP versions 5
 *
 * @author    Alexander Schneider <alexanderschneider85@gmail.com>
 * @copyright 2008-2017 Alexander Schneider
 * @license   http://www.gnu.org/licenses/gpl-2.0.html  GNU General Public License, version 2
 * @version   SVN: $id$
 * @link      http://wordpress.org/extend/plugins/user-access-manager/
 */
namespace UserAccessManager\Config;

use UserAccessManager\UserAccessManagerTestCase;

class ConfigFactoryTest extends UserAccessManagerTestCase
{
    /**
     * @group  unit
     * @covers \UserAccessManager\Config\ConfigFactory::__construct()
     *
     * @return ConfigFactory
     */
    public function testCanCreateInstance()
    {
        $configFactory = new ConfigFactory($this->getWordpress());

        self::assertInstanceOf('\UserAccessManager\Config\ConfigFactory', $configFactory);

        return $configFactory;
    }

    /**
     * @group   unit
     * @depends testCanCreateInstance
     * @covers  \UserAccessManager\Config\ConfigFactory::createConfig()
     *
     * @param ConfigFactory $configFactory
     */
    public function testCreateApacheFileProtection(ConfigFactory $configFactory)
    {
        $fileObject = $configFactory->createConfig('key');
        self::assertInstanceOf('\UserAccessManager\Config\Config', $fileObject);
    }

}

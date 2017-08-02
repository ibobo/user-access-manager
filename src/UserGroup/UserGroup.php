<?php
/**
 * UserGroup.php
 *
 * The UserGroup class file.
 *
 * PHP versions 5
 *
 * @author    Alexander Schneider <alexanderschneider85@gmail.com>
 * @copyright 2008-2017 Alexander Schneider
 * @license   http://www.gnu.org/licenses/gpl-2.0.html  GNU General Public License, version 2
 * @version   SVN: $id$
 * @link      http://wordpress.org/extend/plugins/user-access-manager/
 */
namespace UserAccessManager\UserGroup;

use UserAccessManager\Config\MainConfig;
use UserAccessManager\Database\Database;
use UserAccessManager\ObjectHandler\ObjectHandler;
use UserAccessManager\Util\Util;
use UserAccessManager\Wrapper\Php;
use UserAccessManager\Wrapper\Wordpress;

/**
 * Class UserGroup
 *
 * @package UserAccessManager\UserGroup
 */
class UserGroup extends AbstractUserGroup
{
    const USER_GROUP_TYPE = 'UserGroup';

    /**
     * @var string
     */
    protected $type = self::USER_GROUP_TYPE;

    /**
     * UserGroup constructor.
     *
     * @param Php                          $php
     * @param Wordpress                    $wordpress
     * @param Database                     $database
     * @param MainConfig                   $config
     * @param Util                         $util
     * @param ObjectHandler                $objectHandler
     * @param AssignmentInformationFactory $assignmentInformationFactory
     * @param null                         $id
     */
    public function __construct(
        Php $php,
        Wordpress $wordpress,
        Database $database,
        MainConfig $config,
        Util $util,
        ObjectHandler $objectHandler,
        AssignmentInformationFactory $assignmentInformationFactory,
        $id = null
    ) {
        parent::__construct(
            $php,
            $wordpress,
            $database,
            $config,
            $util,
            $objectHandler,
            $assignmentInformationFactory
        );

        if ($id !== null) {
            $this->load((int)$id);
        }
    }

    /**
     * Loads the user group.
     *
     * @param $id
     *
     * @return bool
     */
    public function load($id)
    {
        $query = $this->database->prepare(
            "SELECT *
            FROM {$this->database->getUserGroupTable()}
            WHERE ID = %d
            LIMIT 1",
            $id
        );

        $dbUserGroup = $this->database->getRow($query);

        if ($dbUserGroup !== null) {
            $this->id = $id;
            $this->name = $dbUserGroup->groupname;
            $this->description = $dbUserGroup->groupdesc;
            $this->readAccess = $dbUserGroup->read_access;
            $this->writeAccess = $dbUserGroup->write_access;
            $this->ipRange = $dbUserGroup->ip_range;

            return true;
        }

        return false;
    }

    /**
     * Saves the user group.
     *
     * @return bool
     */
    public function save()
    {
        if ($this->id === null) {
            $return = $this->database->insert(
                $this->database->getUserGroupTable(),
                [
                    'groupname' => $this->name,
                    'groupdesc' => $this->description,
                    'read_access' => $this->readAccess,
                    'write_access' => $this->writeAccess,
                    'ip_range' => $this->ipRange
                ]
            );

            if ($return !== false) {
                $this->id = $this->database->getLastInsertId();
            }
        } else {
            $return = $this->database->update(
                $this->database->getUserGroupTable(),
                [
                    'groupname' => $this->name,
                    'groupdesc' => $this->description,
                    'read_access' => $this->readAccess,
                    'write_access' => $this->writeAccess,
                    'ip_range' => $this->ipRange
                ],
                ['ID' => $this->id]
            );
        }

        return ($return !== false);
    }

    /**
     * Deletes the user group.
     *
     * @return bool
     */
    public function delete()
    {
        if ($this->id === null) {
            return false;
        }

        $success = $this->database->delete(
            $this->database->getUserGroupTable(),
            ['ID' => $this->id]
        );

        if ($success !== false) {
            $success = parent::delete();
        }

        return $success;
    }
}
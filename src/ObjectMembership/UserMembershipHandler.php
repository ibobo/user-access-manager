<?php
/**
 * UserMembershipHandler.php
 *
 * The UserMembershipHandler class file.
 *
 * PHP versions 5
 *
 * @author    Alexander Schneider <alexanderschneider85@gmail.com>
 * @copyright 2008-2017 Alexander Schneider
 * @license   http://www.gnu.org/licenses/gpl-2.0.html  GNU General Public License, version 2
 * @version   SVN: $id$
 * @link      http://wordpress.org/extend/plugins/user-access-manager/
 */
namespace UserAccessManager\ObjectMembership;

use UserAccessManager\Database\Database;
use UserAccessManager\Object\ObjectHandler;
use UserAccessManager\UserGroup\AbstractUserGroup;
use UserAccessManager\UserGroup\AssignmentInformation;
use UserAccessManager\UserGroup\AssignmentInformationFactory;
use UserAccessManager\Wrapper\Php;

/**
 * Class UserMembershipHandler
 *
 * @package UserAccessManager\UserGroup
 */
class UserMembershipHandler extends ObjectMembershipHandler
{
    /**
     * @var string
     */
    protected $generalObjectType = ObjectHandler::GENERAL_USER_OBJECT_TYPE;

    /**
     * @var Php
     */
    private $php;

    /**
     * @var ObjectHandler
     */
    private $objectHandler;

    /**
     * @var Database
     */
    private $database;

    /**
     * UserMembershipHandler constructor.
     *
     *
     * @param AssignmentInformationFactory $assignmentInformationFactory
     * @param Php                          $php
     * @param Database                     $database
     * @param ObjectHandler                $objectHandler
     */
    public function __construct(
        AssignmentInformationFactory $assignmentInformationFactory,
        Php $php,
        Database $database,
        ObjectHandler $objectHandler
    ) {
        parent::__construct($assignmentInformationFactory);

        $this->php = $php;
        $this->database = $database;
        $this->objectHandler = $objectHandler;
    }

    /**
     * Returns the object and type name.
     *
     * @param string $objectId
     * @param string $typeName
     *
     * @return string
     */
    public function getObjectName($objectId, &$typeName = '')
    {
        $typeName = $this->generalObjectType;
        $user = $this->objectHandler->getUser($objectId);
        return ($user !== false) ? $user->display_name : $objectId;
    }

    /**
     * Checks if the user is a member of the user group.
     *
     * @param AbstractUserGroup          $userGroup
     * @param bool                       $lockRecursive
     * @param string                     $objectId
     * @param null|AssignmentInformation $assignmentInformation
     *
     * @return bool
     */
    public function isMember(AbstractUserGroup $userGroup, $lockRecursive, $objectId, &$assignmentInformation = null)
    {
        $assignmentInformation = null;
        $recursiveMembership = [];
        $user = $this->objectHandler->getUser($objectId);

        if ($user !== false) {
            $capabilitiesTable = $this->database->getCapabilitiesTable();
            $capabilities = (isset($user->{$capabilitiesTable}) === true) ? $user->{$capabilitiesTable} : [];

            if (is_array($capabilities) === true && count($capabilities) > 0) {
                $assignedRoles = $userGroup->getAssignedObjects(ObjectHandler::GENERAL_ROLE_OBJECT_TYPE);

                $recursiveRoles = array_intersect(
                    array_keys($capabilities),
                    array_keys($assignedRoles)
                );

                if (count($recursiveRoles) > 0) {
                    $recursiveMembership[ObjectHandler::GENERAL_ROLE_OBJECT_TYPE] = array_combine(
                        $recursiveRoles,
                        $this->php->arrayFill(
                            0,
                            count($recursiveRoles),
                            $this->assignmentInformationFactory->createAssignmentInformation(
                                ObjectHandler::GENERAL_ROLE_OBJECT_TYPE
                            )
                        )
                    );
                }
            }
        }

        $isMember = $userGroup->isObjectAssignedToGroup(
            $this->generalObjectType,
            $objectId,
            $assignmentInformation
        );


        return $this->checkAccessWithRecursiveMembership($isMember, $recursiveMembership, $assignmentInformation);
    }

    /**
     * Returns the user role objects.
     *
     * @param AbstractUserGroup $userGroup
     * @param bool              $lockRecursive
     * @param null              $objectType
     *
     * @return array
     */
    public function getFullObjects(AbstractUserGroup $userGroup, $lockRecursive, $objectType = null)
    {
        $users = [];

        $databaseUsers = (array)$this->database->getResults(
            "SELECT ID, user_nicename
                FROM {$this->database->getUsersTable()}"
        );

        foreach ($databaseUsers as $user) {
            if ($userGroup->isObjectMember($this->generalObjectType, $user->ID) === true) {
                $users[$user->ID] = $this->generalObjectType;
            }
        }

        return $users;
    }
}

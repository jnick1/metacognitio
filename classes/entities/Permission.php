<?php

/**
 * Created by PhpStorm.
 * User: Jacob
 * Date: 4/6/2017
 * Time: 9:43 PM
 */
class Permission
{

    const PERMISSION_AUTHOR = 1;
    const PERMISSION_EDITOR = 2;
    const PERMISSION_EDITOR_IN_CHIEF = 3;
    const PERMISSION_EXECUTIVE = 4;
    const PERMISSION_GRAPHIC_DESIGNER = 5;
    const PERMISSION_PRESIDENT = 6;
    const PERMISSION_PUBLIC_RELATIONS_DIRECTOR = 7;
    const PERMISSION_SECRETARY = 8;
    const PERMISSION_TREASURER = 9;
    const PERMISSION_VICE_PRESIDENT = 10;

    private $description;
    private $name;
    private $permissionID;

    /**
     * Permission constructor.
     * @param int $permissionID
     * @throws Exception
     */
    public function __construct(int $permissionID)
    {
        $dbc = new DatabaseConnection();
        $params = ["i", $permissionID];
        $permission = $dbc->query("select", "SELECT * FROM `permission` WHERE `pkPermissionID`=?", $params);
        if ($permission) {
            $result = [
                $this->setPermissionID($permissionID),
                $this->setName($permission["nmName"]),
                $this->setDescription($permission["txDescription"])
            ];
            if (in_array(false, $result, true)) {
                throw new Exception("Permission->__construct($permissionID) -  Unable to construct Permission object; variable assignment failure - (".implode(" ", array_keys($result,false,true)).")");
            }
        } else {
            throw new Exception("Permission->__construct($permissionID) -  Unable to select from database");
        }
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return "{".implode(" ", [$this->getPermissionID(), $this->getName()])."}";
    }

    /**
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getPermissionID(): int
    {
        return $this->permissionID;
    }

    /**
     * @param string $description
     * @return bool
     */
    public function setDescription(string $description): bool
    {
        $dbc = new DatabaseConnection();
        if (strlen($description) <= $dbc->getMaximumLength("permission", "txDescription")) {
            $this->description = $description;
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $name
     * @return bool
     */
    public function setName(string $name): bool
    {
        $dbc = new DatabaseConnection();
        $params = ["s", $name];
        if ($dbc->query("exists", "SELECT * FROM `permission` WHERE `nmName`=?", $params)) {
            $this->name = $name;
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param int $permissionID
     * @return bool
     */
    private function setPermissionID(int $permissionID): bool
    {
        $this->permissionID = $permissionID;
        return true;
    }


}
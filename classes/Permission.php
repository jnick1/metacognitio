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
        $options = [
            "options" => [
                "min_range" => 1,
                "max_range" => 10
            ]
        ];
        if ($filtered = filter_var($permissionID, FILTER_VALIDATE_INT, $options)) {
            $dbc = new DatabaseConnection();
            $params = ["i", $filtered];
            $result = $dbc->query("select", "SELECT * FROM `permission` WHERE `pkPermissionID`=?", $params);
            if ($result) {
                $this->permissionID = $filtered;
                $this->name = $result["nmName"];
                $this->description = $result["txDescription"];
            } else {
                throw new Exception("Permission->__construct -  Unable to select from database");
            }
        } else {
            throw new InvalidArgumentException("Permission->__construct -  Not a recognized permission");
        }
    }

    /**
     *
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getPermissionID()
    {
        return $this->permissionID;
    }

    /**
     * @param mixed $permissionID
     */
    public function setPermissionID($permissionID)
    {
        $this->__construct($permissionID);
    }

}
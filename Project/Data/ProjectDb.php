<?php
/**
 * Created by PhpStorm.
 * User: konst
 * Date: 28.11.2015 г.
 * Time: 23:11
 */

namespace Data;


use EndF\DB\SimpleDB;

class ProjectDb extends SimpleDB
{
    public function __construct($connection = null)
    {
        parent::__construct($connection);
    }

    public function getUserRole($userId)
    {
        $sql = "SELECT ur.roleName FROM users u JOIN usersRoles ur ON u.roleId = ur.roleId WHERE u.userId = ?";
        $this->prepare($sql);
        $this->execute(array($userId));

        $result = $this->fetchRowAssoc();
        return $result['roleName'];
    }

    public function seed()
    {
        parent::seed();

        $this->insertUserRoles();

        $this->createVenuesTable();

        $this->createConferencesTable();

        $this->createHallsTable();

        $this->createLecturesTable();

        $this->createUsersLecturesTable();
    }

    private function insertUserRoles()
    {
        $sql = "SELECT * FROM usersRoles LIMIT 1";
        $this->prepare($sql);
        $this->execute();
        if($this->affectedRows() <= 0){
            $sql = "INSERT INTO usersRoles (`roleName`, `rolePriority`) VALUES ('User', 0), ('Conference Owner', 1), ('Conference Administrator', 2), ('Administrator', 3)";
            $this->execSql($sql);
        }
    }

    private function createVenuesTable()
    {
        if(!$this->tableExists('venues')){
            $sql = "CREATE TABLE  venues(id INT PRIMARY KEY AUTO_INCREMENT, `name` VARCHAR(100) NOT NULL, `location` VARCHAR(200) NOT NULL )";
            $this->execSql($sql);
        }
    }

    private function createConferencesTable()
    {
        if(!$this->tableExists('conferences')) {
            $sql = "CREATE TABLE conferences(id INT PRIMARY KEY AUTO_INCREMENT, title VARCHAR(100) NOT NULL, description BLOB, date DATETIME NOT NULL, duration INT NOT NULL, venueId INT, ownerId CHAR(40), FOREIGN KEY(venueId) REFERENCES venues(id), FOREIGN KEY(ownerId) REFERENCES users(userId))";
            $this->execSql($sql);
        }
    }

    private function createHallsTable()
    {
        if(!$this->tableExists('halls')) {
            $sql = "CREATE TABLE halls(id INT PRIMARY KEY AUTO_INCREMENT, `name` NVARCHAR(100), capacity INT NOT NULL, venueId INT NOT NULL, FOREIGN KEY(venueId) REFERENCES venues(id))";
            $this->execSql($sql);
        }
    }

    private function createLecturesTable()
    {
        if(!$this->tableExists('lectures')) {
            $sql = "CREATE TABLE lectures(id INT PRIMARY KEY AUTO_INCREMENT, title NVARCHAR(100) NOT NULL, description BLOB, date DATETIME, duration INT, speakerId CHAR(40), conferenceId INT, hallId INT, FOREIGN KEY(speakerId) REFERENCES users(userId), FOREIGN KEY(conferenceId) REFERENCES conferences(id), FOREIGN KEY(hallId) REFERENCES halls(id))";
            $this->execSql($sql);
        }
    }

    private function createUsersLecturesTable()
    {
        if(!$this->tableExists('usersLectures')) {
            $sql = "CREATE TABLE usersLectures(userId CHAR(40) NOT NULL, lectureId INT NOT NULL, FOREIGN KEY(userId) REFERENCES users(userId), FOREIGN KEY(lectureId) REFERENCES lectures(id))";
            $this->execSql($sql);
        }
    }


}
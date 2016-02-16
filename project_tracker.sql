DROP DATABASE IF EXISTS project_tracker;

CREATE DATABASE project_tracker;
USE project_tracker;

CREATE TABLE employee(
	id INT NOT NULL AUTO_INCREMENT,
	username VARCHAR(255) NOT NULL,
	password VARCHAR(255),
	employeeType ENUM('Manager', 'Employee', 'Admin'),	
	name VARCHAR(255),
	status ENUM('Active', 'Inactive') NOT NULL,
	division ENUM('divA', 'divB', 'divC', 'divD'),
	PRIMARY KEY (id)
);	

CREATE TABLE project(
	id INT NOT NULL AUTO_INCREMENT,
	name VARCHAR(255),
	timeEstimate FLOAT(10,2),
	status ENUM('Active', 'Inactive') NOT NULL,
	PRIMARY KEY (id)
);

CREATE TABLE division(
	id INT NOT NULL AUTO_INCREMENT,
	name ENUM('divA', 'divB', 'divC', 'divD'),
	PRIMARY KEY (id)
);

CREATE TABLE projectDivisionList(
	id INT NOT NULL AUTO_INCREMENT,
	projectID INT,
	divisionName ENUM('divA', 'divB', 'divC', 'divD'),
	PRIMARY KEY (id)
);

CREATE TABLE projectTimeHistory(
	id INT NOT NULL AUTO_INCREMENT,
	employeeID INT,
	projectID INT,
	projectName VARCHAR(255),
	time FLOAT(10,2),
	PRIMARY KEY (id)
);

CREATE TABLE employeeProjectList(
	id INT NOT NULL AUTO_INCREMENT,
	employeeID INT,
	projectID INT,
	projectName VARCHAR(255),
	PRIMARY KEY (id)
);

CREATE TABLE loginSession(
    sessionId VARCHAR(255) NOT NULL,
    authenticatedEmployee INT NOT NULL,
    PRIMARY KEY(sessionID),
    FOREIGN KEY(authenticatedEmployee) REFERENCES employee(id)
);

-- Create the user which the app will use to connect to the DB
DROP PROCEDURE IF EXISTS project_tracker.drop_user_if_exists ;
DELIMITER $$
CREATE PROCEDURE project_tracker.drop_user_if_exists()
BEGIN
  DECLARE foo BIGINT DEFAULT 0 ;
  SELECT COUNT(*)
  INTO foo
    FROM mysql.user
      WHERE User = 'project_tracker' and  Host = 'localhost';
   IF foo > 0 THEN
         DROP USER 'project_tracker'@'localhost' ;
  END IF;
END ;$$
DELIMITER ;
CALL project_tracker.drop_user_if_exists() ;
DROP PROCEDURE IF EXISTS project_tracker.drop_users_if_exists ;

CREATE USER 'project_tracker'@'localhost' IDENTIFIED BY 'project_tracker';
GRANT SELECT, INSERT, UPDATE, DELETE, EXECUTE, LOCK TABLES, CREATE TEMPORARY TABLES ON project_tracker.* TO 'project_tracker'@'localhost';

-- Populate pre-defined admin, manager, and employee users
INSERT INTO employee (
	  username, password, employeeType, name, status, division
	) VALUES
	  ('admin', 'admin', 'Admin', 'Admin', 'Active', null),
	  ('manager', 'manager', 'Manager', 'Manager', 'Active', 'divA'),
	  ('empA', 'empA', 'Employee', 'EmployeeA', 'Active', 'divA')
;

-- Populate pre-defined divisions
INSERT INTO division (
	  id, name
	) VALUES
	  (1, 'divA'),
	  (2, 'divB'),
	  (3, 'divC'),
	  (4, 'divD')
;


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

-- Populate pre-defined admin, manager, and employee users
INSERT INTO employee (
	  username, password, employeeType, name, status, division
	) VALUES
	  ('admin', 'admin', 'Admin', 'Admin', 'Active', null),
	  ('manager', 'manager', 'Manager', 'Manager', 'Active', 'divA'),
	  ('empA', 'empA', 'Employee', 'EmployeeA', 'Active', 'divA')
;

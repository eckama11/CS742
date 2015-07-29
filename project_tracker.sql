DROP DATABASE IF EXISTS project_tracker;

CREATE DATABASE project_tracker;
USE project_tracker;

CREATE TABLE employee(
	id INT NOT NULL AUTO_INCREMENT,
	username VARCHAR(255) NOT NULL,
	password VARCHAR(255),
	employeeType ENUM('Manager', 'Employee'),	
	name VARCHAR(255),
	status ENUM('Active', 'Inactive') NOT NULL,
	division ENUM('divA', 'divB', 'divC', 'divD')
);	

CREATE TABLE project(
	id INT NOT NULL AUTO_INCREMENT,
	name VARCHAR(255),
	timeEstimate FLOAT(10,2)
	status ENUM('Active', 'Inactive') NOT NULL,
);

CREATE TABLE division(
	id INT NOT NULL AUTO_INCREMENT,
	name ENUM('divA', 'divB', 'divC', 'divD')
);

CREATE TABLE projectDivisionList(
	id INT NOT NULL AUTO_INCREMENT,
	projectID INT,
	divisionName ENUM('divA', 'divB', 'divC', 'divD')
);

CREATE TABLE projectTimeHistory(
	id INT NOT NULL AUTO_INCREMENT,
	employeeID INT,
	projectID INT,
	projectName VARCHAR(255),
	time FLOAT(10,2)
);

CREATE TABLE employeeProjectList(
	id INT NOT NULL AUTO_INCREMENT,
	employeeID INT,
	projectID INT,
	projectName VARCHAR(255)
);
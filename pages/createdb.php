<?php
	include_once('classes.php');
	$pdo = Tools ::connect();

	$role = 'CREATE TABLE Roles(
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    role VARCHAR(32) NOT NULL UNIQUE ) DEFAULT CHARSET="utf8"';

	$customer = 'CREATE TABLE Customers(
    id INT NOT NULL AUTO_INCREMENT primary key,
    login varchar(32)not null unique,
	pass varchar(128)not null,
	roleid int,
	foreign key(roleid) references Roles(id) on update cascade,
	discount int,
	total int,
	imagepath varchar(255)) default charset="utf8"';

	$cat = 'create table Categories(
    id int not null auto_increment primary key,
	category varchar(64)not null unique) default charset="utf8"';

	$sub = 'create table SubCategories(
    id int not null auto_increment primary key,
	sucategory varchar(64)not null unique,
	catid int,
	foreign key(catid) references Categories(id) on update cascade) default charset="utf8"';

	$item = 'create table Items(
    id int not null auto_increment primary key,
	itemname varchar(128)not null,
	catid int,
	foreign key(catid) references Categories(id) on update cascade,
	pricein int not null,
	pricesale int not null,
	info varchar(256) not null,
	rate double,
	imagepath varchar(256) not null, action int) default charset="utf8"';

	$images = 'create table Images(
    id int not null auto_increment primary key,
	itemid int,
	foreign key(itemid) references Items(id) on delete cascade,
	imagepath varchar(255)) default charset="utf8"';

	$sale = 'create table Sales(id int not null auto_increment primary key,
	customername varchar(32),
	itemname varchar(128),
	pricein int,
	pricesale int,
	datesale date) default charset="utf8"';

	$pdo->exec($role);
	$pdo->exec($customer);
	$pdo->exec($cat);
	$pdo->exec($sub);
	$pdo->exec($item);
	$pdo->exec($images);
	$pdo->exec($sale);
DROP TABLE IF EXISTS `Awardee`;
DROP TABLE IF EXISTS `People`;
DROP TABLE IF EXISTS `Organization`;
DROP TABLE IF EXISTS `Institution`;
DROP TABLE IF EXISTS `Prize`;
create table Awardee (id int primary key, date date, city varchar(100), country varchar(100));
create table People (id int primary key, givenName varchar(100), familyName varchar(100), gender varchar(10));
create table Organization (id int primary key, orgName varchar(500));
create table Institution (id int, awardYear year, name varchar(500), city varchar(100), country varchar(100), primary key(id, awardYear, name));
create table Prize (id int, awardYear year, category varchar(100), sortOrder varchar(10), primary key(id, awardYear));
load data local infile 'awardee.del' into table Awardee
fields terminated by '|';
load data local infile 'people.del' into table People
fields terminated by '|';
load data local infile 'org.del' into table Organization
fields terminated by '|';
load data local infile 'ins.del' into table Institution
fields terminated by '|';
load data local infile 'prize.del' into table Prize
fields terminated by '|';
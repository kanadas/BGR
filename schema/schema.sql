CREATE TABLE Users ( id NUMBER(6) NOT NULL PRIMARY KEY,
	login NVARCHAR2(100) NOT NULL UNIQUE,
	pass NVARCHAR2(32) NOT NULL);

CREATE TABLE Person (
	id NUMBER(6) NOT NULL PRIMARY KEY,
	name NVARCHAR2(100) NOT NULL UNIQUE);

CREATE TABLE Game (
	id NUMBER(6) NOT NULL PRIMARY KEY,
	name NVARCHAR2(100) NOT NULL UNIQUE,
	year NUMBER(4) NOT NULL,
	description NCLOB NOT NULL,
	bggscore NUMBER(4,2) NOT NULL,
	designerid NUMBER(6) NOT NULL REFERENCES Person,
	minplayers NUMBER(2) NOT NULL,
	maxplayers NUMBER(2) NOT NULL,
	avgplaytime NUMBER(4) NOT NULL,
	complexity NUMBER(3,2) NOT NULL);

CREATE TABLE Rating (
	value NUMBER(2) NOT NULL,
	userid NUMBER(6) NOT NULL REFERENCES Users,
	gameid NUMBER(6) NOT NULL REFERENCES Game,
	PRIMARY KEY (value, userid));	

CREATE TABLE Publisher (
	id NUMBER(6) NOT NULL PRIMARY KEY,
	name NVARCHAR2(100) NOT NULL UNIQUE);

CREATE TABLE GamePublisher (
	gameid NUMBER(6) NOT NULL REFERENCES Game,
	publisherid NUMBER(6) NOT NULL REFERENCES Publisher,
	PRIMARY KEY (gameid, publisherid));

CREATE TABLE GameArtist (
	gameid NUMBER(6) NOT NULL REFERENCES Game,
	artistid NUMBER(6) NOT NULL REFERENCES Person,
	PRIMARY KEY (gameid, artistid));

CREATE TABLE TagType (
	id NUMBER(3) NOT NULL PRIMARY KEY,
	name VARCHAR2(20) NOT NULL UNIQUE,
	weight NUMBER(4,4) NOT NULL);

CREATE TABLE Tag (
	id NUMBER(6) NOT NULL PRIMARY KEY,
	name VARCHAR2(100) NOT NULL,
	tagtype NUMBER(3) NOT NULL REFERENCES TagType,
	CONSTRAINT tag_name_type_unique UNIQUE (name, tagtype));

CREATE TABLE GameTag (
	gameid NUMBER(6) NOT NULL REFERENCES Game,
	tagid NUMBER(6) NOT NULL REFERENCES Tag,
	PRIMARY KEY (gameid, tagid));


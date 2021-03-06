CREATE TABLE Users ( 
	id NUMBER(3) NOT NULL PRIMARY KEY,
	login VARCHAR2(50) NOT NULL UNIQUE,
	pass VARCHAR2(32) NOT NULL);

CREATE TABLE Game (
	id NUMBER(4) NOT NULL PRIMARY KEY,
	name VARCHAR2(100) NOT NULL,
	year NUMBER(4) NOT NULL,
	description CLOB NOT NULL,
	bggscore NUMBER(4,2) NOT NULL,
	minplayers NUMBER(2) NOT NULL,
	maxplayers NUMBER(2) NOT NULL,
	avgplaytime NUMBER(4) NOT NULL,
	complexity NUMBER(3,2) NOT NULL,
	CONSTRAINT game_name_year_unique UNIQUE (name, year));

CREATE TABLE Rating (
	value NUMBER(2) NOT NULL,
	userid NUMBER(3) NOT NULL REFERENCES Users,
	gameid NUMBER(4) NOT NULL REFERENCES Game,
	PRIMARY KEY (gameid, userid));	

CREATE TABLE TagType (
	id NUMBER(2) NOT NULL PRIMARY KEY,
	name VARCHAR2(20) NOT NULL UNIQUE,
	weight NUMBER(4,4) NOT NULL);

CREATE TABLE Tag (
	id NUMBER(6) NOT NULL PRIMARY KEY,
	name VARCHAR2(70) NOT NULL,
	tagtype NUMBER(2) NOT NULL REFERENCES TagType,
	CONSTRAINT tag_name_type_unique UNIQUE (name, tagtype));

CREATE TABLE GameTag (
	gameid NUMBER(4) NOT NULL REFERENCES Game,
	tagid NUMBER(6) NOT NULL REFERENCES Tag,
	PRIMARY KEY (gameid, tagid));

CREATE OR REPLACE VIEW UserTagRating AS
	SELECT Rating.userid as userid, Tag.id as tagid, SUM(Rating.value) / COUNT(*) as avgRating FROM Rating, Game, GameTag, Tag WHERE
		Rating.gameid = Game.id AND
		Game.id = GameTag.gameid AND
		Tag.id = GameTag.tagid
		GROUP BY Rating.userid, Tag.id;

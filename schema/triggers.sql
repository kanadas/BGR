CREATE SEQUENCE UsersSeq START WITH 1 INCREMENT BY 1;
CREATE TRIGGER users_in_trigger
BEFORE INSERT ON Users
FOR EACH ROW
BEGIN
	SELECT UsersSeq.nextval INTO :NEW.id FROM dual;
END;
/

CREATE SEQUENCE GameSeq START WITH 1 INCREMENT BY 1;
CREATE TRIGGER game_in_trigger
BEFORE INSERT ON Game
FOR EACH ROW
BEGIN
	SELECT GameSeq.nextval INTO :NEW.id FROM dual;
END;
/

CREATE SEQUENCE PersonSeq START WITH 1 INCREMENT BY 1;
CREATE TRIGGER person_in_trigger
BEFORE INSERT ON Person
FOR EACH ROW
BEGIN
	SELECT PersonSeq.nextval INTO :NEW.id FROM dual;
END;
/

CREATE SEQUENCE PublisherSeq START WITH 1 INCREMENT BY 1;
CREATE TRIGGER publisher_in_trigger
BEFORE INSERT ON Publisher
FOR EACH ROW
BEGIN
	SELECT PublisherSeq.nextval INTO :NEW.id FROM dual;
END;
/

CREATE SEQUENCE TagSeq START WITH 1 INCREMENT BY 1;
CREATE TRIGGER tag_in_trigger
BEFORE INSERT ON Tag
FOR EACH ROW
BEGIN
	SELECT TagSeq.nextval INTO :NEW.id FROM dual;
END;
/

CREATE SEQUENCE TagTypeSeq START WITH 1 INCREMENT BY 1;
CREATE TRIGGER tagtype_in_trigger
BEFORE INSERT ON TagType
FOR EACH ROW
BEGIN
	SELECT TagTypeSeq.nextval INTO :NEW.id FROM dual;
END;
/

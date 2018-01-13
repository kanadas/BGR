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

CREATE SEQUENCE TagSeq START WITH 1 INCREMENT BY 1;
CREATE TRIGGER tag_in_trigger
BEFORE INSERT ON Tag
FOR EACH ROW
BEGIN
	SELECT TagSeq.nextval INTO :NEW.id FROM dual;
END;
/

CREATE PROCEDURE INS(p_name TagType.name%TYPE, p_weight TagType.weight%TYPE) IS 
BEGIN
	INSERT INTO TagType (name, weight) VALUES (p_name, p_weight);
END INS;
/ 
EXECUTE INS('Type', '0.25');
EXECUTE INS('Category', '0.25');
EXECUTE INS('Mechanisms', '0.25');
EXECUTE INS('Family', '0.25');
DROP PROCEDURE INS;

CREATE PROCEDURE INS(p_id TagType.id%TYPE, p_name TagType.name%TYPE, p_weight TagType.weight%TYPE) IS 
BEGIN
	INSERT INTO TagType (id, name, weight) VALUES (p_id, p_name, p_weight);
END INS;
/ 
EXECUTE INS(1, 'Type', '0.25');
EXECUTE INS(2, 'Category', '0.25');
EXECUTE INS(3, 'Mechanisms', '0.25');
EXECUTE INS(4, 'Family', '0.25');
DROP PROCEDURE INS;

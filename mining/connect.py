import cx_Oracle
con = cx_Oracle.connect('tk385674/salamandra@labora.mimuw.edu.pl:1521/LABS')
print(con.version)
con.close()

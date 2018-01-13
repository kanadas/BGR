import requests
from bs4 import BeautifulSoup
import re
import cx_Oracle
import os
from unidecode import unidecode

os.environ["NLS_LANG"] = "AMERICAN_AMERICA.EE8ISO8859P2"
#os.environ["NLS_LANG"] = ".AL32UTF8"

TAGID = {"Type" : 1, "Category" : 2, "Mechanism" : 3, "Family" : 4, "Publisher" : 5, "Designer" : 6}

def getstring(x): return x.string.replace("'", "''")

def updatetable(cur, namelist, gameid, tagtype):
    #print("SELECT name, id FROM " + tablename + " WHERE name IN (" + ','.join("'" + name + "'" for name in namelist) + ") AND tagtype = " + tagtype")

    cur.execute(unidecode("SELECT name, id FROM Tag WHERE name IN (" + ','.join("'" + name + "'" for name in namelist) + ") AND tagtype = " + tagtype))
    rows = dict(list(map(lambda p: (p[0].replace("'", "''"), p[1]), cur.fetchall())))
    for name in namelist:
        if rows.get(unidecode(name)) : cur.execute('INSERT INTO GameTag (gameid, tagid) VALUES (:1, :2)', (gameid, rows[unidecode(name)]))
        else :
            cur.execute(unidecode("INSERT INTO Tag (name, tagtype) VALUES ('" + name + "', '" + tagtype + "')"))
            cur.execute('SELECT TagSeq.currval FROM dual')
            tid = cur.fetchone()[0]
            cur.execute('INSERT INTO GameTag (gameid, tagid) VALUES (:1, :2)', (gameid, tid))


con = cx_Oracle.connect('tk385674/salamandra@labora.mimuw.edu.pl:1521/LABS')
gamecur = con.cursor()
gamecur.prepare("""INSERT INTO Game (name, year, description, bggscore, minplayers, maxplayers, avgplaytime, complexity) VALUES
                (:1, :2, :3, :4, :5, :6, :7, :8, :9)""")
cur = con.cursor()


urlpref = "https://boardgamegeek.com/browse/boardgame/page/"
pagenum = 1
ids = [];
regex = re.compile('/boardgame/(\d+).*')
while pagenum <= 20: 
    link ='https://www.boardgamegeek.com/xmlapi/boardgame/'
    page = requests.get(urlpref + str(pagenum))
    soup = BeautifulSoup(page.content, 'html.parser')
    tags = soup.find('table', id='collectionitems').find_all('td', class_='collection_objectname')
    ids = [re.search(regex, tag.a['href']).group(1) for tag in tags]
    for i in ids: link += i + ','
    xml = requests.get(link + '?stats=1')
    if xml.status_code != 200:
        continue
    soup = BeautifulSoup(xml.content, 'xml')
    games = soup.find_all('boardgame')
    for game in games:
        name = game.find('name', primary='true')
        if not name: continue
        name = name.string.replace("'", "''")
        minplayers = game.find('minplayers').string
        maxplayers = game.find('maxplayers').string
        playingtime = game.find('playingtime').string
        if not playingtime: playingtime = int(game.find('minplayingtime')) + int(game.find('maxplayingtime')) / 2
        description = game.find('description').string.replace("'", "''")
        year = game.find('yearpublished').string
        score = game.find('statistics').ratings.average.string
        complexity = game.find('averageweight').string
        types = list(map(getstring, game.find_all('boardgamesubdomain')))
        publishers = list(map(getstring, game.find_all('boardgamepublisher')))
        categories = list(map(getstring, game.find_all('boardgamecategory')))
        mechanisms = list(map(getstring, game.find_all('boardgamemechanic')))
        families = list(map(getstring, game.find_all('boardgamefamily')))
        designer = list(map(getstring, game.find('boardgamedesigner')))
        gamecur.execute(None, (unidecode(name), year, unidecode(description), score, minplayers, maxplayers, playingtime, complexity)) 
        cur.execute('SELECT GameSeq.currval FROM dual')
        gameid = cur.fetchone()[0]
        if types: updatetable(cur, types, gameid, TAGID['Type'])
        if categories: updatetable(cur, categories, gameid, TAGID['Category'])
        if mechanisms: updatetable(cur, mechanisms, gameid, TAGID['Mechanism'])
        if families: updatetable(cur, families, gameid, TAGID['Family'])
        if publishers: updatetable(cur, publishers, gameid, TAGID['Publisher'])
        if designers: updatetable(cur, designers, gameid, TAGID['Designer'])
    print(pagenum)
    pagenum += 1
    #con.commit();

con.commit()
gamecur.close()
cur.close()
con.close()
print("FINISHED")

import requests
from bs4 import BeautifulSoup
import re
import cx_Oracle
import os
from unidecode import unidecode

#os.environ["NLS_LANG"] = "AMERICAN_AMERICA.EE8ISO8859P2"
os.environ["NLS_LANG"] = ".AL32UTF8"

TAGID = {"Type" : 1, "Category" : 2, "Mechanism" : 3, "Family" : 4}

def getstring(x): return x.string.replace("'", "''")

def updatetable(cur, namelist, gameid, tablename, outtable, seqname, tagtype = None):
    andtype = ""
    typ = ""
    typnum = ""
    if tagtype: 
        andtype = " AND tagtype = " + str(tagtype)
        typ = ", type"
        typnum = ", " + str(tagtype)

    print("SELECT name, id FROM " + tablename + " WHERE name IN (" + ','.join("'" + name + "'" for name in namelist) + ")" + andtype)

    cur.execute("SELECT name, id FROM " + tablename + " WHERE name IN (" + ','.join("'" + name + "'" for name in namelist) + ")" + andtype)
    rows = dict(list(map(lambda p: (p[0].replace("'", "''"), p[1]), cur.fetchall())))
    for name in namelist:
        if rows.get(name) : cur.execute('INSERT INTO ' + outtable + ' VALUES (:1, :2)', (gameid, rows[name]))
        else :
            cur.execute('INSERT INTO ' + tablename + " (name" + typ + ") VALUES ('" + name + "'" + typnum + ")")
            cur.execute('SELECT ' + seqname + '.currval FROM dual')
            tid = cur.fetchone()[0]
            cur.execute('INSERT INTO ' + outtable + ' VALUES (:1, :2)', (gameid, tid))
        


con = cx_Oracle.connect('tk385674/salamandra@labora.mimuw.edu.pl:1521/LABS')
gamecur = con.cursor()
gamecur.prepare("""INSERT INTO Game (name, year, description, bggscore, minplayers, maxplayers, avgplaytime, complexity, designerid) VALUES
                (:1, :2, :3, :4, :5, :6, :7, :8, :9)""")
cur = con.cursor()


urlpref = "https://boardgamegeek.com/browse/boardgame/page/"
pagenum = 1
ids = [];
regex = re.compile('/boardgame/(\d+).*')
while pagenum <= 4: #148:
    link ='https://www.boardgamegeek.com/xmlapi/boardgame/'
    page = requests.get(urlpref + str(pagenum))
    soup = BeautifulSoup(page.content, 'html.parser')
    tags = soup.find('table', id='collectionitems').find_all('td', class_='collection_objectname')
    ids += [re.search(regex, tag.a['href']).group(1) for tag in tags]
    for i in ids: link += i + ','
    xml = requests.get(link + '?stats=1')
    if xml.status_code != 200:
        print("Wrong status code (" + str(xml.status_code) + ") for page: " + str(pagenum))
        continue
    soup = BeautifulSoup(xml.content, 'xml')
    games = soup.find_all('boardgame')
    for game in games:
        name = game.find('name', primary='true')
        if not name: continue
        name = name.string.replace("'", "''")

        #print(name)

        minplayers = game.find('minplayers').string
        maxplayers = game.find('maxplayers').string
        playingtime = game.find('playingtime').string
        if not playingtime: playingtime = int(game.find('minplayingtime')) + int(game.find('maxplayingtime')) / 2
        description = game.find('description').string.replace("'", "''")
        year = game.find('yearpublished').string
        score = game.find('statistics').ratings.average.string
        designer = game.find('boardgamedesigner').string.replace("'", "''")
        complexity = game.find('averageweight').string
        types = list(map(getstring, game.find_all('boardgamesubdomain')))
        publishers = list(map(getstring, game.find_all('boardgamepublisher')))
        artists = list(map(getstring, game.find_all('boardgameartist')))
        categories = list(map(getstring, game.find_all('boardgamecategory')))
        mechanisms = list(map(getstring, game.find_all('boardgamemechanic')))
        families = list(map(getstring, game.find_all('boardgamefamily')))
        cur.execute("SELECT id FROM Person WHERE name = '" + designer + "'")
        desid = cur.fetchone()
        if not desid:
            cur.execute("INSERT INTO Person (name) VALUES ('" + designer + "')")
            cur.execute('SELECT PersonSeq.currval FROM dual')
            desid = cur.fetchone()[0]
        else: desid = desid[0]
        gamecur.execute(None, (name, year, description, score, minplayers, maxplayers, playingtime, complexity, desid)) 
        cur.execute('SELECT GameSeq.currval FROM dual')
        gameid = cur.fetchone()[0]
        if types: updatetable(cur, types, gameid, 'Tag', 'GameTag (gameid, tagid)', 'TagSeq', TAGID['Type'])
        if publishers: updatetable(cur, publishers, gameid, 'Publisher', 'GamePublisher (gameid, publisherid)', 'PublisherSeq')
        if artists: updatetable(cur, artists, gameid, 'Person', 'GameArtist (gameid, artistid)', 'PersonSeq')
        if categories: updatetable(cur, categories, gameid, 'Tag', 'GameTag (gameid, tagid)', 'TagSeq', TAGID['Category'])
        if mechanisms: updatetable(cur, mechanisms, gameid, 'Tag', 'GameTag (gameid, tagid)', 'TagSeq', TAGID['Mechanism'])
        if families: updatetable(cur, families, gameid, 'Tag', 'GameTag (gameid, tagid)', 'TagSeq', TAGID['Family'])
    pagenum += 1

con.commit()
gamecur.close()
cur.close()
con.close()
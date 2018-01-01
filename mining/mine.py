import requests
from bs4 import BeautifulSoup
import re
import cx_Oracle

def getstring(x): return x.string

def updatetable(cur, namelist, gameid, tablename, outtable, seqname):
    cur.execute("SELECT name, id FROM " + tablename + " WHERE name IN (%s)" % ','.join("'%s'" % name for name in namelist)) 
    rows = dict(cur.fetchall())
    for name in namelist:
        if rows.get(name) : cur.execute('INSERT INTO ' + outtable + ' VALUES (:1, :2)', (gameid, rows[name]))
        else :
            cur.execute('INSERT INTO ' + tablename + " (name) VALUES ('" + name + "')")
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
while pagenum <= 1: #148:
    link ='https://www.boardgamegeek.com/xmlapi/boardgame/'
    page = requests.get(urlpref + str(pagenum))
    soup = BeautifulSoup(page.content, 'html.parser')
    tags = soup.find('table', id='collectionitems').find_all('td', class_='collection_objectname')
    ids += [re.search(regex, tag.a['href']).group(1) for tag in tags]
    for i in ids: link += i + ','
    xml = requests.get(link + '?stats=1')
    if xml.status_code != 200:
        print("Wrong status code (" + str(xml.status_code) + ") for page: " + str(pagenum))
    soup = BeautifulSoup(xml.content, 'xml')
    games = soup.find_all('boardgame')
    for game in games[0:1]:
        name = game.find('name', primary='true')
        if not name: continue
        name = name.string
        minplayers = game.find('minplayers').string
        maxplayers = game.find('maxplayers').string
        playingtime = game.find('playingtime').string
        if not playingtime: playingtime = int(game.find('minplayingtime')) + int(game.find('maxplayingtime')) / 2
        description = game.find('description').string
        year = game.find('yearpublished').string
        score = game.find('statistics').ratings.average.string
        designer = game.find('boardgamedesigner').string
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
        updatetable(cur, types, gameid, 'Types', 'GameType (gameid, typeid)', 'TypesSeq')
        updatetable(cur, publishers, gameid, 'Publisher', 'GamePublisher (gameid, publisherid)', 'PublisherSeq')
        updatetable(cur, artists, gameid, 'Person', 'GameArtist (gameid, artistid)', 'PersonSeq')
        updatetable(cur, categories, gameid, 'Category', 'GameCategory (gameid, categoryid)', 'CategorySeq')
        updatetable(cur, mechanisms, gameid, 'Mechanism', 'GameMechanism (gameid, mechanismid)', 'MechanismSeq')
        updatetable(cur, families, gameid, 'Family', 'GameFamily (gameid, familyid)', 'FamilySeq')
    pagenum += 1

con.commit()
gamecur.close()
cur.close()
con.close()

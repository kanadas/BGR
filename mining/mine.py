import requests
from bs4 import BeautifulSoup
import re
import cx_Oracle

def getstring(x): return x.string

def updatetable(cur, namelist, gameid, tablename, outtable, seqname):
    cur.execute("SELECT name, id FROM " + tablename + " WHERE name IN (%s)" % ','.join('"%s"' % name for name in tablename)) 
    rows = dict(cur.fetchall())
    for name in tablename:
        if rows[name] : cur.execute('INSERT INTO ' + outgametable + ' VALUE (:1, :2)', gameid, rows[name])
        else :
            cur.execute('INSERT INTO ' + tablename + ' (name) VALUE (":1")', name)
            cur.execute('SELECT ' + seqname + '.currval FROM dual')
            tid = cur.fetch()[0]
            cur.execute('INSERT INTO ' + outgametable + ' VALUE(:1, :2)', gameid, tid)
        


con = cx_Oracle.connect('tk385674/tomkan81@labora.mimuw.edu.pl:1521/LABS')
gamecur = con.cursor()
gamecur.prepare("""INSERT INTO Game (name, year, description, bggscore, minplayers, maxplayers, avgplaytime, complexity) VALUES
                (:1, :2, :3, :4, :5, :6, :7, :8)""")
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
        gamecur.execute(None, name, year, description, bggscore, minplayers, maxplayers, playingtime, complexity) 
        cur.execute('SELECT GameSeq.currval FROM dual')
        gameid = cur.fetch()[0]
        updatetable(cur, types, gameid, 'Types', 'GameType (gameid, typeid)', 'TypesSeq')
        updatetable(cur, publishers, gameid, 'Publisher', 'GamePublisher (gameid, publisherid)', 'PublisherSeq')
        updatetable(cur, artists, gameid, 'Artist', 'GameArtist (gameid, artistid)', 'ArtistSeq')
        updatetable(cur, categories, gameid, 'Category', 'GameCategory (gameid, categoryid)', 'CategorySeq')
        updatetable(cur, mechanisms, gameid, 'Mechanism', 'GameMechanism (gameid, mechanismid)', 'MechanismSeq')
        updatetable(cur, families, gameid, 'Family', 'GameFamily (gameid, familyid)', 'FamilySeq')
        
        print("Name: " + name)
        print("Players: " + minplayers + '-' + maxplayers)
        print("Playingtime: " + playingtime)
        print("Description: " + description)
        print("Year: " + year) 
        print("Score: " + score)
        print("Designer: " + designer)
        print("Complexity: " + complexity)
        print("Types: ")
        print(types)
        print("Publishers: ")
        print(publishers)
        print("Artists: ")
        print(artists)
        print("Categories: ")
        print(categories)
        print("Mechanisms: ")
        print(mechanisms)
        print("Families: ")
        print(families) 
    pagenum += 1


con.close()

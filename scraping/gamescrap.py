import requests
from bs4 import BeautifulSoup
import re

def getstring(x): return x.string

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
        types = list(map(getstring, game.find_all('boardgamesubdomain')))
        publishers = list(map(getstring, game.find_all('boardgamepublisher')))
        artists = list(map(getstring, game.find_all('boardgameartist')))
        categories = list(map(getstring, game.find_all('boardgamecategory')))
        mechanisms = list(map(getstring, game.find_all('boardgamemechanic')))
        families = list(map(getstring, game.find_all('boardgamefamily')))

        print("Name: " + name)
        print("Players: " + minplayers + '-' + maxplayers)
        print("Playingtime: " + playingtime)
        print("Description: " + description)
        print("Year: " + year)
        print("Score: " + score)
        print("Designer: " + designer)
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


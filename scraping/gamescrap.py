from bs4 import BeautifulSoup
import re

def getids():
    urlpref = "https://boardgamegeek.com/browse/boardgame/page/"
    pagenum = 1
    ids = [];
    regex = re.compile('https://boardgamegeek.com/boardgame/(\d+).*')
    while pagenum <= 148:
        page = requests.get(urlpref + str(pagenum))
        soup = BeautifulSoup(page.content, 'html.parser')
        tags = soup.find('table', id='collectionitems').find_all('td', class_='collection_objectname')
        links += [tag.a['href'] for tag in tags]
        pagenum += 1
    return links

links = getlinks()

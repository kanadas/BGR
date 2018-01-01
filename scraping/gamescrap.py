import requests
from bs4 import BeautifulSoup

def getlinks():
    urlpref = "https://boardgamegeek.com/browse/boardgame/page/"
    pagenum = 1
    links = [];
    while pagenum <= 148:
        page = requests.get(urlpref + str(pagenum))
        soup = BeautifulSoup(page.content, 'html.parser')
        tags = soup.find('table', id='collectionitems').find_all('td', class_='collection_objectname')
        links += [tag.a['href'] for tag in tags]
        pagenum += 1
    return links

def scrapgame(url):
    page = requests.get(url);
    soup = BeautifulSoup(page.content, 'html.parser')

    print(soup.find('div', id='mainbody').prettify())

#links = getlinks()

scrapgame('https://boardgamegeek.com/boardgame/182028/through-ages-new-story-civilization')


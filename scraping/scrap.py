import requests
from bs4 import BeautifulSoup

def scraptable(url):
    page = requests.get(url);
    soup = BeautifulSoup(page.content, 'html.parser');
    tags = soup.find_all('table', class_='forum_table')[0].find_all('a')
    return [a.string for a in tags]

mechanics = scraptable("https://boardgamegeek.com/browse/boardgamemechanic");
designers = scraptable("https://boardgamegeek.com/browse/boardgamedesigner");
publishers = scraptable("https://boardgamegeek.com/browse/boardgamepublisher");
artists = scraptable("https://boardgamegeek.com/browse/boardgameartist");
categories = scraptable("https://boardgamegeek.com/browse/boardgamecategory");

print(mechanics);
print(designers);
print(publishers);
print(artists);
print(categories);

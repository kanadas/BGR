import requests
from bs4 import BeautifulSoup

def scraptable(url):
    num = 1;
    res = [];
    while True:
        page = requests.get(url + "/page/" + str(num));
        soup = BeautifulSoup(page.content, 'html.parser');
        tags = soup.find_all('table', class_='forum_table')[0].find_all('a')
        if not tags: 
            return res
        res += list(filter(lambda s: s != None, [a.string for a in tags]))
        num += 1;

mechanics = scraptable("https://boardgamegeek.com/browse/boardgamemechanic");
#designers = scraptable("https://boardgamegeek.com/browse/boardgamedesigner");
#publishers = scraptable("https://boardgamegeek.com/browse/boardgamepublisher");
#artists = scraptable("https://boardgamegeek.com/browse/boardgameartist");
#categories = scraptable("https://boardgamegeek.com/browse/boardgamecategory");
#families = scraptable("https://boardgamegeek.com/browse/boardgamefamily");

print(mechanics);

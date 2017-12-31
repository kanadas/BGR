import requests
page = requests.get("https://boardgamegeek.com/browse/boardgamemechanic");

from bs4 import BeautifulSoup
soup = BeautifulSoup(page.content, 'html.parser');

mechs = soup.find_all('table', class_='forum_table')[0].find_all('a')

mechanic = [a.string for a in mechs]

print(mechanic)

import requests
import json

url = 'https://gogoanime.consumet.org/recent-release'
response = requests.get(url)
data = json.loads(response.text)

for i, item in enumerate(data):
    with open(str(i+1) + ".html", "w", encoding="utf-8") as file:
        file.write(f"Title: {item['animeTitle']}\nEpisode: {item['episodeNum']}\n")
    print(f"Anime {i+1} saved to {i+1}.txt")

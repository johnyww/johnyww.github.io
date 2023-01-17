import requests
import json

url = 'https://gogoanime.consumet.org/recent-release'
response = requests.get(url)
data = json.loads(response.text)

for i, item in enumerate(data):
    with open(str(i+1) + ".html", "w") as file:
        file.write(item['animeImg'])
    print(f"Image url {i+1} saved to {i+1}.txt")

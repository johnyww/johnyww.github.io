import requests
import json

url = "https://gogoanime.consumet.org/popular"
response = requests.get(url)
data = json.loads(response.text)

# Create an output file and write the titles and links to it
with open("anime-image_list.txt", "w") as f:
    for anime in data:
        image = anime['animeImg']
        f.write(f"{image}\n")

print("Anime list saved to anime-image_list.txt")

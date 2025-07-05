import requests
import json

url = "https://gogoanime.consumet.org/popular"
response = requests.get(url)
data = json.loads(response.text)

# Create an output file and write the titles and links to it
with open("anime_list.txt", "w") as f:
    for anime in data:
        title = anime['animeTitle']
        released = anime['releasedDate']
        link = anime['animeUrl']
        f.write(f"{title},{released},{link},")

print("Anime list saved to anime_list.txt")

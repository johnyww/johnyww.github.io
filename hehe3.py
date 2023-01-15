import requests
import json

url = "https://gogoanime.consumet.org/popular"
response = requests.get(url)
data = json.loads(response.text)

# Create an output file and write the image URLs to it
with open("anime_images.txt", "w") as f:
    for anime in data:
        image_url = anime['animeImg']
        f.write(f"{image_url}\n")

print("Anime image URLs saved to anime_images.txt")

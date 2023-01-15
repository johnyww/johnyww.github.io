import requests
import json

url = "https://gogoanime.consumet.org/popular"
response = requests.get(url)
data = json.loads(response.text)

count = 1
# Iterate over the anime data and create a html file for each image URL
for anime in data:
    image_url = anime['animeImg']
    # Create a filename with the current count value
    filename = str(count) + ".html"
    # Open a new file with the same name and write the image data to it
    with open(filename, "w") as f:
        f.write(f'{image_url}')
    print(f"{filename} saved.")
    count += 1

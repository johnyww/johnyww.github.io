import requests
import json

url = "https://gogoanime.consumet.org/popular"
response = requests.get(url)
data = json.loads(response.text)

count = 1
# Iterate over the anime data and create a html file for each title, url and release date
for anime in data:
    title = anime['animeTitle']
    url = anime['animeUrl']
    release_date = anime['releasedDate']
    # Create a filename with the current count value
    filename = str(count) + ".html"
    # Open a new file with the same name and write the title, url and release date to it
    with open(filename, "w") as f:
        f.write(f'Title : {title}\n')
        f.write(f'\n')
        f.write(f'Released Date : {release_date}\n')
        f.write(f'\n')
    print(f"{filename} saved.")
    count += 1

import requests

r = requests.get("https://www.fanfiction.net/s/10677106")
print(r.text)
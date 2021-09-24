"""import cloudscraper


scraper = cloudscraper.create_scraper()  


# returns a CloudScraper instance
# Or: scraper = cloudscraper.CloudScraper()  # CloudScraper inherits from requests.Session



print(scraper.get("https://m.fanfiction.net/s/12169338/1/Daughter-of-Wisdom").text)
"""


import cloudscraper as cfscrape
from random import seed
from random import choice
import urllib.request, json, signal, sys, pickle, os
#pip install pysocks signal cloudscraper pickle
import base64

def handler(signum, frame):
	raise Exception("end of time")

try:
	arg = sys.argv[1]
	if arg == "test":
		url="https://bot.whatismyipaddress.com/"
	else:
		message = sys.argv[1]
		message_bytes = message.encode('ascii')
		base64_bytes = base64.b64decode(message_bytes)
		url = base64_bytes.decode('ascii')

except:
	print("null")
	exit(0)

wontwork = []
currentkey = 0
"""
auth = choice(authedproxies['auths'])
for l in authedproxies['proxies']:
	proxies.append("http://"+auth['user']+':'+auth['pass']+"@"+l)
"""

#init cache
cfcache = {}

def cfremove(key):
	cfcache.pop(key, None)

def cfadd(key, cache):
	cfcache[key] = cache
	cfcacheuse[key] = 1

cfcachefile = "cf.cache"
if (os.path.isfile(cfcachefile)):
	with open(cfcachefile, 'rb') as f:
		#print("got from cache")
		cfcache = pickle.load(f)
else:
	#print("first init")
	cfcache={"cookies":"dummy"}
	

#print("1. LAST:",lastproxy)



def scrape():
	global currentkey
	#cookies = cfcache[currentkey]
	#print("cookie exist")
	scraper = cfscrape.create_scraper()
	#eturn scraper.get(url,cookies=cookies).content
	return scraper.get(url).content


for i in range(1):
	working = False
	status = "0"
	while status != "200":
		try:
			signal.signal(signal.SIGALRM, handler)
			signal.alarm(5)
			page = scrape()
			print("Page:",str(page.decode("utf-8")))
			status="200"
		except Exception as e:
			exc_type, exc_obj, exc_tb = sys.exc_info()
			fname = os.path.split(exc_tb.tb_frame.f_code.co_filename)[1]
			print(exc_type, e, exc_tb.tb_lineno)
			print("Error:",e)
			status="100"
		if not working:
			pass
		


#print("3. LAST:",lastproxy)

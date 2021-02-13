import cloudscraper as cfscrape
from random import seed
from random import choice
import urllib.request, json, signal, sys, pickle, os
#pip install pysocks signal cloudscraper pickle

def handler(signum, frame):
	raise Exception("end of time")

try:
	arg = sys.argv[1]
	if arg == "test":
		url="https://bot.whatismyipaddress.com/"
	else:
		url=sys.argv[1]
except:
	print("null")
	exit(0)

wontwork = []


proxies = [
	"socks4://172.107.1.164:1080",
	"socks5://47.242.70.166:9999",
	"https://159.8.114.34:25",
	"https://169.57.1.84:80",
	"https://174.138.28.230:8080",
	"https://206.189.154.217:8080",
	"https://103.115.14.41:80",
	"https://103.115.14.153:80",
	"https://159.8.114.34:8123",
	"https://169.57.1.84:8123",
	"https://169.57.1.84:80",
	"https://159.8.114.34:25",
	"https://47.242.70.166:9999",
	"https://91.245.227.61:3128",

	]


#init cache
cfcache = {}
def cfcheck(key): 
	if key in cfcache.keys(): 
		return True
	else: 
		return False 

def cfuse(key):
	if key in cfcacheuse.keys(): 
		cfcacheuse[key] += 1

def cfovercheck(key):
	if key in cfcacheuse.keys(): 
		if cfcacheuse[key] > 4:
			return True
		else:
			return False

def cfremove(key):
	cfcache.pop(key, None)

def cfadd(key, cache):
	cfcache[key] = cache
	cfcacheuse[key] = 1

cfcachefile = "cf.cache"
cfcacheusefile = "cfuse.cache"
if (os.path.isfile(cfcachefile)):
	with open(cfcachefile, 'rb') as f:
		#print("got from cache")
		cfcache = pickle.load(f)
	with open(cfcacheusefile, 'rb') as f:
		#print("got from cache")
		cfcacheuse = pickle.load(f)
else:
	#print("first init")
	cfcache={}
	cfcacheuse={}





def scrape(proxyDict,usedproxy):
	print(usedproxy)
	signal.signal(signal.SIGALRM, handler)
	signal.alarm(5)
	if cfcheck(usedproxy):
		cfuse(usedproxy)
	else:
		scraper = cfscrape.create_scraper(delay=12)  # returns a CloudflareScraper instance
		toreturn = scraper.get(url,proxies=proxyDict).content
		cfadd(usedproxy, scraper)
		#get cookie
		return toreturn

	if cfovercheck(usedproxy):
		cfremove(usedproxy)

	scraper = cfcache[usedproxy]
	return scraper.get(url, proxies=proxyDict).content


for i in proxies:
	signal.signal(signal.SIGALRM, handler)
	signal.alarm(5)
	working = False
	try:
		prox = i
		proxyDict = {"https" : prox}
		page = scrape(proxyDict,prox)
		print(str(page.decode("utf-8")))
		working=True
	except Exception as e:
		#print(e)
		working=False
	if not working:
		wontwork.append(prox)
	
print("\nWont work")
for i in wontwork:
	print(i)	


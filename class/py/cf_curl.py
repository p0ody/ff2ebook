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

authedproxies={               #All very fast
	'proxies' : [
		"209.127.191.180:80",
		"45.94.47.66:80",
		"45.130.255.147:80",
		"45.95.96.132:80",
		"45.130.255.198:80",
		"45.95.96.237:80",
		"45.95.96.187:80",
		"193.8.56.119:80",
		"185.164.56.20:80",
		"45.130.255.243:80",
		],
	'auths' : [
		{
			'user':"djgiawmi-dest",
			'pass':"xwsr12ja4m1b",
		},
		{
			'user':"klzgbcir-dest",
			'pass':"k189kl8zfmx4",
		},
		{
			'user':"gqotztok-dest",
			'pass':"xhnzj7m45mq0",
		},
		{
			'user':"yvvpcpzu-dest",
			'pass':"eszeh8k1903e",
		},
	]
}


proxies = [
	#"socks5://localhost:8070",
	#"https://169.57.1.84:8123", #seems unstable __/--(:/)--\__
	"https://159.8.114.34:8123",
	"https://169.57.157.148:25",
	"https://159.8.114.37:80",
	#"https://35.229.214.209:3128",
	#"https://192.46.232.88:8000",
	"https://104.248.123.76:18080", #unstable
	"https://159.8.114.37:8123",
	"https://159.8.114.37:80",
	"https://169.57.157.146:8123",
	"https://169.57.157.148:80",
	#"https://116.196.85.150:3128", #slow
	#"https://116.17.102.29:3128", #slow
	#"https://180.246.32.151:80", #slow
	"https://119.81.71.27:80",
	#"https://209.126.4.134:3128",
	#"https://122.155.165.191:3128",
	"https://104.248.153.6:80",
	#"https://60.191.11.248:3128", #rarely works
	"https://119.81.189.194:8123",
	#"https://149.28.204.137:3128",
	"https://212.129.34.163:3128", #very slow
	#"https://194.87.146.103:3128", #very slow
	#"https://41.215.84.170:8080",
	#"https://125.26.7.83:8080",  #Once didn't work... you could try again... Used to work well!
	"https://122.155.165.191:3128",
	#"https://156.0.73.75:8080",
	#"https://4.14.219.157:3128",
	#"https://8.129.33.216:8888",
	"https://95.217.186.24:3128",
	"https://35.181.59.25:80",
	#"https://188.166.182.151:80",
	#"https://176.9.85.13:3128",  #Once didn't work... you could try again... Used to work well!
	#"https://104.248.249.47:3122",
	#"https://45.33.66.217:80",
	"https://104.248.123.76:18080",
	#"socks4://103.220.206.122:1080", Broken?
	"socks4://110.78.146.141:4145",
	"socks4://103.252.35.170:1080",
	#"https://144.217.101.245:3129",
	"https://95.217.186.24:3128",
	"https://122.155.165.191:3128",
	"https://136.144.54.195:80",
	#"https://198.50.163.192:3129",
	"https://139.180.142.243:3128",
	"https://144.91.82.190:3128",
	"socks5://173.254.222.170:1085",
	#"socks5://194.36.88.162:80",
	#"socks5://82.165.137.115:7061", Broken?
	"socks5://188.166.34.137:9000",
	#"socks4://194.135.97.126:4145",
	#"socks4://46.254.240.106:43310",
	"socks4://109.195.194.79:48447",
	"socks4://70.83.106.82:55801", # Fast 'n Unreliable (May work, or skip to another one quickly)
	"socks4://36.89.180.103:31062",
	"socks4://85.29.147.222:4145",
	"socks4://88.203.134.102:1080",
	"socks4://88.84.212.14:4145",
	"socks4://81.18.90.43:4153",
	"socks4://85.30.248.210:4153",
	"socks4://94.179.135.230:58516",
	"socks4://31.43.63.70:4145",
	"socks4://80.52.169.233:4153",
	"socks4://95.73.198.37:4145",
	"socks4://41.86.56.224:41833",
	"socks4://80.64.77.58:4153",
	"socks4://92.42.8.22:4153",
	"socks4://213.173.75.243:4153",
	"socks4://181.196.176.22:57361",
	"socks4://92.247.11.242:1080",
	]

auth = choice(authedproxies['auths'])
for l in authedproxies['proxies']:
	proxies.append("http://"+auth['user']+':'+auth['pass']+"@"+l)


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


for i in range(1):
	working = False
	while not working:
		try:
			signal.signal(signal.SIGALRM, handler)
			signal.alarm(5)
			prox = choice(proxies)
			proxyDict = {"https" : prox}
			page = scrape(proxyDict,prox)
			print(str(page.decode("utf-8")))
			working=True
		except Exception as e:
			#print(e)
			working=False
		if not working:
			wontwork.append(prox)
		



with open(cfcacheusefile, 'wb') as f:
	pickle.dump(cfcacheuse, f)

with open(cfcachefile, 'wb') as f:
	pickle.dump(cfcache, f)
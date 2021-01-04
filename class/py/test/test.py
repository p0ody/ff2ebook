import pickle
import os



proxies = ["154.167.5.3", "364.234.56.2", "192.168.0.1", "4.5.77.44"]


#init cache
cfcache = {}
def cfcheck(key): 
	if key in cfcache.keys(): 
		return True
	else: 
		return False 

def cfadd(key, cache):
	cfcache[key] = cache
cfcachefile = "cf.cache"
if (os.path.isfile(cfcachefile)):
	with open(cfcachefile, 'rb') as f:
		#print("got from cache")
		cfcache = pickle.load(f)
else:
	#print("first init")
	cfcache={}



#run time
for l in proxies:
	if cfcheck(l):
		print(l,cfcache[l])
	else:
		print(l)


#get cookie

cfadd("192.168.0.1", "guid=657")





#at the end
with open(cfcachefile, 'wb') as f:
	pickle.dump(cfcache, f)
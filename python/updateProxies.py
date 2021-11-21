#!/usr/bin/python
from threading import Thread
from queue import Queue
import requests

DOMAIN = "localhost/ff2ebook"

proxyList = [{"ip":"", "auth":""}, {"ip":"159.8.114.34:8123", "auth":""}, {"ip":"159.8.114.34:8123", "auth":""}, {"ip":"159.8.114.34:8123", "auth":""}]

def testProxy(proxy: dict):
	url = "http://"+ DOMAIN +"/scripts/testProxy.php?ip="+ proxy["ip"]
	if proxy["auth"] != "":
		url += "&auth="+ proxy["auth"]

	r = requests.get(url)
	print(r.text)
	
threads = list()

for proxy in proxyList:
	x = Thread(target=testProxy, args=(proxy,))
	threads.append(x)
	x.start()

#for index, thread in enumerate(threads):
#	thread.join()
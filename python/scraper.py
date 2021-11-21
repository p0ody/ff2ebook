#!/usr/bin/python
import cloudscraper
import argparse
import ssl

parser = argparse.ArgumentParser(description='cloudscraper client to fetch html from a URL')
parser.add_argument('-u','--url', help='Input URL',required=True)
parser.add_argument('-p','--proxy', help='Proxy server to use',required=False)
parser.add_argument('-a','--auth', help='Auth credential, format user:password',required=False)
args = parser.parse_args()

proxy = ""
authInfo = ""

if args.auth:
	authInfo = args.auth + "@"

if args.proxy:
	proxy = authInfo + args.proxy

	proxy = { 
				"http"  : "http://" + proxy, 
				"https" : "http://" + proxy,
				"ssl"	: "http://" + proxy,
				"ftp"	: "http://" + proxy
				}

# Need to disable check hostname otherwise im getting captcha when using proxy :(
context = ssl.create_default_context(ssl.Purpose.SERVER_AUTH)
context.check_hostname = False

scraper = cloudscraper.create_scraper(
	browser={
        'browser': 'firefox',
        'platform': 'windows',
        'mobile': False
    }, ssl_context=context)

res = scraper.get(args.url, verify=True, proxies=proxy, timeout=10)

if res.status_code == 200:
	print(res.text)
	elapsed = int(res.elapsed.total_seconds() * 1000)
	print("<duration:%s>" % elapsed)

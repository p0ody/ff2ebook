#!/usr/bin/python

from selenium import webdriver
import argparse

#config
REMOTE_DRIVER = ""

parser = argparse.ArgumentParser(description='Selenium client to fetch html from a URL')
parser.add_argument('-u','--url', help='Input URL',required=True)
parser.add_argument('-p','--proxy', help='Proxy server to use',required=False)
parser.add_argument('-a','--auth', help='Auth credential, format user:password',required=False)
args = parser.parse_args()

options = webdriver.ChromeOptions()
options.add_argument("--no-sandbox")
options.add_argument("disable-gpu")

if args.proxy:
    proxy = args.proxy
    if args.auth:
        proxy = args.auth + "@" + proxy
    
    webdriver.DesiredCapabilities.CHROME["proxy"] ={
        "httpProxy":proxy,
        "ftpProxy":proxy,
        "sslProxy":proxy,
        "noProxy":None,
        "proxyType":"MANUAL",
        "autodetect":False
    }
    webdriver.DesiredCapabilities.CHROME["timeouts"] = {
        "pageLoad": 10000
    }
    
    

driver = webdriver.Remote(
    command_executor=REMOTE_DRIVER,
    desired_capabilities=webdriver.DesiredCapabilities.CHROME,
    options=options
)

try:
    driver.get(args.url)
    if (driver.page_source):
        print(driver.page_source)


        navigationStart = driver.execute_script("return window.performance.timing.navigationStart")
        domComplete = driver.execute_script("return window.performance.timing.domComplete")
        duration = domComplete - navigationStart

        print("<duration:%s>" % duration)
        
finally:
    driver.close()
    driver.quit()



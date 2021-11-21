#!/usr/bin/python3

from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
import argparse
import time

#config
REMOTE_DRIVER = "http://localhost:4444/wd/hub"

parser = argparse.ArgumentParser(description='Selenium client to fetch html from a URL')
parser.add_argument('-u','--url', help='Input URL',required=True)
parser.add_argument('-p','--proxy', help='Proxy server to use',required=False)
parser.add_argument('-a','--auth', help='Auth credential, format user:password',required=False)
args = parser.parse_args()

options = webdriver.ChromeOptions()
options.add_argument("--no-sandbox")
options.add_argument("disable-gpu")
options.add_argument("--disable-blink-features=AutomationControlled")
options.add_argument("--disable-dev-shm-usage")
options.add_experimental_option("excludeSwitches", ["enable-automation"])
options.add_experimental_option('useAutomationExtension', False)

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
    print("end")
    driver.close()
    driver.quit()



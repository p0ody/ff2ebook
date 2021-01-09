import cloudscraper as cfscrape

scraper = cfscrape.create_scraper(delay=10)
proxies = {"https": "socks5://localhost:8070"}

cookies={'__cfduid': 'dda37f5ec33e6728ea9a2e7b4f4bcea651609758480', 'cf_clearance': ''}
source  = scraper.get("https://m.fanfiction.net/s/11377425/1/", proxies=proxies, cookies=cookies).content
tokens, user_agent = scraper.get_tokens("https://www.fanfiction.net/s/11377425/1/Perseus-Son-of-Vesta-Scorched-Earth", proxies=proxies)
print("Token =",tokens)
print("\n\n\n")
print(source)
CREATE TABLE scraper
(
  url varchar(100) NOT NULL,
  lastUpdated INT UNSIGNED,
  isWorking BOOLEAN DEFAULT TRUE,
  priority INT UNSIGNED,
  PRIMARY KEY (url)
);

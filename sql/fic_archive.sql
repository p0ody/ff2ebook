CREATE TABLE fic_archive
(
  site varchar(6) NOT NULL,
  id int(20) NOT NULL,
  title varchar(255),
  author varchar(255),
  updated int(255),
  filename varchar(255),
  PRIMARY KEY (id, site)
);
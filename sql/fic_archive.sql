CREATE TABLE fic_archive
(
  site varchar(10) NOT NULL,
  id varchar(20) NOT NULL,
  title varchar(255),
  author varchar(255),
  updated int(255),
  filename varchar(255),
  PRIMARY KEY (id, site)
);
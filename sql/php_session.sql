CREATE TABLE sessions
(
  id varchar(32) NOT NULL,
  access int(10) unsigned,
  data longtext,
  PRIMARY KEY (id)
);

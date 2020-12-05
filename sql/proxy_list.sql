CREATE TABLE proxy_list
(
  ip varchar(21) NOT NULL,
  latency int UNSIGNED,
  working bool DEFAULT FALSE,
  PRIMARY KEY (ip)
);

CREATE TABLE proxy_list
(
  ip varchar(21) NOT NULL,
  latency int UNSIGNED,
  working bool DEFAULT FALSE,
  total_hits int UNSIGNED DEFAULT 0,
  times_down int UNSIGNED DEFAULT 0,
  auth varchar(60),
  PRIMARY KEY (ip)
);

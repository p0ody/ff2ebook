CREATE TABLE fic_archive
(
  site varchar(6) NOT NULL,
  id MEDIUMINT UNSIGNED NOT NULL,
  title varchar(100),
  author varchar(100),
  updated INT UNSIGNED,
  filename varchar(35),
  lastDL INT UNSIGNED DEFAULT 0,
  lastChecked INT UNSIGNED DEFAULT 0,
  PRIMARY KEY (id, site)
);

-- Table UPDATE
-- ALTER TABLE fic_archive ADD lastDL int(255) DEFAULT 0;

-- 2020-02-22 Performance update
-- ALTER TABLE fic_archive MODIFY COLUMN id MEDIUMINT UNSIGNED;
-- ALTER TABLE fic_archive MODIFY COLUMN title varchar(100);
-- ALTER TABLE fic_archive MODIFY COLUMN author varchar(100);
-- ALTER TABLE fic_archive MODIFY COLUMN lastDL INT UNSIGNED;
-- ALTER TABLE fic_archive MODIFY COLUMN updated INT UNSIGNED;
-- ALTER TABLE fic_archive MODIFY COLUMN filename varchar(35);

-- 2021-09-27 Added column lastChecked
-- ALTER TABLE fic_archive ADD lastChecked INT UNSIGNED DEFAULT 0;


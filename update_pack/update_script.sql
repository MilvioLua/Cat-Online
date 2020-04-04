drop table language;

ALTER TABLE teacher
ADD about text;

INSERT INTO settings (settings_id, type, description)
VALUES ('32', 'protocol', 'smtp');

INSERT INTO settings (settings_id, type, description)
VALUES ('33', 'smtp_host', 'ssl://smtp.googlemail.com');

INSERT INTO settings (settings_id, type, description)
VALUES ('34', 'smtp_port', '465');

INSERT INTO settings (settings_id, type, description)
VALUES ('35', 'smtp_user', '');

INSERT INTO settings (settings_id, type, description)
VALUES ('36', 'smtp_pass', '');
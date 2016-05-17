CREATE USER 'qlfile'@'localhost' IDENTIFIED BY 'qlfile123';
grant all privileges on *.* to qlfile@localhost identified by 'qlfile123';
GRANT ALL PRIVILEGES ON * . * TO 'qlfile'@'192.168.1.223'identified by 'qlfile123';
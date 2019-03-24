update banve set  dungchung = 0 where dungchung is null;

INSERT INTO message_lang(msg_no,des_vn,screen,des_en) VALUES ('MENU032', 'MENU', 'Dowload dwg', 'Dowload dwg');

CREATE TABLE `dwg`  (
  `dwg_id` int(11) NOT NULL AUTO_INCREMENT,
  `file_id` int(11) NULL DEFAULT NULL,
  `user_req_id` int(11) NULL DEFAULT NULL,
  `user_app_id` int(11) NULL DEFAULT NULL,
  `req_date` datetime(0) NULL DEFAULT NULL,
  `status` int(2) NULL DEFAULT NULL,
  `app_date` datetime(0) NULL DEFAULT NULL,
  `note` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  PRIMARY KEY (`dwg_id`) USING BTREE
) ;
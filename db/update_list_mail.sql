INSERT INTO message_lang(msg_no,des_vn,screen,des_en)
VALUES ( 'YEU041', 'Bạn chưa đính kèm bản vẽ !', 'YEUCAU', 'You must attach file !');
INSERT INTO message_lang(msg_no,des_vn,screen,des_en)
VALUES ( 'USER032', 'CC mail', 'USER', 'CC mail');

ALTER TABLE m_user
ADD COLUMN list_mail  text NULL AFTER upd_datetime;
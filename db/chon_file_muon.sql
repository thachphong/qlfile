update  message_lang 
set des_vn = 'File: "%s" bị trùng, không thể thêm mới, vui lòng chọn file khác hoặc Xin mượn bản vẽ !'
,des_en = 'File: "%s" is exist, can not add new, plaese choose other file or borrow drawing !'
where msg_no ='SYS011';

INSERT INTO message_lang(msg_no,des_vn,screen,des_en)
VALUES ( 'YEU042', 'Bạn phải chọn file mượn !', 'YEUCAU', 'You must choose borrow drawings!');
INSERT INTO message_lang(msg_no,des_vn,screen,des_en)
VALUES ( 'YEU043', 'Bạn chưa nhập ngày hẹn trả !', 'YEUCAU', 'You must enter return date!');
INSERT INTO message_lang(msg_no,des_vn,screen,des_en)
VALUES ( 'YEU044', 'Bạn chưa nhập lý do mượn !', 'YEUCAU', 'You must enter reason borrow!');

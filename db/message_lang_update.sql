INSERT INTO message_lang(msg_no,des_vn,screen,des_en)
VALUES ('SYS012', 'Mã bản vẽ \"%s\" chưa có, không thể tạo upload file!', 'SYS', 'Drawing no \"%s\" is not exists , can not upload file !');
INSERT INTO message_lang(msg_no,des_vn,screen,des_en)
VALUES ('BVE048', 'Hủy', 'BANVE', 'Cancel');
INSERT INTO message_lang(msg_no,des_vn,screen,des_en)
VALUES ('BVE049', 'Phục hồi', 'BANVE', 'Restore');
INSERT INTO message_lang(msg_no,des_vn,screen,des_en)
VALUES ('BVE050', 'Bạn chắc chắn muốn hủy bản vẽ này？', 'BANVE', 'Are you sure cancel this drawing ?');
INSERT INTO message_lang(msg_no,des_vn,screen,des_en)
VALUES ('BVE051', 'Phục hồi', 'BANVE', 'Are you sure restore this drawing ?');
INSERT INTO message_lang(msg_no,des_vn,screen,des_en)
VALUES ('BVE052', 'Tồn tại bản vẽ con, không thể hủy !', 'BANVE', 'Exists child drawing, can not cancel !');
INSERT INTO message_lang(msg_no,des_vn,screen,des_en)
VALUES ('BVE053', 'Di chuyển', 'BANVE', 'Move');
INSERT INTO message_lang(msg_no,des_vn,screen,des_en)
VALUES ('PRIN014', 'Bản vẽ', 'PRINT', 'Drawing name');
INSERT INTO message_lang(msg_no,des_vn,screen,des_en)
VALUES ('LSMT017', 'Người mượn', 'LSMTRA', 'Borrow user');
INSERT INTO message_lang(msg_no,des_vn,screen,des_en)
VALUES ('LSMT018', 'Yêu cầu trả', 'LSMTRA', 'Request return');
INSERT INTO message_lang(msg_no,des_vn,screen,des_en)
VALUES ('LSMT019', 'Gửi mail thành công !', 'LSMTRA', 'Send mail successful');
INSERT INTO message_lang(msg_no,des_vn,screen,des_en)
VALUES ('LSMT020', 'Gửi mail thất bại', 'LSMTRA', 'Send mail fail');

ALTER TABLE file ADD COLUMN borrow_user_id  int ;
ALTER TABLE file ADD COLUMN borrow_send_flg  int default 0;

CREATE FUNCTION `f_get_all_banve_new`(pa_banve_no text,pa_banve_name text ) RETURNS text 
BEGIN 
 DECLARE v_all_banve text; 
 
 select
					concat((select COALESCE( group_concat( banve_id separator ','),'') as c
								from    banve,
								        (select @pv2 := (select group_concat(banve_id separator ',') 
								        from banve
								        where del_flg = 0
								            and banve_no like pa_banve_no
								            and lower(banve_name) like  lower(pa_banve_name) )
								        ) initialisation
								where   find_in_set(parent_id, @pv2) > 0
									and     @pv2 := concat(@pv2, ',', banve_id)
					)
					,',',
					(select group_concat(t.c separator ',')
					from (select DISTINCT f_get_all_parent(bv.banve_id) as c

								from banve bv
								where del_flg = 0
									and bv.banve_no like pa_banve_no
									and lower(bv.banve_name) like  lower(pa_banve_name)
					) t 
					)
					) into v_all_banve;
 RETURN v_all_banve;
END;

CREATE  FUNCTION `f_get_all_parent`(pa_banve_id int ) RETURNS varchar(100) 
BEGIN 
 DECLARE v_all_parent VARCHAR(100); 
 
 SELECT group_concat(T2.banve_id separator ',') into v_all_parent
FROM (
    SELECT
        @r AS _id,
        (SELECT @r := parent_id FROM banve WHERE banve_id = _id) AS parent_id,
        @l := @l + 1 AS lvl
    FROM
        (SELECT @r := pa_banve_id, @l := 0) vars,
        banve m
    WHERE @r <> 0) T1
JOIN banve T2
ON T1._id = T2.banve_id;
 RETURN v_all_parent;
END;
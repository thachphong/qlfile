/*
Navicat MySQL Data Transfer

Source Server         : mysql_local
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : qlfile

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2016-05-18 16:59:00
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `banve`
-- ----------------------------
DROP TABLE IF EXISTS `banve`;
CREATE TABLE `banve` (
  `banve_id` int(11) NOT NULL AUTO_INCREMENT,
  `banve_no` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `banve_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `level` int(11) DEFAULT NULL,
  `del_flg` int(11) DEFAULT '0',
  `add_user_id` int(11) DEFAULT NULL,
  `add_datetime` datetime DEFAULT NULL,
  `upd_user_id` int(11) DEFAULT NULL,
  `upd_datetime` datetime DEFAULT NULL,
  `dungchung` int(11) DEFAULT '0',
  `kho_giay` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`banve_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of banve
-- ----------------------------
INSERT INTO `banve` VALUES ('1', 'TOP', '', '-1', '0', '0', null, null, null, null, '0', null);
INSERT INTO `banve` VALUES ('2', '1110001', 'jjjjjjjjj', '1', '1', '0', '1', '2016-05-16 23:34:08', '1', '2016-05-16 23:40:52', '0', '4');
INSERT INTO `banve` VALUES ('3', '1110010', 'fdgsdgdfgsd', '2', '2', '0', '1', '2016-05-16 23:40:52', '1', '2016-05-16 23:40:52', '0', '3');

-- ----------------------------
-- Table structure for `capphat`
-- ----------------------------
DROP TABLE IF EXISTS `capphat`;
CREATE TABLE `capphat` (
  `capphat_id` int(11) NOT NULL AUTO_INCREMENT,
  `donvi_id` int(11) DEFAULT NULL,
  `phongban_id` int(11) DEFAULT NULL,
  `tonhom_id` int(11) DEFAULT NULL,
  `file_id` int(11) DEFAULT NULL,
  `add_user_id` int(11) DEFAULT NULL,
  `add_datetime` datetime DEFAULT NULL,
  `upd_user_id` int(11) DEFAULT NULL,
  `upd_datetime` datetime DEFAULT NULL,
  `soluong` int(11) DEFAULT NULL,
  PRIMARY KEY (`capphat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of capphat
-- ----------------------------

-- ----------------------------
-- Table structure for `don`
-- ----------------------------
DROP TABLE IF EXISTS `don`;
CREATE TABLE `don` (
  `don_id` int(11) NOT NULL AUTO_INCREMENT,
  `don_no` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tieude` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `noidung` text COLLATE utf8_unicode_ci,
  `loaidon` int(1) DEFAULT '0',
  `trangthai` int(1) DEFAULT NULL,
  `user_kt` int(11) DEFAULT NULL,
  `user_duyet` int(11) DEFAULT NULL,
  `user_ttql` int(11) DEFAULT NULL,
  `ngay_kt` datetime DEFAULT NULL,
  `ngay_duyet` datetime DEFAULT NULL,
  `ngay_ttql` datetime DEFAULT NULL,
  `maso_duyet` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ghichu` text COLLATE utf8_unicode_ci,
  `ngay_duyet_muon` datetime DEFAULT NULL,
  `ngay_hen_tra` datetime DEFAULT NULL,
  `lydo_muon` text COLLATE utf8_unicode_ci,
  `bophan` int(11) DEFAULT NULL,
  `add_user_id` int(11) DEFAULT NULL,
  `add_datetime` datetime DEFAULT NULL,
  `upd_user_id` int(11) DEFAULT NULL,
  `upd_datetime` datetime DEFAULT NULL,
  `del_flg` int(1) DEFAULT '0',
  PRIMARY KEY (`don_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of don
-- ----------------------------

-- ----------------------------
-- Table structure for `don_folder`
-- ----------------------------
DROP TABLE IF EXISTS `don_folder`;
CREATE TABLE `don_folder` (
  `folder_id` int(11) NOT NULL,
  `don_id` int(11) NOT NULL,
  `add_user_id` int(11) DEFAULT NULL,
  `add_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`folder_id`,`don_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of don_folder
-- ----------------------------

-- ----------------------------
-- Table structure for `don_vi`
-- ----------------------------
DROP TABLE IF EXISTS `don_vi`;
CREATE TABLE `don_vi` (
  `donvi_id` int(11) NOT NULL AUTO_INCREMENT,
  `donvi_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `del_flg` int(11) DEFAULT '0',
  `add_user_id` int(11) DEFAULT '0',
  `add_datetime` date DEFAULT '0000-00-00',
  `upd_user_id` int(11) DEFAULT '0',
  `upd_datetime` date DEFAULT '0000-00-00',
  PRIMARY KEY (`donvi_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of don_vi
-- ----------------------------
INSERT INTO `don_vi` VALUES ('1', 'Don Vi 1', '0', '10', '2016-01-30', '10', '2016-01-30');
INSERT INTO `don_vi` VALUES ('2', 'Đơn vị 2', '0', '10', '2016-01-30', '10', '2016-01-30');
INSERT INTO `don_vi` VALUES ('3', 'Đơn vị 3', '0', '10', '2016-01-30', '10', '2016-01-30');

-- ----------------------------
-- Table structure for `download`
-- ----------------------------
DROP TABLE IF EXISTS `download`;
CREATE TABLE `download` (
  `download_id` int(18) NOT NULL AUTO_INCREMENT,
  `file_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `add_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`download_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of download
-- ----------------------------

-- ----------------------------
-- Table structure for `file`
-- ----------------------------
DROP TABLE IF EXISTS `file`;
CREATE TABLE `file` (
  `file_id` int(11) NOT NULL AUTO_INCREMENT,
  `file_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `don_id` int(11) DEFAULT NULL,
  `file_type` varchar(10) DEFAULT NULL,
  `status` int(2) DEFAULT '0',
  `add_user_id` int(11) DEFAULT NULL,
  `add_datetime` datetime DEFAULT NULL,
  `upd_user_id` int(11) DEFAULT NULL,
  `upd_datetime` datetime DEFAULT NULL,
  `new_flg` int(1) DEFAULT '1',
  `del_flg` int(1) DEFAULT '0',
  `file_exist` int(1) DEFAULT '1',
  PRIMARY KEY (`file_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of file
-- ----------------------------

-- ----------------------------
-- Table structure for `folder`
-- ----------------------------
DROP TABLE IF EXISTS `folder`;
CREATE TABLE `folder` (
  `folder_id` int(11) NOT NULL AUTO_INCREMENT,
  `folder_name` varchar(50) DEFAULT NULL,
  `parent_folder_id` int(11) DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL,
  `del_flg` int(1) DEFAULT NULL,
  `add_user_id` int(11) DEFAULT NULL,
  `add_datetime` date DEFAULT NULL,
  `upd_user_id` int(11) DEFAULT NULL,
  `upd_datetime` date DEFAULT NULL,
  PRIMARY KEY (`folder_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of folder
-- ----------------------------
INSERT INTO `folder` VALUES ('1', 'TOP', '-1', null, '0', null, null, null, null);
INSERT INTO `folder` VALUES ('2', 'Folder1', '1', null, '0', '10', '2016-01-31', '10', '2016-01-31');
INSERT INTO `folder` VALUES ('3', 'Folder11', '2', null, '0', '10', '2016-01-31', '10', '2016-01-31');
INSERT INTO `folder` VALUES ('4', 'Folder12', '2', null, '0', '10', '2016-01-31', '10', '2016-01-31');
INSERT INTO `folder` VALUES ('5', 'F_11_01', '3', null, '0', '1', '2016-02-13', '1', '2016-02-13');
INSERT INTO `folder` VALUES ('6', 'F_11_01_01', '5', null, '0', '1', '2016-02-13', '1', '2016-02-13');
INSERT INTO `folder` VALUES ('7', 'F_11_01_02', '5', null, '0', '1', '2016-02-13', '1', '2016-02-13');
INSERT INTO `folder` VALUES ('8', ' F_11_01', '3', null, '0', '1', '2016-02-13', '1', '2016-02-13');
INSERT INTO `folder` VALUES ('9', 'F_11_01_02_001', '7', null, '0', '1', '2016-02-13', '1', '2016-02-13');
INSERT INTO `folder` VALUES ('10', 'F_11_01_02_001_001', '9', null, '0', '1', '2016-02-13', '1', '2016-02-13');
INSERT INTO `folder` VALUES ('11', 'F_11_01_02_001_002', '9', null, '0', '1', '2016-02-13', '1', '2016-02-13');
INSERT INTO `folder` VALUES ('12', 'F_11_01_02_001_003', '9', null, '0', '1', '2016-02-13', '1', '2016-02-13');
INSERT INTO `folder` VALUES ('13', 'foler_2', '1', null, '0', '1', '2016-02-26', '1', '2016-02-26');

-- ----------------------------
-- Table structure for `level`
-- ----------------------------
DROP TABLE IF EXISTS `level`;
CREATE TABLE `level` (
  `level_id` int(11) NOT NULL AUTO_INCREMENT,
  `level_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `upload` int(1) DEFAULT '0',
  `kiemtra` int(1) DEFAULT '0',
  `duyet` int(1) DEFAULT '0',
  `phanbo` int(1) DEFAULT '0',
  `trungtam_quanly` int(1) DEFAULT '0',
  `print` int(1) DEFAULT '0',
  `admin` int(1) DEFAULT '0',
  PRIMARY KEY (`level_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of level
-- ----------------------------
INSERT INTO `level` VALUES ('1', 'Admin', '1', '1', '1', '1', '1', '1', '1');
INSERT INTO `level` VALUES ('2', 'Upload', '1', '0', '0', '0', '0', '0', '0');
INSERT INTO `level` VALUES ('3', 'IN', '0', '0', '0', '0', '0', '1', '0');
INSERT INTO `level` VALUES ('4', 'Kiểm tra', '0', '1', '0', '0', '0', '0', '0');
INSERT INTO `level` VALUES ('5', 'Duyệt', '0', '0', '1', '0', '0', '0', '0');
INSERT INTO `level` VALUES ('6', 'Upload,IN', '1', '0', '0', '0', '0', '1', '0');
INSERT INTO `level` VALUES ('7', 'Upload,IN,Kiểm tra', '1', '1', '0', '0', '0', '1', '0');
INSERT INTO `level` VALUES ('8', 'Upload,IN,Kiểm tra,Duyệt', '1', '1', '1', '0', '0', '1', '0');
INSERT INTO `level` VALUES ('9', 'Upload,IN,Kiểm tra,Duyệt,Phân bổ', '1', '1', '1', '1', '0', '1', '0');
INSERT INTO `level` VALUES ('10', 'Upload,IN,Kiểm tra,Duyệt,Phân bổ,ttql', '1', '1', '1', '1', '1', '1', '0');
INSERT INTO `level` VALUES ('11', 'Trung tâm quản lý', '0', '0', '0', '0', '1', '0', '0');
INSERT INTO `level` VALUES ('12', 'Upload,Kiểm tra', '1', '1', '0', '0', '0', '0', '0');
INSERT INTO `level` VALUES ('13', 'Upload,Duyệt', '1', '0', '1', '0', '0', '0', '0');
INSERT INTO `level` VALUES ('14', 'Upload,Phân bổ', '1', '0', '0', '1', '0', '0', '0');
INSERT INTO `level` VALUES ('15', 'Upload,TTQL', '1', '0', '0', '0', '1', '0', '0');
INSERT INTO `level` VALUES ('16', '', '0', '0', '0', '0', '1', '0', '1');
INSERT INTO `level` VALUES ('17', '', '0', '0', '0', '0', '1', '0', '1');
INSERT INTO `level` VALUES ('18', null, '0', '0', '0', '0', '1', '0', '1');
INSERT INTO `level` VALUES ('19', null, '0', '0', '1', '0', '1', '0', '1');
INSERT INTO `level` VALUES ('20', null, '0', '1', '1', '0', '1', '0', '1');

-- ----------------------------
-- Table structure for `lichsu_in`
-- ----------------------------
DROP TABLE IF EXISTS `lichsu_in`;
CREATE TABLE `lichsu_in` (
  `lichsu_in_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `file_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `soluong` int(11) DEFAULT NULL,
  `ngay_in` datetime DEFAULT NULL,
  `add_datetime` datetime DEFAULT NULL,
  `log_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`lichsu_in_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of lichsu_in
-- ----------------------------
INSERT INTO `lichsu_in` VALUES ('3', 'HONGKY', 'BMD-CG06190-Cacte sau motor vo nhom (chup gio 190x0.6).pdf', '1', '2016-01-15 07:23:53', '2016-02-23 20:38:18', null);
INSERT INTO `lichsu_in` VALUES ('4', 'HONGKY', 'BMD-CG06190-Cacte sau motor vo nhom (chup gio 190x0.6).pdf', '1', '2016-01-15 07:48:55', '2016-02-23 20:38:18', null);
INSERT INTO `lichsu_in` VALUES ('5', 'HONGKY', 'BMD-CG06190-Cacte sau motor vo nhom (chup gio 190x0.6).pdf', '1', '2016-01-15 07:23:53', '2016-02-23 20:50:56', 'PrintLogs__.xls');
INSERT INTO `lichsu_in` VALUES ('6', 'HONGKY', 'BMD-CG06190-Cacte sau motor vo nhom (chup gio 190x0.6).pdf', '1', '2016-01-15 07:48:55', '2016-02-23 20:50:56', 'PrintLogs__.xls');
INSERT INTO `lichsu_in` VALUES ('7', 'HONGKY', 'BMD-CG06190-Cacte sau motor vo nhom (chup gio 190x0.6).pdf', '1', '2016-01-15 07:23:53', '2016-02-23 21:04:14', 'PrintLogs__.xls');
INSERT INTO `lichsu_in` VALUES ('8', 'HONGKY', 'BMD-CG06190-Cacte sau motor vo nhom (chup gio 190x0.6).pdf', '1', '2016-01-15 07:48:55', '2016-02-23 21:04:14', 'PrintLogs__.xls');
INSERT INTO `lichsu_in` VALUES ('9', 'HONGKY', 'BMD-CG06190-Cacte sau motor vo nhom (chup gio 190x0.6).pdf', '1', '2016-01-15 07:23:00', '2016-02-23 21:46:40', 'PrintLogs__ - Copy.xls');
INSERT INTO `lichsu_in` VALUES ('10', 'HONGKY', 'BMD-CG06190-Cacte sau motor vo nhom (chup gio 190x0.6).pdf', '1', '2016-01-15 07:48:00', '2016-02-23 21:46:40', 'PrintLogs__ - Copy.xls');
INSERT INTO `lichsu_in` VALUES ('11', 'admin', 'file_pdf_02.pdf', '5', '2016-01-16 02:15:00', '2016-02-23 21:46:40', 'PrintLogs__ - Copy.xls');
INSERT INTO `lichsu_in` VALUES ('12', 'admin', 'file_pdf_02.pdf', '2', '2016-01-16 09:23:00', '2016-02-23 21:46:40', 'PrintLogs__ - Copy.xls');
INSERT INTO `lichsu_in` VALUES ('13', 'admin', 'file-eee-02.pdf', '1', '2016-01-15 07:19:00', '2016-03-13 11:46:43', 'PrintLogs__3.xls');
INSERT INTO `lichsu_in` VALUES ('14', 'HONGKY', 'BMD-CG06190-Cacte sau motor vo nhom (chup gio 190x0.6).pdf', '1', '2016-01-15 07:23:00', '2016-03-13 11:46:43', 'PrintLogs__3.xls');
INSERT INTO `lichsu_in` VALUES ('15', 'HONGKY', 'BMD-CG06190-Cacte sau motor vo nhom (chup gio 190x0.6).pdf', '1', '2016-01-15 07:48:00', '2016-03-13 11:46:43', 'PrintLogs__3.xls');

-- ----------------------------
-- Table structure for `may`
-- ----------------------------
DROP TABLE IF EXISTS `may`;
CREATE TABLE `may` (
  `may_id` int(11) NOT NULL AUTO_INCREMENT,
  `may_no` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `may_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `del_flg` int(1) DEFAULT '0',
  `add_datetime` datetime DEFAULT NULL,
  `add_user_id` int(11) DEFAULT NULL,
  `upd_datetime` datetime DEFAULT NULL,
  `upd_user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`may_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of may
-- ----------------------------
INSERT INTO `may` VALUES ('1', 'sd', 'dsds', '0', '2016-03-12 22:50:20', '1', '0000-00-00 00:00:00', '2147483647');
INSERT INTO `may` VALUES ('2', 'sđ', 'dsd', '0', '2016-03-12 22:52:05', '1', '0000-00-00 00:00:00', '2147483647');
INSERT INTO `may` VALUES ('3', 'sssssss', 'ddd', '0', '2016-03-12 22:53:12', '1', '0000-00-00 00:00:00', '2147483647');
INSERT INTO `may` VALUES ('4', 'gdsfs', 'sdfsfsf', '0', '2016-03-15 22:48:56', '1', '0000-00-00 00:00:00', '2147483647');
INSERT INTO `may` VALUES ('5', 'gdsfs11', 'dsds', '0', '2016-03-15 22:54:11', '1', '0000-00-00 00:00:00', '2147483647');

-- ----------------------------
-- Table structure for `may_file`
-- ----------------------------
DROP TABLE IF EXISTS `may_file`;
CREATE TABLE `may_file` (
  `may_file_id` int(11) NOT NULL AUTO_INCREMENT,
  `may_id` int(11) DEFAULT NULL,
  `file_id` int(11) DEFAULT NULL,
  `add_datetime` datetime DEFAULT NULL,
  `add_user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`may_file_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of may_file
-- ----------------------------
INSERT INTO `may_file` VALUES ('1', '1', '281', '2016-03-15 23:53:36', '1');
INSERT INTO `may_file` VALUES ('3', '1', '277', '2016-03-15 23:54:25', '1');
INSERT INTO `may_file` VALUES ('4', '2', '281', '2016-03-15 23:54:38', '1');
INSERT INTO `may_file` VALUES ('5', '4', '282', '2016-03-15 23:57:01', '1');
INSERT INTO `may_file` VALUES ('7', '4', '277', '2016-03-15 23:57:18', '1');

-- ----------------------------
-- Table structure for `message_lang`
-- ----------------------------
DROP TABLE IF EXISTS `message_lang`;
CREATE TABLE `message_lang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `msg_no` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `des_vn` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `screen` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `des_en` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `add_user_id` int(11) DEFAULT NULL,
  `add_datetime` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of message_lang
-- ----------------------------
INSERT INTO `message_lang` VALUES ('1', 'MENU0001', 'KIểm tra yêu cầu XD', 'MENU', 'Inspection Required Approval', null, null);
INSERT INTO `message_lang` VALUES ('2', 'MENU0002', 'Lập yêu cầu XD', 'MENU', 'Establish Requirements', null, null);
INSERT INTO `message_lang` VALUES ('3', 'MENU0003', 'Lịch sử yêu cầu XD', 'MENU', 'Required Approval History', null, null);
INSERT INTO `message_lang` VALUES ('4', 'MENU0004', 'Mượn bản vẽ', 'MENU', 'Borrow Drawings', null, null);
INSERT INTO `message_lang` VALUES ('5', 'MENU0005', 'Phân bổ file', 'MENU', 'Allocation of Drawings', null, null);
INSERT INTO `message_lang` VALUES ('6', 'MENU0006', 'In File', 'MENU', null, null, null);
INSERT INTO `message_lang` VALUES ('7', 'MENU0007', 'Cấp phát file', 'MENU', null, null, null);
INSERT INTO `message_lang` VALUES ('8', 'MENU0008', 'Admin', 'MENU', null, null, null);
INSERT INTO `message_lang` VALUES ('9', 'MENU0009', 'Lịch sử mượn trả file', 'MENU', null, null, null);
INSERT INTO `message_lang` VALUES ('10', 'MENU0010', 'Lịch sử file cũ', 'MENU', null, null, null);
INSERT INTO `message_lang` VALUES ('11', 'MENU0011', 'Convert lại pdf', 'MENU', null, null, null);
INSERT INTO `message_lang` VALUES ('12', 'MENU0012', 'Lịch sử phân bổ', 'MENU', null, null, null);
INSERT INTO `message_lang` VALUES ('13', 'MENU0013', 'Danh mục bản vẽ', 'MENU', null, null, null);
INSERT INTO `message_lang` VALUES ('14', 'MENU0014', 'Danh sách máy', 'MENU', null, null, null);
INSERT INTO `message_lang` VALUES ('15', 'MENU0015', 'Đưa bản vẽ vào máy', 'MENU', null, null, null);
INSERT INTO `message_lang` VALUES ('16', 'MENU0016', 'Lịch sử in file', 'MENU', null, null, null);
INSERT INTO `message_lang` VALUES ('17', 'MENU0017', 'Lịch sử cấp phát file', 'MENU', null, null, null);
INSERT INTO `message_lang` VALUES ('18', 'MENU0018', 'Đơn vị', 'MENU', null, null, null);
INSERT INTO `message_lang` VALUES ('19', 'MENU0019', 'Phòng ban', 'MENU', null, null, null);
INSERT INTO `message_lang` VALUES ('20', 'MENU0020', 'Tổ nhóm', 'MENU', null, null, null);
INSERT INTO `message_lang` VALUES ('21', 'MENU0021', 'User', 'MENU', null, null, null);
INSERT INTO `message_lang` VALUES ('22', 'MENU0022', 'Thư mục', 'MENU', null, null, null);
INSERT INTO `message_lang` VALUES ('23', 'MENU0023', 'Phân quyền', 'MENU', null, null, null);
INSERT INTO `message_lang` VALUES ('24', 'MENU0024', 'Lịch sử download', 'MENU', null, null, null);
INSERT INTO `message_lang` VALUES ('25', 'MENU0025', 'Xóa đơn', 'MENU', null, null, null);
INSERT INTO `message_lang` VALUES ('26', 'MENU0026', 'Xin Chào', 'MENU', 'Wellcome', null, null);
INSERT INTO `message_lang` VALUES ('28', 'MENU0027', 'Thoát', 'MENU', 'Logout', null, null);
INSERT INTO `message_lang` VALUES ('30', 'BTN001', 'Tìm', null, 'Find', null, null);
INSERT INTO `message_lang` VALUES ('32', 'SYS001', 'HỆ THỐNG XÉT DUYỆT BẢN VẼ', 'MENU', 'DRAWING APPROVAL SYSTEM', null, null);

-- ----------------------------
-- Table structure for `m_user`
-- ----------------------------
DROP TABLE IF EXISTS `m_user`;
CREATE TABLE `m_user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `user_name_disp` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `pass` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `donvi` int(11) DEFAULT NULL,
  `phong_ban` int(11) DEFAULT NULL,
  `to_nhom` int(11) DEFAULT NULL,
  `ip` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `level` int(11) NOT NULL DEFAULT '0',
  `del_flg` int(11) DEFAULT '0',
  `email` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `add_user_id` int(11) DEFAULT NULL,
  `add_datetime` date DEFAULT NULL,
  `upd_user_id` int(11) DEFAULT NULL,
  `upd_datetime` date DEFAULT NULL,
  KEY `pk_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of m_user
-- ----------------------------
INSERT INTO `m_user` VALUES ('1', 'admin', 'Admin', '6671367c704cfadc7ef46d373d5e5714', '1', '1', '1', null, '1', '0', 'phongvnit02@gmail.com', null, null, '10', '2016-02-26');
INSERT INTO `m_user` VALUES ('2', 'm2', 'trty', '6671367c704cfadc7ef46d373d5e5714', '1', '1', '1', '', '19', '0', null, '1', '2016-02-02', '1', '2016-02-02');
INSERT INTO `m_user` VALUES ('3', 'm23', 'trty', '6671367c704cfadc7ef46d373d5e5714', '1', '1', '1', '', '16', '0', null, '1', '2016-02-02', '1', '2016-02-02');
INSERT INTO `m_user` VALUES ('4', 'sdada', 'safasf', '6671367c704cfadc7ef46d373d5e5714', '1', '1', null, '', '20', '0', null, '1', '2016-02-02', '1', '2016-02-02');
INSERT INTO `m_user` VALUES ('5', 'duyet', 'Nguyễn văn duyệt', '6671367c704cfadc7ef46d373d5e5714', '1', '1', '1', '', '5', '0', 'thachphong28@gmail.com', '1', '2016-02-07', '1', '2016-02-07');
INSERT INTO `m_user` VALUES ('6', 'kiemtra', 'Nguyễn văn kiểm tra', '6671367c704cfadc7ef46d373d5e5714', '1', '1', '1', '', '4', '0', 'phongvnit01@gmail.com', '1', '2016-02-07', '1', '2016-02-07');
INSERT INTO `m_user` VALUES ('7', 'ttql', 'Nguyễn văn ttql', '6671367c704cfadc7ef46d373d5e5714', '1', '1', '1', '', '11', '0', 'whirlwind2887@gmail.com', '1', '2016-02-07', '1', '2016-02-07');
INSERT INTO `m_user` VALUES ('8', 'Upload', 'ong upload', '6671367c704cfadc7ef46d373d5e5714', '1', '1', '1', '', '2', '0', 'thachphong28@yahoo.com', '1', '2016-02-17', '8', '2016-02-18');
INSERT INTO `m_user` VALUES ('9', 'in', 'ong in', '6671367c704cfadc7ef46d373d5e5714', '1', '1', '1', '', '3', '0', null, '1', '2016-02-17', '1', '2016-02-17');
INSERT INTO `m_user` VALUES ('10', 'ttql2', 'ttql2', '6671367c704cfadc7ef46d373d5e5714', '1', '2', null, '', '1', '0', null, '1', '2016-02-26', '1', '2016-02-26');
INSERT INTO `m_user` VALUES ('11', 'xxx', 'xxx', '6671367c704cfadc7ef46d373d5e5714', '2', '5', null, null, '3', '0', null, '1', '2016-02-26', '1', '2016-02-26');

-- ----------------------------
-- Table structure for `phanquyen`
-- ----------------------------
DROP TABLE IF EXISTS `phanquyen`;
CREATE TABLE `phanquyen` (
  `phanquyen_id` int(11) NOT NULL AUTO_INCREMENT,
  `phongban_id` int(11) NOT NULL,
  `tonhom_id` int(11) DEFAULT NULL,
  `folder_id` int(11) NOT NULL,
  `add_user_id` int(11) DEFAULT NULL,
  `add_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`phanquyen_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of phanquyen
-- ----------------------------
INSERT INTO `phanquyen` VALUES ('1', '1', '1', '2', '1', '2016-02-12 19:32:52');
INSERT INTO `phanquyen` VALUES ('3', '3', null, '3', '1', '2016-02-12 19:47:48');
INSERT INTO `phanquyen` VALUES ('4', '4', null, '3', '1', '2016-02-12 19:47:48');
INSERT INTO `phanquyen` VALUES ('5', '5', null, '3', '1', '2016-02-12 19:47:48');
INSERT INTO `phanquyen` VALUES ('12', '5', null, '2', '1', '2016-02-12 19:53:52');
INSERT INTO `phanquyen` VALUES ('13', '1', '1', '4', '1', '2016-02-12 23:11:56');
INSERT INTO `phanquyen` VALUES ('14', '2', null, '13', '1', '2016-02-26 14:22:11');

-- ----------------------------
-- Table structure for `phong_ban`
-- ----------------------------
DROP TABLE IF EXISTS `phong_ban`;
CREATE TABLE `phong_ban` (
  `phongban_id` int(11) NOT NULL AUTO_INCREMENT,
  `phongban_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `donvi_id` int(11) DEFAULT NULL,
  `del_flg` int(11) DEFAULT NULL,
  `add_user_id` int(11) DEFAULT NULL,
  `add_datetime` date DEFAULT NULL,
  `upd_user_id` int(11) DEFAULT NULL,
  `upd_datetime` date DEFAULT NULL,
  PRIMARY KEY (`phongban_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of phong_ban
-- ----------------------------
INSERT INTO `phong_ban` VALUES ('1', 'Phong ban 1', '1', '0', null, null, null, null);
INSERT INTO `phong_ban` VALUES ('2', 'Phong ban 2', '1', '0', null, null, null, null);
INSERT INTO `phong_ban` VALUES ('3', 'Phong ban 3', '1', '0', null, null, null, null);
INSERT INTO `phong_ban` VALUES ('4', 'Phong ban 4', '1', '0', null, null, null, null);
INSERT INTO `phong_ban` VALUES ('5', 'Phong ban 6', '2', '0', '1', '2016-02-12', '1', '2016-02-12');

-- ----------------------------
-- Table structure for `to_nhom`
-- ----------------------------
DROP TABLE IF EXISTS `to_nhom`;
CREATE TABLE `to_nhom` (
  `tonhom_id` int(11) NOT NULL AUTO_INCREMENT,
  `tonhom_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phongban_id` int(11) DEFAULT NULL,
  `donvi_id` int(11) DEFAULT NULL,
  `del_flg` int(1) DEFAULT NULL,
  `add_user_id` int(11) DEFAULT NULL,
  `add_datetime` date DEFAULT NULL,
  `upd_user_id` int(11) DEFAULT NULL,
  `upd_datetime` date DEFAULT NULL,
  PRIMARY KEY (`tonhom_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of to_nhom
-- ----------------------------
INSERT INTO `to_nhom` VALUES ('1', 'To 1', '1', '1', '0', '10', '2016-01-30', '10', '2016-01-30');
INSERT INTO `to_nhom` VALUES ('2', 'To 2', '1', '1', '0', '1', '2016-02-16', '1', '2016-02-16');
INSERT INTO `to_nhom` VALUES ('3', 'To 21', '2', '1', '0', '1', '2016-02-16', '1', '2016-02-16');

-- ----------------------------
-- Procedure structure for `pro_get_allchild`
-- ----------------------------
DROP PROCEDURE IF EXISTS `pro_get_allchild`;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `pro_get_allchild`(IN `p_folder_id` int)
BEGIN
	#Routine body goes here...
	select  folder_id,
        folder_name,
        parent_folder_id 
	from    (select * from folder
					 order by parent_folder_id, folder_id) products_sorted,
					(select @pv := p_folder_id) initialisation
	where   find_in_set(parent_folder_id, @pv) > 0
	and     @pv := concat(@pv, ',', folder_id);
END
;;
DELIMITER ;

-- ----------------------------
-- Function structure for `f_get_allfolder_child`
-- ----------------------------
DROP FUNCTION IF EXISTS `f_get_allfolder_child`;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `f_get_allfolder_child`(`pa_user_id` int) RETURNS int(11)
BEGIN
	DECLARE v_folder_id int;
	DECLARE done INT DEFAULT FALSE;
	DECLARE C_PHANQUYEN CURSOR FOR select folder_id from phanquyen p
		INNER JOIN m_user u on u.phong_ban = p.phongban_id and COALESCE(u.to_nhom,0)= COALESCE(p.tonhom_id,0)
		where user_id =pa_user_id;
	
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
	CREATE TEMPORARY TABLE IF NOT EXISTS temp_folder as select folder_id,
							folder_name,
							parent_folder_id  from folder where 0=1;
  delete from temp_folder;
	open C_PHANQUYEN;
	read_loop: LOOP
		FETCH C_PHANQUYEN into v_folder_id;
		IF done THEN
			LEAVE read_loop;
		end if;
		insert into temp_folder 
		select  folder_id,
					folder_name,
					parent_folder_id 
		from folder where folder_id = v_folder_id
		union
		select  folder_id,
								folder_name,
								parent_folder_id 
		from    (select * from folder
								 order by parent_folder_id, folder_id) products_sorted,
								(select @pv := CONCAT(v_folder_id,'')) initialisation
		where   find_in_set(parent_folder_id, @pv) > 0
				and     @pv := concat(@pv, ',', folder_id);
	end LOOP;
	close C_PHANQUYEN;
	RETURN 0;
END
;;
DELIMITER ;

-- ----------------------------
-- Function structure for `f_get_all_banve`
-- ----------------------------
DROP FUNCTION IF EXISTS `f_get_all_banve`;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `f_get_all_banve`(pa_banve_no text ,pa_banve_name text) RETURNS int(11)
BEGIN
	DECLARE v_count_parent int DEFAULT 1;
	CREATE TEMPORARY TABLE IF NOT EXISTS temp_banve  (banve_id int,chk_mk int,parent_id int,chk_parent int);
	
	insert into temp_banve
	select banve_id,1,parent_id,1
	from banve
	where del_flg = 0
	and banve_no like  pa_banve_no
	and lower(banve_name) like  lower(pa_banve_name) ;
	-- GET ALL PARENT
	while v_count_parent > 0 do
	
	     select count(tem.parent_id) into v_count_parent
			from temp_banve tem 
			inner join banve head on tem.parent_id = head.banve_id
			where  tem.parent_id <> 1 and 
						tem.chk_parent = 1;

	     if v_count_parent > 0 then 
				 create TEMPORARY table temp_banve1
				 select * from qlfile.temp_banve;	

				 insert into temp_banve
		     select DISTINCT tem.parent_id,3,head.parent_id,0 
				 from temp_banve1 tem 
				 inner join banve head on tem.parent_id = head.banve_id
				 where  tem.parent_id <> 1 and 
					tem.chk_parent = 1;

		     update temp_banve set chk_parent =2 where chk_parent=1;
		     update temp_banve set chk_parent =1 where chk_parent=0;
				 drop TEMPORARY table temp_banve1;
	     end if;	    
	end while ;
  insert into temp_banve
	select  banve_id,1,1,1
              from    banve,
                    			(select @pv := (select group_concat(banve_id separator ',') 
                    			 from banve
                    			 where del_flg = 0
                    			 and banve_no like  pa_banve_no
													 and lower(banve_name) like  lower(pa_banve_name) )
                    			) initialisation
                    where   find_in_set(parent_id, @pv) > 0
                    and     @pv := concat(@pv, ',', banve_id);
	RETURN 0;
END
;;
DELIMITER ;

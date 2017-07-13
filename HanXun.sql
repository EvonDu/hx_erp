-- phpMyAdmin SQL Dump
-- version 4.7.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: 2017-07-10 21:52:05
-- 服务器版本： 5.7.18
-- PHP Version: 7.0.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT = @@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS = @@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION = @@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `HanXun`
--

-- --------------------------------------------------------

--
-- 表的结构 `t_authority`
--

CREATE TABLE `t_authority` (
  `Fid`          INT(11)     NOT NULL,
  `Fauth`        VARCHAR(20) NOT NULL DEFAULT '',
  `Fauth_name`   VARCHAR(20) NOT NULL DEFAULT '',
  `Fmemo`        VARCHAR(20) NOT NULL,
  `Fpid`         INT(11)     NOT NULL DEFAULT '0',
  `Fstatus`      INT(11)     NOT NULL DEFAULT '1',
  `Fversion`     INT(11)     NOT NULL DEFAULT '0',
  `Fcreate_time` DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Fmodify_time` DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

--
-- 转存表中的数据 `t_authority`
--

INSERT INTO `t_authority` (`Fid`, `Fauth`, `Fauth_name`, `Fmemo`, `Fpid`, `Fstatus`, `Fversion`, `Fcreate_time`, `Fmodify_time`)
VALUES
  (1, '1', '管理员权限', '全部权限', 0, 1, 0, '2017-07-08 15:05:00', '2017-07-08 15:06:04'),
  (2, '2000', '查看商品管理模块', '', 1, 1, 0, '2017-07-08 10:25:55', '2017-07-09 14:33:15'),
  (3, '3000', '查看仓库管理模块', '', 1, 1, 0, '2017-07-08 10:26:38', '2017-07-09 14:33:22'),
  (4, '4000', '查看财务管理模块', '', 1, 1, 0, '2017-07-08 10:26:38', '2017-07-09 14:33:29'),
  (5, '5000', '查看生产管理模块', '', 1, 1, 0, '2017-07-08 10:27:25', '2017-07-09 14:33:37'),
  (6, '6000', '查看系统管理模块', '', 1, 1, 0, '2017-07-08 10:27:25', '2017-07-09 14:33:43'),
  (8, '1000', '查看销售管理模块', '销售模块', 1, 1, 0, '2017-07-08 10:25:55', '2017-07-09 14:33:52');

-- --------------------------------------------------------

--
-- 表的结构 `t_role`
--

CREATE TABLE `t_role` (
  `Fid`          INT(11)     NOT NULL,
  `Fpid`         INT(11)     NOT NULL DEFAULT '0',
  `Fsegment`     VARCHAR(20) NOT NULL DEFAULT '',
  `Frole_name`   VARCHAR(20) NOT NULL DEFAULT '',
  `Fmemo`        VARCHAR(20) NOT NULL,
  `Fversion`     INT(11)     NOT NULL DEFAULT '0',
  `Fstatus`      TINYINT(1)  NOT NULL DEFAULT '1',
  `Fcreate_time` DATETIME             DEFAULT CURRENT_TIMESTAMP,
  `Fmodify_time` DATETIME             DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

--
-- 转存表中的数据 `t_role`
--

INSERT INTO `t_role` (`Fid`, `Fpid`, `Fsegment`, `Frole_name`, `Fmemo`, `Fversion`, `Fstatus`, `Fcreate_time`, `Fmodify_time`)
VALUES
  (1, 0, '', '管理员', '', 0, 1, '2017-07-02 15:03:21', '2017-07-02 15:06:33'),
  (18, 1, '', '仓库管理员', '', 0, 1, '2017-07-09 14:28:24', '2017-07-09 14:28:24'),
  (20, 1, '', '财务员', '', 0, 1, '2017-07-09 14:32:05', '2017-07-09 14:32:05'),
  (21, 1, '', '生产管理', '', 0, 1, '2017-07-09 14:32:39', '2017-07-09 14:32:39'),
  (22, 1, '', '销售员', '', 0, 1, '2017-07-09 14:43:30', '2017-07-09 14:43:30');

-- --------------------------------------------------------

--
-- 表的结构 `t_role_authority`
--

CREATE TABLE `t_role_authority` (
  `Fid`          INT(11)    NOT NULL,
  `Frole_id`     INT(11)    NOT NULL,
  `Fauth_id`     INT(11)    NOT NULL,
  `Fstatus`      TINYINT(1) NOT NULL DEFAULT '1',
  `Fversion`     INT(11)    NOT NULL DEFAULT '0',
  `Fcreate_time` DATETIME   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Fmodify_time` DATETIME   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

--
-- 转存表中的数据 `t_role_authority`
--

INSERT INTO `t_role_authority` (`Fid`, `Frole_id`, `Fauth_id`, `Fstatus`, `Fversion`, `Fcreate_time`, `Fmodify_time`)
VALUES
  (2, 1, 1, 1, 0, '2017-07-08 15:06:43', '2017-07-08 15:06:43'),
  (6, 18, 3, 1, 0, '2017-07-09 14:28:24', '2017-07-09 14:28:24'),
  (8, 20, 4, 1, 0, '2017-07-09 14:32:05', '2017-07-09 14:32:05'),
  (9, 21, 5, 1, 0, '2017-07-09 14:32:39', '2017-07-09 14:32:39'),
  (10, 22, 8, 1, 0, '2017-07-09 14:43:30', '2017-07-09 14:43:30');

-- --------------------------------------------------------

--
-- 表的结构 `t_user`
--

CREATE TABLE `t_user` (
  `Fuid`         INT(11) UNSIGNED NOT NULL,
  `Fname`        VARCHAR(20)      NOT NULL DEFAULT '',
  `Fmobile`      VARCHAR(20)      NOT NULL DEFAULT '',
  `Femail`       VARCHAR(50)      NOT NULL DEFAULT '',
  `Fpassword`    VARCHAR(50)      NOT NULL DEFAULT '',
  `Frole_id`     INT(11)          NOT NULL DEFAULT '0',
  `Fmemo`        VARCHAR(100)              DEFAULT NULL,
  `Fversion`     INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `Fstatus`      TINYINT(1)       NOT NULL DEFAULT '1',
  `Fcreate_time` DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Fmodify_time` DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

--
-- 转存表中的数据 `t_user`
--

INSERT INTO `t_user` (`Fuid`, `Fname`, `Fmobile`, `Femail`, `Fpassword`, `Frole_id`, `Fmemo`, `Fversion`, `Fstatus`, `Fcreate_time`, `Fmodify_time`)
VALUES
  (1, 'root', '18565884265', '381090826@qq.com', '96e79218965eb72c92a549dd5a330112', 1, NULL, 0, 1,
      '2017-06-25 22:14:21', '2017-07-08 15:09:47'),
  (3, 'gavinlu', '18565884261', '11111@QQ.COM', '698d51a19d8a121ce581499d7b701668', 1, NULL, 0, 1,
      '2017-07-02 15:40:12', '2017-07-07 22:44:12'),
  (14, 'qqq', '18565884264', '11111@QQ.COM', 'b59c67bf196a4758191e42f76670ceba', 1, '', 0, 1, '2017-07-02 16:33:32',
       '2017-07-07 22:44:19'),
  (18, 'test销售', '18666666666', '11111@QQ.COM', '96e79218965eb72c92a549dd5a330112', 21, '', 0, 1, '2017-07-09 14:34:24',
       '2017-07-09 14:34:24'),
  (19, '测试销售', '18777777777', '11111@QQ.COM', '96e79218965eb72c92a549dd5a330112', 22, '', 0, 1, '2017-07-09 14:44:00',
       '2017-07-09 14:44:00'),
  (20, '吴树豪', '15920328339', '11111@QQ.COM', '96e79218965eb72c92a549dd5a330112', 1, '', 0, 1, '2017-07-09 14:55:52',
       '2017-07-09 14:55:52');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `t_authority`
--
ALTER TABLE `t_authority`
  ADD PRIMARY KEY (`Fid`),
  ADD UNIQUE KEY `auth` (`Fauth`);

--
-- Indexes for table `t_role`
--
ALTER TABLE `t_role`
  ADD PRIMARY KEY (`Fid`);

--
-- Indexes for table `t_role_authority`
--
ALTER TABLE `t_role_authority`
  ADD PRIMARY KEY (`Fid`);

--
-- Indexes for table `t_user`
--
ALTER TABLE `t_user`
  ADD PRIMARY KEY (`Fuid`),
  ADD UNIQUE KEY `mobile` (`Fmobile`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `t_authority`
--
ALTER TABLE `t_authority`
  MODIFY `Fid` INT(11) NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 10;
--
-- 使用表AUTO_INCREMENT `t_role`
--
ALTER TABLE `t_role`
  MODIFY `Fid` INT(11) NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 23;
--
-- 使用表AUTO_INCREMENT `t_role_authority`
--
ALTER TABLE `t_role_authority`
  MODIFY `Fid` INT(11) NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 11;
--
-- 使用表AUTO_INCREMENT `t_user`
--
ALTER TABLE `t_user`
  MODIFY `Fuid` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 21;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT = @OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS = @OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION = @OLD_COLLATION_CONNECTION */;


/*后续加入*/
CREATE TABLE `t_category` (
  `Fid`            INT(11)     NOT NULL,
  `Fpid`           INT(11)     NOT NULL DEFAULT '0',
  `Fcategory_name` VARCHAR(20) NOT NULL DEFAULT '',
  `Fmemo`          VARCHAR(20) NOT NULL DEFAULT '',
  `Fversion`       INT(11)     NOT NULL DEFAULT '0',
  `Fstatus`        INT(11)     NOT NULL DEFAULT '1',
  `Fop_uid`        INT(11)     NOT NULL DEFAULT '0',
  `Fcreate_time`   DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Fmodify_time`   DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `t_category`
--
ALTER TABLE `t_category`
  ADD PRIMARY KEY (`Fid`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `t_category`
--
ALTER TABLE `t_category`
  MODIFY `Fid` INT(11) NOT NULL AUTO_INCREMENT;


CREATE TABLE `HanXun`.`t_category_detail` (
  `Fid`          INT                                  NOT NULL AUTO_INCREMENT,
  `Fcategory_id` INT                                  NOT NULL DEFAULT '0',
  `Fvalue`       VARCHAR(20)                          NOT NULL DEFAULT '',
  `Fmemo`        VARCHAR(20)                          NOT NULL DEFAULT '',
  `Fversion`     INT                                  NOT NULL DEFAULT '0',
  `Fstatus`      INT                                  NOT NULL DEFAULT '1',
  `Fop_uid`      INT                                  NOT NULL DEFAULT '0',
  `Fcreate_time` DATETIME                             NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Fmodify_time` DATETIME ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Fid`)
)
  ENGINE = InnoDB;

CREATE TABLE `HanXun`.`t_goods` (
  `Fid`          INT                                  NOT NULL AUTO_INCREMENT,
  `Fname`        VARCHAR(20)                          NOT NULL DEFAULT '',
  `Fsku_id`      INT                                  NOT NULL DEFAULT '0',
  `Fmemo`        VARCHAR(20)                          NOT NULL DEFAULT '',
  `Fversion`     INT                                  NOT NULL DEFAULT '0',
  `Fstatus`      BOOLEAN                              NOT NULL DEFAULT TRUE,
  `Fop_uid`      INT                                  NOT NULL DEFAULT '0',
  `Fcreate_time` DATETIME                             NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Fmodify_time` DATETIME ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Fid`)
)
  ENGINE = InnoDB;

-- --------------------------------------------------------

--
-- 表的结构 `t_sku`
--

CREATE TABLE `t_sku` (
  `Fid`             INT(11)        NOT NULL,
  `Fgoods_id`       VARCHAR(20)    NOT NULL DEFAULT '0',
  `Fsku_id`         VARCHAR(20)    NOT NULL DEFAULT '',
  `Fname`           VARCHAR(20)    NOT NULL DEFAULT ''
  COMMENT '商品名',
  `Fproduct_number` VARCHAR(20)    NOT NULL DEFAULT ''
  COMMENT '货号',
  `Fbar_code`       VARCHAR(20)    NOT NULL DEFAULT ''
  COMMENT '条形码',
  `Frecord_number`  VARCHAR(20)    NOT NULL DEFAULT ''
  COMMENT '备案号',
  `Fbrand_id`       INT(11)        NOT NULL DEFAULT '0'
  COMMENT '品牌',
  `Fcategory_id`    INT(11)        NOT NULL DEFAULT '0'
  COMMENT '商品类别',
  `Fproperty_id`    INT(11)        NOT NULL DEFAULT '0'
  COMMENT '商品属性',
  `Fprice`          DECIMAL(10, 0) NOT NULL DEFAULT '0'
  COMMENT '市场价',
  `Fsource_area`    VARCHAR(20)    NOT NULL DEFAULT ''
  COMMENT '原产地',
  `Fimport`         TINYINT(1)     NOT NULL DEFAULT '0'
  COMMENT '进口',
  `Funit`           VARCHAR(5)     NOT NULL DEFAULT ''
  COMMENT '单位',
  `Fweight`         VARCHAR(10)    NOT NULL DEFAULT ''
  COMMENT '重量',
  `Fpic`            VARCHAR(20)    NOT NULL DEFAULT ''
  COMMENT '小图',
  `Fcolor_id`       INT(11)        NOT NULL DEFAULT '0',
  `Fsize_id`        INT(11)        NOT NULL DEFAULT '0'
  COMMENT '规格',
  `Fmemo`           VARCHAR(20)    NOT NULL DEFAULT '',
  `Fversion`        INT(11)        NOT NULL DEFAULT '0',
  `Fstatus`         TINYINT(1)     NOT NULL DEFAULT '1',
  `Fcreate_time`    DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Fmodify_time`    DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `t_sku`
--
ALTER TABLE `t_sku`
  ADD PRIMARY KEY (`Fid`),
  ADD UNIQUE KEY `sku_id` (`Fsku_id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `t_sku`
--
ALTER TABLE `t_sku`
  MODIFY `Fid` INT(11) NOT NULL AUTO_INCREMENT;
COMMIT;


CREATE TABLE `HanXun`.`t_color` (
  `Fid`          INT                                  NOT NULL AUTO_INCREMENT,
  `Fname`        VARCHAR(5)                           NOT NULL DEFAULT '',
  `Fcolor_num`   VARCHAR(5)                           NOT NULL DEFAULT '',
  `Fcolor_code`  VARCHAR(10)                          NOT NULL DEFAULT '',
  `Fversion`     INT                                  NOT NULL DEFAULT '0',
  `Fstatus`      BOOLEAN                              NOT NULL DEFAULT TRUE,
  `Fop_uid`      INT                                  NOT NULL DEFAULT '0',
  `Fmemo`        VARCHAR(20)                          NOT NULL DEFAULT '',
  `Fcreate_time` DATETIME                             NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Fmodify_time` DATETIME ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Fid`),
  UNIQUE `color_num` (`Fcolor_num`)
)
  ENGINE = InnoDB;
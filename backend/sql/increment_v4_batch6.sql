-- 批次6：活动·合成（P043-P046）2026-11-08
-- 3 张新表 + 原站合成主题/玩法种子数据（与原站当日快照一致）

CREATE TABLE ht_synthesis_theme (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  types INT NOT NULL DEFAULT 1,
  name VARCHAR(100) NOT NULL DEFAULT '',
  image VARCHAR(255) NOT NULL DEFAULT '',
  rule_text TEXT NULL,
  status TINYINT NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='合成主题';

CREATE TABLE ht_synthesis_play (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  theme_id INT NOT NULL DEFAULT 0,
  title VARCHAR(255) NOT NULL DEFAULT '',
  image VARCHAR(255) NOT NULL DEFAULT '',
  start_time BIGINT NOT NULL DEFAULT 0 COMMENT '毫秒',
  end_time BIGINT NOT NULL DEFAULT 0 COMMENT '毫秒',
  activity_id INT NOT NULL DEFAULT 0,
  description VARCHAR(255) NOT NULL DEFAULT '',
  is_auto TINYINT NOT NULL DEFAULT 0,
  is_delay TINYINT NOT NULL DEFAULT 0,
  is_advance TINYINT NOT NULL DEFAULT 0,
  advance_date INT NOT NULL DEFAULT 1,
  delay_date INT NOT NULL DEFAULT 1,
  real_start_time BIGINT NOT NULL DEFAULT 0,
  real_end_time BIGINT NOT NULL DEFAULT 0,
  stock INT DEFAULT NULL,
  classify VARCHAR(20) NOT NULL DEFAULT 'regular',
  type VARCHAR(10) NOT NULL DEFAULT '10',
  status TINYINT NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='合成活动(玩法)';

CREATE TABLE ht_synthesis_record (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL DEFAULT 0,
  play_id INT NOT NULL DEFAULT 0,
  consume TEXT NULL COMMENT '消耗材料json',
  result TEXT NULL COMMENT '合成结果json',
  method INT NOT NULL DEFAULT 10 COMMENT '10销毁20保留30回收',
  create_time VARCHAR(30) NOT NULL DEFAULT '',
  createtime BIGINT NOT NULL DEFAULT 0,
  status TINYINT NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='合成记录';

INSERT INTO ht_synthesis_theme (id,types,name,image,rule_text) VALUES (1,1,'1','1','1');
INSERT INTO ht_synthesis_play (id,theme_id,title,image,start_time,end_time,activity_id,description,real_start_time,real_end_time,stock,classify,type)
VALUES (11,1,'《今晚吃鸡》专属合成','/uploads/20261108/c125ef4fa80e2c6e6a3fe923af9d72b8.png',1794114000000,1794117600000,1,'《今晚吃鸡》专属合成',1794114000000,1794117600000,1,'regular','10');

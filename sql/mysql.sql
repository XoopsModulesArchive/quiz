CREATE TABLE question (
  id int(10) unsigned NOT NULL auto_increment,
  qid int(10) unsigned NOT NULL,
  question varchar(200) character set utf8 collate utf8_bin NOT NULL,
  qnumber int(10) unsigned NOT NULL ,
  score int(10) unsigned NOT NULL ,
  ans1 varchar(200) character set utf8 collate utf8_bin default NULL,
  ans2 varchar(200) character set utf8 collate utf8_bin default NULL,
  ans3 varchar(200) character set utf8 collate utf8_bin default NULL,
  ans4 varchar(200) character set utf8 collate utf8_bin default NULL,
  answer enum('1','2','3','4') NOT NULL,
  PRIMARY KEY  (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE quiz (
  id int(10) unsigned NOT NULL auto_increment,
  name varchar(45) character set utf8 collate utf8_bin NOT NULL,
  cid int(10) unsigned NOT NULL,
  description text character set utf8 collate utf8_bin,
  bdate datetime NOT NULL,
  edate datetime NOT NULL,
  weight int(11) default '0',
  PRIMARY KEY  (id)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

CREATE TABLE quiz_users (
  id int(10) unsigned NOT NULL,
  userid int(11) unsigned NOT NULL,
  score int(11) NOT NULL,
  date datetime NOT NULL,
  PRIMARY KEY  (id,userid)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE quiz_cat (
  cid int(11) unsigned NOT NULL auto_increment,
  pid int(11) unsigned NOT NULL default '0',
  title varchar(100) NOT NULL default '',
  imgurl varchar(255) NOT NULL default '',
  description text NOT NULL,
  weight int(11) NOT NULL default '0',
  PRIMARY KEY  (cid),
  KEY pid (pid)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


CREATE TABLE question_user (
  questId int(10) NOT NULL,
  quizId int(10) NOT NULL,
  userId int(11) NOT NULL,
  userAns enum('1','2','3','4') NOT NULL,
  PRIMARY KEY  (questId,quizId,userId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

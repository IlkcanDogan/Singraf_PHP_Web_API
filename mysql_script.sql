-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Anamakine: localhost:3306
-- Sunucu sürümü: 10.2.26-MariaDB
-- PHP Sürümü: 7.2.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;



DELIMITER $$
--
-- Yordamlar
--
CREATE DEFINER=`mysql`@`localhost` PROCEDURE `sp_ADMIN_BAN` (IN `p_N_NAME` VARCHAR(50), IN `p_BAN` BIT, INOUT `p_ERROR` TINYINT)  BEGIN
	if exists(select ID from tb_users where N_NAME = p_N_NAME) then
		update tb_users set BAN = p_BAN where N_NAME = p_N_NAME;
		set p_ERROR = 0;
	else 
		set p_ERROR = 1;
    end if;
END$$

CREATE DEFINER=`mysql`@`localhost` PROCEDURE `sp_ADMIN_BAN_READER` ()  BEGIN
	select * from tb_users where BAN = 1;
END$$

CREATE DEFINER=`mysql`@`localhost` PROCEDURE `sp_ADVERT_POST` (IN `p_MAIL` VARCHAR(40), IN `p_PASS` CHAR(32), IN `p_TOKEN` CHAR(6), IN `p_PRODUCT_NAME` VARCHAR(150), IN `p_COMMENT` VARCHAR(250), IN `p_ADRESS` VARCHAR(250), IN `p_TEL` CHAR(11), IN `p_IMAGE_NO_1` CHAR(6), IN `p_IMAGE_NO_2` CHAR(6), IN `p_IMAGE_NO_3` CHAR(6), IN `p_IP` CHAR(15))  BEGIN
declare p_ID int;
set p_ID = (select ID from tb_users where MAIL = p_MAIL and PASS = p_PASS and TOKEN = p_TOKEN);
	if(p_ID > 0) then
		insert into tb_advert(USER_ID,PRODUCT_NAME,COMMENT,ADRESS,TEL,IMAGE_NO_1,IMAGE_NO_2,IMAGE_NO_3,IP)
        VALUES(p_ID,p_PRODUCT_NAME,p_COMMENT,p_ADRESS,p_TEL,p_IMAGE_NO_1,p_IMAGE_NO_2,p_IMAGE_NO_3,p_IP);
        set @p_ERROR = 111;
    else 
		set @p_ERROR = 1;
    end if;
select @p_ERROR;
end$$

CREATE DEFINER=`mysql`@`localhost` PROCEDURE `sp_AGAIN_CODE_CHECK` (IN `p_N_NAME` VARCHAR(40), IN `p_CHECK_CODE` CHAR(6), OUT `p_ERROR` TINYINT)  BEGIN

    
	if exists (SELECT ID from tb_users WHERE N_NAME = p_N_NAME) then
		set @p_time = current_time(); 
		update tb_users set CHECK_CODE = p_CHECK_CODE, SEND_TIME = @p_time where N_NAME = p_N_NAME;
        set p_ERROR = 0;
	else
		set p_ERROR = 1;
    end if;
	
end$$

CREATE DEFINER=`mysql`@`localhost` PROCEDURE `sp_ALL_VIDEOS` (IN `p_START` INT)  BEGIN
if(p_START != 0) THEN
    select ID, 
    USER_ID,
    N_NAME,
    F_NAME,
    L_NAME,
    PIC_NO,
    REG_FRAME_ID,
    MUSIC_NAME,
    MELODY_NAME_SURNAME,
    LIKES,
    COM,
    DISLIKES,
    VIEWS,
    CATEGORY_ID,
    CATEGORY_NAME,
    VIDEO_NO,
    date_format(UPLOAD_DATE, '%d-%m-%Y %H:%i') AS UPLOAD_DATE
	from user_data where BAN = 0 and ID< p_START ORDER BY ID desc LIMIT 5;
    ELSE
    select ID, 
    USER_ID,
    N_NAME,
    F_NAME,
    L_NAME,
    PIC_NO,
    REG_FRAME_ID,
    MUSIC_NAME,
    MELODY_NAME_SURNAME,
    LIKES,
    COM,
    DISLIKES,
    VIEWS,
    CATEGORY_ID,
    CATEGORY_NAME,
    VIDEO_NO,
    date_format(UPLOAD_DATE, '%d-%m-%Y %H:%i') AS UPLOAD_DATE
	from user_data where BAN = 0 ORDER BY ID desc LIMIT 5;
    end if;
end$$

CREATE DEFINER=`mysql`@`localhost` PROCEDURE `sp_CATEGORY_GET` ()  BEGIN

select * from tb_music_category;


END$$

CREATE DEFINER=`mysql`@`localhost` PROCEDURE `sp_CHECK` (IN `p_N_NAME` VARCHAR(40), OUT `p_ERROR` BIT)  BEGIN

    
	if exists (SELECT ID from tb_users WHERE N_NAME = p_N_NAME) then
		update `tb_users` set `CHECK_CODE` = null, `ACTIVE` = 1 where N_NAME = p_N_NAME;
        set p_ERROR = 0;
	else
		set p_ERROR = 1;
    end if;
	
end$$

CREATE DEFINER=`mysql`@`localhost` PROCEDURE `sp_CODE_GET` (IN `p_N_NAME` VARCHAR(40), OUT `p_CHECK_CODE` CHAR(6))  BEGIN

    
	if exists (SELECT ID from tb_users WHERE N_NAME = p_N_NAME) then
        set @p_ACTIVE = (select ACTIVE from tb_users WHERE N_NAME = p_N_NAME);
        if(@p_ACTIVE != 1) THEN
        	set p_CHECK_CODE = (select CHECK_CODE from tb_users where N_NAME = p_N_NAME);
        ELSE
        	set p_CHECK_CODE = 2;
        end if;			
    else
    	set p_CHECK_CODE = 1;
    end if;
	
end$$

CREATE DEFINER=`mysql`@`localhost` PROCEDURE `sp_COIN` (IN `p_MAIL` VARCHAR(50), IN `p_PASS` CHAR(32), INOUT `p_ERROR` TINYINT)  BEGIN
set p_ERROR = 0;
set @COIN = (select COIN from tb_users where MAIL = p_MAIL and PASS = p_PASS);
	if (@COIN is not null) then
		update tb_users set COIN = (@COIN + 1) where MAIL = p_MAIL;
 
 
 
    else
		set  p_ERROR = 1;
    end if;







END$$

CREATE DEFINER=`mysql`@`localhost` PROCEDURE `sp_COIN_REWARD` (IN `p_MAIL` VARCHAR(50) CHARSET utf8, IN `p_PASS` VARCHAR(32) CHARSET utf8)  BEGIN
set @p_ERROR = 111;

if exists (select ID from tb_users where MAIL = p_MAIL and PASS = p_PASS) then
	set @coin_h = (select COIN_H from tb_users where MAIL = p_MAIL and PASS = p_PASS);
	if (@coin_h > 0) then
		update tb_users SET COIN_H = (@coin_h - 1) where MAIL = p_MAIL;
        
        set @co = (select COIN from tb_users where MAIL = p_MAIL);
        update tb_users SET COIN = (@co + 2) where MAIL = p_MAIL;
	else
		set @p_ERROR = 2; -- Hak bitti.
    end if;
else
	set @p_ERROR = 3; -- mail ve pass hatası.
end if;

select @p_ERROR;
END$$

CREATE DEFINER=`mysql`@`localhost` PROCEDURE `sp_COMMENT_EDIT` (IN `p_MAIL` VARCHAR(50) CHARSET utf8, IN `p_PASS` CHAR(32) CHARSET utf8, IN `p_COMMENT_ID` INT, IN `p_COMMENT_DEL` CHAR CHARSET utf8, IN `p_NEW_COMMENT` VARCHAR(250) CHARSET utf8)  BEGIN
declare p_ID int;
declare p_MUSIC_ID int;
set @p_ERROR = 9898;
set p_ID = (select ID from tb_users where MAIL = p_MAIL and PASS = p_PASS);
if (p_ID > 0) then
	set p_MUSIC_ID = (select MUSIC_ID from tb_comments where ID = p_COMMENT_ID);
    set @a = (select ID from tb_music where ID = p_MUSIC_ID and USER_ID = p_ID);
	if exists (select ID from tb_comments where USER_ID = p_ID and ID = p_COMMENT_ID || @a > 0) then
    
		if (p_COMMENT_DEL = 1) then
			delete from tb_comments where ID = p_COMMENT_ID;     
            set @COM_COUNT = 0;
            set @p_ERROR = 0;
		elseif (p_NEW_COMMENT != "X") then
			update tb_comments set COMMENT = p_NEW_COMMENT where ID = p_COMMENT_ID;
			set @p_ERROR = 0;
        end if;
        
	else
		set @p_ERROR = 2; -- kullanıcının boyle bir yorumu yok.
    end if;
else
	set @p_ERROR = 1; -- kullanıcı bilgi hatası.
end if;
select @p_ERROR;
END$$

CREATE DEFINER=`mysql`@`localhost` PROCEDURE `sp_COMMENT_GET` (IN `p_MAIL` VARCHAR(50), IN `p_PASS` CHAR(32), IN `p_MUSIC_ID` INT, IN `p_START` INT)  BEGIN
declare p_ID int;
declare p_M_ID int;
set p_ID = (select ID from tb_users where MAIL = p_MAIL and PASS = p_PASS);
if (p_ID > 0) then
	set p_M_ID = (select ID from tb_music where ID = p_MUSIC_ID);
	if(p_START != 0) then
		if (p_M_ID >0) then
			select
				COMMENT_ID,
				USER_ID,
				N_NAME,
				F_NAME,
				L_NAME,
				PIC_NO,
				REG_FRAME_ID,
				COMMENT,
				date_format(DATE, '%d-%m-%Y') as DATE
			from comments_data where MUSIC_ID = p_MUSIC_ID and COMMENT_ID < p_START ORDER BY COMMENT_ID desc LIMIT 5;
		else
			 set @p_ERROR = 1; -- video  yok
		end if;
	else
		if (p_M_ID >0) then
			select
				COMMENT_ID,
				USER_ID,
				N_NAME,
				F_NAME,
				L_NAME,
				PIC_NO,
				REG_FRAME_ID,
				COMMENT,
				date_format(DATE, '%d-%m-%Y') as DATE
			from comments_data where MUSIC_ID = p_MUSIC_ID ORDER BY COMMENT_ID desc LIMIT 5;
		else
			 set @p_ERROR = 1; -- video  yok
		end if;
    end if;
end if;
-- select @p_ERROR;
END$$

CREATE DEFINER=`mysql`@`localhost` PROCEDURE `sp_COMMENT_POST` (IN `p_MAIL` VARCHAR(50), IN `p_PASS` CHAR(32), IN `p_MUSIC_ID` INT, IN `p_COMMENT` VARCHAR(250), IN `p_IP` CHAR(15), IN `cmN_NAME` VARCHAR(40) CHARSET utf8, OUT `p_PLAYER_ID` CHAR(36) CHARSET utf8, OUT `p_N_NAME` VARCHAR(40) CHARSET utf8, OUT `p_ERROR` SMALLINT, OUT `cmPLAYER_ID` CHAR(36))  BEGIN
declare p_ID int;
set p_ID = (select ID from tb_users where MAIL = p_MAIL and PASS = p_PASS);
	if (p_ID > 0) then
		if exists(select ID from tb_music where ID = p_MUSIC_ID) then
			insert into tb_comments(USER_ID,MUSIC_ID,COMMENT,IP)
            values(p_ID,p_MUSIC_ID,p_COMMENT,p_IP);
            set @MUSIC_USER = (select USER_ID from tb_music where ID = p_MUSIC_ID);
            
       		set p_N_NAME = (select N_NAME from tb_users where ID = p_ID);
        	if(@MUSIC_USER != p_ID) THEN
        		call sp_PUSH_SEND(@MUSIC_USER,p_PLAYER_ID);
        	end if;
            
            set @cmUSER_ID = (SELECT ID from tb_users where N_NAME = cmN_NAME);
            call sp_PUSH_SEND(@cmUSER_ID,cmPLAYER_ID);
            
            
        else
			set p_ERROR = 2; -- Bu ID de bir müzik yok.
        end if;
    else
		set p_ERROR = 1; -- Token hatası.
    end if;

END$$

CREATE DEFINER=`mysql`@`localhost` PROCEDURE `sp_DISLIKE` (IN `p_MAIL` VARCHAR(40) CHARSET utf8, IN `p_PASS` CHAR(32) CHARSET utf8, IN `p_MUSIC_ID` INT, IN `p_DISLIKE` CHAR(1), OUT `p_ERROR` TINYINT)  BEGIN
	SET p_ERROR = 111;
if exists (select ID from tb_music where ID = p_MUSIC_ID) then
	set @ID = (select ID from tb_users where BAN = 0 and MAIL= p_MAIL and PASS=p_PASS);
    if(@ID > 0) then
    
		 if(p_DISLIKE = 0) then
			if exists (select ID from tb_likes where USER_ID=@ID and MUSIC_ID = p_MUSIC_ID) then
            
				delete from tb_likes where USER_ID = @ID and MUSIC_ID=p_MUSIC_ID;
			else 
				set p_ERROR = 7; -- zaten beğenmeyi geri almış.
			end if;
		end if;
		-- --------------------------------------------------------------------------------------
	else
		set p_ERROR = 1;
    end if;
else
	set p_ERROR = 5;
end if;
end$$

CREATE DEFINER=`mysql`@`localhost` PROCEDURE `sp_FIRSTS` ()  BEGIN
	select 
	USER_ID,
	N_NAME,
	F_NAME,
	L_NAME,
	PIC_NO,
	REG_FRAME_ID,
	MUSIC_NAME,
	MELODY_NAME_SURNAME,
	LIKES,
	VIEWS,
	CATEGORY_ID,
    CATEGORY_NAME,
	VIDEO_NO,
	date_format(UPLOAD_DATE, '%d-%m-%Y') as UPLOAD_DATE 
	from user_data where VIDEO_NO in (select VIDEO_NO from tb_firsts) group by CATEGORY_ID desc;
END$$

CREATE DEFINER=`mysql`@`localhost` PROCEDURE `sp_FORGET_PASS` (IN `p_MAIL` VARCHAR(50), IN `p_CHECK_CODE` CHAR(6))  BEGIN
	if exists(select ID from tb_users where MAIL = p_MAIL and ACTIVE = 1) then
		update tb_users set CHECK_CODE = p_CHECK_CODE where MAIL = p_MAIL;
        
        set @p_ERROR = 111; -- islem basarili.
    else
		set @p_ERROR = 1; -- mail adresi bulunamadı.
    end if;
select @p_ERROR;
end$$

CREATE DEFINER=`mysql`@`localhost` PROCEDURE `sp_FORGET_PASS_CHECK` (IN `p_MAIL` VARCHAR(40), IN `p_PASS` CHAR(32))  BEGIN
	if exists(select ID from tb_users where MAIL = p_MAIL) then
		update tb_users set CHECK_CODE = null where MAIL = p_MAIL;
        update tb_users set PASS = p_PASS where MAIL = p_MAIL;
        set @p_ERROR = 111; -- işlem başarılı.
    else
		set @p_ERROR = 1; -- Mail hesabi yok.
    end if;
select @p_ERROR;
END$$

CREATE DEFINER=`mysql`@`localhost` PROCEDURE `sp_FORGET_PASS_CODE_GET` (IN `p_MAIL` VARCHAR(40) CHARSET utf8)  BEGIN
	if exists (select ID from tb_users where MAIL = p_MAIL) then
		if exists (select CHECK_CODE from tb_users where MAIL = p_MAIL) then
			set @p_ERROR = (select CHECK_CODE from tb_users where MAIL = p_MAIL);
		else
			set @p_ERROR = 2; -- check_code null olduğu için zaten şifre değişmiş.
        end if;
    else
		set @p_ERROR = 1; -- Kullanıcı yok.
    end if;
select @p_ERROR;
end$$

CREATE DEFINER=`mysql`@`localhost` PROCEDURE `sp_FRAME_BUY` (IN `p_MAIL` VARCHAR(50), IN `p_PASS` CHAR(32), IN `p_TOKEN` CHAR(6), IN `p_FRAME_ID` TINYINT)  BEGIN
	set @ID = (select ID from tb_users where MAIL=p_MAIL and PASS = p_PASS and TOKEN = p_TOKEN);
    if (@ID is not null) then
		if exists(select ID from tb_frame_type where ID = p_FRAME_ID) then
			if not exists(select ID from tb_user_frame where USER_ID=@ID and FRAME_ID=p_FRAME_ID) then
				set @COIN = (select COIN from tb_users where ID=@ID);
				if(@COIN >= 600) then
					update tb_users set COIN = (@COIN - 600) where ID=@ID;
					insert into tb_user_frame (USER_ID,FRAME_ID) values(@ID,p_FRAME_ID); 
					set @p_ERROR = (select COIN from tb_users where ID = @ID);
				else 
					set @p_ERROR = 4; -- Coin hatası.
				end if;
			else 
				set @p_ERROR = 3; -- çerçeve zaten alınmış.
			end if;
        else
			set @p_ERROR = 2; -- tanımlı çerçeve yok.
        end if;
    else
		set @p_ERROR = 1; -- token hatası
    end if;
select @p_ERROR;
END$$

CREATE DEFINER=`mysql`@`localhost` PROCEDURE `sp_FRAME_GET` ()  BEGIN
	select * from tb_frame_type;
END$$

CREATE DEFINER=`mysql`@`localhost` PROCEDURE `sp_LOGIN` (IN `p_MAIL` VARCHAR(50) CHARSET utf8, IN `p_PASS` CHAR(32) CHARSET utf8, IN `p_UUID` CHAR(16) CHARSET utf8)  BEGIN
if not exists (SELECT ID from tb_users WHERE MAIL = p_MAIL) then
		set @p_ERROR = 1;
    else
		if not exists(SELECT ID from tb_users WHERE  PASS = p_PASS and MAIL = p_MAIL) then
			set @p_ERROR = 2;
        else			
			if((select BAN from tb_users where MAIL = p_MAIL) = 0) then
				if((select ACTIVE from tb_users where MAIL = p_MAIL) = 1)then 
                
					if exists(SELECT ID from tb_users WHERE UUID = p_UUID and MAIL = p_MAIL) then
						set @p_ERROR = 111;
				
							select 
                            ID,
							N_NAME, 
							F_NAME, 
							L_NAME,
							MAIL,
							CITY,
							date_format(B_DATE, '%d-%m-%Y') as B_DATE,
							SCORE,
							BIO,
							COIN,
							PIC_NO,
							REG_FRAME_ID from tb_users where MAIL = p_MAIL;
					else
                    	if ((SELECT UUID from tb_users where UUID = "0" and MAIL = p_MAIL) = "0") THEN
                        	update tb_users set UUID = p_UUID where MAIL = p_MAIL;
							set @p_ERROR = 111;
				
							select 
                            ID,
							N_NAME, 
							F_NAME, 
							L_NAME,
							MAIL,
							CITY,
							date_format(B_DATE, '%d-%m-%Y') as B_DATE,
							SCORE,
							BIO,
							COIN,
							PIC_NO,
							REG_FRAME_ID from tb_users where MAIL = p_MAIL;
                        ELSE
                        	set @p_ERROR = 5;
                        end if;
                        
					end if;
                else
					set @p_ERROR = 4;
                end if;
			else
				set @p_ERROR = 3;
			end if;
        end if;
    end if;
   select @p_ERROR;
end$$

CREATE DEFINER=`mysql`@`localhost` PROCEDURE `sp_LOGOUT` (IN `p_MAIL` VARCHAR(50), IN `p_PASS` CHAR(32), OUT `p_ERROR` BIT)  BEGIN
	if not exists(select ID from tb_users where MAIL = p_MAIL and PASS = p_PASS) then
		set p_ERROR = 1;
	else
		update tb_users set UUID = 0 where MAIL = p_MAIL;
       	set @uid = (select ID from tb_users where MAIL = p_MAIL);
        delete from tb_push WHERE USER_ID = @uid; 
        set p_ERROR = 0;
    end if;
end$$

CREATE DEFINER=`mysql`@`localhost` PROCEDURE `sp_MESSAGE_TOKEN` (IN `p_MAIL` VARCHAR(50) CHARSET utf8, IN `p_PASS` CHAR(32) CHARSET utf8)  BEGIN
	select ID,N_NAME,PIC_NO from tb_users where MAIL = p_MAIL and PASS = p_PASS; 
END$$

CREATE DEFINER=`mysql`@`localhost` PROCEDURE `sp_MUSIC_REPORTS` (IN `p_MAIL` VARCHAR(50), IN `p_PASS` CHAR(32), IN `p_MUSIC_ID` INT)  BEGIN
	set @ID =  (select ID from tb_users where MAIL = p_MAIL and PASS=p_PASS);
    if(@ID is not null) then
		if exists(select ID from tb_music where ID = p_MUSIC_ID) then
			if not exists(select ID from tb_music_reports where USER_ID = @ID and MUSIC_ID = p_MUSIC_ID) then
				insert into tb_music_reports(USER_ID,MUSIC_ID) values(@ID,p_MUSIC_ID);
				set @p_ERROR = 111; -- işlem başarılı.
            else
				set @p_ERROR = 3; -- Zaten şikayet edilmiş.
            end if;
        else
			set @p_ERROR = 2; -- Böyle bir video yok.
        end if;
    else
		set @p_ERROR=1; -- Token hatası.
    end if;
select @p_ERROR;
END$$

CREATE DEFINER=`mysql`@`localhost` PROCEDURE `sp_MUSIC_SEARCH` (IN `p_TEXT` VARCHAR(60) CHARSET utf8, IN `p_START` INT)  BEGIN
if(p_START != 0) THEN
	select ID, 
    USER_ID,
    N_NAME,
    F_NAME,
    L_NAME,
    PIC_NO,
    REG_FRAME_ID,
    MUSIC_NAME,
    MELODY_NAME_SURNAME,
    LIKES,
    COM,
    DISLIKES,
    VIEWS,
    CATEGORY_ID,
    CATEGORY_NAME,
    VIDEO_NO,
    date_format(UPLOAD_DATE, '%d-%m-%Y %H:%i:%S') AS UPLOAD_DATE
	from user_data where BAN = 0 and MUSIC_NAME LIKE CONCAT(p_TEXT, '%') and ID< p_START ORDER BY ID desc LIMIT 5;
	
ELSE 
	select ID, 
    USER_ID,
    N_NAME,
    F_NAME,
    L_NAME,
    PIC_NO,
    REG_FRAME_ID,
    MUSIC_NAME,
    MELODY_NAME_SURNAME,
    LIKES,
    COM,
    DISLIKES,
    VIEWS,
    CATEGORY_ID,
    CATEGORY_NAME,
    VIDEO_NO,
    date_format(UPLOAD_DATE, '%d-%m-%Y %H:%i:%S') AS UPLOAD_DATE
	from user_data where BAN = 0 and MUSIC_NAME LIKE CONCAT(p_TEXT, '%') ORDER BY ID desc LIMIT 5;
end if;
END$$

CREATE DEFINER=`mysql`@`localhost` PROCEDURE `sp_NEWS_CONTENT_GET` (IN `p_ID` INT)  BEGIN
	select
    CONTENT,
    DATE,
    IMG1,
    IMG2 from tb_news_content where ID = p_ID;
END$$

CREATE DEFINER=`mysql`@`localhost` PROCEDURE `sp_NEWS_GET` (IN `p_START` INT)  BEGIN
	if(p_START = 0) then
		select ID, 
			TITLE,
            IMG_LINK
			from tb_news limit 10;
     else
		select ID, 
			TITLE,
            IMG_LINK
			from tb_news limit p_START, 10;
    end if;
end$$

CREATE DEFINER=`mysql`@`localhost` PROCEDURE `sp_PLAYER_ID` (IN `p_MAIL` VARCHAR(50) CHARSET utf8, IN `p_PASS` CHAR(32) CHARSET utf8, IN `p_PLAYER_ID` CHAR(36) CHARSET utf8)  BEGIN
declare p_ID int;
set p_ID = (select ID from tb_users where MAIL = p_MAIL and PASS = p_PASS);
	if (p_ID > 0) then
		if not exists(select ID from tb_push where USER_ID = p_ID) then
			insert into tb_push(USER_ID,PLAYER_ID) values(p_ID,p_PLAYER_ID);
			set @p_ERROR = 0; -- islem basarili.
        else 
			update tb_push set PLAYER_ID = p_PLAYER_ID where USER_ID = p_ID;
            set @p_ERROR = 0; -- islem basarili.
		end if;
    else
		set @p_ERROR = 1; -- Token hatası.
    end if;
select @p_ERROR;
END$$

CREATE DEFINER=`mysql`@`localhost` PROCEDURE `sp_PROFILE_EDIT` (IN `p_MAIL` VARCHAR(50) CHARSET utf8, IN `p_PASS` CHAR(32) CHARSET utf8, IN `p_N_NAME` VARCHAR(40) CHARSET utf8, IN `p_F_NAME` VARCHAR(40) CHARSET utf8, IN `p_L_NAME` VARCHAR(40) CHARSET utf8, IN `p_CITY` VARCHAR(30) CHARSET utf8, IN `p_BIO` VARCHAR(150) CHARSET utf8, IN `p_PIC_NO` INT, IN `p_B_DATE` CHAR(10) CHARSET utf8, IN `p_REG_FRAME_ID` TINYINT, IN `p_INSTA` VARCHAR(45) CHARSET utf8, IN `p_FACE` VARCHAR(45) CHARSET utf8, IN `p_TWIT` VARCHAR(45) CHARSET utf8, IN `p_NEW_PASS` CHAR(32) CHARSET utf8)  BEGIN
declare coinn int;
declare _ID int;
		if not exists(SELECT ID from tb_users WHERE MAIL = p_MAIL) then
			set @p_ERROR = 1;
		else
			if not exists(SELECT ID from tb_users WHERE PASS = p_PASS and MAIL = p_MAIL) then
				set @p_ERROR = 2;
			else			
				if((select BAN from tb_users where MAIL = p_MAIL) = 0) then
						-- -------------------------------------------------
						if(p_N_NAME != 333) then
							if not exists(select N_NAME from tb_users where N_NAME = p_N_NAME) then
								set coinn = (select COIN from tb_users where MAIL = p_MAIL);
								if(coinn >= 400) then
									update tb_users set COIN = (coinn - 400) where MAIL = p_MAIL;
									UPDATE tb_users SET N_NAME = p_N_NAME WHERE MAIL = p_MAIL;
                                    set @p_ERROR = 111;
                                    select COIN from tb_users where MAIL = p_MAIL;
								else
									set @p_ERROR = 6;
								end if; 
							else 
								set @p_ERROR = 5;
							end if; 
						end if;
                        
					-- -------------------------------------------------
						if(p_F_NAME != 333) then
							update tb_users set F_NAME = p_F_NAME where MAIL = p_MAIL;
						end if;
					-- -------------------------------------------------
						if(p_L_NAME != 333) then
							update tb_users set L_NAME = p_L_NAME where MAIL = p_MAIL;
						end if;
					-- -------------------------------------------------
						if(p_CITY != 333) then
							update tb_users set CITY = p_CITY where MAIL = p_MAIL;
						end if;
					-- -------------------------------------------------
						if(p_BIO != 333) then
							update tb_users set BIO = p_BIO where MAIL = p_MAIL;
						end if;
					-- -------------------------------------------------
						if(p_PIC_NO != 333) then
                            select PIC_NO from tb_users where MAIL = p_MAIL;
							update tb_users set PIC_NO = p_PIC_NO where MAIL = p_MAIL;
                            
						end if;
					-- -------------------------------------------------
						if(p_B_DATE != 333) then
							if((p_B_DATE >= '1950-01-01' && p_B_DATE <= '2014-12-31') || p_B_DATE = '0000-00-00') then
								update tb_users set B_DATE = p_B_DATE where MAIL = p_MAIL;
                            end if;
						end if;
					-- -------------------------------------------------
						if(p_INSTA != 333) then
							update tb_users set INSTAGRAM = p_INSTA where MAIL = p_MAIL;
                            
                            if(p_INSTA = "")THEN
                            	update tb_users set INSTAGRAM = null where MAIL = p_MAIL;
                            end if;
                            set @p_ERROR = 111;
						end if;                    
                    -- -------------------------------------------------
						if(p_FACE != 333) then
							update tb_users set FACEBOOK = p_FACE where MAIL = p_MAIL;
                            
                            if(p_FACE = "")THEN
                            	update tb_users set FACEBOOK = null where MAIL = p_MAIL;
                            end if;
                            
                            set @p_ERROR = 111;
						end if; 
                    -- -------------------------------------------------
						if(p_TWIT != 333) then
							update tb_users set TWITTER = p_TWIT where MAIL = p_MAIL;
                      
                            if(p_TWIT = "")THEN
                            	update tb_users set TWITTER = null where MAIL = p_MAIL;
                            end if;
                            set @p_ERROR = 111;
						end if; 
                    -- -------------------------------------------------
						if(p_REG_FRAME_ID != 333) then
							set _ID = (Select ID from tb_users where MAIL = p_MAIL);
							if exists (Select ID from tb_user_frame where USER_ID = _ID and FRAME_ID = p_REG_FRAME_ID ) then
								update tb_users set REG_FRAME_ID = p_REG_FRAME_ID where MAIL = p_MAIL;
							else
								if(p_REG_FRAME_ID = 0) then
									update tb_users set REG_FRAME_ID = p_REG_FRAME_ID where MAIL = p_MAIL;
                                end if;
							end if;
						end if;                                
					-- -------------------------------------------------
						if(p_NEW_PASS != 333) then
							update tb_users set PASS = p_NEW_PASS where MAIL = p_MAIL;
                        end if;

                    
				else
					set @p_ERROR = 3;
				end if;
			end if;
		end if;
	select @p_ERROR;
end$$

CREATE DEFINER=`mysql`@`localhost` PROCEDURE `sp_PROFILE_GET` (IN `p_ID` VARCHAR(40))  BEGIN -- ----------------------------------------------- FROM tb_users where ID = p_ID;
declare p_BAN bit;


set @check = (select fnc_isNumber(p_ID) as isNumber);

if(@check = 1) THEN
	set p_BAN = (select BAN from tb_users where ID = p_ID);	
ELSE
    set p_BAN = (SELECT BAN from tb_users where N_NAME = p_ID);
    set p_ID = (SELECT ID from tb_users where N_NAME = p_ID);
end if;


if(p_BAN is null) then -- is null yanlış düzenle
	set @p_BAN = 1;
else
    if(p_BAN = 1) then
		set @p_ERROR = 2;
    else
        select
        ID AS USER_ID,
		N_NAME,
        F_NAME,
        L_NAME,
        CITY,
        date_format(B_DATE, '%d-%m-%Y') as B_DATE,
        BIO,
        COIN,
        SCORE,
        PIC_NO,
        INSTAGRAM,
        TWITTER,
        FACEBOOK,
        REG_FRAME_ID from tb_users where ID = p_ID;
    end if;
end if;
  
end$$

CREATE DEFINER=`mysql`@`localhost` PROCEDURE `sp_PROFILE_GET_VIDEO` (IN `p_ID` VARCHAR(40) CHARSET utf8)  BEGIN -- ----------------------------------------------- FROM tb_users where ID = p_ID;
declare p_BAN bit;

set @check = (select fnc_isNumber(p_ID) as isNumber);
if(@check = 1) THEN
	set p_BAN = (select BAN from tb_users where ID = p_ID);	
ELSE
    set p_BAN = (SELECT BAN from tb_users where N_NAME = p_ID);
    set p_ID = (SELECT ID from tb_users where N_NAME = p_ID);
end if;

if(p_BAN is null) then -- is null yanlış düzenle
	set @p_BAN = 1;
else
    if(p_BAN = 1) then
		set @p_ERROR = 2;
    else
    
        select
        ID AS MUSIC_ID,
        MUSIC_NAME,
        MELODY_NAME_SURNAME,
        LIKES,
        VIEWS,
        CATEGORY_ID,
        CATEGORY_NAME,
        VIDEO_NO,
        date_format(UPLOAD_DATE, '%d-%m-%Y %H:%i') AS UPLOAD_DATE
        from  user_data where BAN = 0 and USER_ID = p_ID;
    end if;
end if;
  
end$$

CREATE DEFINER=`mysql`@`localhost` PROCEDURE `sp_PUSH_SEND` (IN `p_ID` INT, OUT `p_PLAYER_ID` CHAR(36) CHARSET utf8)  BEGIN
	if exists (select ID from tb_push where USER_ID = p_ID) then
		set p_PLAYER_ID = (select PLAYER_ID from tb_push where USER_ID = p_ID);
	else
		set p_PLAYER_ID = 1; -- player_id yok.
    end if;
end$$

CREATE DEFINER=`mysql`@`localhost` PROCEDURE `sp_REGISTER` (IN `p_N_NAME` VARCHAR(40) CHARSET utf8, IN `p_F_NAME` VARCHAR(40) CHARSET utf8, IN `p_L_NAME` VARCHAR(40) CHARSET utf8, IN `p_MAIL` VARCHAR(40) CHARSET utf8, IN `p_PASS` CHAR(32) CHARSET utf8, IN `p_CHECK_CODE` CHAR(6) CHARSET utf8, IN `p_IP` VARCHAR(15) CHARSET utf8, OUT `ERRORRS` INT)  BEGIN
SET ERRORRS = 0;
set @current_time = (select current_time());
	if exists (SELECT ID from tb_users WHERE N_NAME = p_N_NAME) then
		set ERRORRS = 1;
    else
		if exists(SELECT ID from tb_users WHERE  MAIL=p_MAIL) then
			set ERRORRS = 2;
        else
			insert into tb_users(N_NAME,F_NAME,L_NAME,MAIL,PASS,CHECK_CODE,IP)
			values(p_N_NAME,p_F_NAME,p_L_NAME,p_MAIL,p_PASS,p_CHECK_CODE,p_IP);
            update tb_users set LOGIN = 0 where N_NAME = p_N_NAME;
            set ERRORRS = 0;
        end if;
    end if;
end$$

CREATE DEFINER=`mysql`@`localhost` PROCEDURE `sp_SAVED_FRAME` (IN `p_MAIL` VARCHAR(50), IN `p_PASS` CHAR(32), IN `p_TOKEN` CHAR(6))  BEGIN
	set @ID = (select ID from tb_users where MAIL=p_MAIL and PASS=p_PASS and TOKEN = p_TOKEN);
    if(@ID is not null) then
		select FRAME_ID from tb_user_frame where USER_ID = @ID;
        set @p_ERROR = 0;
	else
		set @p_ERROR = 1;
    end if;
    select @p_ERROR;
END$$

CREATE DEFINER=`mysql`@`localhost` PROCEDURE `sp_SELF_VIDEO_COMM_DEL` (IN `p_MAIL` VARCHAR(60) CHARSET utf8, IN `p_PASS` CHAR(32) CHARSET utf8, IN `p_VIDEO_ID` INT, IN `p_COM_ID` INT)  BEGIN
set @p_ERROR = 111;
set @ID = (select ID from tb_users where MAIL = p_MAIL and PASS = p_PASS);

if(@ID > 0) then
	if exists (select ID from tb_music where USER_ID = @ID and ID = p_VIDEO_ID) then
		if exists(select ID from tb_comments where MUSIC_ID = p_VIDEO_ID and ID = p_COM_ID) then
			delete from tb_comments where ID = p_COM_ID;
		else
			set @p_ERROR = 2;
        end if;
	else
		set @p_ERROR = 1;
    end if;
end if;
select @p_ERROR;

END$$

CREATE DEFINER=`mysql`@`localhost` PROCEDURE `sp_USE_FIRST` ()  BEGIN
truncate tb_firsts;
-- TRUNCATE tb_likes;
-- TRUNCATE tb_comments;

UPDATE tb_music_category set COUNT = 0;

delete from tb_music WHERE FIRST = 1;

insert into tb_firsts(VIDEO_NO,LIKES)
SELECT VIDEO_NO, MAX(LIKES) from tb_music WHERE CATEGORY_ID = 8;

insert into tb_firsts(VIDEO_NO,LIKES)
SELECT VIDEO_NO, MAX(LIKES) from tb_music WHERE CATEGORY_ID = 9;

insert into tb_firsts(VIDEO_NO,LIKES)
SELECT VIDEO_NO, MAX(LIKES) from tb_music WHERE CATEGORY_ID = 10;

insert into tb_firsts(VIDEO_NO,LIKES)
SELECT VIDEO_NO, MAX(LIKES) from tb_music WHERE CATEGORY_ID = 11;

insert into tb_firsts(VIDEO_NO,LIKES)
SELECT VIDEO_NO, MAX(LIKES) from tb_music WHERE CATEGORY_ID = 12;

insert into tb_firsts(VIDEO_NO,LIKES)
SELECT VIDEO_NO, MAX(LIKES) from tb_music WHERE CATEGORY_ID = 13;

insert into tb_firsts(VIDEO_NO,LIKES)
SELECT VIDEO_NO, MAX(LIKES) from tb_music WHERE CATEGORY_ID = 14;

insert into tb_firsts(VIDEO_NO,LIKES)
SELECT VIDEO_NO, MAX(LIKES) from tb_music WHERE CATEGORY_ID = 15;

insert into tb_firsts(VIDEO_NO,LIKES)
SELECT VIDEO_NO, MAX(LIKES) from tb_music WHERE CATEGORY_ID = 16;

insert into tb_firsts(VIDEO_NO,LIKES)
SELECT VIDEO_NO, MAX(LIKES) from tb_music WHERE CATEGORY_ID = 17;

insert into tb_firsts(VIDEO_NO,LIKES)
SELECT VIDEO_NO, MAX(LIKES) from tb_music WHERE CATEGORY_ID = 18;

insert into tb_firsts(VIDEO_NO,LIKES)
SELECT VIDEO_NO, MAX(LIKES) from tb_music WHERE CATEGORY_ID = 19;

insert into tb_firsts(VIDEO_NO,LIKES)
SELECT VIDEO_NO, MAX(LIKES) from tb_music WHERE CATEGORY_ID = 20;

insert into tb_firsts(VIDEO_NO,LIKES)
SELECT VIDEO_NO, MAX(LIKES) from tb_music WHERE CATEGORY_ID = 21;

insert into tb_firsts(VIDEO_NO,LIKES)
SELECT VIDEO_NO, MAX(LIKES) from tb_music WHERE CATEGORY_ID = 22;

insert into tb_firsts(VIDEO_NO,LIKES)
SELECT VIDEO_NO, MAX(LIKES) from tb_music WHERE CATEGORY_ID = 23;

UPDATE tb_music set FIRST = 1 WHERE VIDEO_NO in (SELECT VIDEO_NO from tb_firsts);

SELECT VIDEO_NO from tb_music WHERE FIRST = 1;
DELETE FROM tb_music WHERE FIRST = 0;
    
END$$

CREATE DEFINER=`mysql`@`localhost` PROCEDURE `sp_VIDEO_EDIT` (IN `p_MAIL` VARCHAR(60) CHARSET utf8, IN `p_PASS` CHAR(32) CHARSET utf8, IN `p_MUSIC_NAME` VARCHAR(60) CHARSET utf8, IN `p_MELODY_NAME_SURNAME` VARCHAR(60) CHARSET utf8, IN `p_CATEGORY_ID` INT, IN `p_DELETE` INT, IN `p_VIDEO_ID` INT)  BEGIN

declare p_ID int;
SET @p_ERROR = 111;
    set p_ID = (SELECT ID from tb_users WHERE MAIL = p_MAIL and PASS = p_PASS and BAN = 0);
	if (p_ID > 0 ) then
		if exists (select ID from tb_music where ID = p_VIDEO_ID and USER_ID = p_ID) then
		  -- ---------------------------------------------
          if (p_MUSIC_NAME != 333) then
			update tb_music set MUSIC_NAME = p_MUSIC_NAME where ID = p_VIDEO_ID;
          end if;
		  -- ---------------------------------------------
		  if (p_MELODY_NAME_SURNAME != 333) then
			update tb_music set MELODY_NAME_SURNAME = p_MELODY_NAME_SURNAME where ID = p_VIDEO_ID;
          end if;
          -- ---------------------------------------------
           if (p_CATEGORY_ID != 333) then
				if exists (select ID from tb_music_category where ID = p_CATEGORY_ID) then
					update tb_music set CATEGORY_ID = p_CATEGORY_ID where ID = p_VIDEO_ID;
                end if;
          end if;
          -- ---------------------------------------------
			if(p_DELETE != 3 && p_DELETE = 1) then
				set @p_ERROR = (select VIDEO_NO from tb_music where ID = p_VIDEO_ID);
                
                set @s = (select COUNT(*) from tb_likes WHERE MUSIC_ID = p_VIDEO_ID);
                set @ts = (SELECT SCORE from tb_users WHERE ID = p_ID);       
                update tb_users set SCORE = (@ts - @s) where ID = p_ID;
                
                DELETE FROM tb_firsts WHERE VIDEO_NO = @p_ERROR;
                
				delete FROM tb_music where ID = p_VIDEO_ID;
                delete from tb_comments where MUSIC_ID = p_VIDEO_ID;
                delete from tb_likes where MUSIC_ID = p_VIDEO_ID;
                
               
            end if;
          
          
        else
			set @p_ERROR = 2;
        end if;
		
    else
		set @p_ERROR = 1;
    end if;
	
	select @p_ERROR;
end$$

CREATE DEFINER=`mysql`@`localhost` PROCEDURE `sp_VIDEO_GET` (IN `p_ID` VARCHAR(40))  BEGIN
	if (p_ID > 0) then
		select
			ID,
			MUSIC_NAME,
            MELODY_NAME_SURNAME,
            LIKES,
            VIEWS,
            CATEGORY_ID,
            VIDEO_NO,
            date_format(UPLOAD_DATE, '%d-%m-%Y') AS UPLOAD_DATE FROM tb_music where USER_ID = p_ID;
			
        
        set @p_ERROR = 0;
        
    else
		set @p_ERROR = 1;
    end if;
  select @p_ERROR;
end$$

CREATE DEFINER=`mysql`@`localhost` PROCEDURE `sp_VIDEO_GET_CATEGORY` (IN `p_START` INT, IN `p_CATEGORY_ID` INT)  BEGIN
	if(p_START != 0) THEN
    
    select ID, 
		USER_ID,
		N_NAME,
		F_NAME,
		L_NAME,
		PIC_NO,
		REG_FRAME_ID,
		MUSIC_NAME,
		MELODY_NAME_SURNAME,
		LIKES,
		COM,
		DISLIKES,
		VIEWS,
		CATEGORY_ID,
        CATEGORY_NAME,
		VIDEO_NO,
		date_format(UPLOAD_DATE, '%d-%m-%Y %H:%i') AS UPLOAD_DATE
		from user_data where BAN = 0 and CATEGORY_ID = p_CATEGORY_ID and ID < p_START ORDER BY ID desc LIMIT 5;
    ELSE
    
    select ID, 
		USER_ID,
		N_NAME,
		F_NAME,
		L_NAME,
		PIC_NO,
		REG_FRAME_ID,
		MUSIC_NAME,
		MELODY_NAME_SURNAME,
		LIKES,
		COM,
		DISLIKES,
		VIEWS,
		CATEGORY_ID,
        CATEGORY_NAME,
		VIDEO_NO,
		date_format(UPLOAD_DATE, '%d-%m-%Y %H:%i') AS UPLOAD_DATE
		from user_data where BAN = 0 and CATEGORY_ID = p_CATEGORY_ID ORDER BY ID desc LIMIT 5;
        
    end if;
END$$

CREATE DEFINER=`mysql`@`localhost` PROCEDURE `sp_VIDEO_L` (IN `p_MAIL` VARCHAR(40) CHARSET utf8, IN `p_PASS` CHAR(32) CHARSET utf8, IN `p_VIDEO_ID` INT)  BEGIN
set @ID = (select ID from tb_users where MAIL = p_MAIL and PASS = p_PASS);
    if(@ID > 0) then
        if exists(select ID from tb_likes where USER_ID = @ID and MUSIC_ID = p_VIDEO_ID) then
			set @p_CH = 'ios-thumbs-up';
		else
			set @p_CH = 'ios-thumbs-up-outline';
        end if; 
    end if;
select @p_CH;
END$$

CREATE DEFINER=`mysql`@`localhost` PROCEDURE `sp_VIDEO_LIKED_INF` (IN `p_START` INT, IN `p_MUSIC_ID` INT)  BEGIN
if(p_START != 0) THEN

    select tb_likes.ID, tb_likes.USER_ID, tb_users.N_NAME, tb_users.PIC_NO, tb_users.F_NAME, tb_users.L_NAME from 
    tb_likes inner join tb_users on tb_likes.USER_ID = tb_users.ID where tb_likes.MUSIC_ID = p_MUSIC_ID and tb_likes.ID < p_START ORDER BY tb_likes.ID desc LIMIT 10;
    
    ELSE
	
select tb_likes.ID, tb_likes.USER_ID, tb_users.N_NAME, tb_users.PIC_NO, tb_users.F_NAME, tb_users.L_NAME from  
    tb_likes inner join tb_users on tb_likes.USER_ID = tb_users.ID where tb_likes.MUSIC_ID = p_MUSIC_ID ORDER BY tb_likes.ID desc LIMIT 10;


    end if;
end$$

CREATE DEFINER=`mysql`@`localhost` PROCEDURE `sp_VIDEO_LIKE_VIEW` (IN `p_MAIL` VARCHAR(40) CHARSET utf8, IN `p_PASS` CHAR(32) CHARSET utf8, IN `p_MUSIC_ID` INT, IN `p_LIKE` CHAR(1) CHARSET utf8, IN `p_VIEWS` CHAR(1) CHARSET utf8, OUT `p_PLAYER_ID` CHAR(36) CHARSET utf8, OUT `p_N_NAME` VARCHAR(40) CHARSET utf8, OUT `p_ERROR` SMALLINT)  BEGIN
	SET p_ERROR = 111;
if exists (select ID from tb_music where ID = p_MUSIC_ID) then
	set @ID = (select ID from tb_users where BAN = 0 and MAIL= p_MAIL and PASS=p_PASS);
    if(@ID > 0) then
		
		SET @VIEWS = (SELECT VIEWS FROM tb_music where ID=p_MUSIC_ID);
		SET @SCORE = (SELECT SCORE FROM tb_users where ID=@ID);
        --
        set @MUSIC_USER = (select USER_ID from tb_music where ID = p_MUSIC_ID);
        if(@MUSIC_USER != @ID) THEN
        	call sp_PUSH_SEND(@MUSIC_USER,p_PLAYER_ID);
        end if;
        set p_N_NAME = (select N_NAME from tb_users where ID = @ID);
    
		if(p_LIKE = 1) then
			if not exists (select ID from tb_likes where USER_ID=@ID and MUSIC_ID = p_MUSIC_ID) then
            
				insert into tb_likes(USER_ID,MUSIC_ID)
				values(@ID,p_MUSIC_ID);
                
			else
				set p_ERROR = 2;
            end if;
		end if;
            
        -- --------------------------------------------------------------------------------------
		if(p_VIEWS = 1) then
			if not exists (select ID from tb_views where USER_ID=@ID and MUSIC_ID = p_MUSIC_ID) then
				update tb_music set VIEWS = @VIEWS + 1 where ID=p_MUSIC_ID;
				insert into tb_views(USER_ID,MUSIC_ID)
                values(@ID,p_MUSIC_ID);
			else 
				set p_ERROR = 4;
			end if;
		end if;
		-- --------------------------------------------------------------------------------------
	else
		set p_ERROR = 1;
    end if;
else
	set p_ERROR = 5;
end if;
end$$

CREATE DEFINER=`mysql`@`localhost` PROCEDURE `sp_VIDEO_UPLOAD` (IN `p_MAIL` VARCHAR(40) CHARSET utf8, IN `p_PASS` CHAR(32) CHARSET utf8, IN `p_MUSIC_NAME` VARCHAR(60) CHARSET utf8, IN `p_MELODY_NAME_SURNAME` VARCHAR(90) CHARSET utf8, IN `p_CATEGORY_ID` INT, IN `p_VIDEO_NO` INT, IN `p_IP` CHAR(15) CHARSET utf8)  BEGIN

declare p_ERROR int;
declare p_ID int;
SET p_ERROR = 0;
    set p_ID = (SELECT ID from tb_users WHERE MAIL = p_MAIL and PASS = p_PASS and BAN = 0);
	if (p_ID > 0) then
		if exists (select ID from tb_music_category where ID = p_CATEGORY_ID) then
			insert into tb_music(
			USER_ID,
			MUSIC_NAME,
			MELODY_NAME_SURNAME,
			CATEGORY_ID,
			VIDEO_NO,
			IP
			) values(
			p_ID,
			p_MUSIC_NAME,
			p_MELODY_NAME_SURNAME,
			p_CATEGORY_ID,
			p_VIDEO_NO,
			p_IP
			);
        
			select
			USER_ID,
			MUSIC_NAME,
            MELODY_NAME_SURNAME,
            LIKES,
            VIEWS,
            CATEGORY_ID,
            VIDEO_NO,
            date_format(UPLOAD_DATE, '%d-%m-%Y') AS UPLOAD_DATE FROM tb_music where USER_ID = p_ID and VIDEO_NO = p_VIDEO_NO;            
			set p_ERROR = 111;
        end if;
    else
		set p_ERROR = 1;
    end if;
	
	select p_ERROR;
end$$

CREATE DEFINER=`mysql`@`localhost` PROCEDURE `sp_WAIT_MESSAGE` (IN `p_MESSAGE` VARCHAR(600) CHARSET utf8, IN `p_SENDER_N_NAME` VARCHAR(40) CHARSET utf8, IN `p_RECEIVER_N_NAME` VARCHAR(40) CHARSET utf8)  BEGIN
set @p_ERROR = 0;
set @p_SENDER_ID = (select ID from tb_users where N_NAME = p_SENDER_N_NAME);
set @p_RECEIVER_ID = (select ID from tb_users where N_NAME = p_RECEIVER_N_NAME);
	if(@p_RECEIVER_ID > 0 && @p_SENDER_ID > 0) then
		insert into tb_wait_message(MESSAGE,SENDER_ID,RECEIVER_ID) values(p_MESSAGE,@p_SENDER_ID,@p_RECEIVER_ID);
    else 
		set @p_ERROR = 1;
    end if;
select @p_ERROR;
END$$

CREATE DEFINER=`mysql`@`localhost` PROCEDURE `sp_WAIT_MESSAGE_SEND` (IN `p_N_NAME` VARCHAR(40) CHARSET utf8)  BEGIN
set @ID = (select ID from tb_users where N_NAME = p_N_NAME);
select 
tb_wait_message.MESSAGE,
tb_wait_message.DATE,
tb_users.N_NAME AS SENDER,
tb_users.PIC_NO AS IMAGE,
tb_users.ID
from tb_wait_message join tb_users ON tb_users.ID = tb_wait_message.SENDER_ID where RECEIVER_ID = @ID;
delete from tb_wait_message where RECEIVER_ID = @ID;
    
END$$

--
-- İşlevler
--
CREATE DEFINER=`mysql`@`localhost` FUNCTION `fnc_isNumber` (`deger` VARCHAR(50)) RETURNS INT(11) BEGIN
IF (deger REGEXP ('^[0-9]+$'))
    THEN
      RETURN 1;
    ELSE
      RETURN 0;
    END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Görünüm yapısı durumu `comments_data`
-- (Asıl görünüm için aşağıya bakın)
--
CREATE TABLE `comments_data` (
`USER_ID` int(11)
,`N_NAME` varchar(40)
,`F_NAME` varchar(40)
,`L_NAME` varchar(40)
,`PIC_NO` int(11)
,`REG_FRAME_ID` tinyint(11)
,`MUSIC_ID` int(11)
,`COMMENT` varchar(250)
,`COMMENT_ID` int(11)
,`DATE` timestamp
);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `tb_comments`
--

CREATE TABLE `tb_comments` (
  `ID` int(11) NOT NULL,
  `USER_ID` int(11) NOT NULL,
  `MUSIC_ID` int(11) DEFAULT NULL,
  `COMMENT` varchar(250) DEFAULT NULL,
  `DATE` timestamp NOT NULL DEFAULT current_timestamp(),
  `IP` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tetikleyiciler `tb_comments`
--
DELIMITER $$
CREATE TRIGGER `trg_comments_delete` AFTER DELETE ON `tb_comments` FOR EACH ROW BEGIN
 declare v_COM int;
 set v_COM = (select COM from tb_music where ID = OLD.MUSIC_ID);
 update tb_music set COM = v_COM - 1 where ID = OLD.MUSIC_ID;
 
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_comments_insert` AFTER INSERT ON `tb_comments` FOR EACH ROW BEGIN
 declare v_COM int;
 set v_COM = (select COM from tb_music where ID = NEW.MUSIC_ID);
 update tb_music set COM = v_COM + 1 where ID = NEW.MUSIC_ID;
 
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `tb_firsts`
--

CREATE TABLE `tb_firsts` (
  `ID` int(11) NOT NULL,
  `VIDEO_NO` int(11) NOT NULL,
  `LIKES` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



--
-- Tablo için tablo yapısı `tb_frame_type`
--

CREATE TABLE `tb_frame_type` (
  `ID` int(10) NOT NULL,
  `FRAME_NAME` varchar(30) DEFAULT NULL,
  `P_NO` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Tablo döküm verisi `tb_frame_type`
--

INSERT INTO `tb_frame_type` (`ID`, `FRAME_NAME`, `P_NO`) VALUES
(1, 'NEON', 1),
(2, 'ARGON', 2),
(3, 'BUBLE', 3),
(4, 'SOFT', 4),
(5, 'NARURE', 5),
(6, 'HAPPY', 6);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `tb_likes`
--

CREATE TABLE `tb_likes` (
  `ID` int(10) NOT NULL,
  `USER_ID` int(11) NOT NULL,
  `MUSIC_ID` int(11) NOT NULL,
  `DATE` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


--
-- Tetikleyiciler `tb_likes`
--
DELIMITER $$
CREATE TRIGGER `trg_likes_delete` AFTER DELETE ON `tb_likes` FOR EACH ROW BEGIN
 declare v_LIKES int;
 set v_LIKES = (select LIKES from tb_music where ID = OLD.MUSIC_ID);
 update tb_music set LIKES = v_LIKES - 1 where ID = OLD.MUSIC_ID;
 
set @v_UID = (select USER_ID from tb_music where ID = OLD.MUSIC_ID);
set @v_TS = (SELECT SCORE from tb_users where ID = @v_UID);
 
update tb_users set SCORE = @v_TS - 1 where ID = @v_UID;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_likes_insert` AFTER INSERT ON `tb_likes` FOR EACH ROW BEGIN
 declare v_LIKES int;
 set v_LIKES = (select LIKES from tb_music where ID = NEW.MUSIC_ID);
 update tb_music set LIKES = v_LIKES + 1 where ID = NEW.MUSIC_ID;
 

set @v_UID = (select USER_ID from tb_music where ID = NEW.MUSIC_ID);
set @v_TS = (SELECT SCORE from tb_users where ID = @v_UID);
 
update tb_users set SCORE = @v_TS + 1 where ID = @v_UID;
 
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `tb_music`
--

CREATE TABLE `tb_music` (
  `ID` int(10) UNSIGNED NOT NULL,
  `USER_ID` int(11) DEFAULT NULL,
  `MUSIC_NAME` varchar(60) NOT NULL,
  `MELODY_NAME_SURNAME` varchar(90) NOT NULL,
  `LIKES` int(11) NOT NULL,
  `DISLIKES` int(11) NOT NULL,
  `VIEWS` int(11) NOT NULL,
  `CATEGORY_ID` int(11) DEFAULT NULL,
  `VIDEO_NO` int(11) NOT NULL,
  `UPLOAD_DATE` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `IP` char(15) DEFAULT NULL,
  `COM` int(11) DEFAULT 0,
  `FIRST` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


--
-- Tetikleyiciler `tb_music`
--
DELIMITER $$
CREATE TRIGGER `trg_music_category_delete` AFTER DELETE ON `tb_music` FOR EACH ROW BEGIN
 declare v_COUNT int;
 set v_COUNT = (select count(*) from tb_music where CATEGORY_ID = OLD.CATEGORY_ID);
 update tb_music_category set COUNT = v_COUNT  where ID = OLD.CATEGORY_ID;
 
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_music_category_insert` AFTER INSERT ON `tb_music` FOR EACH ROW BEGIN
 declare v_COUNT int;
 set v_COUNT = (select count(*) from tb_music where CATEGORY_ID = NEW.CATEGORY_ID);
 update tb_music_category set COUNT = v_COUNT  where ID = NEW.CATEGORY_ID;
 
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_music_category_update` AFTER UPDATE ON `tb_music` FOR EACH ROW BEGIN
 declare v_COUNT int;
 declare v_COUNT2 int;
 set v_COUNT = (select count(*) from tb_music where CATEGORY_ID = NEW.CATEGORY_ID);
 set v_COUNT2 = (select count(*) from tb_music where CATEGORY_ID = OLD.CATEGORY_ID);
 update tb_music_category set COUNT = v_COUNT  where ID = NEW.CATEGORY_ID;
 update tb_music_category set COUNT = v_COUNT2  where ID = OLD.CATEGORY_ID;
 END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `tb_music_category`
--

CREATE TABLE `tb_music_category` (
  `ID` int(11) NOT NULL,
  `CATEGORY_NAME` varchar(30) DEFAULT NULL,
  `COUNT` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Tablo döküm verisi `tb_music_category`
--

INSERT INTO `tb_music_category` (`ID`, `CATEGORY_NAME`, `COUNT`) VALUES
(8, 'Elektronik', 0),
(9, 'Hip-Hop', 0),
(10, 'Caz', 0),
(11, 'Pop', 1),
(12, 'Rock', 0),
(13, 'Türkü', 0),
(14, 'Techno', 0),
(15, 'Metal', 0),
(16, 'Rap', 2),
(17, 'Punk ', 0),
(18, 'Reggea', 0),
(19, 'Arabesk', 0),
(20, 'Özgün', 0),
(21, 'Tasavvuf', 0),
(22, 'TSM', 0),
(23, 'Şiir', 0);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `tb_music_reports`
--

CREATE TABLE `tb_music_reports` (
  `ID` int(10) NOT NULL,
  `USER_ID` int(11) NOT NULL,
  `MUSIC_ID` int(11) NOT NULL,
  `DATE` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



--
-- Tablo için tablo yapısı `tb_news`
--

CREATE TABLE `tb_news` (
  `ID` int(11) NOT NULL,
  `TITLE` text NOT NULL,
  `LINK` text NOT NULL,
  `IMG_LINK` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



--
-- Tablo için tablo yapısı `tb_news_content`
--

CREATE TABLE `tb_news_content` (
  `ID` int(11) NOT NULL,
  `TITLE` text NOT NULL,
  `DATE` text DEFAULT NULL,
  `CONTENT` text DEFAULT NULL,
  `IMG1` text DEFAULT NULL,
  `IMG2` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



--
-- Tablo için tablo yapısı `tb_push`
--

CREATE TABLE `tb_push` (
  `ID` int(11) NOT NULL,
  `USER_ID` int(11) NOT NULL,
  `PLAYER_ID` char(36) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `tb_users`
--

CREATE TABLE `tb_users` (
  `ID` int(11) NOT NULL,
  `N_NAME` varchar(40) NOT NULL,
  `F_NAME` varchar(40) NOT NULL,
  `L_NAME` varchar(40) NOT NULL,
  `MAIL` varchar(50) NOT NULL,
  `PASS` char(32) NOT NULL,
  `CITY` varchar(30) DEFAULT NULL,
  `B_DATE` date DEFAULT NULL,
  `SCORE` int(11) DEFAULT 0,
  `BIO` varchar(150) DEFAULT NULL,
  `COIN` int(11) DEFAULT 400,
  `PIC_NO` int(11) DEFAULT 0,
  `REG_FRAME_ID` tinyint(11) DEFAULT 0,
  `BAN` bit(1) DEFAULT b'0',
  `ACTIVE` bit(1) DEFAULT b'0',
  `CHECK_CODE` char(6) DEFAULT NULL,
  `UUID` char(16) DEFAULT '0',
  `S_DATE` timestamp NOT NULL DEFAULT current_timestamp(),
  `IP` varchar(15) NOT NULL,
  `COIN_H` int(11) NOT NULL DEFAULT 48,
  `INSTAGRAM` varchar(45) DEFAULT NULL,
  `TWITTER` varchar(45) DEFAULT NULL,
  `FACEBOOK` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


--
-- Tablo için tablo yapısı `tb_user_frame`
--

CREATE TABLE `tb_user_frame` (
  `ID` int(10) NOT NULL,
  `USER_ID` int(11) NOT NULL,
  `FRAME_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `tb_views`
--

CREATE TABLE `tb_views` (
  `ID` int(10) NOT NULL,
  `USER_ID` int(11) NOT NULL,
  `MUSIC_ID` int(11) NOT NULL,
  `DATE` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `tb_wait_message`
--

CREATE TABLE `tb_wait_message` (
  `ID` int(11) NOT NULL,
  `MESSAGE` text NOT NULL,
  `SENDER_ID` int(11) NOT NULL,
  `SENDER` int(11) NOT NULL,
  `RECEIVER_ID` int(11) NOT NULL,
  `DATE` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Görünüm yapısı durumu `user_data`
-- (Asıl görünüm için aşağıya bakın)
--
CREATE TABLE `user_data` (
`USER_ID` int(11)
,`N_NAME` varchar(40)
,`F_NAME` varchar(40)
,`L_NAME` varchar(40)
,`MAIL` varchar(50)
,`PASS` char(32)
,`CITY` varchar(30)
,`B_DATE` date
,`BIO` varchar(150)
,`PIC_NO` int(11)
,`REG_FRAME_ID` tinyint(11)
,`COIN` int(11)
,`SCORE` int(11)
,`BAN` bit(1)
,`ID` int(10) unsigned
,`MUSIC_NAME` varchar(60)
,`MELODY_NAME_SURNAME` varchar(90)
,`LIKES` int(11)
,`COM` int(11)
,`DISLIKES` int(11)
,`VIEWS` int(11)
,`CATEGORY_ID` int(11)
,`CATEGORY_NAME` varchar(30)
,`VIDEO_NO` int(11)
,`UPLOAD_DATE` timestamp
);

-- --------------------------------------------------------

--
-- Görünüm yapısı `comments_data`
--
DROP TABLE IF EXISTS `comments_data`;

CREATE ALGORITHM=UNDEFINED DEFINER=`mysql`@`localhost` SQL SECURITY DEFINER VIEW `comments_data`  AS  select `tb_users`.`ID` AS `USER_ID`,`tb_users`.`N_NAME` AS `N_NAME`,`tb_users`.`F_NAME` AS `F_NAME`,`tb_users`.`L_NAME` AS `L_NAME`,`tb_users`.`PIC_NO` AS `PIC_NO`,`tb_users`.`REG_FRAME_ID` AS `REG_FRAME_ID`,`tb_comments`.`MUSIC_ID` AS `MUSIC_ID`,`tb_comments`.`COMMENT` AS `COMMENT`,`tb_comments`.`ID` AS `COMMENT_ID`,`tb_comments`.`DATE` AS `DATE` from (`tb_users` join `tb_comments` on(`tb_users`.`ID` = `tb_comments`.`USER_ID`)) ;

-- --------------------------------------------------------

--
-- Görünüm yapısı `user_data`
--
DROP TABLE IF EXISTS `user_data`;

CREATE ALGORITHM=UNDEFINED DEFINER=`mysql`@`localhost` SQL SECURITY DEFINER VIEW `user_data`  AS  select `tb_users`.`ID` AS `USER_ID`,`tb_users`.`N_NAME` AS `N_NAME`,`tb_users`.`F_NAME` AS `F_NAME`,`tb_users`.`L_NAME` AS `L_NAME`,`tb_users`.`MAIL` AS `MAIL`,`tb_users`.`PASS` AS `PASS`,`tb_users`.`CITY` AS `CITY`,`tb_users`.`B_DATE` AS `B_DATE`,`tb_users`.`BIO` AS `BIO`,`tb_users`.`PIC_NO` AS `PIC_NO`,`tb_users`.`REG_FRAME_ID` AS `REG_FRAME_ID`,`tb_users`.`COIN` AS `COIN`,`tb_users`.`SCORE` AS `SCORE`,`tb_users`.`BAN` AS `BAN`,`tb_music`.`ID` AS `ID`,`tb_music`.`MUSIC_NAME` AS `MUSIC_NAME`,`tb_music`.`MELODY_NAME_SURNAME` AS `MELODY_NAME_SURNAME`,`tb_music`.`LIKES` AS `LIKES`,`tb_music`.`COM` AS `COM`,`tb_music`.`DISLIKES` AS `DISLIKES`,`tb_music`.`VIEWS` AS `VIEWS`,`tb_music`.`CATEGORY_ID` AS `CATEGORY_ID`,`tb_music_category`.`CATEGORY_NAME` AS `CATEGORY_NAME`,`tb_music`.`VIDEO_NO` AS `VIDEO_NO`,`tb_music`.`UPLOAD_DATE` AS `UPLOAD_DATE` from ((`tb_users` join `tb_music` on(`tb_users`.`ID` = `tb_music`.`USER_ID`)) join `tb_music_category` on(`tb_music_category`.`ID` = `tb_music`.`CATEGORY_ID`)) ;

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `tb_comments`
--
ALTER TABLE `tb_comments`
  ADD PRIMARY KEY (`ID`);

--
-- Tablo için indeksler `tb_firsts`
--
ALTER TABLE `tb_firsts`
  ADD PRIMARY KEY (`ID`);

--
-- Tablo için indeksler `tb_frame_type`
--
ALTER TABLE `tb_frame_type`
  ADD PRIMARY KEY (`ID`);

--
-- Tablo için indeksler `tb_likes`
--
ALTER TABLE `tb_likes`
  ADD PRIMARY KEY (`ID`);

--
-- Tablo için indeksler `tb_music`
--
ALTER TABLE `tb_music`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fg_musicdb` (`USER_ID`),
  ADD KEY `fg_musicdb_category` (`CATEGORY_ID`);

--
-- Tablo için indeksler `tb_music_category`
--
ALTER TABLE `tb_music_category`
  ADD PRIMARY KEY (`ID`);

--
-- Tablo için indeksler `tb_music_reports`
--
ALTER TABLE `tb_music_reports`
  ADD PRIMARY KEY (`ID`);

--
-- Tablo için indeksler `tb_news`
--
ALTER TABLE `tb_news`
  ADD PRIMARY KEY (`ID`);

--
-- Tablo için indeksler `tb_news_content`
--
ALTER TABLE `tb_news_content`
  ADD PRIMARY KEY (`ID`);

--
-- Tablo için indeksler `tb_push`
--
ALTER TABLE `tb_push`
  ADD PRIMARY KEY (`ID`);

--
-- Tablo için indeksler `tb_users`
--
ALTER TABLE `tb_users`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `usersMail` (`MAIL`),
  ADD KEY `usersNick` (`N_NAME`),
  ADD KEY `usersPass` (`PASS`);

--
-- Tablo için indeksler `tb_user_frame`
--
ALTER TABLE `tb_user_frame`
  ADD PRIMARY KEY (`ID`);

--
-- Tablo için indeksler `tb_views`
--
ALTER TABLE `tb_views`
  ADD PRIMARY KEY (`ID`);

--
-- Tablo için indeksler `tb_wait_message`
--
ALTER TABLE `tb_wait_message`
  ADD PRIMARY KEY (`ID`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `tb_comments`
--
ALTER TABLE `tb_comments`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Tablo için AUTO_INCREMENT değeri `tb_firsts`
--
ALTER TABLE `tb_firsts`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Tablo için AUTO_INCREMENT değeri `tb_frame_type`
--
ALTER TABLE `tb_frame_type`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Tablo için AUTO_INCREMENT değeri `tb_likes`
--
ALTER TABLE `tb_likes`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;

--
-- Tablo için AUTO_INCREMENT değeri `tb_music`
--
ALTER TABLE `tb_music`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- Tablo için AUTO_INCREMENT değeri `tb_music_category`
--
ALTER TABLE `tb_music_category`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Tablo için AUTO_INCREMENT değeri `tb_music_reports`
--
ALTER TABLE `tb_music_reports`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Tablo için AUTO_INCREMENT değeri `tb_news`
--
ALTER TABLE `tb_news`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Tablo için AUTO_INCREMENT değeri `tb_news_content`
--
ALTER TABLE `tb_news_content`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Tablo için AUTO_INCREMENT değeri `tb_push`
--
ALTER TABLE `tb_push`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=222;

--
-- Tablo için AUTO_INCREMENT değeri `tb_users`
--
ALTER TABLE `tb_users`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=208;

--
-- Tablo için AUTO_INCREMENT değeri `tb_user_frame`
--
ALTER TABLE `tb_user_frame`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `tb_views`
--
ALTER TABLE `tb_views`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `tb_wait_message`
--
ALTER TABLE `tb_wait_message`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

CREATE  TABLE `engine4_api_auth` (
  `user_id` INT NOT NULL ,
  `token` VARCHAR(40) NOT NULL ,
  `expire_date` BIGINT NOT NULL ,
  PRIMARY KEY (`user_id`) )
ENGINE = InnoDB;

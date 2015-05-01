CREATE TABLE IF NOT EXISTS `cron_task` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE TABLE IF NOT EXISTS `cron_trigger` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cronId` INT(10) UNSIGNED NOT NULL,
  `dayOfWeek` VARCHAR(255) NULL DEFAULT '*',
  `month` VARCHAR(255) NULL DEFAULT '*',
  `day` VARCHAR(255) NULL DEFAULT '*',
  `hour` VARCHAR(255) NULL DEFAULT '*',
  `minute` VARCHAR(255) NULL DEFAULT '*',
  PRIMARY KEY (`id`),
  INDEX `fk_cron_trigger_cron1_idx` (`cronId` ASC),
  CONSTRAINT `fk_cron_trigger_cron1`
    FOREIGN KEY (`cronId`)
    REFERENCES `cron_task` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE TABLE IF NOT EXISTS `cron_trigger_source` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cronTriggerId` INT(10) UNSIGNED NOT NULL,
  `sourceType` VARCHAR(16) NOT NULL,
  `sourceTemplateId` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_cron_trigger_action_cron_trigger1_idx` (`cronTriggerId` ASC),
  INDEX `fk_cron_trigger_action_search_template1_idx` (`sourceTemplateId` ASC),
  CONSTRAINT `fk_cron_trigger_action_cron_trigger1`
    FOREIGN KEY (`cronTriggerId`)
    REFERENCES `cron_trigger` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_cron_trigger_action_search_template1`
    FOREIGN KEY (`sourceTemplateId`)
    REFERENCES `search_template` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE TABLE IF NOT EXISTS `cron_source_item` (
  `cronTriggerSourceId` INT(10) UNSIGNED NOT NULL,
  `sourceId` INT(10) UNSIGNED NOT NULL,
  INDEX `fk_cron_source_item_cron_trigger_action1_idx` (`cronTriggerSourceId` ASC),
  CONSTRAINT `fk_cron_source_item_cron_trigger_action1`
    FOREIGN KEY (`cronTriggerSourceId`)
    REFERENCES `cron_trigger_source` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE TABLE IF NOT EXISTS `cron_trigger_action` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cronTriggerSourceId` INT(10) UNSIGNED NOT NULL,
  `data` TEXT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_cron_trigger_action_cron_trigger_source1_idx` (`cronTriggerSourceId` ASC),
  CONSTRAINT `fk_cron_trigger_action_cron_trigger_source1`
    FOREIGN KEY (`cronTriggerSourceId`)
    REFERENCES `cron_trigger_source` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE TABLE IF NOT EXISTS `cron_trigger_log` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cronTriggerId` INT(10) UNSIGNED NOT NULL,
  `started` DATETIME NULL DEFAULT NULL,
  `state` VARCHAR(16) NOT NULL,
  `message` VARCHAR(255) NULL DEFAULT '',
  PRIMARY KEY (`id`),
  INDEX `fk_cron_trigger_log_cron_trigger1_idx` (`cronTriggerId` ASC),
  CONSTRAINT `fk_cron_trigger_log_cron_trigger1`
    FOREIGN KEY (`cronTriggerId`)
    REFERENCES `cron_trigger` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci

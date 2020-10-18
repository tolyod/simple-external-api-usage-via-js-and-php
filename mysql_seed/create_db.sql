CREATE TABLE `videos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `video_title` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(20) NOT NULL DEFAULT 'hz',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3910427871745 DEFAULT CHARSET=utf8;

CREATE TABLE `videos_readings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `video_id` bigint(20) unsigned NOT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reading_value` json NOT NULL,
  `content_id_virtual` bigint(20) GENERATED ALWAYS AS (json_unquote(json_extract(`reading_value`,'$.content_id'))) VIRTUAL NOT NULL,
  `status_virtual` varchar(20) GENERATED ALWAYS AS (json_unquote(json_extract(`reading_value`,'$.status'))) VIRTUAL NOT NULL,
  `login_virtual` varchar(4096) GENERATED ALWAYS AS (json_unquote(json_extract(`reading_value`,'$.login'))) VIRTUAL NOT NULL,
  PRIMARY KEY (`id`),
  KEY `video_id` (`video_id`),
  CONSTRAINT `videos_readings_ibfk_1` FOREIGN KEY (`video_id`) REFERENCES `videos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1924 DEFAULT CHARSET=utf8;


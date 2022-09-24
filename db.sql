/*
 Navicat Premium Data Transfer

 Source Server         : 192.168.31.10
 Source Server Type    : MySQL
 Source Server Version : 50736
 Source Host           : 192.168.31.10:3306
 Source Schema         : pass_info

 Target Server Type    : MySQL
 Target Server Version : 50736
 File Encoding         : 65001

 Date: 24/09/2022 18:18:22
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for generate_info
-- ----------------------------
DROP TABLE IF EXISTS `generate_info`;
CREATE TABLE `generate_info`  (
  `id` int(24) NOT NULL AUTO_INCREMENT ,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
  `number` int(18) NULL DEFAULT NULL ,
  `gender` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
  `dept` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
  `class` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
  `generate_time` datetime(0) NULL DEFAULT NULL ,
  `ip` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
  `ip_belonging` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
  `request_type` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
  `photo_path` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
  `agent` varchar(512) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for open_info
-- ----------------------------
DROP TABLE IF EXISTS `open_info`;
CREATE TABLE `open_info`  (
  `id` int(24) NOT NULL AUTO_INCREMENT ,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
  `number` int(18) NULL DEFAULT NULL ,
  `gender` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
  `dept` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
  `class` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
  `open_time` datetime(0) NULL DEFAULT NULL ,
  `ip` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
  `ip_belonging` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
  `gen_id` int(24) NOT NULL ,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_gen_id`(`gen_id`) USING BTREE,
  CONSTRAINT `fk_gen_id` FOREIGN KEY (`gen_id`) REFERENCES `generate_info` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

SET FOREIGN_KEY_CHECKS = 1;

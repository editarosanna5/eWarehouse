DROP PROCEDURE IF EXISTS InitTables;

DELIMITER $$
CREATE PROCEDURE InitTables()
BEGIN
	-- Jenis Komoditas
	INSERT INTO `Types` (type_name) VALUES ('Pre-Starter Broiler Poultry Feed');
	INSERT INTO `Types` (type_name) VALUES ('Starter Broiler Poultry Feed');
	INSERT INTO `Types` (type_name) VALUES ('Finisher Broiler Poultry Feed');
	INSERT INTO `Types` (type_name) VALUES ('Pre-Starter Layer Poultry Feed');
	INSERT INTO `Types` (type_name) VALUES ('Starter Layer Poultry Feed');
	INSERT INTO `Types` (type_name) VALUES ('Grower Layer Poultry Feed');
	INSERT INTO `Types` (type_name) VALUES ('Laying-Phase Layer Poultry Feed');

	-- Daftar Status Palet
	-- INSERT INTO `PalletStatus` (pallet_status) VALUES ('RESERVED_PALLET_ID');
	INSERT INTO `PalletStatus` (pallet_status) VALUES ('UNFILLED');
	INSERT INTO `PalletStatus` (pallet_status) VALUES ('WAITING_TO_BE_STORED');
	INSERT INTO `PalletStatus` (pallet_status) VALUES ('MOVING_TO_STORAGE_ZONE');
	INSERT INTO `PalletStatus` (pallet_status) VALUES ('ON_STORAGE');
	INSERT INTO `PalletStatus` (pallet_status) VALUES ('MOVING_TO_LOADING_ZONE');
	INSERT INTO `PalletStatus` (pallet_status) VALUES ('WAITING_TO_BE_LOADED');
	INSERT INTO `PalletStatus` (pallet_status) VALUES ('LOADING');
	INSERT INTO `PalletStatus` (pallet_status) VALUES ('READY_LOADING_ZONE');

	-- Daftar Status Karung
	INSERT INTO `BagStatus` (bag_status) VALUES ('ID_READY');
	INSERT INTO `BagStatus` (bag_status) VALUES ('READY');
	INSERT INTO `BagStatus` (bag_status) VALUES ('ON_PALLET');
	INSERT INTO `BagStatus` (bag_status) VALUES ('LOADED');

	-- Daftar Kelompok Perangkat
	INSERT INTO `DeviceGroups` (group_name) VALUES ('CONVEYOR_PACKAGING');
	INSERT INTO `DeviceGroups` (group_name) VALUES ('FORKLIFT_STORING');
	INSERT INTO `DeviceGroups` (group_name) VALUES ('FORKLIFT_PICKING');
	INSERT INTO `DeviceGroups` (group_name) VALUES ('CONVEYOR_LOADING');

	-- Daftar Status DO
	INSERT INTO `OrderStatus` (order_status) VALUES ('ON_QUEUE');
	INSERT INTO `OrderStatus` (order_status) VALUES ('ONGOING');
	INSERT INTO `OrderStatus` (order_status) VALUES ('COMPLETED');
END $$

DELIMITER ;
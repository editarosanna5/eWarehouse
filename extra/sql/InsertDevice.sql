DROP PROCEDURE IF EXISTS InsertDevices;

DELIMITER $$
CREATE PROCEDURE InsertDevices(PackagingZone INT, ForkliftStoring INT, ForkliftPicking INT, LoadingZone INT)
BEGIN
	DECLARE i INT;

	-- Menambah Device Packing Zone Sebanyak PackagingZone
	SET i = 1;

	PackagingZone_insert: LOOP
		IF i > PackagingZone THEN
			LEAVE PackagingZone_insert;
		END IF;

		INSERT INTO `Devices` (group_id) VALUES (1);
		SET i = i + 1;
	END LOOP;

	-- Menambah Device Forklift Storing Sebanyak ForkliftStoring
	SET i = 1;

	ForkliftStoring_insert: LOOP
		IF i > ForkliftStoring THEN
			LEAVE ForkliftStoring_insert;
		END IF;

		INSERT INTO `Devices` (group_id) VALUES (2);
		SET i = i + 1;
	END LOOP;

	SET i = 1;

	-- Menambah Device Forklift Picking Sebanyak Forklifticking
	ForkliftPicking_insert: LOOP
		IF i > ForkliftPicking THEN
			LEAVE ForkliftPicking_insert;
		END IF;

		INSERT INTO `Devices` (group_id) VALUES (3);
		SET i = i + 1;
	END LOOP;

	SET i = 1;

	-- Menambah Loading Zone Storing Sebanyak LoadingZone
	LoadingZone_insert: LOOP
		IF i > LoadingZone THEN
			LEAVE LoadingZone_insert;
		END IF;

		INSERT INTO `Devices` (group_id) VALUES (4);
		SET i = i + 1;
	END LOOP;
END $$

DELIMITER ;
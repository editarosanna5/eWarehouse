DROP PROCEDURE IF EXISTS InsertDevices;

DELIMITER $$
CREATE PROCEDURE InsertDevices(ConveyorPackaging INT, ForkliftStoring INT, ForkliftPicking INT, ConveyorLoading INT)
BEGIN
	DECLARE i INT;

	-- Menambah Device Conveyor Packaging Sebanyak ConveyorPackaging
	SET i = 1;

	ConveyorPackaging_insert: LOOP
		IF i > ConveyorPackaging THEN
			LEAVE ConveyorPackaging_insert;
		END IF;

		INSERT INTO `GroupMembers` (member_id, group_id) VALUES (i, 1);
		SET i = i + 1;
	END LOOP;

	-- Menambah Device Forklift Storing Sebanyak ForkliftStoring
	SET i = 1;

	ForkliftStoring_insert: LOOP
		IF i > ForkliftStoring THEN
			LEAVE ForkliftStoring_insert;
		END IF;

		INSERT INTO `GroupMembers` (member_id, group_id) VALUES (i, 2);
		SET i = i + 1;
	END LOOP;

	-- Menambah Device Forklift Picking Sebanyak ForkliftPicking
	SET i = 1;

	ForkliftPicking_insert: LOOP
		IF i > ForkliftPicking THEN
			LEAVE ForkliftPicking_insert;
		END IF;

		INSERT INTO `GroupMembers` (member_id, group_id) VALUES (i, 3);
		SET i = i + 1;
	END LOOP;

	SET i = 1;

	-- Menambah Device Conveyor Loading Sebanyak ConveyorLoading
	ConveyorLoading_insert: LOOP
		IF i > ConveyorLoading THEN
			LEAVE ConveyorLoading_insert;
		END IF;

		INSERT INTO `GroupMembers` (member_id, group_id) VALUES (i, 4);
		SET i = i + 1;
	END LOOP;
END $$

DELIMITER ;
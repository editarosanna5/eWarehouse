DROP PROCEDURE IF EXISTS InsertBags;

DELIMITER $$
CREATE PROCEDURE InsertBags(typeId INTEGER, palletId INTEGER, statusid INTEGER, poNumber INTEGER, productionTimestamp DATETIME)
BEGIN
	-- Menambah Row Sebanyak RowCount
	DECLARE i INT;
	DECLARE poNumberString VARCHAR(255);

	SET i = 1;

	bag_insert: LOOP
		IF i > 49 THEN
			LEAVE bag_insert;
		END IF;

		SET poNumberString = concat('PO_', poNumber - 1 + i);
		INSERT INTO Bags (type_id, pallet_id, status_id, po_number, production_timestamp) VALUES (typeId, palletId, statusid, poNumberString, productionTimestamp);

		IF i = 1 THEN
			UPDATE Pallets SET type_id = typeId, bag_count = i, oldest_bag_timestamp = productionTimestamp WHERE id = palletId;
		ELSE
			UPDATE Pallets SET type_id = typeId, bag_count = i WHERE id = palletId;
		END IF;

		SET i = i + 1;
	END LOOP;
END $$

DELIMITER ;
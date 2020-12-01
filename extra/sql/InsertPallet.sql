DROP PROCEDURE IF EXISTS InsertPallet;

DELIMITER $$
CREATE PROCEDURE InsertPallet(PalletCount INT)
BEGIN
	-- Menambah Pallet Sebanyak PalletCount
	DECLARE i INT;

	SET i = 1;

	pallet_insert: LOOP
		IF i > PalletCount THEN
			LEAVE pallet_insert;
		END IF;

		INSERT INTO `Pallets` () VALUES ();
		SET i = i + 1;
	END LOOP;
END $$

DELIMITER ;
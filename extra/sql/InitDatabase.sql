DROP PROCEDURE IF EXISTS InitDatabase;

DELIMITER $$
CREATE PROCEDURE InitDatabase(PalletCount INT)
BEGIN
	-- Reserved ID untuk Palet
	DECLARE i INT;
	
	SET i = 1;

	INSERT INTO `PalletStatus` (pallet_status) VALUES ('RESERVED_PALLET_ID');
	
	pallet_insert: LOOP
		IF i > PalletCount THEN
			LEAVE pallet_insert;
		END IF;

		INSERT INTO `Pallets` (status_id) VALUES (1);
		SET i = i + 1;
	END LOOP;
END $$

DELIMITER ;
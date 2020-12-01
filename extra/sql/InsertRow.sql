DROP PROCEDURE IF EXISTS InsertRow;

DELIMITER $$
CREATE PROCEDURE InsertRow(RowCount INT)
BEGIN
	-- Menambah Row Sebanyak RowCount
	DECLARE i INT;

	SET i = 1;

	row_insert: LOOP
		IF i > RowCount THEN
			LEAVE row_insert;
		END IF;

		INSERT INTO `Rows` () VALUES ();
		SET i = i + 1;
	END LOOP;
END $$

DELIMITER ;
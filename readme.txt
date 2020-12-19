Setup database:
1.	cd ke path mysql
	contoh command: cd C:\xampp\mysql\bin
2.	run mysql di terminal
	command: -u root
3.	jalankan script create.sql
	command: source create.sql

	a.	jika error, edit path di line 161-167 pada create.sql agar sesuai
	b.	sebelum rerun script create.sql, run script delete sql
		command: source delete.sql
	c.	rerun script create.sql
		command: source create.sql

Setup url:
1.	Modifikasi* file C:\Windows\System32\drivers\etc\hosts
	
	Tambahkan
		127.0.0.1 e-warehouse
	pada file hosts
	
	*modifikasi harus dilakukan dengan text editor yang dirun sebagai admin

2.	Modifikasi file C:\xampp\apache\conf\extra\httpd-vhosts.conf
	
	uncomment

		NameVirtualHost *:80
	
	dan tambahkan
	
		<VirtualHost  *:80>
		    DocumentRoot "C:/xampp/htdocs/eWarehouse/public"
		    ServerName e-warehouse
		</VirtualHost>

	pada file httpd-vhosts.conf

Akses app:
1.	Run server dan mysql
2.	Buka http://e-warehouse pada browser

Route list:
1.	http://e-warehouse
	deskripsi:	home page.

2.	http://e-warehouse/packing
	deskripsi:	terdapat form untuk mengisi data karung yang akan ditambahkan.
				10 angka terakhir po number menandakan id pertama untuk 49 set karung.

3.	http://e-warehouse/packing/create
	deskripsi:	api untuk menambahkan 49 karung berdasarkan form route 2.
				api ini dipanggil secara otomatis saat form route 2 disubmit.

4.	http://e-warehouse/entrance/update
	deskripsi:	api untuk memindahkan karung ke palet.
				status palet menjadi "WAITING_TO_BE_STORED".
				status karung menjadi "ON_PALLET".
				put request:
					a.	new_pallet_id
						format		:	P-0000000XXX (XXX: 001-500)
						deskripsi	:	palet tempat penyimpanan karung.
										discan oleh scanner packaging zone.
					b.	device_id
						format		:	DEV-01-00X (X: 1-2)
						deskripsi	:	id scanner.
										2 angka di tengah menunjukkan area/device group.
										3 angka terakhir menunjukkan nomor line dari area packaging.

5.	http://e-warehouse/storage
	deskripsi:	terdapat form untuk mengisi data palet yang akan disimpan.

6.	http://e-warehouse/storage/map
	deskripsi:	api untuk melihat opsi line penyimpanan.
				jika terdapat opsi line penyimpanan, maka status palet langsung diupdate menjadi "MOVING_TO_STORAGE_ZONE".
				api ini dapat diakses melalui form pada route 5, atau secara langsung dengan get request:
					a.	pallet_id
						format		:	P-0000000XXX (XXX: 001-500)
						deskripsi	:	palet yang akan disimpan.
					b.	device_id
						format		:	DEV-03-00X (X: 1-2)
						deskripsi	:	id scanner forklift penyimpan.

7.	http://e-warehouse/storage/onstorage/update
	deskripsi:	api yang dipanggil saat scanning id line penyimpanan.
				berfungsi untuk memeriksa apakah line penyimpanan sesuai (merupakan salah satu opsi), dan melakukan update status palet menjadi "ON_STORAGE".
				put request:
					a.	row_id
						format		:	ROW-00XX (X: 1-20)
						deskripsi	:	id line penyimpanan.
										discan oleh forklift penyimpan.
					b.	device_id
						format		:	DEV-03-00X (X: 1-2)
						deskripsi	:	id scanner forklift penyimpan.

8.	http://e-warehouse/pickup
	deskripsi:	terdapat form untuk mengisi data order.

9.	http://e-warehouse/pickup/map
	deskripsi:	api yang dipanggil ketika form route 8 disubmit.
				berfungsi untuk menampilkan opsi palet yang dapat diambil beserta posisinya pada line penyimpanan.
				melakukan perhitungan jumlah palet yang dibutuhkan dari dalam gudang (dengan memperhitungkan palet yang tersisa pada loading zone).

10.	http://e-warehouse/pickup/update
	deskripsi:	api yang dipanggil saat scanning palet oleh forklift pengambil.
				memastikan bahwa palet merupakan salah satu opsi palet untuk diambil berdasarkan form pada route 8.
				melakukan update status palet menjadi "MOVING_TO_LOADING_ZONE".
				melakukan perhitungan sisa jumlah palet yang harus diangkut.
				put request:
					a.	pallet_id
						format		:	P-0000000XXX (XXX: 001-500)
						deskripsi	:	palet yang akan diambil.
					b.	device_id
						format		:	DEV-04-00X (X: 1-2)
						deskripsi	:	id scanner forklift pengambil.

11.	http://e-warehouse/loading/palletready/update
	deskripsi:	api yang dipanggil saat scanning palet pada loading zone.
				melakukan update status palet menjadi "READY_LOADING_ZONE".
				put request:
					a.	pallet_id
						format		:	P-0000000XXX (XXX: 001-500)
						deskripsi	:	palet yang sedang diambil.
					b.	device_id
						format		:	DEV-05-00X (X: 1-2)
						deskripsi	:	id scanner pada loading zone line X.

12.	http://e-warehouse/loading/onloading/update
	deskripsi:	api yang dipanggil saat scanning karung pada loading zone.
				melakukan update status palet menjadi "LOADING" (jika belum).
				melakukan update status karung menjadi "LOADED".
				melakukan perhitungan sisa jumlah karung yang harus diangkut.
				put request:
					a.	bag_id
						format		:	B-XXXXXXXXXX
						deskripsi	:	karung yang sedang diload.
					b.	device_id
						format		:	DEV-05-00X (X: 1-2)
						deskripsi	:	id scanner pada loading zone line X.
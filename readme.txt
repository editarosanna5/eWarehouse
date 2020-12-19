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
2.	Buka http://e-warehouse.com pada browser
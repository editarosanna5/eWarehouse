# Lumen PHP Framework

[![Build Status](https://travis-ci.org/laravel/lumen-framework.svg)](https://travis-ci.org/laravel/lumen-framework)
[![Total Downloads](https://img.shields.io/packagist/dt/laravel/framework)](https://packagist.org/packages/laravel/lumen-framework)
[![Latest Stable Version](https://img.shields.io/packagist/v/laravel/framework)](https://packagist.org/packages/laravel/lumen-framework)
[![License](https://img.shields.io/packagist/l/laravel/framework)](https://packagist.org/packages/laravel/lumen-framework)

Laravel Lumen is a stunningly fast PHP micro-framework for building web applications with expressive, elegant syntax. We believe development must be an enjoyable, creative experience to be truly fulfilling. Lumen attempts to take the pain out of development by easing common tasks used in the majority of web projects, such as routing, database abstraction, queueing, and caching.

## Official Documentation

Documentation for the framework can be found on the [Lumen website](https://lumen.laravel.com/docs).

## Contributing

Thank you for considering contributing to Lumen! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Security Vulnerabilities

If you discover a security vulnerability within Lumen, please send an e-mail to Taylor Otwell at taylor@laravel.com. All security vulnerabilities will be promptly addressed.

## License

The Lumen framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

# Tahapan Run Server + Database 
Semua tahapan dibawah ini hanya berlaku jika belum pernah install sama sekali. Jika ada error kontak edita / david
**Table of contents:**
1. [Install Program](#install-program)
2. [Setup Database](#setup-database)
3. [Setup URL](#setup-url)
4. [Akses App](#akses-app)
5. [Route List](#route-list)
6. [Contoh Program Penggunaan di ESP32 (Arduino IDE)](#contoh-program-esp32)

## Install Program
1. Install [XAMPP](https://www.apachefriends.org/index.html) 
2. Install [Composer](https://getcomposer.org/download/)
3. Clone folder github ke `C:\xampp\htdocs`
4. Setelah clone, buka terminal di folder `eWarehouse`
5. ketik dan run command: `composer install`

## Setup Database
1.	cd ke path mysql <br> command: `cd C:\xampp\htdocs\eWarehouse\extra\sql`
2.	run mysql di terminal <br> command: `mysql -u root`
3.	jalankan script create.sql <br> command: `source create.sql`
	- jika error, edit path di line 161-167 pada create.sql agar sesuai
	- sebelum rerun script create.sql, run script delete sql
		command: `source delete.sql`
	- rerun script create.sql
		command: `source create.sql`

## Setup URL
*tidak wajib dijalankan, hanya untuk mempermudah saja*
*tapi jadi tidak bisa mengakses phpMyAdmin*
1.	Modifikasi* file C:\Windows\System32\drivers\etc\hosts
	- Tambahkan `127.0.0.1 e-warehouse` pada file hosts
	- modifikasi harus dilakukan dengan text editor yang dirun sebagai admin

2.	Modifikasi file C:\xampp\apache\conf\extra\httpd-vhosts.conf
	- uncomment `NameVirtualHost *:80 ` dan tambahkan potongan kode dibawah ini pada file httpd-vhosts.conf
	```	
		<VirtualHost  *:80>
		    DocumentRoot "C:/xampp/htdocs/eWarehouse/public"
		    ServerName e-warehouse
		</VirtualHost>
	``` 

## Akses app
1.	Run server dan mysql (jalankan XAMPP)
2.	Buka http://e-warehouse pada browser 
3.  Jika ingin membuka dari device lain, buka http://ipaddress-laptop

## Route list:
> **Deskripsi yang bagus juga dapat dilihat pada Dokumen B300**

*Parameter API disini masih cukup tidak lengkap, Dokumentasi perlu dibagusin lebih jauh lagi*

*http://e-warehouse dapat diganti dengan http://ipaddress-laptop*

1.	http://e-warehouse
	-	deskripsi &rightarrow; home page

2.	http://e-warehouse/packing
	-	deskripsi &rightarrow; terdapat form untuk mengisi data karung yang akan ditambahkan.
	- 10 angka terakhir po number menandakan id pertama untuk 49 set karung.

3.	http://e-warehouse/packing/create
	-	deskripsi &rightarrow; api untuk menambahkan 49 karung berdasarkan form route 2.
	- api ini dipanggil secara otomatis saat form route 2 disubmit.

4.	http://e-warehouse/entrance/update
	-	deskripsi &rightarrow; api untuk memindahkan karung ke palet.
	- status palet berubah menjadi "WAITING_TO_BE_STORED".
	- status karung berubah menjadi "ON_PALLET".
	- put request:
		- new_pallet_id
		: deskripsi &rightarrow; palet tempat penyimpanan karung. 
		:	format &rightarrow; P-0000000XXX (XXX = 001..500)
		: discan oleh scanner packaging zone.
		- device_id
		: format &rightarrow; DEV-01-00X (X = 1..2)
		:	deksripsi &rightarrow; id scanner.
		: 2 angka di tengah menunjukkan area/device group.
		: 3 angka terakhir menunjukkan nomor line dari area packaging.

5.	http://e-warehouse/storage
	- deskripsi &rightarrow; terdapat form untuk mengisi data palet yang akan disimpan.

6.	http://e-warehouse/storage/map
	-	deskripsi &rightarrow; api untuk melihat opsi line penyimpanan.
	- jika terdapat opsi line penyimpanan, maka status palet langsung diupdate menjadi "MOVING_TO_STORAGE_ZONE".
	- api ini dapat diakses melalui form pada route 5, atau secara langsung dengan get request:
		- pallet_id
		: format &rightarrow; P-0000000XXX (XXX: 001-500)
		: deskripsi	&rightarrow; palet yang akan disimpan.
		- device_id
		: format &rightarrow; DEV-03-00X (X: 1-2)
		: deskripsi	&rightarrow; id scanner forklift penyimpan.

7.	http://e-warehouse/storage/onstorage/update
	- deskripsi &rightarrow; api yang dipanggil saat scanning id line penyimpanan.
	- berfungsi untuk memeriksa apakah line penyimpanan sesuai (merupakan salah satu opsi), dan melakukan update status palet menjadi "ON_STORAGE".
	- put request:
		- row_id
		: format &rightarrow; ROW-00XX (X: 1-20)
		: deskripsi &rightarrow; id line penyimpanan.
		: discan oleh forklift penyimpan.
		- device_id
		: format &rightarrow; DEV-03-00X (X: 1-2)
		: deskripsi &rightarrow; id scanner forklift penyimpan.

8.	http://e-warehouse/pickup
	- deskripsi &rightarrow; terdapat form untuk mengisi data order.

9.	http://e-warehouse/pickup/map
	- deskripsi &rightarrow; api yang dipanggil ketika form route 8 disubmit.
	- berfungsi untuk menampilkan opsi palet yang dapat diambil beserta posisinya pada line penyimpanan.
	- melakukan perhitungan jumlah palet yang dibutuhkan dari dalam gudang (dengan memperhitungkan palet yang tersisa pada loading zone).

10.	http://e-warehouse/pickup/update
	- deskripsi &rightarrow; api yang dipanggil saat scanning palet oleh forklift pengambil.
	- memastikan bahwa palet merupakan salah satu opsi palet untuk diambil berdasarkan form pada route 8.
	- melakukan update status palet menjadi "MOVING_TO_LOADING_ZONE".
	- melakukan perhitungan sisa jumlah palet yang harus diangkut.
	- put request:
		- pallet_id
		: format &rightarrow; P-0000000XXX (XXX: 001-500)
		: deskripsi &rightarrow; palet yang akan diambil.
		- device_id
		: format &rightarrow; DEV-04-00X (X: 1-2)
		: deskripsi &rightarrow; id scanner forklift pengambil.

11.	http://e-warehouse/loading/palletready/update
	- deskripsi &rightarrow; api yang dipanggil saat scanning palet pada loading zone.
	- melakukan update status palet menjadi "READY_LOADING_ZONE".
	- put request:
		- pallet_id
		: format &rightarrow; P-0000000XXX (XXX: 001-500)
		: deskripsi &rightarrow; palet yang sedang diambil.
		- device_id
		: format &rightarrow; DEV-05-00X (X: 1-2)
		: deskripsi &rightarrow; id scanner pada loading zone line X.

12.	http://e-warehouse/loading/onloading/update
	- deskripsi &rightarrow; api yang dipanggil saat scanning karung pada loading zone.
	- melakukan update status palet menjadi "LOADING" (jika belum).
	-	melakukan update status karung menjadi "LOADED".
	- melakukan perhitungan sisa jumlah karung yang harus diangkut.
	- put request:
		- bag_id
		: format &rightarrow; B-XXXXXXXXXX
		: deskripsi &rightarrow; karung yang sedang diload.
		- device_id
		: format &rightarrow; DEV-05-00X (X: 1-2)
		: deskripsi &rightarrow; id scanner pada loading zone line X.

## Contoh Program ESP32
```
#include "WiFi.h"
#include "HTTPClient.h"
 
const char* ssid = "ssid";
const char* password =  "pass";
 
void setup() {
  Serial.begin(115200);
  WiFi.begin(ssid, password); 
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.println("Connecting to WiFi..");
  }
  Serial.println("Connected to the WiFi network");
}
 
void loop() {
 if(WiFi.status()== WL_CONNECTED){
   HTTPClient http;   
   http.begin("http://192.168.1.109/entrance/update");
   http.addHeader("Content-Type", "application/json");  
   int httpResponseCode = http.PUT("{\"new_pallet_id\" : \"P-0000000003\",\"device_id\" : \"DEV-01-001\"}");   
   if(httpResponseCode>0){
    String response = http.getString();   
    Serial.println(httpResponseCode);
    Serial.println(response);          
   }else{
    Serial.print("Error on sending PUT Request: ");
    Serial.println(httpResponseCode);
   }
   http.end();
 }else{
    Serial.println("Error in WiFi connection");
 }
  delay(10000);
}
```
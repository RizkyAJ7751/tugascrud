# tugascrud
1.Membuat database db_crud

CREATE DATABASE IF NOT EXISTS db_crud;
USE db_crud;

2.Membuat tabel tb_barang dengan kolom id int auto_increment key,kode varchar,nama varchar,gambar varchar,asal varchar,jumlah int,satuan varchar,tanggal_diterima date,tanggal_disimpan timestamp.

CREATE TABLE IF NOT EXISTS tb_barang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode VARCHAR(50) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    gambar VARCHAR(255),
    asal VARCHAR(50) NOT NULL,
    jumlah INT NOT NULL,
    satuan VARCHAR(20) NOT NULL,
    tanggal_diterima DATE NOT NULL,
    tanggal_disimpan TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

3.Membuat tabel users dengan kolom id int auto_increment key,username varchar,password varchar,email varchar,created_at timestamp auto_increment key.


CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


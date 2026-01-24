CREATE TABLE addresses (
  id int(11) NOT NULL AUTO_INCREMENT,
  street varchar(255) DEFAULT NULL,
  number varchar(50) DEFAULT NULL,
  complement varchar(255) DEFAULT NULL,
  neighborhood varchar(100) DEFAULT NULL,
  city varchar(100) DEFAULT NULL,
  state varchar(50) DEFAULT NULL,
  region varchar(50) DEFAULT NULL,
  postal_code varchar(20) DEFAULT NULL,
  country varchar(50) DEFAULT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (id)
);

CREATE TABLE categories (
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(100) DEFAULT NULL,
  PRIMARY KEY (id)
);

CREATE TABLE companies (
  id int(11) NOT NULL AUTO_INCREMENT,
  address_id int(11) DEFAULT NULL,
  name varchar(255) DEFAULT NULL,
  cnpj varchar(20) DEFAULT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (id),
  KEY address_id (address_id)
);

CREATE TABLE users (
  id int(11) NOT NULL AUTO_INCREMENT,
  company_id int(11) DEFAULT NULL,
  address_id int(11) DEFAULT NULL,
  name varchar(255) DEFAULT NULL,
  email varchar(255) DEFAULT NULL,
  password varchar(255) DEFAULT NULL,
  role varchar(50) DEFAULT NULL,
  document varchar(50) DEFAULT NULL,
  phone varchar(20) DEFAULT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (id),
  UNIQUE KEY email (email),
  KEY company_id (company_id),
  KEY address_id (address_id)
);

CREATE TABLE clients (
  id int(11) NOT NULL AUTO_INCREMENT,
  company_id int(11) NOT NULL,
  created_by int(11) DEFAULT NULL,
  updated_by int(11) DEFAULT NULL,
  type enum('fisica','juridica') NOT NULL DEFAULT 'fisica',
  name varchar(255) DEFAULT NULL,
  cpf varchar(20) DEFAULT NULL,
  company_name varchar(255) DEFAULT NULL,
  fantasy_name varchar(255) DEFAULT NULL,
  cnpj varchar(255) DEFAULT NULL,
  ie varchar(50) DEFAULT NULL,
  segment varchar(100) DEFAULT NULL,
  email varchar(100) DEFAULT NULL,
  phone varchar(20) DEFAULT NULL,
  cep varchar(15) DEFAULT NULL,
  street varchar(255) DEFAULT NULL,
  number varchar(20) DEFAULT NULL,
  complement varchar(100) DEFAULT NULL,
  neighborhood varchar(100) DEFAULT NULL,
  city varchar(100) DEFAULT NULL,
  state varchar(2) DEFAULT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (id),
  KEY company_id (company_id),
  KEY fk_clients_created_by (created_by),
  KEY fk_clients_updated_by (updated_by)
);

CREATE TABLE products (
  id int(11) NOT NULL AUTO_INCREMENT,
  company_id int(11) DEFAULT NULL,
  category_id int(11) DEFAULT NULL,
  name varchar(255) DEFAULT NULL,
  code varchar(50) DEFAULT NULL,
  price decimal(10,2) DEFAULT NULL,
  stock int(11) DEFAULT 0,
  category varchar(50) DEFAULT NULL,
  active tinyint(1) DEFAULT 1,
  created_by int(11) DEFAULT NULL,
  updated_by int(11) DEFAULT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (id),
  KEY company_id (company_id),
  KEY category_id (category_id),
  KEY fk_products_created_by (created_by),
  KEY fk_products_updated_by (updated_by)
);

CREATE TABLE sales (
  id int(11) NOT NULL AUTO_INCREMENT,
  company_id int(11) DEFAULT NULL,
  user_id int(11) DEFAULT NULL,
  updated_by int(11) DEFAULT NULL,
  client_id int(11) DEFAULT NULL,
  total decimal(10,2) DEFAULT NULL,
  status varchar(50) DEFAULT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (id),
  KEY company_id (company_id),
  KEY user_id (user_id),
  KEY client_id (client_id),
  KEY fk_sales_updated_by (updated_by)
);

CREATE TABLE sale_items (
  id int(11) NOT NULL AUTO_INCREMENT,
  sale_id int(11) DEFAULT NULL,
  product_id int(11) DEFAULT NULL,
  quantity int(11) DEFAULT NULL,
  unit_price decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY sale_id (sale_id),
  KEY product_id (product_id)
);

CREATE TABLE stock (
  id int(11) NOT NULL AUTO_INCREMENT,
  company_id int(11) NOT NULL,
  product_id int(11) NOT NULL,
  quantity int(11) NOT NULL,
  type varchar(10) NOT NULL,
  date datetime DEFAULT current_timestamp(),
  PRIMARY KEY (id)
);

CREATE TABLE stock_movements (
  id int(11) NOT NULL AUTO_INCREMENT,
  company_id int(11) DEFAULT NULL,
  product_id int(11) DEFAULT NULL,
  user_id int(11) DEFAULT NULL,
  type varchar(10) DEFAULT NULL,
  quantity int(11) DEFAULT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (id),
  KEY company_id (company_id),
  KEY product_id (product_id),
  KEY user_id (user_id)
);
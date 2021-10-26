show databases;

drop database email_services;
create database email_services;
use email_services;

/* it will drop the table if exists */
drop table if exists admin;
create table admin(
ad_id int not null auto_increment,
ad_name varchar(255),
ad_email varchar(255),
primary key (ad_id)
);
desc admin;


insert into admin (ad_id, ad_name, ad_email) values(
1, 'admin1','admin1@gmail.com');
insert into admin (ad_id, ad_name, ad_email) values(
2, 'admin2','admin2@gmail.com');
insert into admin (ad_id, ad_name, ad_email) values(
3, 'admin3','admin3@gmail.com');
select * from admin;

/* it will drop the table if exists */
drop table if exists merchent;
create table merchent(
mr_id int not null auto_increment,
mr_name varchar(255),
mr_email varchar(255),
mr_password varchar(255),
mr_image blob,
mr_token varchar(255),
mr_status int,
mr_create_time datetime default null,
mr_current_time datetime default null,
primary key (mr_id)
);
desc merchent;

insert into merchent (mr_id, mr_name, mr_email, mr_password, mr_image, mr_token, mr_status, mr_create_time, mr_current_time) values (
1, 'Hussain Ali', 'haq@gmail.com', '90haq', 'C:/xampp/htdocs/Programmers Force (Internship)/Assignment-2/luci.png', 'abcxyz', 1, current_timestamp(), current_timestamp());

select * from merchent;



/* it will drop the table if exists */
drop table if exists card;
create table card(
crd_id int auto_increment not null,
crd_merchent_id int,
crd_card_No bigint(11), /* your credit card number	*/
crd_credit float(7,2), /* example (100.00) */
crd_cvc int (4), /*3 digits at back of credit card*/
crd_valid_from date, /* your card validity */
crd_valid_through date,  /* your card expiery */
primary key (crd_id),
foreign key(crd_merchent_id) references merchent (mr_id)
);
desc card;

/* it will drop the table if exists */
drop table if exists responses;
create table responses(
rsp_id int auto_increment not null,
rsp__status varchar(10),
rsp__description varchar(255),
rsp__error_type varchar(20),
primary key (rsp_id)
);
desc responses;

/* it will drop the table if exists */
drop table if exists secondary_user;
create table secondary_user(
sru_id int auto_increment not null,
sru_merchant_id int,
sru_name varchar(20),
sru_password varchar(255),
sru_email varchar(30),
sru_token varchar(50),
sru_status int,
sru_email_permission bool,
sru_list_view_permission bool,
sru_payment_permission bool,
primary key (sru_id),
foreign key(sru_merchant_id) references merchent(mr_id)
);
desc secondary_user;

/* it will drop the table if exists */
drop table if exists requests;
create table requests(
req_id int auto_increment not null,
req_response_id int unique,
req_merchant_id int,
req_email_subject varchar(50),
req_email_from varchar(20),
req_send_to varchar(50),
req_cc varchar(50),
req_bcc varchar(50),
req_email_body varchar(500),
primary key (req_id),
foreign key(req_merchant_id) references merchent(mr_id),
foreign key(req_response_id) references responses(rsp_id)
);
desc requests;

/*
another way to see enteties(columns) of a table
show columns from requests;
*/

-- This was done at the ANSYS office on April 21 
alter table fi_emp_list add column email varchar(100);
-- Dec 13, 2014
create table fi_lwp (id int primary key auto_increment, emp_id int, from_dt date, to_date date, days int, created_by int);
-- Feb 20, 2015
insert into fi_leave_types values('','Withoutpay','Allowed','Y');

INSERT INTO  `ansysleave`.`fi_leave_types` (
`id` ,
`typename` ,
`comments` ,
`status`
)
VALUES (
NULL ,  'Bereavement',  'Allowed up to 3 Days',  'Y'
);

ALTER TABLE  `fi_lwp` ADD UNIQUE (
`emp_id` ,
`from_dt` ,
`to_date`
);

-- Apr 25, 2015
ALTER TABLE  `fi_leave` ADD  `approved_date` DATETIME NULL ;
ALTER TABLE  `fi_lwp` ADD  `approved_date` DATETIME NULL ;
-- Apr 27, 2015
ALTER TABLE  `fi_lwp` ADD  `applied` DATETIME NULL AFTER  `to_date` ;



---Sept 30 2015-----
CREATE TABLE fi_config (
id INT(6) NOT NULL AUTO_INCREMENT PRIMARY KEY,
param VARCHAR(250) NOT NULL,
value VARCHAR(250) NOT NULL,
)

INSERT INTO  `ansysleave`.`fi_config` (
`id` ,
`param` ,
`value`
)
VALUES (
NULL ,  'leave_max_date',  '2017-01-01'
);


----------16 Feb 2016 ----------
alter table fi_emp_list add column joining_date date default null;
alter table fi_emp_list add column left_on date default null;

--------- 29 Feb 2016 ---------
create table fi_medical_certificates (id int(11) auto_increment primary key not null, leave_id int(11), file_name varchar(255));

ALTER TABLE  `fi_compoff` ADD UNIQUE (`emp_id` ,`work_date`);


-----------9 June 2017 ---------
alter table fi_leave add column leave_cancelled enum('0','1') default '0';


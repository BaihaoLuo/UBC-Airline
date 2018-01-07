/* This script is used to create all tables and data in the database */

DROP TABLE   Vip2;
DROP TABLE   Vip1;
DROP TABLE   Vip;
DROP TABLE   Lounge;
DROP TABLE   Protector;
DROP TABLE   SpecialNeeds;
DROP TABLE   Modification;
DROP TABLE   Luggage_belongsto;
DROP TABLE   Ticket_refersto_has;
DROP TABLE   Customer;
DROP TABLE   Flight_schedule;
DROP TABLE   Time;
DROP TABLE   Employee2;
DROP TABLE   Employee1;


CREATE TABLE Customer(passportID char(8), airmile varchar(12), birthday date, name varchar(40), email varchar(20), addr varchar(40), phoneNum char(10), primary key(passportID));
INSERT INTO Customer VALUES('AB000509',	1050,'1995-01-02','John','john@gmail.com',' #100,2nd Ave',6048858987);
INSERT INTO Customer VALUES('SE012635',5202,'1975-03-14','Josh','josh@gmail.com','#203 10th Ave',6045832145);
INSERT INTO Customer VALUES('BC452698',253,'1954-08-05','Stan','stan@gmail.com','#412 41st Ave', 4588759658);
INSERT INTO Customer VALUES('CA854712',4502,'2001-02-24','Sarah','sarah@gmail.com','#123 13th Ave',5248579654);
INSERT INTO Customer VALUES('UY521369',1000,'1996-09-01','Susie', 'susie@gmail.com', '#243 1st Ave',7858549852);
INSERT INTO Customer VALUES('AC899662',2000,'1940-10-22','David','david@yahoo.com','#2 13th Ave',6045656233);
INSERT INTO Customer VALUES('EX220063',102, '1936-05-01', 'Fu','fu@foxmail.com',   '#30 16th Ave',6042365105);
INSERT INTO Customer VALUES('AC002356',561, '2000-06-02', 'Kyoko','kyoko@yahoo.com',  '#204 10th Ave',6043216546);
INSERT INTO Customer VALUES('CN225789',10, '2005-02-01', 'Su', 'su@gmail.com',      '#22 66th Ave',7783356040);
INSERT INTO Customer VALUES('CN220336',102, '1986-02-13', 'Che','che@gmail.com',   '#156 50th Ave',7786562203);


CREATE TABLE Time(takeOffTime timestamp(0), arrivalTime timestamp(0), primary key(takeOffTime, arrivalTime));
INSERT INTO Time Values('2017-06-15 07:45:00','2017-06-15 10:16:00');
INSERT INTO Time Values('2017-05-10 18:15:00','2017-05-10 20:11:00');
INSERT INTO Time Values('2017-04-09 23:10:00','2017-04-10 17:10:00');
INSERT INTO Time Values('2017-06-04 16:50:00','2017-06-04 19:32:00');
INSERT INTO Time Values('2017-06-15 10:55:00','2017-06-15 18:33:00');
INSERT INTO Time Values('2017-06-16 10:00:00','2017-06-16 13:00:00');

CREATE TABLE Flight_schedule(flightNum char(6) primary key, 
	airline varchar(20), 
    arrivalAirport char(3),
    departureAirport char(3),
    takeOffTime timestamp(0),
    arrivalTime timestamp(0), 
    delayTime varchar(4), 
    foreign key(takeOffTime,arrivalTime) references Time(takeOffTime,arrivalTime));
   
INSERT INTO  Flight_schedule Values('WJ7291', 'Westjet','YVR','YEG','2017-06-15 07:45:00','2017-06-15 10:16:00',15);
INSERT INTO  Flight_schedule Values('AC549', 'Air Canada','YYZ','JFK','2017-05-10 18:15:00','2017-05-10 20:11:00',90);
INSERT INTO  Flight_schedule Values('CX110', 'Cathay Pacific','YVR','HKG','2017-04-09 23:10:00','2017-04-10 17:10:00',0);
INSERT INTO  Flight_schedule Values('AC255', 'Air Canada','YUL','YVR','2017-06-04 16:50:00','2017-06-04 19:32:00',10);
INSERT INTO  Flight_schedule Values('WJ3474', 'Westjet','YEG','YHZ','2017-06-15 10:55:00','2017-06-15 18:33:00',0);
INSERT INTO  Flight_schedule Values('AD250', 'Air DUANG','YVR','YEG','2017-06-16 10:00:00','2017-06-16 13:00:00',25);

CREATE TABLE Ticket_refersto_has(ticketNum char(13) primary key,
	mealPlan char(20) not NULL CHECK (mealPlan in ( 'Reg' , 'Kosher' , 'None' , 'Veg')),
	class char(20) not NULL CHECK (class in ('Economy','First Class','Business')),
	seatNum char(3) not NULL, 
	gateNum char(3),
	flightNum char(6), 
	passportID char(8),
	foreign key(flightNum)references Flight_schedule(flightNum) on delete cascade, 
	foreign key(passportID) references Customer(passportID) on delete cascade);

INSERT INTO Ticket_refersto_has VALUES('0142147571114', 'Reg',	'Economy',	'14E',	   'A12',	'AC549',	'AB000509');
INSERT INTO Ticket_refersto_has VALUES('0142151275434', 'Kosher',	'First Class',	'9B',  'C29',	'AC255',	'BC452698');
INSERT INTO Ticket_refersto_has VALUES('8382177546344', 'None',	'Business',	'13C',	   'B11',	'WJ7291',	'UY521369');
INSERT INTO Ticket_refersto_has VALUES('8382191906399', 'Veg',	'Business',	'10A',	    'D71',	'WJ3474',	'SE012635');
INSERT INTO Ticket_refersto_has VALUES('1602354541420', 'None',	'Economy',	'25D',	    'C42',	'CX110',	'CA854712');
INSERT INTO Ticket_refersto_has VALUES('6462147571114', 'Reg',	'Economy',	'15E',	    'A12',	'AC549',	'AC899662');
INSERT INTO Ticket_refersto_has VALUES('5421515475434', 'Kosher',	'First Class',	'19B',	'C29',	'AC255',	'EX220063');
INSERT INTO Ticket_refersto_has VALUES('8382135654654', 'None',	'Business',	'33C',	    'B11',	'WJ7291',	'AC002356');
INSERT INTO Ticket_refersto_has VALUES('1658465865154', 'Veg',	'Business',	'20A',	    'D71',	'WJ3474',	'CN225789');
INSERT INTO Ticket_refersto_has VALUES('9895515498151', 'None', 'Economy',	'15D',	    'C42',	'CX110',	'CN220336');

/* 2nd tickets */
INSERT INTO Ticket_refersto_has VALUES('1435354355154', 'Veg',	'Business',	'20A',	    'D71',	'AC549',	'CN225789');
INSERT INTO Ticket_refersto_has VALUES('0853455498151', 'None', 'Economy',	'15D',	    'C42',	'AC549',	'CN220336');


INSERT INTO Ticket_refersto_has VALUES('2253455498151', 'None', 'Economy',	'22D',	    'C02',	'AD250',	'CN220336');



CREATE TABLE Employee1(userName varchar(20) primary key,password varchar(20));
CREATE TABLE Employee2(SIN char(9) primary key,
						name varchar(20), 
						jobPos varchar(20), 
						userName varchar(20), 
						foreign key(userName) references Employee1(userName));

INSERT INTO Employee1 VALUES('JessicaC',	'cpsc304');
INSERT INTO Employee1 VALUES('RickI',		'125f1d45ad41e2adf');
INSERT INTO Employee1 VALUES('BillI',		'558dfa41frtn36hth');
INSERT INTO Employee1 VALUES('JeremyM',		'78545dfadf11');
INSERT INTO Employee1 VALUES('JackM',		'abc123');

INSERT INTO Employee2 VALUES('524369854', 'Jessica','Concierge', 	'JessicaC');
INSERT INTO Employee2 VALUES('524771453', 'Rick',   'Information',	'RickI');
INSERT INTO Employee2 VALUES('236984114', 'Bill',	'Information',	'BillI');
INSERT INTO Employee2 VALUES('014852369', 'Jeremy',	'Manager',	'JeremyM');
INSERT INTO Employee2 VALUES('123589647', 'Jack',	'Information',	'JackM');


CREATE TABLE Luggage_belongsto(
							weight integer,
							LID char(9) primary key, 
                            fee integer,
                            ticketNum char(13), 
                             foreign key(ticketNum) references Ticket_refersto_has(ticketNum) on delete cascade);
                            
INSERT INTO Luggage_belongsto VALUES(10, 'A33452215', 0,'0142147571114');   
INSERT INTO Luggage_belongsto VALUES(12, 'B46546512', 0,'5421515475434');  
INSERT INTO Luggage_belongsto VALUES(22, 'S46511654', 5,'8382177546344');  
INSERT INTO Luggage_belongsto VALUES(55, 'Z46545646', 15,'9895515498151'); 
INSERT INTO Luggage_belongsto VALUES(20, 'H65456465', 0,'1658465865154');


CREATE TABLE Lounge(Location varchar(20) primary key,airport varchar(20));
CREATE TABLE Vip(passportID char(8) primary key, 
				 membership varchar(20), 
				 Location varchar(20), 
				 foreign key(Location) references Lounge(Location),
				 foreign key(passportID) references Customer(passportID));

INSERT INTO Lounge VALUES('Room 203(yvr203)',	'YVR');
INSERT INTO Lounge VALUES('Room 396(yyj396)',	'YYJ');
INSERT INTO Lounge VALUES('Room 23(yka23)',	'YKA');
INSERT INTO Lounge VALUES('Room 201(yvr201)',	'YVR');
INSERT INTO Lounge VALUES('Room 203(yxx203)',   'YXX');

INSERT INTO Vip VALUES('AB000509','Air Canada','Room 203(yvr203)');
INSERT INTO Vip VALUES('SE012635','Delta',     'Room 396(yyj396)');
INSERT INTO Vip VALUES('BC452698','United Airline','Room 23(yka23)');
INSERT INTO Vip VALUES('CA854712','Cathay Pacific','Room 201(yvr201)');
INSERT INTO Vip VALUES('UY521369','Westjet','Room 203(yxx203)');

CREATE TABLE Modification(SIN char(9),
			time timestamp,
			ticketNum char(13), 
			primary key(SIN, time),
			foreign key(SIN) references Employee2(SIN)); 
/*			foreign key(ticketNum) references Ticket_refersto_has(ticketNum));*/
            
INSERT INTO Modification VALUES('236984114','2017-05-09 08:15:00', 6462147571114);
INSERT INTO Modification VALUES('014852369','2017-05-04 12:00:00', 5421515475434);
INSERT INTO Modification VALUES('236984114','2017-06-01 13:05:00', 8382135654654);
INSERT INTO Modification VALUES('123589647','2017-04-01 13:10:00', 9895515498151);

/* Division Case */
INSERT INTO Modification VALUES('524771453','2017-06-05 15:05:00', 1658465865154);
INSERT INTO Modification VALUES('524369854','2017-06-05 16:05:00', 1658465865154);
INSERT INTO Modification VALUES('236984114','2017-06-05 17:05:00', 1658465865154);
INSERT INTO Modification VALUES('014852369','2017-06-05 18:05:00', 1658465865154);
INSERT INTO Modification VALUES('123589647','2017-06-05 19:05:00', 1658465865154);


CREATE TABLE SpecialNeeds(status varchar(20),passportID char(8) primary key, foreign key(passportID) references Customer(passportID));
INSERT INTO SpecialNeeds VALUES('Senior','AC899662');
INSERT INTO SpecialNeeds VALUES('Senior','EX220063');
INSERT INTO SpecialNeeds VALUES('Minor','AC002356');
INSERT INTO SpecialNeeds VALUES('Minor','CN225789');
INSERT INTO SpecialNeeds VALUES('Disablity','CN220336');

/*
CREATE TABLE Protector(id char(10) primary key);
INSERT INTO Protector VALUES('protector1');
*/


database readme:

1. Load the example data to the your oracle sqlplus database:
	1)login to the sqlplus with your username and password
	2)run "start data.sql" in the sqlplus to import the example data to your database; run"start drop.sql" first if you are reinstall example data;
	3) now after you connnect to the oracle database(in the web code, using php to connect to the database), you can use sql command to access the example data.

2. Drop all the example data in sqlplus:
	1) after login to the sqlplus, run "start drop.sql"; plz drop all the example data before reinstall the example data

3. To check whether the example data are imported to your database/To show all the table in sqlplus:
	1)logout from the sqlplus and login again
	2)run "showAll.sql" to see whether you have the example data in your database


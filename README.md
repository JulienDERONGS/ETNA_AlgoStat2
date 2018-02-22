# Project location and paths
As it was asked for the project to be able to run without any installation whatsoever, the best solution seemed to be for it to run on a private server.
Here are the ways to access it (outside of the code given on SVN, which is the same) :

## As a user
Website (No login needed) : http://95.85.29.173/algostat2


On the "SORT" page (the index), you will be able to sort any sequence of numbers, float or not (, or .), negative or not, with any other character removed from the sequence.
Once sorted, the resulting sorted sequence will be displayed, along with the time and cost of the sort, how many numbers there were in it, and an average time and cost by numbers.


You will also be able to fill the database with any sequence of X random numbers, X times, using each algorithm (ranging from 2 to 150). The purpose here is to fill the database with random results and thus having good samples for the graphs.


On the "GRAPHS" page, accessible from the link on top of the page, are graphs of each type of algorithm. They are here to show, preferably with large samples, how much does each costs, both in terms of time and iterations. They will treat all data already in the database, so the more sorts that were performed, the more accurate will the graphs be (the random sequences generator was implemented to ease this process).

## As an administrator (if you wish to check that the code & database are the same as in the SVN repository)
Server : ssh ******@95.85.29.173 (Password: ******)
  The project folder is in /var/www/html/algostat2
Database (User: ****** / Password: ******) : http://95.85.29.173/phpmyadmin
  The database used is algostat2.

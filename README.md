SSWA
====

Super secure-webapp..

10 Challenges with 4 types of vulnerability.

Usage
====

	git clone https://github.com/d4rkcat/SSWA
	rm -rf /var/www/*
	cp SSWA/index.php /var/www
	chown -hR www-data /var/www
	service apache2 start
	iceweasel localhost &> /dev/null&
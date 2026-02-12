<?php
echo "Servidor actual:<br>";
echo $_SERVER['SERVER_NAME'] . "<br><br>";

echo "IP del servidor:<br>";
echo $_SERVER['SERVER_ADDR'] . "<br><br>";

echo "Hostname del sistema:<br>";
echo gethostname();

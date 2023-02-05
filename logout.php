<?php
require 'config/constants.php';
// distruge toate sesiunile și redirecționează utilizatorul către pagina de pornire
session_destroy();
header('location: ' . ROOT_URL);
die();

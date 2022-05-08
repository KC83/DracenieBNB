<?php
$todo = readRequestVar('todo');

switch ($todo) {
    case 'disconnect':
        unset($_SESSION[getSessionName()]);
        break;
}
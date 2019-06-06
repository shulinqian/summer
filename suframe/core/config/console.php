<?php
$logo = <<<EOF
  _____                                       ______                        
 / ____|                                     |  ____|                       
| (___  _   _ _ __ ___  _ __ ___   ___ _ __  | |__ _ __ __ _ _ __ ___   ___ 
 \___ \| | | | '_ ` _ \| '_ ` _ \ / _ \ '__| |  __| '__/ _` | '_ ` _ \ / _ \
 ____) | |_| | | | | | | | | | | |  __/ |    | |  | | | (_| | | | | | |  __/
|_____/ \__,_|_| |_| |_|_| |_| |_|\___|_|    |_|  |_|  \__,_|_| |_| |_|\___|
EOF;

return [
	'name' => 'summer framework',
	'description' => 'welcome to use summer framework',
	'debug' => \Inhere\Console\Console::VERB_ERROR,
	'logoText' => $logo,
	'logoStyle' => \Inhere\Console\Component\Style\Style::SUCCESS,
	'profile' => false,
	'version' => '0.0.1',
	'publishAt' => '2019.06.04',
	'updateAt' => '2019.06.04',
	'rootPath' => '',
	'strictMode' => false,
	'hideRootPath' => true,
];
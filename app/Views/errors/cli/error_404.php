<?php /** @var \CodeIgniter\View\View $this */ ?>
<?php

use CodeIgniter\CLI\CLI;

CLI::error('ERROR: ' . $code);
CLI::write($message);
CLI::newLine();

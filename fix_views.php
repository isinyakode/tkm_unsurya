<?php
$viewsDir = __DIR__ . '/app/Views';

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewsDir));
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $path = $file->getRealPath();
        $content = file_get_contents($path);
        $original = $content;

        // Add correct phpdoc if it is missing
        $phpdoc = "<?php /** @var \CodeIgniter\View\View \$this */ ?>\n";
        if (strpos($content, "* @var \CodeIgniter\View\View \$this") === false) {
            // prepend to file
            // Make sure not to duplicate <?php if it already starts with it
            $content = $phpdoc . preg_replace('/^<\?php /** @var \\\CodeIgniter\\\View\\\View \$this \*\/\ \?>\n/', '', $content);
        }

        // Replace void return short echos
        $content = str_replace("<?= \$this->extend", "<?php \$this->extend", $content);
        $content = str_replace("<?= \$this->section", "<?php \$this->section", $content);
        $content = str_replace("<?= \$this->endSection", "<?php \$this->endSection", $content);

        if ($content !== $original) {
            file_put_contents($path, $content);
            echo "Fixed: $path\n";
        }
    }
}
echo "Done.\n";

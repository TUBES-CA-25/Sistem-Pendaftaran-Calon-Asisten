<?php
$dir = new RecursiveDirectoryIterator('app/View');
$ite = new RecursiveIteratorIterator($dir);
$count = 0;
foreach($ite as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $path = $file->getPathname();
        $content = file_get_contents($path);
        $newContent = str_replace(
            'class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm [will-change:opacity] [transform:translateZ(0)]"',
            'class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"',
            $content
        );
        if ($content !== $newContent) {
            file_put_contents($path, $newContent);
            echo "Updated: " . $path . "\n";
            $count++;
        }
    }
}
echo "Total updated: " . $count . "\n";

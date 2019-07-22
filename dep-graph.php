<?php
declare(strict_types=1);

function absoluteDeps(string $component, array $deps): array
{
    $absoluteDeps = $deps[$component];
    foreach ($deps[$component] as $directDep) {
        $absoluteDeps = array_merge($absoluteDeps, absoluteDeps($directDep, $deps));
    }
    $absoluteDeps = array_unique($absoluteDeps);
    sort($absoluteDeps);
    return $absoluteDeps;
}

$deps = [];
foreach (glob('./src/**/*.php') as $file) {
    $code = file_get_contents($file);
    $matches = [];
    preg_match('/namespace BusFactor\\\\(.*);/', $code, $matches);
    $component = $matches[1];
    if (!isset($deps[$component])) {
        $deps[$component] = [];
    }

    preg_match_all('/use BusFactor\\\\(.*);/', $code, $matches);
    if (count($matches) < 2) {
        continue;
    }
    foreach ($matches[1] as $match) {
        $dep = substr($match, 0, strpos($match, '\\'));
        if ($component != $dep) {
            $deps[$component][$dep] = $dep;
            $deps[$component] = array_unique($deps[$component]);
        }
    }
}

foreach ($deps as $component => $directDeps) {
    foreach ($directDeps as $directDep) {
        $absoluteDeps = absoluteDeps($directDep, $deps);
        foreach ($absoluteDeps as $absoluteDep) {
            unset($deps[$component][$absoluteDep]);
        }
    }
}

$dot = [];
foreach ($deps as $component => $directDeps) {
    $dot[] = '    ' . $component;
    foreach ($directDeps as $directDep) {
        $dot[] = sprintf('    %s -> %s', $component, $directDep);
    }
}

echo 'digraph {' . PHP_EOL;
echo implode(PHP_EOL, $dot) . PHP_EOL;
echo '}' . PHP_EOL;

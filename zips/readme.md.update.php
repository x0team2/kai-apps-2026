<?php
$readmeFile = 'readme.md';

// Check if README.md exists; if not, create it with the header
if (!file_exists($readmeFile)) {
    $header = "# KaiOS 2.5 Apps\n\n";
    file_put_contents($readmeFile, $header);
}

// Read existing content
$content = file_get_contents($readmeFile);

// Find all ZIP files in the current directory
$zipFiles = glob("*.zip");

// Append entries for each ZIP file if not already present
foreach ($zipFiles as $zipFile) {
    if (strpos($content, $zipFile) === false) {
        $entry = "## {$zipFile}\n\n[{$zipFile}](https://x0team2.github.io/kai-apps-2026/zips/{$zipFile})\n\n";
        $content .= $entry;
    }
}

// Prepare the updated date
$updatedDate = date("Y-m-d H:i:s");

// Remove existing "Last Updated" line if exists
$content = preg_replace('/^Last Updated: .*\n?$/m', '', $content);

// Append the updated date at the bottom
$content .= "\nLast Updated: {$updatedDate}\n";

// Write back the updated content to README.md
file_put_contents($readmeFile, $content);

echo "README.md has been updated with the latest date.\n";
?>
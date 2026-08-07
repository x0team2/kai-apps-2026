<?php
// Directory setup (create directories if they don't exist)
$upload_dir = './';
$apk_dir = './';
$zip_dir = './';
$count_file = 'count.txt';

if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
if (!is_dir($apk_dir)) mkdir($apk_dir, 0755, true);
if (!is_dir($zip_dir)) mkdir($zip_dir, 0755, true);

// Initialize count file if it doesn't exist
if (!file_exists($count_file)) {
    file_put_contents($count_file, "");
}

// File upload handler
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['app_file'])) {
    $file = $_FILES['app_file'];
    $file_type = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $target_dir = ($file_type == 'apk') ? $apk_dir : $zip_dir;
    
    $target_file = $target_dir . basename($file['name']);
    $uploadOk = 1;
    
    // Check if file already exists
    if (file_exists($target_file)) {
        $upload_message = "Sorry, file already exists.";
        $uploadOk = 0;
    }
    
    // Check file size (5MB max)
    if ($file['size'] > 5000000) {
        $upload_message = "Sorry, your file is too large (max 5MB).";
        $uploadOk = 0;
    }
    
    // Allow only APK and ZIP formats
    if ($file_type != "apk" && $file_type != "zip") {
        $upload_message = "Sorry, only APK (Android) and ZIP (KaiOS) files are allowed.";
        $uploadOk = 0;
    }
    
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 1) {
        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            $upload_message = "The file ". htmlspecialchars(basename($file["name"])). " has been uploaded.";
            // Initialize download count for the new file
            $current_counts = file($count_file, FILE_IGNORE_NEW_LINES);
            $current_counts[] = $file['name'] . ":0"; // Set initial count to 0
            file_put_contents($count_file, implode("\n", $current_counts));
        } else {
            $upload_message = "Sorry, there was an error uploading your file.";
        }
    }
}

// Function to get download count
function getDownloadCount($filename) {
    global $count_file;
    $counts = file($count_file, FILE_IGNORE_NEW_LINES);
    foreach ($counts as $count) {
        list($file, $number) = explode(':', $count);
        if ($file == $filename) {
            return (int)$number;
        }
    }
    return 0;
}

// Function to increment download count
function incrementDownloadCount($filename) {
    global $count_file;
    $counts = file($count_file, FILE_IGNORE_NEW_LINES);
    $new_counts = [];
    foreach ($counts as $count) {
        list($file, $number) = explode(':', $count);
        if ($file == $filename) {
            $number++;
        }
        $new_counts[] = $file . ':' . $number;
    }
    file_put_contents($count_file, implode("\n", $new_counts));
}

// Get lists of files filtered by extension
function getFileList($dir, $ext) {
    $files = [];
    if (is_dir($dir)) {
        foreach (scandir($dir) as $file) {
            if ($file != '.' && $file != '..' && strtolower(pathinfo($file, PATHINFO_EXTENSION)) == $ext) {
                $files[] = [
                    'name' => $file,
                    'path' => $dir . $file,
                    'size' => filesize($dir . $file),
                    'date' => date("F d Y H:i:s", filemtime($dir . $file)),
                    'count' => getDownloadCount($file) // Get download count
                ];
            }
        }
    }
    return $files;
}

$android_apps = getFileList($apk_dir, 'apk');
$kaios_apps = getFileList($zip_dir, 'zip');

// Handle download requests
if (isset($_GET['download'])) {
    $file_to_download = basename($_GET['download']);
    $ext = strtolower(pathinfo($file_to_download, PATHINFO_EXTENSION));
    $file_path = ($ext == 'apk') ? $apk_dir . $file_to_download : $zip_dir . $file_to_download;
    
    if (file_exists($file_path)) {
        incrementDownloadCount($file_to_download);
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_path));
        readfile($file_path);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>App Hosting Platform</title>

<!-- Primary Meta Tags -->
<meta name="description" content="Host and download Android (APK) and KaiOS (ZIP) apps with download tracking. Simple platform for app distribution.">
<meta name="keywords" content="APK, ZIP, Android apps, KaiOS apps, app hosting, file sharing">
<meta name="author" content="App Hosting Platform">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:title" content="App Hosting Platform - Share Android & KaiOS Apps">
<meta property="og:description" content="Upload and download APK and ZIP files with download counter tracking">
<meta property="og:url" content="https://x0.rf.gd/host/">
<meta property="og:site_name" content="App Hosting">
<meta property="og:image" content="banner.png">
<meta property="og:image:alt" content="App Hosting Platform with Android and KaiOS logos">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">

<!-- Twitter -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="App Hosting Platform - Share Android & KaiOS Apps">
<meta name="twitter:description" content="Upload and download APK and ZIP files with download counter tracking">
<meta name="twitter:image" content="banner.png">
<meta name="twitter:image:alt" content="App Hosting Platform with Android and KaiOS logos">

<!-- Theme Color -->
<meta name="theme-color" content="#4f46e5">



    <style>
        :root {
            --primary-color: #4f46e5;
            --secondary-color: #f9fafb;
            --accent-color: #10b981;
            --error-color: #ef4444;
            --text-color: #111827;
            --light-text: #6b7280;
            --border-color: #e5e7eb;
            --card-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f3f4f6;
            color: var(--text-color);
            line-height: 1.6;
            padding: 20px;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        /* Header Styles */
        header {
            background-color: white;
            box-shadow: var(--card-shadow);
            padding: 2rem;
            margin-bottom: 2rem;
            border-radius: 8px;
            text-align: center;
            background: linear-gradient(135deg, #f5f7fa 0%, #f3f4f6 100%);
        }
        
        h1 {
            color: var(--primary-color);
            margin-bottom: 0.5rem;
            font-size: 2.2rem;
        }
        
        /* Upload Section */
        .upload-section {
            background-color: white;
            padding: 2rem;
            margin-bottom: 2rem;
            border-radius: 8px;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
        }
        
        .upload-section:hover {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .upload-form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }
        
        .file-input-wrapper input[type="file"] {
            font-size: 100px;
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        
        .custom-file-upload {
            border: 2px dashed var(--border-color);
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .custom-file-upload:hover {
            border-color: var(--primary-color);
            background-color: rgba(79, 70, 229, 0.05);
        }
        
        .upload-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            color: var(--primary-color);
        }
        
        .upload-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
            font-size: 1rem;
            width: 100%;
        }
        
        .upload-btn:hover {
            background-color: #4338ca;
        }
        
        .upload-message {
            margin-top: 1rem;
            padding: 0.75rem;
            border-radius: 4px;
            text-align: center;
            font-weight: 500;
        }
        
        .success-message {
            background-color: #f0fdf4;
            color: #166534;
        }
        
        .error-message {
            background-color: #fee2e2;
            color: #991b1b;
        }
        
        /* Apps Section */
        .apps-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .app-list {
            background-color: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: var(--card-shadow);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .app-list:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .app-list h2 {
            color: var(--primary-color);
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid var(--primary-color);
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .app-list h2::before {
            content: "";
            display: inline-block;
            width: 24px;
            height: 24px;
            background-size: contain;
            background-repeat: no-repeat;
        }
        
        .android-title::before {
            background-image: url('https://placehold.co/24x24?text=A');
        }
        
        .kaios-title::before {
            background-image: url('https://placehold.co/24x24?text=K');
        }
        
        .app-item {
            padding: 1rem 0;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
        }
        
        .app-item:last-child {
            border-bottom: none;
        }
        
        .app-info {
            flex: 1;
            min-width: 0;
        }
        
        .app-name {
            font-weight: 600;
            margin-bottom: 0.25rem;
            font-size: 1.1rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .app-meta {
            font-size: 0.85rem;
            color: var(--light-text);
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            align-items: center;
        }
        
        .download-count {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
        
        .download-count::before {
            content: "↓";
        }
        
        .download-btn {
            background-color: var(--accent-color);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s ease;
            white-space: nowrap;
            font-weight: 500;
        }
        
        .download-btn:hover {
            background-color: #0d9488;
        }
        
        .empty-message {
            padding: 1rem 0;
            color: var(--light-text);
            text-align: center;
            font-style: italic;
        }
        
        /* Platform Indicators */
        .platform-indicator {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: bold;
            text-transform: uppercase;
            margin-right: 0.5rem;
        }
        
        .android-indicator {
            background-color: #3ddc84;
            color: white;
        }
        
        .kaios-indicator {
            background-color: #0078d7;
            color: white;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .apps-section {
                grid-template-columns: 1fr;
            }
            
            header {
                padding: 1.5rem;
            }
            
            .upload-section, .app-list {
                padding: 1.25rem;
            }
        }
        
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .app-item {
            animation: fadeIn 0.3s ease forwards;
        }
        
        .app-item:nth-child(odd) {
            animation-delay: 0.05s;
        }
        
        .app-item:nth-child(even) {
            animation-delay: 0.1s;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>App Hosting Platform</h1>
            <p>Upload and distribute your Android (APK) and KaiOS (ZIP) applications</p>
        </header>
        
        <section class="upload-section">
            <h2>Upload Your App</h2>
            <form class="upload-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
                <div class="file-input-wrapper">
                    <label class="custom-file-upload">
                        <div class="upload-icon">📁</div>
                        <span>Click to select APK or ZIP file</span>
                        <span style="font-size: 0.8rem; color: var(--light-text);">Max file size: 5MB</span>
                        <input type="file" name="app_file" id="app_file" required accept=".apk,.zip">
                    </label>
                </div>
                <button type="submit" class="upload-btn">Upload App</button>
            </form>
            <?php if (isset($upload_message)): ?>
                <div class="upload-message <?php echo ($uploadOk ?? 1) == 0 ? 'error-message' : 'success-message'; ?>">
                    <?php echo $upload_message; ?>
                </div>
            <?php endif; ?>
        </section>
        
        <section class="apps-section">
            <div class="app-list">
                <h2 class="android-title">Android Apps</h2>
                <?php if (!empty($android_apps)): ?>
                    <?php foreach ($android_apps as $app): ?>
                        <div class="app-item">
                            <div class="app-info">
                                <div class="app-name"><?php echo pathinfo($app['name'], PATHINFO_FILENAME); ?></div>
                                <div class="app-meta">
                                    <span class="platform-indicator android-indicator">Android</span>
                                    <span><?php echo round($app['size'] / 1024 / 1024, 2); ?> MB</span>
                                    <span>•</span>
                                    <span><?php echo $app['date']; ?></span>
                                    <span>•</span>
                                    <span class="download-count"><?php echo $app['count']; ?></span>
                                </div>
                            </div>
                            <a href="?download=<?php echo urlencode($app['name']); ?>" class="download-btn">Download</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="empty-message">No Android apps available</p>
                <?php endif; ?>
            </div>
            
            <div class="app-list">
                <h2 class="kaios-title">KaiOS Apps</h2>
                <?php if (!empty($kaios_apps)): ?>
                    <?php foreach ($kaios_apps as $app): ?>
                        <div class="app-item">
                            <div class="app-info">
                                <div class="app-name"><?php echo pathinfo($app['name'], PATHINFO_FILENAME); ?></div>
                                <div class="app-meta">
                                    <span class="platform-indicator kaios-indicator">KaiOS</span>
                                    <span><?php echo round($app['size'] / 1024 / 1024, 2); ?> MB</span>
                                    <span>•</span>
                                    <span><?php echo $app['date']; ?></span>
                                    <span>•</span>
                                    <span class="download-count"><?php echo $app['count']; ?></span>
                                </div>
                            </div>
                            <a href="?download=<?php echo urlencode($app['name']); ?>" class="download-btn">Download</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="empty-message">No KaiOS apps available</p>
                <?php endif; ?>
            </div>
        </section>
    </div>
</body>
</html>

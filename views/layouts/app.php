<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="NYT Authentication System">
    <title><?php echo htmlspecialchars($title ?? 'NYT'); ?></title>
    <!-- Base Styles -->
    <link rel="stylesheet" href="/css/app.css">
    <!-- Page Specific Styles -->
    <?php if (isset($styles) && is_array($styles)): ?>
        <?php foreach ($styles as $style): ?>
            <link rel="stylesheet" href="<?php echo htmlspecialchars($style); ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <!-- Main Content -->
    <div id="app">
        <?php echo $content ?? ''; ?>
    </div>

    <!-- Base Scripts -->
    <script src="/js/app.js"></script>
    <!-- Page Specific Scripts -->
    <?php if (isset($scripts) && is_array($scripts)): ?>
        <?php foreach ($scripts as $script): ?>
            <script src="<?php echo htmlspecialchars($script); ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
